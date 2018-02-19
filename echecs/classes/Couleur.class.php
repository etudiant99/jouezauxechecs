<?php
class Couleur
{
    protected $_unecouleur = array();
    
    public function __construct()
    {
        $this->setCouleur();
    }

    protected function setCouleur()
    {
        $this->_unecouleur[0] = '-1';
        $this->_unecouleur[1] = '-1';
        $this->_unecouleur[1] = '1';
    }
    
    public function Couleur($i)
    {
        if ($i != 0 & $i != -1 & $i != 1)
            return 'Couleur inexistante';
        return $this->_unecouleur[$i];
    }
}
?>