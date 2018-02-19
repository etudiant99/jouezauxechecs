<?php

class courriel
{
    private $_nbmailers;
    private $_nommailers;
    
    public function getnbmaillers()
    {
        return $this->_nbmailers;
    }
    
    public function getnommailers()
    {
        return $this->_nommailers;
    }
    
    public function validateEmail($email, $domainCheck = false, $verify = false, $probe_address='', $helo_address='', $return_errors=false)
    {
        global $debug;
        $tcp_connect_timeout = 18000;
        $smtp_timeout = 6000;

        //$debug = true;
        if($debug)
            echo "<pre>";

        # Check email syntax with regex
        if (preg_match('/^([a-zA-Z0-9\'\._\+-]+)\@((\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,7}|[0-9]{1,3})(\]?))$/', $email, $matches))
        {
            $domain = $matches[2];        
        
            # Check availability of  MX/A records
            if ($domainCheck)
            {
                # Construct array of available mailservers
                getmxrr($domain, $mxhosts, $mxweight);
                if(count($mxhosts) > 0)
                {
                    for($i=0;$i<count($mxhosts);$i++)
                        $mxs[$mxhosts[$i]] = $mxweight[$i];
                    asort($mxs);
                    $mailers = array_keys($mxs);
                    $this->_nommailers = array_keys($mxs);
                }
                # No MX found, use A
                elseif(checkdnsrr($domain, 'A'))
                    $mailers[0] = gethostbyname($domain);
                else
                    $mailers=array();
            
                $total = count($mailers);
                $this->_nbmailers = $total;
            
                # Query each mailserver
                // Si domaine est pas bon, 0 mailer
                if($total > 0 && $verify)
                {
                    # Check if mailers accept mail
                    for($n=0; $n < $total; $n++)
                    {
                        # Check if socket can be opened
                        if($debug)
                            echo "Checking server $mailers[$n]...\n";
                        $errno = 0;
                        $errstr = 0;
                        # Try to open up TCP socket
                        if($sock = @fsockopen($mailers[$n], 25, $errno , $errstr, $tcp_connect_timeout))
                        {
                            $response = fread($sock,8192);
                            if($debug)
                                echo "Opening up socket to $mailers[$n]... Succes!\n";
                            stream_set_timeout($sock, $smtp_timeout);
                            $meta = stream_get_meta_data($sock);
                            if($debug)
                                echo "$mailers[$n] replied: $response\n";
                            $cmds = array("HELO $helo_address","MAIL FROM: <$probe_address>","RCPT TO:<$email>","QUIT",);
                            # Hard error on connect -> break out
                            # Error means 'any reply that does not start with 2xx '
                            if(!$meta['timed_out'] && !preg_match('/^2\d\d[ -]/', $response))
                            {
                                $error = "Error: $mailers[$n] said: $response\n";
                                break;
                            }
                            foreach($cmds as $cmd)
                            {
                                $before = microtime(true);
                                fputs($sock, "$cmd\r\n");
                                $response = fread($sock, 4096);
                                $t = 1000*(microtime(true)-$before);
                                if($debug)
                                    echo htmlentities("$cmd\n$response") . "(" . sprintf('%.2f', $t) . " ms)\n";
                                if(!$meta['timed_out'] && preg_match('/^5\d\d[ -]/', $response))
                                {
                                    $error = "Unverified address: $mailers[$n] said: $response";
                                    break 2;
                                }
                            }
                            fclose($sock);
                            if($debug)
                                echo "Succesful communication with $mailers[$n], no hard errors, assuming OK";
                            break; // On arrête quand la communication a pu être établie
                        }
                        elseif($n == $total-1)
                            $error = "None of the mailservers listed for $domain could be contacted";
                    }
                }
                elseif($total <= 0)
                    $error = "No usable DNS records found for domain '$domain'";
            }
        }
        else
            $error = 'Address syntax not correct';
        if($debug)
            echo "</pre>";
            
        if($return_errors)
        {
            # Give back details about the error(s).
            # Return FALSE if there are no errors.
            if(isset($error))
                return nl2br(htmlentities($error));
            else
                return false;
            }
        else
        {
            # 'Old' behaviour, simple to understand
            if(isset($error))
                return true;
            else
                return false;
        }
    }

    
}

?>