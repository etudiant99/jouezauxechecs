<?php
class Piece extends Couleur
{
    private $_nompiece;
    protected $_nom = array('R','N','B','Q','K','P','r','n','b','q','k','p'); // MAJ: couleur blanche
    protected $_nompromotion = array('Q','R','B','N','q','r','b','n');
    protected $_couleur;
    protected $_positionsPossibles;
    protected $_piecesAttaquees;
    protected $_piecesDefendues;
    protected $_roiechec;
    protected $_moncoupspossibles;
    protected $_localisationpiecesattaquees;
    protected $_castling;
    
    public function trouve($p)
    {
        switch($p)
        {
            case 'P':
            case 'p':
                return new Pion($p);
            case 'R':
            case 'r':
                return new Tour($p);
            case 'N':
            case 'n':
                return new Cavalier($p);
            case 'B':
            case 'b':
                return new Fou($p);
            case 'Q':
            case 'q':
                return new Reine($p);
            case 'K':
            case 'k':
                return new Roi($p);
            default:
                return new Vide($p);
        }
    }
    
    public function getCastling()
    {
        return $this->_castling;
    }
    
    public function getNom($piece)
    {
        return $this->_nompiece;
    }
    
    public function getNomsPromotion()
    {
        for ($i=0;$i<count($this->_nompromotion);$i++)
        {
            $noms[] = $this->_nompromotion[$i];
        }
        return $noms;
    }

    public function dessinerPiece($piece,$gid,$depart,$arrivee,$cliquable=true)
    {
        $lapiece = $this->trouve($piece);
        $imagepiece = $lapiece->imageMangee();
        $this->setNom($lapiece);
    
        $imagepiececliquable = '<a href="?action=montrer partie&amp;gid='.$gid.'&amp;depart='.$depart.'&amp;arrivee='.$arrivee.'&amp;piece='.$piece.'">'.$lapiece->imageMangee().'</a>';
        //$imagepiececliquable = '<a href="?action=montrer partie&amp;piece='.$this->getNom($lapiece).'">'.$lapiece->imageMangee().'</a>';
        //&amp;depart='.$depart.
        if ($cliquable)
            $mapiece = $imagepiececliquable;
        else
            $mapiece = $imagepiece;
        
        return '<div class="unepiece">'.$mapiece.'</div>';
    }

    public function dessinerPiecesPromotion($trait,$gid,$depart,$arrivee,$cliquable=true)
    {
        $resultat = null;
        $lespieces = $this->getNomsPromotion();
        if ($trait == '1')
            for ($i=0;$i<4;$i++)
            {
                $resultat .= $this->dessinerPiece($lespieces[$i],$gid,$depart,$arrivee,$cliquable);
            }
        else
            for ($i=4;$i<count($lespieces);$i++)
            {
                $resultat .= $this->dessinerPiece($lespieces[$i],$gid,$depart,$arrivee,$cliquable);
            }
            
        return $resultat.'<br />';
    }

    public function setNom($piece)
    {
        
        $this->_nompiece = $piece->nom();
    }

    public function Couleur($piece)
    {
        if ($piece == "")
            return $this_couleur = 0;
        
        switch($piece)
        {
            case ($piece == strtoupper($piece)):
                return $this_couleur = 1;  // Blancs
                break;
            case ($piece != strtoupper($piece)):
                return $this_couleur = -1; // Noirs
                break;
        }
    }
    
