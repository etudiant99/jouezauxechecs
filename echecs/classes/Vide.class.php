<?php
class Vide extends Piece
{
    protected $_nom;
    protected $_image;
    private $_imagemangee;
    protected $_couleur;
    
    public function __construct($p)
    {
        $this->_nom = $p;
        $this->setCouleur($p);
        $this->setImage();
    }
    
    public function setImage()
    {
        $this->_image = '<img border="0" src="./pieces/vide.gif" align="middle">';
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
        return false;
    }

    public function Paslegal($position,$start,$end)
    {
        
    }

}

?>