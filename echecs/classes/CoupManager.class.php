<?php
class CoupManager extends Echiquier
{
    protected $_ign;
    protected $_mangeaille;
    protected $_trait;
    protected $_touslescoups;
    protected $_positionroiblanc;
    protected $_positionroinoir;
    protected $_roiattaque;

    public function letrait($cip)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        
        $requete = 'SELECT * FROM coups where cip ='.$cip.' ORDER BY ordre DESC LIMIT 1';
        $q = $db->query($requete);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        
        if (strlen($donnees['coups']) < 6 and strlen($donnees['coups']) > 2)
        {
            $letrait = -1;  // Noirs
            $this->_bascule = true;
        }
        else
        {
            $letrait = 1;   // Blancs
            $this->_bascule = false;
        }
        $this->_trait = $letrait;
    }

    public function add($cip,$coup)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $parties = new PartieManager($db);
        
        // trouver le dernier coup de la bonne partie
        $q = $db->query('select max(ordre) as maximum, coups, cip from coups where cip ='.$cip);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $ordre = $donnees['maximum'];
        if ($ordre != '')
        {
            $q = $db->query('select max(ordre) as maximum, coups, cip from coups where cip ='.$cip.' and ordre='.$ordre);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
            $lecoup = $donnees['coups'];
            $lecip = $donnees['cip'];
        
            if (strlen($lecoup) < 6)
            {
                $ajout = $lecoup.' '.$coup;
                $requete = "update coups set coups='".$ajout."' where cip =".$lecip." and ordre=".$ordre;
                $q = $db->query($requete);
            }
            else
            {
                $requete = "INSERT INTO coups (cip,coups) VALUES($cip,'$coup')";
                $q = $db->query($requete);
            }
            $requete = "update parties set date_dernier_coup=now() where gip =".$lecip;
            $q = $db->query($requete);
        }
        else
        {
            $requete = "INSERT INTO coups (cip,coups) VALUES($cip,'$coup')";
            $q = $db->query($requete);
        }
        
        $lapartie = $parties->get($cip);
        $trait = $this->letrait($cip);
        $uidb = $lapartie->uidb();
        $uidn = $lapartie->uidn();
        
        
        if ($trait == $uidn)
            $ajustement = $uidb;
        else
            $ajustement = $uidn;
        
        
        $parties->miseajourcoup($ajustement,$cip);
        
        header('Location: index.php?action=mes parties');
    }

  public function get($id)
  {
    $id = (int) $id;
    
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $q = $db->query('SELECT * FROM coups');
    $donnees = $q->fetch(PDO::FETCH_ASSOC);

    return new Coup($donnees);
  }

    public function getList()
    {
        $joueurs = array();
        
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "SELECT * FROM coups";
        $q = $db->query($requete);

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $coups[] = new Coup($donnees);
        }
        return $coups;
    }
    
    public function nbCoups($numero)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $q = $db->query('select * from coups where cip='.$numero.' order by ordre desc');
        if (!$q)
            die("Table coups inexistante");

        $lescoups = $q->fetch(PDO::FETCH_ASSOC);
        
        $r = $db->query('select count(*) as qt from coups where cip='.$numero);
        $nombre = $r->fetch(PDO::FETCH_ASSOC);
        
        $compteur = $nombre['qt']*2;
        if (strlen($lescoups['coups']) == 0)
            $compteur = 0;
        if (strlen($lescoups['coups']) == 4 || strlen($lescoups['coups']) == 5)
            $compteur--;
        
        return $compteur;
    }
    
    public function setIgn($nopartie)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
            
        $coups = array();
        $this->letrait($nopartie);
        
        $requete = "select coups from coups where cip='".$nopartie."' order by ordre asc";
        $q = $db->query($requete);
        
        $les_coups = " ";
        
        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $coup = $donnees['coups'];
        
            if (strlen($coup > 6))
                $les_coups .= $coup;
            else
                $les_coups .= $coup." ";
            
            $coup = "";
        }
        $ign = trim($les_coups);
        $this->_ign = $ign;
        $this->positionactuelle($ign);
    }

    public function positionactuelle($ign)
    {
        $wCastleLong = true;
        $wCastleShort = true;
        $bCastleLong = true;
        $bCastleShort = true;

        $mangeaille = array('blancs' => array(),'noirs' => array());
        $position = $this->position();
        $promotion = '';
        $coups = explode(" ",$ign);

        if ($ign == "")
            return $position;
        else
            $qtecoups = count($coups);
        
        for ($i=0;$i<$qtecoups;$i++)
        {
            $start = substr($coups[$i],0,2);
            $end = substr($coups[$i],2,2);
            if (strlen($coups[$i]) > 4)
                $promotion = substr($coups[$i],4,1);  

            // les coups sous formes de chiffres, par exemple (12-28)
            $istart = $this->moveToCell($start);
            $iend = $this->moveToCell($end);
            
            $lastmove = $istart.'-'.$iend;
            
            // Interdiction ou pas du roque
            if($position[$istart] == 'K')
            {
                $wCastleLong = false;
                $wCastleShort = false;
            }
            if($position[$istart] == 'k')
            {
                $bCastleLong = false;
                $bCastleShort = false;
            }
            
            // Si la tour bouge
            if($istart == 0)
                $wCastleLong = false;
            else if($istart == 7)
                $wCastleShort = false;
            else if($istart == 56)
                $bCastleLong = false;
            else if($istart == 63)
                $bCastleShort = false;
            
            // Si la tour disparait en se faisant manger    
            if($iend == 0)
                $wCastleLong = false;
            else if($iend == 7)
                $wCastleShort = false;
            else if($iend == 56)
                $bCastleLong = false;
            else if($iend == 63)
                $bCastleShort = false;
                
            // Prise en passant pour les blancs
            if ($iend == $istart+7 && $position[$istart+7] == '' && $position[$istart] == 'P')
            {
                $position[$iend] = $position[$iend-8];
                $position[$iend-8] = '';
            }

            if ($iend == $istart+9 && $position[$istart+9] == '' && $position[$istart] == 'P')
            {
                $position[$iend] = $position[$iend-8];
                $position[$iend-8] = '';
            }

            // Prise en passant pour les noirs
            if ($iend == $istart-7 && $position[$istart-7] == '' && $position[$istart] == 'p')
            {
                $position[$iend] = $position[$iend+8];
                $position[$iend+8] = '';
            }

            if ($iend == $istart-9 && $position[$istart-9] == '' && $position[$istart] == 'p')
            {
                $position[$iend] = $position[$iend+8];
                $position[$iend+8] = '';
            }
        
            // Pieces mangees
            if ($position[$iend] != '')
            {
                if ($position[$iend] == strtoupper($position[$iend]))
                    $mangeaille['blancs'][] = $this->imagepiecemangee($position[$iend]);
                else
                    $mangeaille['noirs'][] = $this->imagepiecemangee($position[$iend]);
            }

            $position[$iend] = $position[$istart];
            $position[$istart] = '';

            // roque
            if($istart == 4 && $iend == 6 && $position[$iend] == 'K')
            {
                $position[5] = 'R';
                $position[7] = '';
            }
            else if($istart == 4 && $iend == 2 && $position[$iend] == 'K')
            {
                $position[3] = 'R';
                $position[0] = '';
            }
            else if($istart == 60 && $iend == 62 && $position[$iend] == 'k')
            {
                $position[61] = 'r';
                $position[63] = '';
            }
            else if($istart == 60 && $iend == 58 && $position[$iend] == 'k')
            {
                $position[59] = 'r';
                $position[56] = '';
            }
            // promotion
            if ($iend < 8 && $position[$iend] == 'p')
                $position[$iend] = $promotion;
            if ($iend > 55 && $position[$iend] == 'P')
                $position[$iend] = $promotion;      
        }
        
        $castling = '';
        if($wCastleShort) $castling .= 'K';
        if($wCastleLong) $castling .= 'Q';
        if($bCastleShort) $castling .= 'k';
        if($bCastleLong) $castling .= 'q';
        if($castling == '') $castling = '-';
        
        $this->_castling = $castling;  

        $this->_mangeaille = $mangeaille;
        $this->_lastmove = $lastmove;
        
        for ($i=0;$i<64;$i++)
        {
            $this->_position[$i] = $position[$i];
        }
        
        $this->lesPieces($this->_position,$piece = -1);
        $this->_positionroiblanc = $this->lesPieces($this->_position,'K');
        $this->_positionroinoir = $this->lesPieces($this->_position,'k');
        if ($this->getTrait() == 1)
            $this->_roiattaque = $this->caseAttaquee($this->_position,$this->_positionroiblanc,'b');
        else
            $this->_roiattaque = $this->caseAttaquee($this->_position,$this->_positionroinoir,'w');
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

    public function positionarbitraire($ign,$qtecoups)
    {
        $lasuite = array();
        $mangeaille = array('blancs' => array(),'noirs' => array());
        $this->positiondepart();
        $position = $this->getPositionDepart();
        $promotion = '';
        
        $coups = explode(" ",$ign);

        if ($ign == "")
            return $position;
        
        for ($i=0;$i<$qtecoups;$i++)
        {
            if (strlen($coups[$i]) == 4 || strlen($coups[$i]) == 5)
            {
                $start = substr($coups[$i],0,2);
                $end = substr($coups[$i],2,2);
            }

            if (strlen($coups[$i]) > 4)
                $promotion = substr($coups[$i],4,1);  

            // les coups sous formes de chiffres, par exemple (12-28)
            $istart = $this->moveToCell($start);
            $iend = $this->moveToCell($end);
            
            $lastmove = $istart.'-'.$iend;
            
            // Prise en passant pour les blancs
            if ($iend == $istart+7 && $position[$istart+7] == '' && $position[$istart] == 'P')
            {
                $position[$iend] = $position[$iend-8];
                $position[$iend-8] = '';
            }

            if ($iend == $istart+9 && $position[$istart+9] == '' && $position[$istart] == 'P')
            {
                $position[$iend] = $position[$iend-8];
                $position[$iend-8] = '';
            }

            // Prise en passant pour les noirs
            if ($iend == $istart-7 && $position[$istart-7] == '' && $position[$istart] == 'p')
            {
                $position[$iend] = $position[$iend+8];
                $position[$iend+8] = '';
            }

            if ($iend == $istart-9 && $position[$istart-9] == '' && $position[$istart] == 'p')
            {
                $position[$iend] = $position[$iend+8];
                $position[$iend+8] = '';
            }

            // Pieces mangees
            if ($position[$iend] != '')
            {
                if ($position[$iend] == strtoupper($position[$iend]))
                    $mangeaille['blancs'][] = $this->imagepiecemangee($position[$iend]);
                else
                    $mangeaille['noirs'][] = $this->imagepiecemangee($position[$iend]);
            }
            
            switch ($position[$istart])
            {
                case 'R':
                case 'r':
                    $piece = 'T';
                    break;
                case 'N':
                case 'n':
                    $piece = 'C';
                    break;
                case 'B':
                case 'b':
                    $piece = 'F';
                    break;
                case 'Q':
                case 'q':
                    $piece = 'D';
                    break;
                case 'K':
                case 'k':
                    $piece = 'R';
                    break;
                default:
                    $piece = '';
            }
            
            switch ($piece.$coups[$i])
            {
                case 'Re1g1':
                case 'Re8g8':
                    $lasuite[] = ' o-o';
                    break;
                case 'Re1c1':
                case 'Re8c8':
                   $lasuite[] = 'o-o-o';
                   break;
                default:
                    if ($position[$iend] != '')
                        $lasuite[] = $piece.substr($coups[$i], 0, 2).'X'.substr($coups[$i], 2, 2);
                    else
                        $lasuite[] = $piece.$coups[$i];
            }
            
            $position[$iend] = $position[$istart];
            $position[$istart] = '';

            // roque
            if($istart == 4 && $iend == 6 && $position[$iend] == 'K')
            {
                $position[5] = 'R';
                $position[7] = '';
            }
            else if($istart == 4 && $iend == 2 && $position[$iend] == 'K')
            {
                $position[3] = 'R';
                $position[0] = '';
            }
            else if($istart == 60 && $iend == 62 && $position[$iend] == 'k')
            {
                $position[61] = 'r';
                $position[63] = '';
            }
            else if($istart == 60 && $iend == 58 && $position[$iend] == 'k')
            {
                $position[59] = 'r';
                $position[56] = '';
            }
            // promotion
            if ($iend < 8 && $position[$iend] == 'p')
                $position[$iend] = $promotion;
            if ($iend > 55 && $position[$iend] == 'P')
                $position[$iend] = $promotion;      
        }
        
        $this->_mangeaille = $mangeaille;
        if (isset($lastmove))
            $this->_lastmove = $lastmove;
        
        for ($i=0;$i<64;$i++)
        {
            $this->_position[$i] = $position[$i];
        }
        $this->_touslescoups = $lasuite;
    }

    private function moveToCell($move)
    {
        $a = substr($move,0,1);
        $b = substr($move,1,1);
        $colonne = 0;

        switch($a)
        {
            case "a":
                $colonne = 0;
                break;
            case "b":
                $colonne = 1;
                break;
            case "c":
                $colonne = 2;
                break;
            case "d":
                $colonne = 3;
                break;
            case "e":
                $colonne = 4;
                break;
            case "f":
                $colonne = 5;
                break;
            case "g":
                $colonne = 6;
                break;
            case "h":
                $colonne = 7;
                break;

        }

	   return($colonne+8*(intval($b)-1));
    }
    
    public function Ign()
    {
        return $this->_ign;
    }
        
    public function Bascule()
    {
        return $this->_bascule;
    }
    
    public function Lastmove()
    {
        return $this->_lastmove;
    }
    
    public function getTrait()
    {
        return $this->_trait;
    }
        
    public function getMangeaille()
    {
        return $this->_mangeaille;
    }
    
    public function getTousLesCoups()
    {
        return $this->_touslescoups;
    }

}

?>