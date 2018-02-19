<?php
class Connection
{
    public static $instance;
    private $_id;
    private $_uid;
    private $_elo;
    private $_session;
    private $_timestand ;
    
    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }
    
    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value)
        {
            $method = 'set'.ucfirst($key);
            
            if (method_exists($this, $method))
                $this->$method($value);
        }
    }

    public function setId($id)
    {
        $this->_id = $id;
    }
    
    public function setUid($id)
    {
        $this->_uid = $id;
    }

    public function setElo($id)
    {
        $this->_elo = $id;
    }
   
    public function setSession($id)
    {
        $this->_session = $id;
    }

    public function setTimestand($id)
    {
        $this->_timestand($id);
    }

    public function detailConnection($id)
    {
        $composantes = array();
        
        return $composantes;
    }
    
    public function id()
    {
        return $this->_id;
    }

    public function uid()
    {
        return $this->_uid;
    }
    
    public function session()
    {
        return $this->_session;
    }

    public function timestand()
    {
        return $this->_timestand;
    }

    public function elo()
    {
        return $this->_elo;
    }

}

?>