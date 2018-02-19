<?php
class Fou extends Piece
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
            $this->_image = '<img border="0" src="./pieces/wb.gif" align="middle">';
        else
            $this->_image = '<img border="0" src="./pieces/bb.gif" align="middle">';
    }
    
    public function SetImageMangee()
    {
        if ($this->couleur($this->_nom) == 1)
            $this->_imagemangee = '<img border="0" src="./pieces/wb_21.gif" align="middle">';
        else
            $this->_imagemangee = '<img border="0" src="./pieces/bb_21.gif" align="middle">';      
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
        $this->initialise($position,$start,$end);
        $couleurdepart = $this->couleur($this->_nom);
        
        $casearrivee = new Piece;
        $p = $position[$end];
        $arrivee = $casearrivee->trouve($p);
        $couleurarrivee = $arrivee->couleur($p);

        if ($couleurdepart == $couleurarrivee)
            return false;

        
        if (abs($this->_dline) != abs($this->_dcol))
            return false;
               
        for ($i=1;$i<abs($this->_dline);$i++)
        {
          $p = $position[$start+$i*$this->sign($this->_dcol)+8*$i*$this->sign($this->_dline)];
          if ($p != '')
               return false;
        }
        
        return true;
    }

    public function Paslegal($position,$start,$end)
    {
        $this->initialise($position,$start,$end);
        $couleurdepart = $this->couleur($position[$start]);
        
        $casearrivee = new Piece;
        $p = $position[$end];
        $arrivee = $casearrivee->trouve($p);
        $couleurarrivee = $arrivee->couleur($p);
        
        if (abs($this->_dline) != abs($this->_dcol))
            return false;
        
             
        for ($i=1;$i<abs($this->_dline);$i++)
        {
          $p = $position[$start+$i*$this->sign($this->_dcol)+8*$i*$this->sign($this->_dline)];
          if ($p != '')
               return false;
        }
        
        return true;
    }

}

?>