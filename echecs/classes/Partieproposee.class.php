<?php
class Partieproposee
{
    private $_gidp;
    private $_prospect;
    private $_origine;
    private $_macouleur;
    private $_imagemacouleur;
    private $_imagetacouleur;
    private $_cadence;
    private $_reserve;
    private $_commentaire;
    
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

    public function setGidp($id)
    {
        $this->_gidp = $id;
    }
    
    public function setProspect($id)
    {
        $this->_prospect = $id;
    }

    public function setOrigine($id)
    {
        $this->_origine = $id;
    }

    public function setMacouleur($id)
    {
        $this->_macouleur = $id;
        $this->setImagemacouleur();
    }

    public function setImagemacouleur()
    {
        $macouleur = '';
        if ($this->macouleur() == '-')
        {
            $this->_imagemacouleur = '<img src="./images/icons/dice.png" alt="" border=0>';
            $this->_imagetacouleur = '<img src="./images/icons/dice.png" alt="" border=0>';
        }
           
        if ($this->macouleur() == 'b')
        {
            $this->_imagemacouleur = '<img src="./images/icons/white.gif" alt="" border=0>';
            $this->_imagetacouleur = '<img src="./images/icons/black.gif" alt="" border=0>';
        }
            
        if ($this->macouleur() == 'n')
        {
            $this->_imagemacouleur = '<img src="./images/icons/black.gif" alt="" border=0>';
            $this->_imagetacouleur = '<img src="./images/icons/white.gif" alt="" border=0>';
        }
            
    }

    public function setCadence($id)
    {
        $this->_cadence = $id;
    }

    public function setReserve($id)
    {
        $this->_reserve = $id;
    }
    
    public function setCommentaire($id)
    {
        $this->_commentaire = $id;
    }


    public function detailPartieproposee($id)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $joueurs = new JoueurManager($db);
        $composantes = array();
        
        $composantes['gidp'] = $this->gidp();
        $composantes['prospect'] = $this->prospect();
        $composantes['origine'] = $this->origine();
        $individu = $joueurs->trouveJoueur($composantes['origine']);
        $composantes['elo'] = $individu->elo();
        $composantes['pseudostylise'] = $individu->pseudostylise();
        $composantes['macouleur'] = $this->imagemacouleur();
        $composantes['tacouleur'] = $this->imagetacouleur();
        $composantes['cadence'] = $this->cadence();
        $composantes['reserve'] = $this->reserve();
        $composantes['commentaire'] = $this->commentaire();

        return $composantes;
    }

    public function detailMapartieproposee($id)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $joueurs = new JoueurManager($db);
        
        $composantes = array();
        
        $composantes['gidp'] = $this->gidp();
        $composantes['prospect'] = $this->prospect();
        if ($composantes['prospect'] != 0)
        {
            $individu = $joueurs->trouveJoueur($composantes['prospect']);
            $composantes['pseudostylise'] = $individu->pseudostylise();
            $composantes['elo'] = $individu->elo();
        }
        else
        {
            $composantes['pseudostylise'] = '<strong>Tous</strong>';
            $composantes['elo'] = '';
        }
        $composantes['origine'] = $this->origine();    
        $composantes['macouleur'] = $this->imagemacouleur();
        $composantes['cadence'] = $this->cadence();
        $composantes['reserve'] = $this->reserve();
        $composantes['commentaire'] = $this->commentaire();

        return $composantes;
    }

    public function gidp()
    {
        return $this->_gidp;
    }

    public function prospect()
    {
        return $this->_prospect;
    }

    public function origine()
    {
        return $this->_origine;
    }

    public function macouleur()
    {
        return $this->_macouleur;
    }

    public function imagemacouleur()
    {
        return $this->_imagemacouleur;
    }

    public function imagetacouleur()
    {
        return $this->_imagetacouleur;
    }
  
    public function cadence()
    {
        return $this->_cadence;
    }

    public function reserve()
    {
        return $this->_reserve;
    }

    public function commentaire()
    {
        return $this->_commentaire;
    }

    public function accepter()
    {
        $image = '<img src="./images/icons/accept.png">';
        return $image;
    }

    public function refuser()
    {
        $image = '<img src="./images/icons/delete.png">';
        return $image;
    }
 

}

?>