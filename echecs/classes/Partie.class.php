<?php
class Partie  extends PartieManager
{
    private $_gid;
    private $_uidb;
    private $_uidn;
    protected $_trait;
    private $_tour;
    private $_date_debut;
    private $_date_dernier_coup;
    private $_date_fin;
    private $_cadencep;
    protected $_reservep;
    protected $_reserve_uidb;
    protected $_reserve_uidn;
    private $_finalisation;
    private $_finalisationstylisee;
    private $_actif;
    private $_uidActif;
    private $_laclasse;
    private $_usercolor;
    private $_flip;
    private $_flipbase;
    private $_tempsRestant;
    private $_pourcentageABlancs;
    private $_pourcentageBBlancs;
    private $_pourcentageANoirs;
    private $_pourcentageBNoirs;
    private $_changementB;
    private $_changementN;
    protected $_reserveBlanche;
    protected $_reserveNoire;
    protected $_cliquable;
    protected $_imagemacouleur;
    protected $_adversaire;
    protected $_lesblancs;
    protected $_nbcoups;
    protected $_situation;
    protected $_ign;
    protected $_mangeaille;
    protected $_positionroiblanc;
    protected $_positionroinoir;
    protected $_roiattaque;
    protected $_mat;
    protected $_partienulle;
    protected $_nbcoupspossibles;
        
