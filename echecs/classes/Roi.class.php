<?php
class Roi extends Piece
{
    protected $_nom;
    private $_image;
    private $_imagemangee;
    
    public function __construct($p)
    {
        $this->_nom = $p;
        $this->setCouleur($p);
        $this->setImage();
    }
    
    public function setImage()
    {
        if ($this->couleur($this->_nom) == 1)
            $this->_image = '<img border="0" src="./pieces/wk.gif" align="middle">';
        else
            $this->_image = '<img border="0" src="./pieces/bk.gif" align="middle">';
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
    
    public function legal($position,$start,$end)
    {
        global $w;
        $this->initialise($position,$start,$end);
        $couleurdepart = $this->couleur($this->_nom);
        
        $casearrivee = new Piece;
        $p = $position[$end];
        $arrivee = $casearrivee->trouve($p);
        $couleurarrivee = $arrivee->couleur($p);
        
        if ($couleurdepart == $couleurarrivee)
            return false;
             
        if($this->_dline > -2 && $this->_dline < 2 && $this->_dcol > -2 && $this->_dcol < 2 && ($this->_dcol != 0 || $this->_dline != 0))
            return true;
        
        // le roi ne change pas de ligne et bouge de 2 colonnes vers la droite
        if($this->_dline == 0 && $this->_dcol == 2)
        {
            if($position[$start] == 'K' && $start != 4)
                return false;
                            
            if($position[$start] == 'k' && $start != 60)
                return false;
                            
            if($position[$start+1] != '' || $position[$start+2] != '')
                return false;
                        
            return true;
        }
        else if($this->_dline == 0 && $this->_dcol == -2) // le roi bouge vers la gauche
        {
            if($position[$start] == 'K' && $start != 4)
                return false;
                                
            if($position[$start] == 'k' && $start != 60)
                return false;
                                
            if($position[$start-1] != '' || $position[$start-2] != '' || $position[$start-3] != '')
                return false;
                            
            return true;
        }
                        
        return(false);
    }

    public function Paslegal($position,$start,$end)
    {
        global $w;
        $this->initialise($position,$start,$end);
        $couleurdepart = $this->couleur($this->_nom);
        
        $casearrivee = new Piece;
        $p = $position[$end];
        $arrivee = $casearrivee->trouve($p);
        $couleurarrivee = $arrivee->couleur($p);

        if($this->_dline > -2 && $this->_dline < 2 && $this->_dcol > -2 && $this->_dcol < 2 && ($this->_dcol != 0 || $this->_dline != 0))
            if ($position[$start+1 != ''])
                return true;
    }
}

?>