<?php
class Coup
{
    private $_cip;
    private $_ordre;
    private $_coups;
    
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

    public function setCip($id)
    {
        $this->_cip = $id;
    }
    
    public function setOrdre($id)
    {
        $this->_ordre = $id;
    }

    public function setCoups($id)
    {
        $this->_coups = $id;
    }
    
    public function detailCoup($id)
    {
        $composantes = array();
        
        return $composantes;
    }
    
    public function cip()
    {
        return $this->_cip;
    }

    public function ordre()
    {
        return $this->_ordre;
    }

    public function coups()
    {
        return $this->_coups;
    }

}

?>