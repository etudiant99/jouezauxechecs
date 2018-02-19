<?php
class Pion extends Piece
{
    protected $_nom;
    private $_image;
    private $_imagemangee;
    
    public function __construct($p)
    {
        $this->_nom = $p;
        $this->setCouleur($p);
        $this->setImage();
        $this->SetImageMangee();
    }
    
    public function setImage()
    {
        if ($this->couleur($this->_nom) == 1)
            $this->_image = '<img border="0" src="./pieces/wp.gif" align="middle">';
        else
            $this->_image = '<img border="0" src="./pieces/bp.gif" align="middle">';
    }
    
    public function SetImageMangee()
    {
        if ($this->couleur($this->_nom) == 1)
            $this->_imagemangee = '<img border="0" src="./pieces/wp_21.gif" align="middle">';
        else
            $this->_imagemangee = '<img border="0" src="./pieces/bp_21.gif" align="middle">';      
    }
    
    public function nom()
    {
        return $this->_nom;
    }
    
    public function image()
    {
        return $this->_image;
    }
    
    public function imageMangee()
    {
        return $this->_imagemangee;
    }
    
    // la position est le contenu de chacune des 64 cases
    // $start est le numéro de la case de départ pour le coup
    // $end est le numéro de la case d'arrivée pour le coup
    // $derniercoup est le numéro de la case d'arrivée pour le dernier coup de l'adversaire
    public function legal($position,$start,$end,$derniercoup,$enpassant=0)
    {
        // Au départ, $start vaut 48
        // Au départ, $end vaut 0
        // Au départ, $derniercoup vaut 28 ok
        // Au départ, $position[$start] vaut p
        
        global $w;
        $nom = $this->nom(); // nom/couleur de la pièce qui sera bouger
        
        $this->initialise($position,$start,$end);
        $couleurjoueur = $this->couleur($this->_nom); // 1 Blancs -1 Noirs 0 case vide (ici Blancs parce que P)

        if($position[$start] == 'P') // Pion blanc exemple case de départ: 8
        {
            if ($start > 31 && $start < 40)
            {
                if ($derniercoup == $start-1)
                    $enpassant = -1;
                if ($derniercoup == $start+1)
                    $enpassant = 1;
            }
            
            // on avance le pion de une case
            if($this->_dline == 1 && $this->_dcol == 0 && $position[$end] == '')
                return true;

            // on avance le pion de deux cases
            if($this->_dline == 2 && $this->_dcol == 0 && $position[$end] == '' && $position[$end-8] == '' && $start > 7 && $start < 16)
                return true;

            // déplacement en diagonale avec possibilite de faire la prise en passant
            if($this->_dline == 1 && ($this->_dcol == 1 || $this->_dcol == -1))
            {
                $casearrivee = new Piece;
                $p = $position[$end];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);

                if ($couleurjoueur == $couleurarrivee)
                {
                    $w[] = $end;
                    
                    if (isset($w))
                        $this->_piecesDefendues = $w;
                }

                if($p != '' && ($couleurjoueur != $couleurarrivee))
                    return true;
                else if ($this->_dcol == $enpassant)
                    return true;  
            }
            return false;
        }

        if($position[$start] == 'p') // Pion noir
        {
            if ($start > 23 && $start < 32)
            {
                if ($derniercoup == $start-1)
                    $enpassant = -1;
                if ($derniercoup == $start+1)
                    $enpassant = 1;
            }

            // on avance le pion de une case
            if($this->_dline == -1 && $this->_dcol == 0 && $position[$end] == '')
                return true;
            
            // on avance le pion de deux cases  
            if($this->_dline == -2 && $this->_dcol == 0 && $position[$end] == '' && $position[$end+8] == '' && $start > 47 && $start < 56)
                return true;
            
            // deplacement en diagonale 
              
            if($this->_dline == -1 && ($this->_dcol == 1 || $this->_dcol == -1))
            {
                $casearrivee = new Piece;
                $p = $position[$end];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);
                
                if ($couleurjoueur == $couleurarrivee)
                {
                    $w[] = $end;
                    
                    if (isset($w))
                        $this->_piecesDefendues = $w;
                }

                if($p != '' && ($couleurjoueur != $couleurarrivee))
                    return true;
                else if ($this->_dcol == $enpassant)
                    return true;  
            }
            return false;
        }
    }

    public function Paslegal($position,$start,$end)
    {
        global $w;
        $nom = $this->nom();

        $this->initialise($position,$start,$end);
        $couleurjoueur = $this->couleur($this->_nom);

        if($position[$start] == 'P')
        {
            // deplacement en diagonale
            if($this->_dline == 1 && ($this->_dcol == 1 || $this->_dcol == -1))
            {
                $casearrivee = new Piece;
                $p = $position[$end];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);

                if ($couleurjoueur == $couleurarrivee)
                {
                    $w[] = $end;
                    
                    if (isset($w))
                        return true;
                }
            }
        }
        
        if($position[$start] == 'p')
        {
            // deplacement en diagonale     
            if($this->_dline == -1 && ($this->_dcol == 1 || $this->_dcol == -1))
            {
                $casearrivee = new Piece;
                $p = $position[$end];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);
                
                if ($couleurjoueur == $couleurarrivee)
                {
                    $w[] = $end;
                    
                    if (isset($w))
                        return true;
                }
            }
        }
        
    }

}
?>