    public function __construct(array $donnees)
    {
        //print_r($donnees);
        //echo '<br />';
        $this->hydrate($donnees);
        $this->setImageMaCouleur();
        $this->setTour();
        $this->setSituation();
        $this->setFlipBase();
        $this->setMat();
        $this->setPartieNulle();
    }
    
    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value)
        {
            $this->setActif($donnees['actif']);
            $method = 'set'.ucfirst($key);
            
            if (method_exists($this, $method))
                $this->$method($value);
        }
    }

    public function getSituation()
    {
        return $this->_situation;
    }
    
    public function getNbCoups()
    {
        return $this->_nbcoups;
    }
    
    public function gid()
    {
        return $this->_gid;
    }
    
    public function uidb()
    {
        return $this->_uidb;
    }

    public function uidn()
    {
        return $this->_uidn;
    }
    
    public function getTrait()
    {
        return $this->_trait;
    }
    
    public function getMangeaille()
    {
        return $this->_mangeaille;
    }

    public function getNbCoupsPossibles()
    {
        return $this->_nbcoupspossibles;
    }
    public function getPositionRoiBlanc()
    {
        return $this->_positionroiblanc;
    }
    
    public function getPositionRoiNoir()
    {
        return $this->_positionroinoir;
    }
    
    public function getRoiAttaque()
    {
        return $this->_roiattaque;
    }
    
    public function getMat()
    {
        return $this->_mat;
    }
    
    public function getPartieNulle()
    {
        return $this->_partienulle;
    }
    public function duree_restante()
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);

        $this->calculJourMinuteSeconde();
        $gid = $this->gid();
        $trait = $this->getTrait(); // 1 blancs  -1 noirs
        $temps_maximum_en_secondes = strtotime($this->datederniercoup())+(86400* $this->cadencep());
        $nombre_secondes_maintenant = time();
        $reste = $temps_maximum_en_secondes - $nombre_secondes_maintenant; // en secondes
        $rnombre_jours_restant = $reste/3600/24; // nous avons un nombre de secondes divisé par 3600 puis par 24

        $changement_b = 0;
        $changement_n = 0;
        $nouvelle_reserve_b = ''; 
        $nouvelle_reserve_n = '';
        $this->_pourcentageABlancs = 0;
        $this->_pourcentageANoirs = 0;
        //exit($_SESSION['uid'].' ' . $this->uidn());
        
        // dependant si trait est aux blancs ou aux noirs
        switch ($trait)
        {
            case 1:   // Trait aux blancs
                if ($this->_tempsRestant == '0j 0h 0min 0s' and $_SESSION['uid'] == $this->uidb())
                {
                    // Total de jours de réserve, additionné avec ce qui reste
                    $changement_b = $this->reserve_uidb() + $rnombre_jours_restant;
                    $this->_reserveBlanche = $changement_b;
                    $nouvelle_reserve_b = $changement_b;
                    $this->_tour = '<a href="?action=terminer partie&amp;gid='.$this->gid().'">partie terminée</a>';
                }
                else
                    $this->_reserveBlanche = $this->reserve_uidb();
                break;
            case -1: // Trait aux noirs
                if (($this->_tempsRestant == '0j 0h 0min 0s') and ($_SESSION['uid'] == $this->uidn()))
                {
                    // Total de jours de réserve, additionné avec ce qui reste
                    $changement_n = $this->reserve_uidn() + $rnombre_jours_restant;
                    $this->_reserveNoire = $changement_n;
                    $nouvelle_reserve_n = $changement_n;
                    $this->_tour = '<a href="?action=terminer partie&amp;gid='.$this->gid().'">partie terminée</a>';
                }
                else
                    $this->_reserveNoire =  $this->reserve_uidn();
                break;
        }
        
        // calcul des nouveaux pourcentages, pour les blancs 
        if ($nouvelle_reserve_b == '')
            $this->_pourcentageABlancs = (int) $this->reserve_uidb()/$this->reservep()*100;
        else
        {
            $this->_pourcentageABlancs = (int) $nouvelle_reserve_b/$this->reservep()*100; // pourcentage disponible pour les blancs
            // si le temps est complètement épuisé, sinon ajustement
            
            if ($nouvelle_reserve_b == 0)
            {
                $this->_pourcentageABlancs = 0;
                $this->_pourcentageBBlancs = 100;
            }
            else
                $this->_pourcentageBBlancs = 100 - $this->_pourcentageABlancs;

            // Cas où il n'y a plus de temps du tout (même plus de réserve), pour les blancs
            if ($this->_pourcentageABlancs == 0)
            {
                //  ici c'est correct  les noirs gagnent au temps
                $sql_un = "update parties set date_fin = now(), finalisation = '6' where gid=".$this->gid();
                $sql_deux = "update statistiques set pertes_b = pertes_b+1  where uid=".$this->uidb();
                $sql_trois = "update statistiques set gains_n = gains_n+1 where uid=".$this->uidn();
        
                if ($adversaire == $this->uidn())
                    $this->calcule_elo($this->uidb(), $this->uidn(), 1);
                else
                    $this->calcule_elo($this->uidn(), $this->uidb(), 1);
                

                $resultat = $db->query($sql_un);
                $resultat = $db->query($sql_deux);
                $resultat = $db->query($sql_trois);
                header('Location:  index.php?action=mes parties');
            }
        }

        // calcul des nouveaux pourcentages, pour les noirs
        if ($nouvelle_reserve_n == '') // pourcentage de 100%
            $this->_pourcentageANoirs = (int) $this->reserve_uidn()/$this->reservep()*100; // pourcentage disponible pour les noirs
        else
        {
            $this->_pourcentageANoirs = (int) $nouvelle_reserve_n/$this->reservep()*100; // pourcentage disponible pour les noirs
            // si le temps est complètement épuisé, sinon ajustement
            
            if ($nouvelle_reserve_n == 0)
            {
                $this->_pourcentageANoirs = 0;
                $this->_pourcentageBNoirs = 100;
            }
            else
                $this->_pourcentageBNoirs = 0;

            // Cas où il n'y a plus de temps du tout (même plus de réserve), pour les noirs
            
            if ($this->_pourcentageANoirs == 0)
            {
                //  ici c'est correct  les blancs gagnent au temps
                $sql_un = "update parties set date_fin = now(), finalisation = '6' where gid=".$this->gid();
                $sql_deux = "update statistiques set pertes_n = pertes_n+1  where uid=".$this->uidn();
                $sql_trois = "update statistiques set gains_b = gains_b+1 where uid=".$this->uidb();
        
                if ($adversaire == $this->uidn())
                    $this->calcule_elo($this->uidb(), $this->uidn(), 1);
                else
                    $this->calcule_elo($this->uidn(), $this->uidb(), 1);
                

                $resultat = $db->query($sql_un);
                $resultat = $db->query($sql_deux);
                $resultat = $db->query($sql_trois);
                header('Location:  index.php?action=mes parties');
            }
            
        }
        $this->_changementB = $changement_b;
        $this->_changementN = $changement_n;
    }
    
    public function actif()
    {
        return $this->_actif;
    }
    
    public function setFlipBase()
    {
        if ($this->getTrait() == 1)
            $this->_flipbase = 0;
        else
            $this->_flipbase = 1;
    }
    
    public function getFlipBase()
    {
        return $this->_flipbase;
    }
    
    public function setUsercolor()
    {
        $uidActif = $_SESSION['uid'];
        if ($uidActif == $this->uidb())
        {
            $this->_usercolor = 1;  // blancs
            $this->_flip = 0;       // on tourne pas l'échiquier
            $this->_adversaire = $this->uidn();
            $this->_lesblancs = $this->uidb();
        } 
        else
        {
            $this->_usercolor = 0;  // noirs
            $this->_flip = 1;       // on tourne l'échiquier
            $this->_adversaire = $this->uidb();
            $this->_lesblancs = $this->uidn();
        }
    }
    
    public function getAdversaire()
    {
        return $this->_adversaire;
    }

    public function getLesBlancs()
    {
        return $this->_lesblancs;
    }

    public function flip()
    {
        return $this->_flip;
    }

    public function getTour()
    {
        return $this->_tour;
    }

    public function datedebut()
    {
        $ladate = $this->_date_debut;
        
        return $ladate;
    }

    public function datederniercoup()
    {
        
        return $this->_date_dernier_coup;
    }

    public function datefin()
    {
        return $this->_date_fin;
    }

    public function cadencep()
    {
        return $this->_cadencep;
    }

    public function reservep()
    {
        return $this->_reservep;
    }

    public function reserve_uidb()
    {
        return number_format($this->_reserve_uidb,1) ;
    }

    public function reserve_uidn()
    {
        return number_format($this->_reserve_uidn,1);
    }

    public function finalisation()
    {
        return $this->_finalisation;
    }
    
    public function finalisationStylisee()
    {
        return $this->_finalisationstylisee;
    }
    
    public function laclasse()
    {
        return $this->_laclasse;
    }

    public function setImageMaCouleur()
    {
        if ($this->actif() == $this->_uidb)
            $image = '<img src="./pieces/white.gif">';
        else
            $image = '<img src="./pieces/black.gif">';
        
        $this->_imagemacouleur = $image;
    }
    
    public function getImageMaCouleur()
    {
        return $this->_imagemacouleur;
    }

    public function tempsRestant()
    {
        return $this->_tempsRestant;
    }
    
    public function pourcentage_a_blancs()
    {
        if ($this->_pourcentageABlancs < 0)
            $this->_pourcentageABlancs = 0;

        return number_format($this->_pourcentageABlancs,0);
    }

    public function changementB()
    {
        return $this->_changementB;
    }
    
    public function changementN()
    {
        return $this->_changementN;
    }


    public function pourcentage_a_noirs()
    {
        if ($this->_pourcentageANoirs < 0)
            $this->_pourcentageANoirs = 0;
            
        return number_format($this->_pourcentageANoirs,0);
    }

    public function cliquable()
    {
        $uidActif = $_SESSION['uid'];
        $trait = $this->getTrait(); // 1 si blanc   &  -1 si noir
        $cliquable = false;
        
        if ($trait == 1 and $this->uidb() == $uidActif)
            $cliquable = true;
        if ($trait == -1 and $this->uidn() == $uidActif)
            $cliquable = true;
            
        $this->_cliquable = $cliquable;
    }
    
    public function setMat()
    {
        $this->_mat = false;
        if ($this->getRoiAttaque())
            if ($this->getNbCoupsPossibles() == 0)
                $this->_mat = true;
    }
    
    public function setPartieNulle()
    {
        $this->_partienulle = false;
        if (!$this->getRoiAttaque())
            if ($this->getNbCoupsPossibles() == 0)
                $this->_partienulle = true;
    }
    
    public function setNbcoupspossibles($id)
    {
        $this->_nbcoupspossibles = $id;
    }
    
    protected function calculJourMinuteSeconde()
    {
        $temps_maximum_en_secondes = strtotime($this->datederniercoup())+(86400* $this->cadencep());
        if ($temps_maximum_en_secondes == 259200)
        {
            $temp_restant = "";
            $nombre_jours_restant = 0;
            $nombre_heures_restant = 0;
            $nombre_minutes_restant = 0;
            $nombre_secondes_restant = 0;
            $temp_restant = $nombre_jours_restant."j ".$nombre_heures_restant."h ".$nombre_minutes_restant."min ".$nombre_secondes_restant."s";
            $this->_tempsRestant = $temp_restant;
            
            return;
        }

        $nombre_secondes_maintenant = time();

        if ($temps_maximum_en_secondes > $nombre_secondes_maintenant)
        {
            $reste = $temps_maximum_en_secondes - $nombre_secondes_maintenant;
            $nombre_jours_restant = floor($reste/3600/24); // 2 jours
            $reste = $reste % (3600*24);
            $nombre_heures_restant = floor($reste/3600); // 19 heures
            $reste = $reste % 3600;
            $nombre_minutes_restant = floor($reste/60); // 42 minutes
            $reste = $reste % 60;
            $nombre_secondes_restant = floor($reste); // 40 secondes
        }
        else
        {
            $nombre_jours_restant = 0;
            $nombre_heures_restant = 0;
            $nombre_minutes_restant = 0;
            $nombre_secondes_restant = 0;
        }

        $temp_restant = $nombre_jours_restant."j ".$nombre_heures_restant."h ".$nombre_minutes_restant."min ".$nombre_secondes_restant."s";
        $this->_tempsRestant = $temp_restant;
        
        return;
    }

    public function getreserveBlanche()
    {
        return $this->_reserveBlanche;
    }

    public function getreserveNoire()
    {
        return $this->_reserveNoire;
    }

    public function getUserColor()
    {
        return $this->_usercolor;
    }
    
    public function getCliquable()
    {
        return $this->_cliquable;
    }
    
    public function getIgn()
    {
        return $this->_ign;
    }
    
    /*
    //
    // Setters
    //
    //
    */
    public function setGid($id)
    {
        $this->_gid = $id;
    }
        
    public function setuidb($id)
    {
        $this->_uidb = $id;
    }

    public function setuidn($id)
    {
        $this->_uidn = $id;
    }
    
    public function settrait($id)
    {
        $this->_trait = $id;
    }
    
    public function setSituation()
    {
        if ($this->getTrait() == 1)
            $this->_situation = 'trait aux blancs';
        else
            $this->_situation = 'trait aux noirs';
    }

    public function setActif($id)
    {
        $this->_actif = $id;
    }

    public function setDate_debut($id)
    {
        $this->_date_debut = $id;
    }

    public function setDate_dernier_coup($id)
    {
        $this->_date_dernier_coup = $id;
    }

    public function setDate_fin($id)
    {
        $this->_date_fin = $id;
    }

    public function setCadencep($id)
    {
        $this->_cadencep = $id;
    }

    public function setReservep($id)
    {
        $this->_reservep = $id;
    }

    public function setReserve_uidb($id)
    {
        $this->_reserve_uidb = $id;
    }

    public function setReserve_uidn($id)
    {
        $this->_reserve_uidn = $id;
    }
    
    public function setNbCoups($id)
    {
        $this->_nbcoups = $id;
    }

    public function setLaclasse($id)
    {
        $this->_laclasse = $id;
    }
    
    public function setLescoups($id)
    {
        $this->_ign = $id;
    }
    
    public function setMangeaille($id)
    {
        $this->_mangeaille = $id;
    }
    
    public function setPositionroiblanc($id)
    {
        $this->_positionroiblanc = $id;
    }

    public function setPositionroinoir($id)
    {
        $this->_positionroinoir = $id;
    }
    
    public function setRoiAttaque($id)
    {
        $this->_roiattaque = $id;
    }
    
    public function setTour()
    {
        $this->setUsercolor();
        $this->Cliquable();
        $mat = $this->getMat();
        $partieNulle = $this->getPartieNulle();
        
        if ($this->getCliquable())
        {
            $temps_maximum_en_secondes = strtotime($this->datederniercoup())+(86400* $this->cadencep());
            $nombre_secondes_maintenant = time();
            if ($temps_maximum_en_secondes < $nombre_secondes_maintenant and !$mat and !$partieNulle)
            {
                switch($this->getUserColor())
                {
                    case 1: // blancs
                        if ($this->getreserveBlanche() < 0)
                        {
                            $this->_tour = '<a href="?action=terminer partie&amp;gid='.$this->gid().'">partie terminée</a>';
                            $this->setLaclasse("fini");
                        }
                        else
                        {
                            $this->_tour = '<a href="?action=montrer partie&amp;gid='.$this->gid().'">à vous de jouer</a>';
                            $this->setLaclasse("vous");
                        }
                        break;
                    case 0: // noirs
                        if ($this->getreserveNoire() < 0)
                        {
                            $this->_tour = '<a href="?action=terminer partie&amp;gid='.$this->gid().'">temps écoulé</a>';
                            $this->setLaclasse("fini");
                        }
                        else
                        {
                            //exit('oui '.$this->_reserveNoire);
                            $this->_tour = '<a href="?action=montrer partie&amp;gid='.$this->gid().'">à vous de jouer</a>';
                            $this->setLaclasse("vous");
                            //$this->_tour = '<a href="?action=terminer partie&amp;gid='.$this->gid().'">partie terminée</a>';
                            //$this->setLaclasse("fini");
                        }
                        break;
                }
            }
            else
            {
                if ($mat)
                {
                    $this->_tour = '<a href="?action=mat&amp;gid='.$this->gid().'">échec et mat</a>';
                    $this->setLaclasse("fini");
                }
                else if ($partieNulle)
                {
                    $this->_tour = '<a href="?action=nulle&amp;gid='.$this->gid().'">partie nulle</a>';
                    $this->setLaclasse("fini");
                } 
                else
                {
                    $this->_tour = '<a href="?action=montrer partie&amp;gid='.$this->gid().'">à vous de jouer</a>';
                    $this->setLaclasse("vous");
                }
            }
        }
        else
        {
            $this->_tour = '<a href="?action=montrer partie&amp;gid='.$this->gid().'">à votre adversaire</a>';
            $this->setLaclasse("adversaire");
        }
    }
    
    public function setFinalisationStylisee($id)
    {
        switch ($id)
        {
            case 0:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Partie non terminée</a>';
                break;
            case 1:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Nulle sur proposition des blancs</a>';
                break;
            case 2:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Nulle sur proposition des noirs</a>';
                break;
            case 3:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Les blancs abandonnent</a>';
                break;
            case 4:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Les noirs abandonnent</a>';
                break;
            case 5:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Les blancs gagnent au temps</a>';
                break;
            case 6:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Les noirs gagnent au temps</a>';
                break;
            case 7:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Les blancs gagnent(mat)</a>';
                break;
            case 8:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Les noirs gagnent(mat)</a>';
                break;
            default:
                $this->_finalisationstylisee = '<a href="?action=rejouer&amp;&gid='.$this->gid().'">Fin de partie anormale</a>';
        }
    }

    public function setFinalisation($id)
    {
        $this->setFinalisationStylisee($id);
        switch ($id) 
        {
            case 0:
                $la_fin = "Partie non terminée";
                break;
            case 1:
                $la_fin = "Nulle sur proposition des blancs";
                break;
            case 2:
                $la_fin = "Nulle sur proposition des noirs";
                break;
            case 3:
                $la_fin = "Les blancs abandonnent";
                break;
            case 4:
                $la_fin = "Les noirs abandonnent";
                break;
            case 5:
                $la_fin = "Les blancs gagnent au temps";
                break;
            case 6:
                $la_fin = "Les noirs gagnent au temps";
                break;
            case 7:
                $la_fin = "Les blancs gagnent(mat)";
                break;
            case 8:
                $la_fin = "Les noirs gagnent(mat)";
                break;
            default:
                $la_fin = "Fin de partie anormale";
        }

        $this->_finalisation = $la_fin;
    }

}
?>