    public function nbCoupsPossibles($gid,$end)
    {
        $lenombre = 0;
        $ign = $this->Ign();
        $position = $this->position();
        $this->letrait($gid);
        $trait = $this->getTrait();
        if ($trait == 1)
            $indice = 1;
        else
            $indice = 0;
        
        $analyse = $this->lespiecesechiquier($position,$indice);

        for ($s=0;$s<count($analyse['piece']);$s++) // nombre de pieces presentes pour le trait
        {
            $depart = $analyse['endroit'][$s]; // numero de case avec une piece dessus
            $piecejouant = $analyse['piece'][$s];  // lettre de ;a piece qui est analysee, si minuscule c'est noir
            
            for ($i=0;$i<64;$i++)
            {
                $lapiece = $this->trouve($piecejouant);
                
                $coupvalide = $lapiece->legal($position,$depart,$i,$end);
                
                $this->roiEchec($position,$depart,$i,$trait);
                if ($coupvalide)
                    if (!$this->getRoiEchec())
                        $lenombre++;
            }
        }
        
        $this->_moncoupspossibles = $lenombre;

        if ($lenombre < 2)
            if ($trait == 1)
                return $lenombre.' coup possible pour les blancs<br />';
            else
                return $lenombre.' coup possible pour les noirs<br />';
        else
            if ($trait == 1)
                return $lenombre.' coups possibles pour les blancs<br />';
            else
                return $lenombre.' coups possibles pour les noirs<br />';
       
    }

    public function donneNbCoupsPossibles()
    {
        return $this->_moncoupspossibles;
    }

    public function lespiecesechiquier($position,$usercolor)
    {
        $pieces = array();
        $endroit = array();
        $retour = array();

        for ($i=0;$i<64;$i++)
        {
            if ($position[$i] != '')
            {
                $couleur = $this->couleurpiece($position,$i);
                $p = $position[$i];
                if ($couleur == $usercolor)
                {
                    $endroit[] = $i;
                    $pieces[] = $p;
                }
            }
        }
        
        $retour['endroit'] = $endroit;
        $retour['piece'] = $pieces;
        
        return $retour;
    }

    public function nbEndroitsPossibles($position,$start,$trait,$derniercoup)
    {
        $endroit = array();
        
        for ($i=0;$i<64;$i++)
        {
            $p = $position[$i];
            $arrivee = $this->trouve($p);
            
            $this->roiEchec($position,$start,$i,$trait);
            
            if ($this->legal($position,$start,$i,$derniercoup))
                if (!$this->getRoiEchec())
                    $endroit[] = $i;                
        }
        return count($endroit);
    }

    protected function initialise($position,$start,$end)
    {
        $this->_position = $position;
        $this->_start = $start;
        $this->_end = $end;
        $this->_startline = $this->parseInt($start/8);
        $this->_startcol = $start - $this->_startline*8;
        $this->_endline = $this->parseInt($end/8);
        $this->_endcol = $end - $this->_endline*8;
        $this->_dline = $this->_endline - $this->_startline;
        $this->_dcol = $this->_endcol - $this->_startcol;
    }
    
    public function endroitsPossibles($position,$start,$trait,$derniercoup)
    {
        $endroit = array();
        
        for ($i=0;$i<64;$i++)
        {
            $this->roiEchec($position,$start,$i,$trait);
            if ($this->legal($position,$start,$i,$derniercoup))
                if (!$this->getRoiEchec())
                    $endroit[] = $i;
                
        }
        return $endroit;
    }
    
    public function deplacer($position,$start,$trait,$derniercoup)
    {
        $couleurdepart = $this->couleur($position[$start]);
        $endroit = array();
        $piecesAttaquees = array();
        $piecesdefendues = array();
        $compteur = 0;
        for ($i=0;$i<64;$i++)
        {
            $p = $position[$i];
            $arrivee = $this->trouve($p);

            $this->roiEchec($position,$start,$i,$trait);
            if ($this->legal($position,$start,$i,$derniercoup))
                if (!$this->getRoiEchec())
                    $endroit[] = $i;
               
            if ($this->Paslegal($position,$start,$i))
                $fauxendroit[] = $i;
                
        }
        
        for ($i=0;$i<count($endroit);$i++)
        {
                $positionPossible = $endroit[$i];
                $p = $position[$positionPossible];
                $arrivee = $this->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);
                if ($position[$positionPossible] != '' && $couleurdepart != $couleurarrivee)
                    $piecesAttaquees[] = $positionPossible;
        }

