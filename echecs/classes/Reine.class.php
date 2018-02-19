<?php
class Reine extends Piece
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
            $this->_image = '<img border="0" src="./pieces/wq.gif" align="middle">';
        else
            $this->_image = '<img border="0" src="./pieces/bq.gif" align="middle">';
    }
    
    public function SetImageMangee()
    {
        if ($this->couleur($this->_nom) == 1)
            $this->_imagemangee = '<img border="0" src="./pieces/wq_21.gif" align="middle">';
        else
            $this->_imagemangee = '<img border="0" src="./pieces/bq_21.gif" align="middle">';      
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

        // une reine ne va pas manger une piece de sa propre couleur
        if ($couleurdepart == $couleurarrivee)
            return false;

        // deplacement comme un fou
        if (abs($this->_dline) == abs($this->_dcol))
        {
            for ($i=1;$i<abs($this->_dline);$i++)
            {
                if ($position[$start+$i*$this->sign($this->_dcol)+8*$i*$this->sign($this->_dline)] != '')
                    return false; // Si elle rencontre une piece de sa couleur sur sa trajectoiree de fou, elle ne passe pas par dessus
            }
            return true;
        }
        else if ($this->_dline == 0)
        {
            for ($i=1;$i<abs($this->_dcol);$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start+$i*$this->sign($this->_dcol)];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);

                if ($couleurarrivee != -1)
                    if ($couleurdepart == $couleurarrivee)
                        return false;
                            
                if ($p != '')
                    return false; // si ce n'est pas une case vide, elle arrete sa trajectoire
            }
            return true;
        }
        else if ($this->_dcol == 0)
        {
            for ($i=1;$i<abs($this->_dline);$i++)
            {
                $p = $position[$start+8*$i*$this->sign($this->_dline)];
                if ($p != '')
                    return false;   // si ce n'est pas une case vide, elle arrete sa trajectoire
            }
            return true;   
        }
        
        return false;  // sinon elle va partout, sauf si une piece de sa couleur l'a arrêtée
    }

    public function Paslegal($position,$start,$end)
    {
        $this->initialise($position,$start,$end);
        $couleurdepart = $this->couleur($this->_nom);
        
        $casearrivee = new Piece;
        $p = $position[$end];
        $arrivee = $casearrivee->trouve($p);
        $couleurarrivee = $arrivee->couleur($p);

        // deplacement comme un fou
        if (abs($this->_dline) == abs($this->_dcol))
        {
            for ($i=1;$i<abs($this->_dline);$i++)
            {
                if ($position[$start+$i*$this->sign($this->_dcol)+8*$i*$this->sign($this->_dline)] != '')
                    return false; // Si elle rencontre une piece de sa couleur sur sa trajectoiree de fou, elle ne passe pas par dessus
            }
            return true;
        }
        else if ($this->_dline == 0)
        {
            for ($i=1;$i<abs($this->_dcol);$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start+$i*$this->sign($this->_dcol)];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);

                if ($couleurarrivee != -1)
                    if ($couleurdepart == $couleurarrivee)
                        return false;
                            
                if ($p != '')
                    return false; // si ce n'est pas une case vide, elle arrete sa trajectoire
            }
            return true;
        }
        else if ($this->_dcol == 0)
        {
            for ($i=1;$i<abs($this->_dline);$i++)
            {
                $p = $position[$start+8*$i*$this->sign($this->_dline)];
                if ($p != '')
                    return false;   // si ce n'est pas une case vide, elle arrete sa trajectoire
            }
            return true;   
        }
        
        return false;  // sinon elle va partout, sauf si une piece de sa couleur l'a arrêtée
    }

}

?>