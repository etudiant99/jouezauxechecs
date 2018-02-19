<?php
include_once "./echecs/classes/Courriel.class.php";
class Inscription
{
    public function valide_infos($pseudo,$courriel,$confirmation_courriel)
    {
        $valeur_pseudo = "";
        $valeur_confirmation = "";
        $type_pseudo = "hidden";
        $type_confirmation = "hidden";
        $statut_pseudo = true;
        $helo_address = "mail.etudiant99.com";
        $verifierdomaine = true;
        $verifieradresse = true;
        $adresseretour = "denis@etudiant99.com";
        $erreursretournees = false;

        $essai = new courriel;
        $resultats = array();

        // 4 Vérifications pour le pseudo
        if ($pseudo == "")
        {
            $valeur_pseudo = 'champ obligatoire';
            $statut_pseudo = false;
        }
        else if (strlen($pseudo) < 5)
        {
            $valeur_pseudo = 'pseudonyme trop court';
            $statut_pseudo = false;
        }
        else if (strlen($pseudo) > 20)
        {
            $valeur_pseudo = 'pseudonyme trop long';
            $statut_pseudo = false;
        }
        else
        {
            // Vérification des informations
            $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);

            $requete = "select count(*) as nombre from login where pseudo = '$pseudo'";
            $resultat = $dbh->query($requete);
            $ligne = $resultat->fetch();
            $nombre = $ligne['nombre'];

            if ($nombre > 0)
            {
                $valeur_pseudo = "pseudonyme déjà présent";
                $statut_pseudo = false;
            }
        }

        // vérification pour le courriel
        if ($courriel == "")
            $valeur_confirmation = "champ obligatoire";

        // vérification pour la confirmation du courriel
        if ($confirmation_courriel == "")
            $valeur_confirmation = "champ obligatoire";
        
        if (($courriel != $confirmation_courriel) && (($courriel != "") && ($confirmation_courriel != "")))
            $valeur_confirmation = "courriels non-identiques";
        
        if ($courriel != "")
        {
            $bonne_adresse = $this->VerifierAdresseMail($courriel);
            if ($bonne_adresse == false)
                $valeur_confirmation = "format entré incorrect";
            else
            {
                $erreur = $essai->validateEmail($courriel,$verifierdomaine,$verifieradresse,$adresseretour,$helo_address,$erreursretournees);
                
                if ($erreur == true)
                    $valeur_confirmation = "adresse courriel inexistante";
            }
        }    
    
        if ($valeur_confirmation != "")
            $type_confirmation = "text";
    
        if ($valeur_pseudo != "")
            $type_pseudo = "text";
    
        $resultats['pseudo'] = $valeur_pseudo;
        $resultats['confirmation'] = $valeur_confirmation;
        $resultats['type_pseudo'] = $type_pseudo;
        $resultats['type_confirmation'] = $type_confirmation;
        $resultats['pseudo_ok'] = $statut_pseudo;
    