        if (isset($fauxendroit))
        {
            for ($i=0;$i<count($fauxendroit);$i++)
            {
                $fauxpositionPossible = $fauxendroit[$i];
                $p = $position[$fauxpositionPossible];
                $arrivee = $this->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);
                if ($position[$fauxpositionPossible] != '' && $couleurdepart == $couleurarrivee && $p != 'K' && $p != 'k')
                    $piecesdefendues[] = $fauxpositionPossible;
            }
        }
        
        if (isset($piecesdefendues))
            $this->_piecesDefendues = $piecesdefendues;
        if (isset($piecesAttaquees))   
            $this->_piecesAttaquees = $piecesAttaquees;
        if (isset($endroit))
            $this->_positionsPossibles = $endroit;
    }

    public function trouveCasesAttaquees($position,$trait)
    {
        $endroit = array();
        $localisation = array();
        $piecesAttaquees = array();

        if ($trait == 1)
            $indice = 0;
        else
            $indice = 1;
            
        $analyse = $this->lespiecesechiquier($position,$indice); // pieces de l'adversaire
        
        for ($i=0;$i<64;$i++)
        {
            for ($j=0;$j<count($analyse['piece']);$j++)
            {
                $depart = $analyse['endroit'][$j]; // numero de case avec une piece dessus
                $couleurdepart = $this->couleur($position[$depart]);
                $pieceadversaire = $analyse['piece'][$j];  // lettre de ;a piece qui est analysee, si minuscule c'est noir
                $lapiece = $this->trouve($pieceadversaire);
                if ($lapiece->legal($position,$depart,$i,-1))
                    $endroit[] = $i;
            }
         }
         for ($i=0;$i<count($endroit);$i++)
         {
            $positionPossible = $endroit[$i];
            $p = $position[$positionPossible];
            $arrivee = $this->trouve($p);
            $couleurarrivee = $arrivee->couleur($p);
            
            if ($p != '' && $couleurdepart != $couleurarrivee)
            {
                $piecesAttaquees[] = $p;
                $localisation[] = $endroit[$i];
            }
        }
        
        $this->_localisationpiecesattaquees = $localisation;
        
        return $localisation;
    }
    
    public function roiEchec($position,$start,$end,$trait)
    {
        $this->_roiechec = false;
        // Si trait = -1 noir
        $this->copyPosition($position);
        $positiontemp = $this->positionOrg();
        $positiontemp[$end] = $positiontemp[$start];
        $positiontemp[$start] = '';
        
        if ($trait == 1)
        {
            $color = 'b';
            $roi = 'K';
        }
        else
        {
            $color = 'w';
            $roi = 'k';
        }
            
        $positionroi = $this->lesPieces($positiontemp,$roi);
        
        if ($this->caseAttaquee($positiontemp,$positionroi,$color) == 1)
            $this->_roiechec = true;
    }

    public function getRoiEchec()
    {
        return $this->_roiechec;
    }

    public function caseAttaquee($position, $cell, $color)
    {
        if($this->caseAttaqueeParCavalier($position, $cell, $color)) return true;
        if($this->caseAttaqueeParFou($position, $cell, $color)) return true;       
        if($this->caseAttaqueeParTour($position, $cell, $color)) return true;
        if($this->caseAttaqueeParPion($position, $cell, $color)) return true;
        if($this->caseAttaqueeParRoi($position, $cell, $color)) return true;
        
        return false;
    }

    public function caseAttaqueeParTour($position, $cell, $color)
    {
        $line = $this->parseInt((int) $cell/8);
        $col = (int) $cell - $line*8;
        
        if($color == 'w')
        {
            $piece1 = 'R';
            $piece2 = 'Q';
        }
        else
        {
            $piece1 = 'r';
            $piece2 = 'q';
        }
        
        for($i=1; $i<9; $i++)
        {
            $a = $this->fcell($position, $line, $col+$i);
            
            if($a == $piece1 || $a == $piece2)
                return true;
            else if($a != '')
                break; 
        }
        
        for($i=1; $i<9; $i++)
        {
            $a = $this->fcell($position, $line, $col-$i);
            
            if($a == $piece1 || $a == $piece2)
                return true;
            else if($a != '')
                break; 
        }
        
        for($i=1; $i<9; $i++)
        {
            $a = $this->fcell($position, $line+$i, $col);
            
            if($a == $piece1 || $a == $piece2)
                return true;
            else if($a != '')
                break; 
        }
        
        for($i=1; $i<9; $i++)
        {
            $a = $this->fcell($position, $line-$i, $col);
            
            if($a == $piece1 || $a == $piece2)
                return true;
            else if($a != '')
                break; 
        }
        
        return false;
    }
    
    public function caseAttaqueeParCavalier($position, $cell, $color)
    {
        $cell = (int) $cell;
        $line = $this->parseInt($cell/8);
        $col = $cell - $line*8;
        
        if($color == 'w')
            $piece = 'N';
        else
            $piece = 'n';
            
        if($this->fcell($position, $line+1, $col-2) == $piece) return true;
        if($this->fcell($position, $line+1, $col+2) == $piece) return true;
        if($this->fcell($position, $line+2, $col-1) == $piece) return true;
        if($this->fcell($position, $line+2, $col+1) == $piece) return true;
        if($this->fcell($position, $line-1, $col-2) == $piece) return true;
        if($this->fcell($position, $line-1, $col+2) == $piece) return true;
        if($this->fcell($position, $line-2, $col-1) == $piece) return true;
        if($this->fcell($position, $line-2, $col+1) == $piece) return true;
        
        return(false);
    }

    public function caseAttaqueeParFou($position, $cell, $color)
    {   
        // ligne et colonne de depart
        $line = $this->parseInt((int) $cell/8);
        $col = (int) $cell - $line*8;
        
        if($color == 'w')
        {
            $piece1 = 'B';
            $piece2 = 'Q';
        }
        else
        {
            $piece1 = 'b';
            $piece2 = 'q';
        }
        
        // ligne de depart, avec incrementation de 2
        for($dl = -1; $dl<=1; $dl += 2)
        {
            for($dc = -1; $dc<=1; $dc= $dc + 2)
            {
                for($i = 1; $i<9; $i++)
                {
                    $a = $this->fcell($position, $line+$dl*$i, $col+$dc*$i);

                    if($a == $piece1 || $a == $piece2)
                        return true;
                        
                    if ($a != '')
                        break;
                        
                }
            }
        }
        
        return false;
    }

    public function caseAttaqueeParPion($position, $cell, $color)
    {
        $line = $this->parseInt((int )$cell/8);
        $col = (int) $cell - $line*8;
        
        if($color == 'w')
        {
            if($this->fcell($position, $line-1, $col-1) == 'P')
                return true;
            if($this->fcell($position, $line-1, $col+1) == 'P')
                return true;
        }
        else if($color == 'b')
        {
            if($this->fcell($position, $line+1, $col-1) == 'p')
                return true;
            if($this->fcell($position, $line+1, $col+1) == 'p')
                return true;
        }
        
        return false;
    }

    public function caseAttaqueeParRoi($position, $cell, $color)
    {
        $line = $this->parseInt((int) $cell/8);
        $col = (int) $cell - $line*8;
        
        if($color == 'w')
            $piece = 'K';
        else
            $piece = 'k';
        
        for($i=-1; $i<=1; $i++)
        {
            for($j=-1; $j<=1; $j++)
            {
                if($i==0 && $j==0)
                    continue;
                if($this->fcell($position, $line+$i, $col+$j) == $piece)
                    return true;
            }
        }
        
        return false;
    }

    private function copyPosition($pos2)
    {
        for($i=0; $i<64; $i++)
            $this->_positionOrg[$i] = $pos2[$i];
    }

    public function fcell($position, $line, $col)
    {
        if($line < 0 || $line > 7 || $col < 0 || $col > 7)
            return '-';
            
        $i = 8*$line+$col;
        
        return $position[$i];
    }

    protected function parseInt($string)
    {
        if(preg_match('/(\d+)/', $string, $array))
            return $array[1];
        else
            return 0;
    }
    
    protected function sign($a)
    {
        if($a < 0)
            return -1;
	   else if($a > 0)
            return 1;
            
        return 0;
    }
    
    public function jouer($cip,$coup,$changementb,$changementn)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $trait = $this->letrait($cip);
        
        if ($changementb != 0)
        {
            $sql = "update parties set reserve_uidb = $changementb where gid=".$cip;
            $resultat = $db->query($sql);
            $sql = "update parties set date_dernier_coup = now() where gid=".$cip;
            $resultat = $db->query($sql);
        }
        else
        {
            $sql = "update parties set date_dernier_coup = now() where gid=".$cip;
            $resultat = $db->query($sql);
        }
        if ($changementn != 0)
        {
            $sql = "update parties set reserve_uidn = $changementn where gid=".$cip;
            $resultat = $db->query($sql);
            $sql = "update parties set date_dernier_coup = now() where gid=".$cip;
            $resultat = $db->query($sql);
        }
        else
        {
            $sql = "update parties set date_dernier_coup = now() where gid=".$cip;
            $resultat = $db->query($sql);
        }

        $this->add($cip,$coup);
    }
    
    public function positionOrg()
    {
        return $this->_positionOrg;
    }

    public function lesPieces($position, $piece = -1)
    {
        $lespieces = array('blancs' => array(),'noirs' => array(),'vides' => array());
        
        for ($i=0;$i<64;$i++)
        {
            $p = $position[$i];
            
            if ($p == $piece)
                return $i;
            
            if ($p == '')
                $lespieces['vides'][] = $i;
            else
            {
                if ($p == ucfirst($p))
                    $lespieces['blancs'][] = $i;
                else
                    $lespieces['noirs'][] = $i;
            }
        }
        return $this->_lespieces = $lespieces;
    }
    
    protected function imagepiecemangee($id)
    {
        $image = "";

        switch($id)
        {
            case "P":
                $image = '<img src="./pieces/wp_21.gif">';
                break;
            case "R":
                $image = '<img src="./pieces/wr_21.gif">';
                break;
            case "N":
                $image = '<img src="./pieces/wn_21.gif">';
                break;
            case "B":
                $image = '<img src="./pieces/wb_21.gif">';
                break;
            case "Q":
                $image = '<img src="./pieces/wq_21.gif">';
                break;
            case "K":
                $image = '<img src="./pieces/wk_21.gif">';
                break;

            case "p":
                $image = '<img src="./pieces/bp_21.gif">';
                break;
                         
            case "r":
                $image = '<img src="./pieces/br_21.gif">';
                break;
            case "n":
                $image = '<img src="./pieces/bn_21.gif">';
                break;
            case "b":
                $image = '<img src="./pieces/bb_21.gif">';
                break;
            case "q":
                $image = '<img src="./pieces/bq_21.gif">';
                break;
            case "k":
                $image = '<img src="./pieces/bk_21.gif">';
                break;
        }
        
        return $image;
    }
    
    public function couleurpiece($position,$case)
    {
        if ($case == -1)
            return -1;
            
        $piece = $position[$case];
        
        if($piece=='p' || $piece=='r' || $piece=='b' || $piece=='n' || $piece=='q' || $piece=='k')
            return 0;
            
        else if($piece=='P' || $piece=='R' || $piece=='B' || $piece=='N' || $piece=='Q' || $piece=='K')
            return 1;
   
        return -1;
    }

    public function positionsPossibles()
    {
        return $this->_positionsPossibles;
    }

    public function piecesAttaquees()
    {
        return $this->_piecesAttaquees;
    }

    public function piecesDefendues()
    {
        return $this->_piecesDefendues;
    }
    

}
?>