<?php
class Tour extends Piece
{
    protected $_nom;
    protected $_image;
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
            $this->_image = '<img border="0" src="./pieces/wr.gif" align="middle">';
        else
            $this->_image = '<img border="0" src="./pieces/br.gif" align="middle">';
    }
    
    public function SetImageMangee()
    {
        if ($this->couleur($this->_nom) == 1)
            $this->_imagemangee = '<img border="0" src="./pieces/wr_21.gif" align="middle">';
        else
            $this->_imagemangee = '<img border="0" src="./pieces/br_21.gif" align="middle">';      
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
            

        // deplacement de colonne vers la colonne h
        if ($this->_dline == 0 && $this->_dcol > 0)
        {
            for ($i=1;$i < $this->_dcol;$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start+$i];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);
                
                if ($couleurarrivee != -1)
                    if ($couleurarrivee == $couleurdepart)
                        return false;
                
                if ($p != '')
                    return false;
            }                
            return true;
        }
        // deplacement de colonne vers la colonne a
        else if($this->_dline == 0 && $this->_dcol < 0)
        {
            for ($i=1;$i<-$this->_dcol;$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start-$i];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);

                if ($couleurarrivee != -1)
                    if ($couleurarrivee == $couleurdepart)
                        return false;
                if($p != '')
                    return false;
            }
            return true;
        }
        else if($this->_dcol == 0 && $this->_dline > 0)
        {
            for ($i=1;$i<$this->_dline;$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start+8*$i];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);
                
                if ($couleurarrivee != -1)
                    if ($couleurarrivee == $couleurdepart)
                        return false;
                if($p != '')
                    return false;
            }

            return true;
        }
        // deplacement vers la ligne 1
        else if($this->_dcol == 0 && $this->_dline < 0)
        {
            for ($i=1;$i<-$this->_dline;$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start-8*$i];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);

                if ($couleurarrivee != -1)
                    if ($couleurarrivee == $couleurdepart)
                        return false;
                if($p != '')
                    return false;
            }
            return true;
        }
        return false;  
    }
    
    public function Paslegal($position,$start,$end)
    {
        $this->initialise($position,$start,$end);
        $couleurdepart = $this->couleur($position[$start]);
        
        $casearrivee = new Piece;
        $p = $position[$end];
        $arrivee = $casearrivee->trouve($p);
        $couleurarrivee = $arrivee->couleur($p);

        // deplacement de colonne vers la colonne h
        if ($this->_dline == 0 && $this->_dcol > 0)
        {
            for ($i=1;$i < $this->_dcol;$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start+$i];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);
                
                if ($couleurarrivee != -1)
                    if ($couleurarrivee == $couleurdepart)
                        return false;
                
                if ($p != '')
                    return false;
            }                
            return true;
        }
        // deplacement de colonne vers la colonne a
        else if($this->_dline == 0 && $this->_dcol < 0)
        {
            for ($i=1;$i<-$this->_dcol;$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start-$i];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);

                if ($couleurarrivee != -1)
                    if ($couleurarrivee == $couleurdepart)
                        return false;
                if($p != '')
                    return false;
            }
            return true;
        }
        else if($this->_dcol == 0 && $this->_dline > 0)
        {
            for ($i=1;$i<$this->_dline;$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start+8*$i];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);
                
                if ($couleurarrivee != -1)
                    if ($couleurarrivee == $couleurdepart)
                        return false;
                
                if($p != '')
                    return false;
            }

            return true;
        }
        // deplacement vers la ligne 1
        else if($this->_dcol == 0 && $this->_dline < 0)
        {
            for ($i=1;$i<-$this->_dline;$i++)
            {
                $casearrivee = new Piece;
                $p = $position[$start-8*$i];
                $arrivee = $casearrivee->trouve($p);
                $couleurarrivee = $arrivee->couleur($p);

                if ($couleurarrivee != -1)
                    if ($couleurarrivee == $couleurdepart)
                        return false;
                if($p != '')
                    return false;
            }
            return true;
        }
        return false;  

    }

}
?>