        return $resultats;
    }

    public function VerifierAdresseMail($adresse) 
    {
        $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#'; 
        if (preg_match($Syntaxe,$adresse))
            return true;    
        else
            return false;  
    }

    public function Genere_Password($size)
    {
        $password = '';        
        
        // Initialisation des caractères utilisables
        $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        
        for($i=0;$i<$size;$i++){
            $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
        }
        
        return $password;
    }

    public function EnvoiCourriel($adresse_email,$nom_utilisateur,$pascrypte)
    {
        // Preparation du mail
        $nom = "Adminstrateur du site";
        $mail = "denis@etudiant99.com";

        // voici la version Mine 
        $headers = "MIME-Version: 1.0\r\n"; 

        // ici on détermine le mail en format text 
        $headers .= "Content-type: text/html; charset=UTF-8\r\n"; 
        
        $headers .= "From: $nom <$mail>\r\nX-Mailer:PHP";
        // dans le From tu mets l'expéditeur du mail 

        $sujet="Validation inscription"; 
        $subject="$sujet"; 
        
        $destinataire=  $nom_utilisateur.' <'.$adresse_email.'>';

        $adresse = $_SERVER['PHP_SELF'];
        $adresseServeur = $_SERVER['SERVER_ADDR'];

        $corps = "<p>Bonjour, ".$nom_utilisateur."</p>";
        $corps .= "<p>Votre inscription au site <b><u>jouezauxechecs</b></u> a été réalisée.<br />";
        $corps.= "Un mot de passe temporaire vous est envoyé maintenant.<br />";
        $corps.= "Il permettra de vous connecter au site, et de jouer des parties d'échecs.</p>";
        
        $corps.= "<p>Je veux vous préciser que ce mot de passe est connu de vous seul.<br />";
        $corps.= "Il a été créé automatiquement. Alors, même l'administrateur du site<br />";
        $corps.= "ne connaît pas ce mot de passe qui vous a été envoyé.</p>";
        
        $corps.= "<p>De plus, ce mot de passe temporaire a été ajouté, automatiquement, à une <br />";
        $corps.= "base de données, mais de facon cryptée, donc illisible par l'administrateur aussi.</p>";
        
        $corps.= "<p>Alors, pour vous connecter au site,</p>";
        
        $corps.="<p>&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Pseudo:</b></u>  ".$nom_utilisateur."</p>";
        $corps.="<p>&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Mot de passe:</u></b>  ".$pascrypte."</p>";
        
        //$corps.='<p><a href="http://etudiant99.com/jouezauxechecs/">Jouer aux echecs</a></p>';
        $corps.='<p><a href="http://localhost/jouezauxechecs/">Jouer aux echecs</a></p>'; // en local
        $corps.="<p>Vous pouvez modifier votre mot de passe par le menu <b><u>profil</b></u>.<br />";
        $corps.="Vous serez déconnecté et devrez vous reconnecter avec le nouveau mot de passe "."</p>";
        $corps.="<p>::::::::::::::: MAIL AUTOMATIQUE - NE PAS Y RÉPONDRE :::::::::::::::</p>";

        mail($destinataire,$subject,$corps,$headers);       
        
        Return;
    }

    public function enregistrer_joueur($pseudo,$courriel,$confirmation_courriel,$jour,$mois,$annee,$sexe,$prenom,$nom,$pays)
    {
        $les_jours = array
        ('jour','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');

        $les_mois = array
        ('mois','janvier','fevrier','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre');

        $les_annees = array
        ('année','2016','2015','2014','2013','2012','2011','2010','2009','2008','2007','2006','2005','2004','2003','2002','2001',
         '2000','1999','1998','1997','1996','1995','1994','1993','1992','1991',
         '1990','1989','1988','1987','1986','1985','1984','1983','1982','1981',
         '1980','1979','1978','1977','1976','1975','1974','1973','1972','1971',
         '1970','1969','1968','1967','1966','1965','1964','1963','1962','1961',
         '1960','1959','1958','1957','1956','1955','1954','1953','1952','1951',
         '1950','1949','1948','1947','1946','1945','1944','1943','1942','1941',
         '1940','1939','1938','1937','1936','1935','1934','1933','1932','1931',
         '1930','1929','1928','1927','1926','1925','1924','1923','1922','1921',
         '1920','1919','1918','1917','1916','1915','1914','1913','1912','1911',
         '1910','1909','1908','1907','1906','1905','1904','1903','1902','1901','1900');

        $les_pays = array
        ('pays','Algérie','Belgique','Bulgarie','Canada','Etats-Unis','France','Liban','Luxembourg','Maroc','Mexique','Québec','Suisse','Tunisie');

        $les_sexes = array
        ('homme','femme');

        $panier = $this->valide_infos($pseudo,$courriel,$confirmation_courriel);
        $valeur_pseudo = $panier['pseudo'];
        $valeur_confirmation = $panier['confirmation'];
        $type_pseudo = $panier['type_pseudo'];
        $type_confirmation = $panier['type_confirmation'];
        $statut_pseudo = $panier['pseudo_ok'];

        if (($statut_pseudo == true) &&($type_confirmation == "hidden")){
            $journee = $les_jours[$jour];
            if (intval($journee) < 10)
                $journee = '0'.$journee;
            if ($les_jours[$jour] == '-')
                $journee = '00';
        
            $mois_annee = $mois;
            if (intval($mois_annee) < 10)
                $mois_annee = '0'.$mois;
                
            if ($les_jours[$jour] == 'jour')
                $journee = '00';

            if ($mois_annee == 'mois')
                $mois_annee = '00';

            if ($les_annees[$annee] == 'année')
                $les_annees[$annee] = '0000';
                        
            $naissance = $les_annees[$annee]."-".$mois_annee."-".$journee;
            $le_sexe_personne = substr($les_sexes[$sexe],0,1);

            switch ($pays)
            {
                case ('0') :
                    $lepays = 'zz.png';
                    break;
                case ('1') :
                    $lepays = 'dz.png';
                    break;
                case ('2') :
                    $lepays = 'be.png';
                    break;
                case ('3') :
                    $lepays = 'bu.png';
                    break;
                case ('4') :
                    $lepays = 'ca.png';
                    break;
                case ('5') :
                    $lepays = 'us.png';
                    break;
                case ('6') :
                    $lepays = 'fr.png';
                    break;
                case ('7') :
                    $lepays = 'lb.png';
                    break;
                case ('8') :
                    $lepays = 'ln.png';
                    break;
                case ('9') :
                    $lepays = 'ma.png';
                    break;
                case ('10') :
                    $lepays = 'me.png';
                    break;
                case ('11') :
                    $lepays = 'qu.png';
                    break;
                case ('12') :
                    $lepays = 'ro.png';
                    break;                
                case ('13') :
                    $lepays = 'ch.png';
                    break;
                case ('14') :
                    $lepays = 'tn.png';
                    break;
                default :
                    $lepays = NULL;
            }
            $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);

            $sql = "insert into users (uid, prenom, nom, sexe, naissance, pays, courriel, date_inscription) 
            values (NULL, $prenom, $nom, '$le_sexe_personne', '$naissance', '$lepays', '$courriel', now())";
            $dbh->exec($sql);
            
            $requete = "select uid from users order by uid desc";
            $resultat = $dbh->query($requete);
            $ligne = $resultat->fetch();
            $mon_uid = $ligne['uid'];      

            $secret = $this->Genere_Password(8);
            $invisible =  md5($secret);
            
            $sql = "insert into login (uid, pseudo, bidon, date_inscription) values ($mon_uid, '$pseudo', '$invisible', now())";
            $dbh->exec($sql);
            
            $sql = "insert into statistiques (uid) values ($mon_uid)";
            $dbh->exec($sql);

            $this->EnvoiCourriel($courriel,$pseudo,$secret);
            
            header('Location: ./index.php');
        }

        return $panier;
    }
}
?>