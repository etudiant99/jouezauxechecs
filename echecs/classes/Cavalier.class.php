<?php
class Cavalier extends Piece
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
            $this->_image = '<img border="0" src="./pieces/wn.gif" align="middle">';
        else
            $this->_image = '<img border="0" src="./pieces/bn.gif" align="middle">';
    }
    
    public function SetImageMangee()
    {
        if ($this->couleur($this->_nom) == 1)
            $this->_imagemangee = '<img border="0" src="./pieces/wn_21.gif" align="middle">';
        else
            $this->_imagemangee = '<img border="0" src="./pieces/bn_21.gif" align="middle">';      
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
        {
            if ($this->_dline * $this->_dcol == 2 || $this->_dline * $this->_dcol == -2  and ($p != 'k' or $p != 'K'))
            {
                if ($p != 'K' and $p != 'k')
                    $w[] = $end;
                    
                if (isset($w))
                    $this->_piecesDefendues = $w;
            }            

        }
        
        if ($couleurdepart == $couleurarrivee)
            return false;

        if ($this->_dline * $this->_dcol == 2 || $this->_dline * $this->_dcol == -2)
            return true;
            
        
        return false;
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

        if ($couleurdepart == $couleurarrivee)
            if ($this->_dline * $this->_dcol == 2 || $this->_dline * $this->_dcol == -2  and ($p != 'k' or $p != 'K'))
                if ($p != 'K' and $p != 'k')
                    return true;
    }

}
?>