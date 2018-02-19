<?php
class Statistique
{
    private $_id;
    private $_uid;
    private $_gains_b;
    private $_pertes_b;
    private $_nulles_b;
    private $_gains_n;
    private $_pertes_n;
    private $_nulles_n;

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

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function setUid($id)
    {
        $this->_uid = $id;
    }

    public function setGains_b($id)
    {
        $this->_gains_b = $id;
    }

    public function setPertes_b($id)
    {
        $this->_pertes_b = $id;
    }

    public function setNulles_b($id)
    {
        $this->_nulles_b = $id;
    }

    public function setGains_n($id)
    {
        $this->_gains_n = $id;
    }

    public function setPertes_n($id)
    {
        $this->_pertes_n = $id;
    }

    public function setNulles_n($id)
    {
        $this->_nulles_n = $id;
    }

    public function id()
    {
        return $this->_id;
    }
    
    public function uid()
    {
        return $this->_uid;
    }

    public function gains_b()
    {
        return $this->_gains_b;
    }

    public function pertes_b()
    {
        return $this->_pertes_b;
    }

    public function nulles_b()
    {
        return $this->_nulles_b;
    }

    public function gains_n()
    {
        return $this->_gains_n;
    }

    public function pertes_n()
    {
        return $this->_pertes_n;
    }

    public function nulles_n()
    {
        return $this->_nulles_n;
    }

    public function gainstotaux()
    {
        $gainsblancs = (int) $this->_gains_b;
        $gainsnoirs = (int) $this->_gains_n;
        $totalgains = $gainsblancs+$gainsnoirs;
        
        return $totalgains;
    }

    public function pertestotales()
    {
        $pertesblancs = (int) $this->_pertes_b;
        $pertesnoirs = (int) $this->_pertes_n;
        $totalpertes = $pertesblancs+$pertesnoirs;
        
        return $totalpertes;
    }

    public function nullestotales()
    {
        $nullesblancs = (int) $this->_nulles_b;
        $nullesnoirs = (int) $this->_nulles_n;
        $totalnulles = $nullesblancs+$nullesnoirs;
        
        return $totalnulles;
    }
    
    public function partiesavecblancs()
    {        
        return $this->_gains_b+$this->pertes_b()+$this->nulles_b();
    }

    public function partiesavecnoirs()
    {        
        return $this->_gains_n+$this->pertes_n()+$this->nulles_n();
    }


    public function partiestotales()
    {        
        return $this->pertestotales()+$this->gainstotaux()+$this->nullestotales();
    }


    public function pourcentagegains()
    {
        if ($this->partiestotales() > 0)
            $pourcentage = number_format($this->gainstotaux()*100/$this->partiestotales(),0).'%';
        else
            $pourcentage = '0 %';
        
        return $pourcentage;
    }

}

?>