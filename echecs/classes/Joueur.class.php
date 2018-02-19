<?php
class Joueur
{
    private $_uid;
    private $_pseudo;
    private $_pseudostylise;
    private $_destinatairestylise;
    private $_elo;
    private $_sexe;
    private $_connecte;
    private $_date_connection;
    private $_date_inscription;
    private $_naissance;
    private $_description;
    private $_pays;
    private $_photo;
    
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

    public function setUid($id)
    {
        $this->_uid = $id;
    }
    
    public function setPseudo($id)
    {
        $this->_pseudo = $id;
    }
    
    public function setPseudostylise($id)
    {
        $sexe = $this->sexe();
        $connecte = $this->connecte();

        if ($sexe == 'h')
            if ($connecte == 1)
                $style = '<a href="user_frame.php?uid='.$this->uid().'" rel="superbox[iframe.wikipedia][454x380]"><img src="images/icons/active.gif" border="0"><font class="homme">'.$this->pseudo().'</a></font>';
            else
                $style = '<a href="user_frame.php?uid='.$this->uid().'" rel="superbox[iframe.wikipedia][454x380]"><font class="homme">'.$this->pseudo().'</font></a>';
        else
            if ($connecte == 1)
                $style = '<a href="user_frame.php?uid='.$this->uid().'" rel="superbox[iframe.wikipedia][454x380]"><img src="images/icons/active.gif" border="0"><font class="femme">'.$this->pseudo().'</font></a>';
            else
                $style = '<a href="user_frame.php?uid='.$this->uid().'" rel="superbox[iframe.wikipedia][454x380]"><font class="femme">'.$this->pseudo().'</font></a>';
        
        $this->_pseudostylise = $style;
    }

    public function setDestinatairestylise($id)
    {
        $sexe = $this->sexe();
        $connecte = $this->connecte();
        
        if ($sexe == 'h')
            if ($connecte == 1)
                $infos = '<a href="user_frame.php?uid='.$this->uid().'" rel="superbox[iframe.wikipedia][454x380]"><img src="images/icons/active.gif" border="0"><font class="homme">'.$this->pseudo().'</a></font>';
            else
                $infos = '<a href="user_frame.php?uid='.$this->uid().'" rel="superbox[iframe.wikipedia][454x380]"><font class="homme">'.$this->pseudo().'</font></a>';
        else
            if ($connecte == 1)
                $infos = '<a href="user_frame.php?uid='.$this->uid().'" rel="superbox[iframe.wikipedia][454x380]"><img src="images/icons/active.gif" border="0"><font class="femme">'.$this->pseudo().'</font></a>';
            else
                $infos = '<a href="user_frame.php?uid='.$this->uid().'" rel="superbox[iframe.wikipedia][454x380]"><font class="femme">'.$this->pseudo().'</font></a>';
  
        $this->_destinatairestylise = $infos;
    }

    public function setElo($id)
    {
        $this->_elo = $id;
    }

    public function setSexe($id)
    {
        $this->_sexe = $id;
        $this->setPseudostylise($id);
        $this->setDestinatairestylise($id);
    }
    
    public function setConnecte($id)
    {
        $this->_connecte = $id;
    }

    public function setDate_connection($id)
    {
        $this->_date_connection = $id;
    }

    public function setDate_inscription($id)
    {
        $this->_date_inscription = $id;
    }

    public function setNaissance($id)
    {
        $this->_naissance = $id;
    }

    public function setDescription($id)
    {
        $this->_description = $id;
    }

    public function setPays($id)
    {
        $this->_pays = $id;
    }

    public function setPhoto($id)
    {
        $presente = $id;
        if ($presente == 'o')
        {
            $nombre_aleatoire = $this->nombre_au_hazard();
            $laphoto = './images/joueurs/h'.$this->uid().'.jpg?'.$nombre_aleatoire; // pour recharger la véritable photo;
        }
        else
            $laphoto = "./images/joueurs/h0.jpg";
        
        $this->_photo = $laphoto;
    }


    public function detailJoueur($id)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        
        $joueurs = new JoueurManager($db);

        $composantes = array();
        
        $composantes['uid'] = $this->uid();
        $composantes['pseudo'] = $this->pseudo();
        $composantes['elo'] = $this->elo();
        $composantes['date_connection'] = $this->formate_date($this->date_connection());
        $composantes['date_inscription'] = $this->formate_date($this->date_inscription());
        $composantes['sexe'] = $this->sexe();
        $composantes['connecte'] = $this->connecte();
        $individu = $joueurs->trouveJoueur($composantes['uid']);
        $composantes['pseudostylise'] = $individu->pseudostylise();

        return $composantes;
    }
    
    public function uid()
    {
        return $this->_uid;
    }

    public function pseudo()
    {
        return $this->_pseudo;
    }
    
    public function pseudostylise()
    {
        return $this->_pseudostylise;
    }

    public function destinatairestylise()
    {
        return $this->_destinatairestylise;
    }

    public function elo()
    {
        return $this->_elo;
    }

    public function sexe()
    {
        return $this->_sexe;
    }
    
    public function connecte()
    {
        return $this->_connecte;
    }

    public function date_connection()
    {
        return $this->_date_connection;
    }

    public function date_inscription()
    {
        return $this->_date_inscription;
    }
    
    public function nb_partie_en_cours()
    {
        return $this->_nb_partie_en_cours;
    }
    
    public function naissance()
    {
        return $this->_naissance;
    }

    public function description()
    {
        return $this->_description;
    }

    public function pays()
    {
        return $this->_pays;
    }

    public function photo()
    {
        return $this->_photo;
    }

    
    public function age()
    {
        $date_naissance = $this->naissance();
        if (!isset($date_naissance))
            return "";
            
        $arr1 = explode('-', $date_naissance);
        $arr2 = explode('/', date('Y/m/d'));
    
        if ($arr1[0] == '0000')
            return "";

        if(($arr1[1] < $arr2[1]) || (($arr1[1] == $arr2[1]) && ($arr1[2] <= $arr2[2])))
            return intval($arr2[0]) - intval($arr1[0])." ans";

        return (intval($arr2[0]) - intval($arr1[0]) - 1)." ans";
    }

    private function nombre_au_hazard()
    {
        //création d'un nombre aléatoire entre 1 et 100

        $nb_min = 1;
        $nb_max = 100;
        $nombre = mt_rand($nb_min,$nb_max);

        return $nombre;
    }

    private function formate_date($date)
    {
        $date_formatee = substr($date,8,2)."/".substr($date,5,2)."/".substr($date,0,4);
    
        if ($date == '')
            return '';
        else
            return $date_formatee;
    }

}

?>