<?php
class PartieManager extends CoupManager
{
    private $_uidActif;
    private $_parties;
    
    public function __construct()
    {
        $this->_uidActif = $_SESSION['uid'];      
        $this->positiondepart();
    }
    
    public function getListePartiesActif()
    {
        return $this->_parties;
    }

    public function getListeParties()
    {
        return $this->_malisteparties;
    }

    public function get($id)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        
        $this->letrait($id);
        $this->setIgn($id);
        $trait = $this->getTrait();
        $lastmove = $this->Lastmove();
         if($lastmove != '')
         {
            $lmove = explode("-",$lastmove);
            $start = $lmove[0];
            $end = $lmove[1];
         }
         else
         {
            $start = -1;
            $end = -1;
         }
        $this->nbCoupsPossibles($id,$end);
        
        
        $id = (int) $id;
        
        $q = $db->query('SELECT * FROM parties WHERE gid = '.$id);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $donnees['actif'] = $this->_uidActif;
        $donnees['trait'] = $trait;
        $donnees['lescoups'] = $this->Ign();
        $donnees['nbcoups'] = $this->nbCoups($id);
        $donnees['mangeaille'] = $this->getMangeaille();
        $donnees['positionroiblanc'] = $this->getPositionRoiBlanc();
        $donnees['positionroinoir'] = $this->getPositionRoiNoir();
        $donnees['roiattaque'] = $this->getRoiAttaque();
        $donnees['nbcoupspossibles'] = $this->donneNbCoupsPossibles();
        
        return new Partie($donnees);
    }
    
    public function countencours()
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "SELECT COUNT(*) FROM parties WHERE finalisation=0";

        return $db->query($requete)->fetchColumn();
    }

    public function countpartiesblancs($uidActif)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "SELECT COUNT(*) FROM parties WHERE finalisation=0 and uidb=$uidActif";

        return $db->query($requete)->fetchColumn();
    }

    public function countpartiesnoirs($uidActif)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "SELECT COUNT(*) FROM parties WHERE finalisation=0 and uidn=$uidActif";

        return $db->query($requete)->fetchColumn();
    }

    public function setListeParties()
    {
        $parties = array();
        
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "SELECT * FROM parties where finalisation=0 ORDER BY gid";
        
        $q = $db->query($requete);

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $gid = $donnees['gid'];
            $this->letrait($gid);
            $trait = $this->getTrait();
            $donnees['actif'] = $this->_uidActif;
            $donnees['nbcoups'] = $this->nbCoups($gid);
            $donnees['trait'] = $trait;
            $parties[] = new Partie($donnees);
        }
        $this->_malisteparties = $parties;
    }
    
    public function setListePartiesActif($joueurActif)
    {
        $parties = array();
        
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "SELECT * FROM parties where (uidb=$joueurActif or uidn=$joueurActif) and finalisation=0 ORDER BY gid";

        $q = $db->query($requete);
        if (!$q)
            die("Table parties inexistante");

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $gid = $donnees['gid'];
            $this->letrait($gid);
            $trait = $this->getTrait();
            $donnees['actif'] = $joueurActif;
            $donnees['trait'] = $trait;
            $donnees['nbcoups'] = $this->nbCoups($gid);
            $parties[] = new Partie($donnees);
        }

        $this->_parties = $parties;
    }

    public function getListPartiesterminees($uidActif)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $parties = array();
        
        
    $requete = "select p.gid, 
                       p.uidb, 
                       p.uidn, 
                       p.finalisation, 
                       blancs.pseudo as pseudob, 
                       noirs.pseudo as pseudon,
                       blancs.connecte as blancs_connectes,
                       noirs.connecte as noirs_connectes,
                       b.sexe as sexeb, 
                       n.sexe as sexen  
               from parties p, 
                    login blancs, 
                    login noirs, 
                    users b, 
                    users n 
               where (uidb=$uidActif or uidn=$uidActif) 
                  and p.uidb=blancs.uid 
                  and p.uidn=noirs.uid 
                  and blancs.uid=b.uid 
                  and noirs.uid=n.uid 
                  and p.date_fin != '0000-00-00' 
                  and efface!=$uidActif order by gid";

        
        $q = $db->query($requete);

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $donnees['actif'] = $uidActif;
            $parties[] = new Partie($donnees);
        }

        return $parties;
    }


    public function effacer($nopartie, $uidActif)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "select * from parties where gid = $nopartie";
        $q = $db->query($requete);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);

        $resultat = $donnees['efface'];
        
        if ($resultat == 0)
        {
            $requete = "update parties set efface = $uidActif where gid=$nopartie";
            $resultat1 = $db->query($requete);            
        }
        else
        {
            $requete1 = "delete from coups where cip=$nopartie";
            $resultat1 = $db->query($requete1);
        
            $requete2 = "delete from parties where gid=$nopartie";
            $resultat2 = $db->query($requete2);
        }
        header('Location:  index.php?action=mes parties terminées');
    }
    
    public function acepter($nopartie, $uidActif)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "select * from partiesproposees where gidp=$nopartie";
        $q = $db->query($requete);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        
        $cadence = $donnees['cadence'];
        $reserve = $donnees['reserve'];
        $mon_gdip = $donnees['gidp'];
        $macouleur = $donnees['macouleur'];
        $origine = $donnees['origine'];
        
        if ($macouleur == "n")
        {
            $uidn = $origine;
            $uidb = $uidActif;
        }
    
        if ($macouleur == "b")
        {
            $uidn = $uidActif;
            $uidb = $origine;
        }
        
        if ($macouleur == "-")
        {
            $reponse = mt_rand(1,1000);
        
        
            if ($reponse%2 == 1)
            {
                $uidb = $uidActif;
                $uidn = $origine;
            }
            else
            {
                $uidb = $origine;
                $uidn = $uidActif;
            }
        }
        
        $sql = "select max(gid) as maximum from parties order by gid";
        $q = $db->query($sql);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $mon_numero_partie = $donnees['maximum'];
        
        $conversion = intval($mon_numero_partie);
        $conversion++;

        $sql = "insert into parties (gid, uidb, uidn, date_debut, cadencep, reservep, date_dernier_coup, reserve_uidb, reserve_uidn) 
        VALUES ($conversion, $uidb, $uidn, now(), $cadence, $reserve, now(), $reserve, $reserve)";
        $db->exec($sql);
        
        $sql = "delete from partiesproposees where gidp=$mon_gdip";
        $db->exec($sql);

        header('Location: index.php?action=mes parties');
    }
    
    public function finTemps($uidActif,$nopartie)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $lapartie = $this->get($nopartie);
        $temps_maximum_en_secondes = strtotime($lapartie->datederniercoup())+(86400* $lapartie->cadencep());
        $nombre_secondes_maintenant = time();
        if ($temps_maximum_en_secondes < $nombre_secondes_maintenant)
        {
            $q = $db->query('SELECT * FROM parties WHERE gid = '.$nopartie);
           	$donnees = $q->fetch(PDO::FETCH_ASSOC);
            $uidb = $donnees['uidb'];
            $uidn = $donnees['uidn'];
            
            if ($uidActif == $uidb)
            {
                $requete1 = 'update parties set date_fin=now(),finalisation=6 where gid='.$nopartie;
                $requete2 = 'update statistiques set pertes_b=pertes_b+1 where uid='.$uidActif;
                $requete3 = 'update statistiques set gains_n=gains_n+1 where uid='.$uidn;
            }
            else
            {
                $requete1 = 'update parties set date_fin=now(),finalisation=5 where gid='.$nopartie;
                $requete2 = 'update statistiques set pertes_n=pertes_n+1 where uid='.$uidActif;
                $requete3 = 'update statistiques set gains_b=gains_b+1 where uid='.$uidb;
            }
            
            $db->query($requete1);
            $db->query($requete2);
            $db->query($requete3);
            
            if ($uidActif == $uidn)
                $this->calcule_elo($uidb,$uidn,1);
            else
                $this->calcule_elo($uidn,$uidb,1);
        }
        header('Location: index.php?action=mes parties');
    }
    
    public function terminerPartie($uidActif,$nopartie)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        
        $q = $db->query('SELECT * FROM parties WHERE gid = '.$nopartie);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $uidb = $donnees['uidb'];
        $uidn = $donnees['uidn'];

        if ($uidActif == $uidb)
        {
            $requete1 = 'update parties set date_fin=now(),finalisation=6 where gid='.$nopartie;
            $requete2 = 'update statistiques set pertes_b=pertes_b+1 where uid='.$uidActif;
            $requete3 = 'update statistiques set gains_n=gains_n+1 where uid='.$uidn;
        }
        else
        {
            $requete1 = 'update parties set date_fin=now(),finalisation=5 where gid='.$nopartie;
            $requete2 = 'update statistiques set pertes_n=pertes_n+1 where uid='.$uidActif;
            $requete3 = 'update statistiques set gains_b=gains_b+1 where uid='.$uidb;
        }
        
        $db->query($requete1);
        $db->query($requete2);
        $db->query($requete3);

        if ($uidActif == $uidn)
            $this->calcule_elo($uidb,$uidn,1);
        else
            $this->calcule_elo($uidn,$uidb,1);
                          
        header('Location: index.php?action=mes parties');
    }
    
    public function abandonner($uidActif,$nopartie)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        //$requete1 = 'update parties set date_fin=now(),finalisation=3 where gid='.$nopartie;
        
        $q = $db->query('SELECT * FROM parties WHERE gid = '.$nopartie);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $uidb = $donnees['uidb'];
        $uidn = $donnees['uidn'];
        
        if ($uidActif == $uidb)
        {
            $requete1 = 'update parties set date_fin=now(),finalisation=3 where gid='.$nopartie;
            $requete2 = 'update statistiques set pertes_b=pertes_b+1 where uid='.$uidActif;
            $requete3 = 'update statistiques set gains_n=gains_n+1 where uid='.$uidn;
        }
        else
        {
            $requete1 = 'update parties set date_fin=now(),finalisation=4 where gid='.$nopartie;
            $requete2 = 'update statistiques set pertes_n=pertes_n+1 where uid='.$uidActif;
            $requete3 = 'update statistiques set gains_b=gains_b+1 where uid='.$uidb;
        }
        
        $db->query($requete1);
        $db->query($requete2);
        $db->query($requete3);
        
        if ($uidActif == $uidn)
            $this->calcule_elo($uidb,$uidn,1);
        else
            $this->calcule_elo($uidn,$uidb,1);
                          
        header('Location: index.php?action=mes parties');
    }
    
    public function nulle($gid)
    {
        $perdant = $_SESSION['uid'];
        
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $q = $db->query('SELECT * FROM parties WHERE gid = '.$gid);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $uidb = $donnees['uidb'];
        $uidn = $donnees['uidn'];

        if ($perdant == $uidb)
        {
            $sql_un = "update parties set date_fin = now(), finalisation = 2 where gid=$gid";
            $sql_deux = "update statistiques set nulles_b = nulles_b+1  where uid=$uidb";
            $sql_trois = "update statistiques set nulles_n = nulles_n+1 where uid=$uidn";
            $this->calcule_elo($uidb, $uidn, 0.5);
        }
        else
        {
            $sql_un = "update parties set date_fin = now(), finalisation = 1 where gid=$gid";
            $sql_deux = "update statistiques set nulles_n = nulles_n+1  where uid=$uidn";
            $sql_trois = "update statistiques set nulles_b = nulles_b+1 where uid=$uidb";
            $this->calcule_elo($uidn, $uidb, 0.5);    
        }

        $db->query($sql_un);
        $db->query($sql_deux);
        $db->query($sql_trois);

        header('Location: index.php?action=mes parties');
        
        return;
    }
    
    public function mat($uidActif,$nopartie)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $q = $db->query('SELECT * FROM parties WHERE gid = '.$nopartie);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $uidb = $donnees['uidb'];
        $uidn = $donnees['uidn'];
        $trait = $this->letrait($nopartie);
        
        if ($trait == $uidb)
            $conclusion = 8;
        else
            $conclusion = 7;
        
        $requete1 = 'UPDATE parties SET date_fin=now(),finalisation='.$conclusion.' WHERE gid='.$nopartie;
        
        if ($conclusion == 7)
        {
            echo 'conclusion: 7'; exit();
            $requete2 = 'UPDATE statistiques set gains_b=gains_b+1 WHERE uid='.$uidb;
            $requete3 = 'UPDATE statistiques set pertes_n=pertes_n+1 WHERE uid='.$uidn;
            $this->calcule_elo($uidb,$uidn,1);
        }
        else
        {
            echo 'conclusion: 8'; exit();
            $requete2 = 'UPDATE statistiques set gains_n=gains_n+1 WHERE uid='.$uidn;
            $requete3 = 'UPDATE statistiques set pertes_b=pertes_b+1 WHERE uid='.$uidb;
            $this->calcule_elo($uidn,$uidb,1);
        }

        $db->query($requete1);
        $db->query($requete2);
        $db->query($requete3);

        header('Location: index.php?action=mes parties');
    }
    
    public function miseajourcoup($trait,$gid)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);

        $requete = 'UPDATE parties set trait='.$trait.',date_dernier_coup=now() where gid='.$gid;
        $db->query($requete);
    }
    
    
    public function effacerpartie($nopartie)
    {
        $uidActif = $_SESSION['uid'];
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $q = $db->query('select count(*) as nombre FROM parties WHERE gid = '.$nopartie);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        $nombre = $donnees['nombre'];
        
        if ($nombre == 1)
        {
            $requete = "select efface from parties where gid = $nopartie";
            $resultat1 = $db->query($requete);
            $donnees = $resultat1->fetch(PDO::FETCH_ASSOC);
            $reponse = $donnees['efface'];
            
            if ($reponse == 0)
            {
                $requete = "update parties set efface = $uidActif where gid=$nopartie";
                $resultat1 = $db->query($requete);
            }
            else
            {
                $requete1 = "delete from coups where cip=$nopartie";
                $resultat1 = $db->query($requete1);
        
                $requete2 = "delete from parties where gid=$nopartie";
                $resultat2 = $db->query($requete2);
            }
        }
        header('Location:  index.php?action=parties terminées');
    }
    
    protected function calcule_elo($uidb, $uidn, $resultat)
    {
        $infos_uidb = $this->calcul_coefficient($uidb);
        $infos_uidn = $this->calcul_coefficient($uidn);
    
        $elo_uidb = $infos_uidb['elo'];
        $coefficient_uidb = $infos_uidb['coefficient'];
    
        $elo_uidn = $infos_uidn['elo'];
        $coefficient_uidn = $infos_uidn['coefficient'];
   
        // calcul des points d'écart
        if ($elo_uidb > $elo_uidn)
            $points_ecart = $elo_uidb - $elo_uidn;
        else
            $points_ecart = $elo_uidn - $elo_uidb;
        
        // calcul du pourcentage de gain/perte
    
        if ($points_ecart < 3)
        {
            $pourcentage_gain = .5;
            $pourcentage_perte = .5;
        }
        else if (($points_ecart > 3) && ($points_ecart < 11))
        {
            $pourcentage_gain = .51;
            $pourcentage_perte = .49;
        }
        else if (($points_ecart > 10) && ($points_ecart < 18))
        {
            $pourcentage_gain = .52;
            $pourcentage_perte = .48;
        }
        else if (($points_ecart > 17) && ($points_ecart < 26))
        {
            $pourcentage_gain = .53;
            $pourcentage_perte = .47;
        }
        else if (($points_ecart > 25) && ($points_ecart < 33))
        {
            $pourcentage_gain = .54;
            $pourcentage_perte = .46;
        }
    
        else if (($points_ecart > 32) && ($points_ecart < 40))
        {
            $pourcentage_gain = .55;
            $pourcentage_perte = .45;
        }
        else if (($points_ecart > 39) && ($points_ecart < 47))
        {
            $pourcentage_gain = .56;
            $pourcentage_perte = .44;
        }
        else if (($points_ecart > 46) && ($points_ecart < 54))
        {
            $pourcentage_gain = .57;
            $pourcentage_perte = .43;
        }
        else if (($points_ecart > 53) && ($points_ecart < 62))
        {
            $pourcentage_gain = .58;
            $pourcentage_perte = .42;
        }
        else if (($points_ecart > 61) && ($points_ecart < 69))
        {
            $pourcentage_gain = .59;
            $pourcentage_perte = .41;
        }
        else if (($points_ecart > 68) && ($points_ecart < 77))
        {
            $pourcentage_gain = .60;
            $pourcentage_perte = .40;
        }
        else if (($points_ecart > 76) && ($points_ecart < 84))
        {
            $pourcentage_gain = .61;
            $pourcentage_perte = .39;
        }

        else if (($points_ecart > 83) && ($points_ecart < 92))
        {
            $pourcentage_gain = .62;
            $pourcentage_perte = .38;
        }

        else if (($points_ecart > 91) && ($points_ecart < 99))
        {
            $pourcentage_gain = .63;
            $pourcentage_perte = .37;
        }
        else if (($points_ecart > 98) && ($points_ecart < 107))
        {
            $pourcentage_gain = .64;
            $pourcentage_perte = .36;
        }
        else if (($points_ecart > 106) && ($points_ecart < 114))
        {
            $pourcentage_gain = .65;
            $pourcentage_perte = .35;
        }
        else if (($points_ecart > 113) && ($points_ecart < 122))
        {
            $pourcentage_gain = .66;
            $pourcentage_perte = .34;
        }
        else if (($points_ecart > 121) && ($points_ecart < 130))
        {
            $pourcentage_gain = .67;
            $pourcentage_perte = .33;
        }
        else if (($points_ecart > 129) && ($points_ecart < 138))
        {
            $pourcentage_gain = .68;
            $pourcentage_perte = .32;
        }
        else if (($points_ecart > 137) && ($points_ecart < 146))
        {
            $pourcentage_gain = .69;
            $pourcentage_perte = .31;
        }


        else if (($points_ecart > 145) && ($points_ecart < 154))
        {
            $pourcentage_gain = .70;
            $pourcentage_perte = .30;
        }

        else if (($points_ecart > 153) && ($points_ecart < 163))
        {
            $pourcentage_gain = .71;
            $pourcentage_perte = .29;
        }
        else if (($points_ecart > 162) && ($points_ecart < 171))
        {
            $pourcentage_gain = .72;
            $pourcentage_perte = .28;
        }
        else if (($points_ecart > 170) && ($points_ecart < 180))
        {
            $pourcentage_gain = .73;
            $pourcentage_perte = .27;
        }
        else if (($points_ecart > 179) && ($points_ecart < 189))
        {
        $pourcentage_gain = .74;
        $pourcentage_perte = .26;
        }
        else if (($points_ecart > 188) && ($points_ecart < 198))
        {
            $pourcentage_gain = .75;
            $pourcentage_perte = .25;
        }
        else if (($points_ecart > 197) && ($points_ecart < 207))
        {
            $pourcentage_gain = .76;
            $pourcentage_perte = .24;
        }
        else if (($points_ecart > 206) && ($points_ecart < 216))
        {
            $pourcentage_gain = .77;
            $pourcentage_perte = .23;
        }
        else if (($points_ecart > 215) && ($points_ecart < 226))
        {
            $pourcentage_gain = .78;
            $pourcentage_perte = .22;
        }
        else if (($points_ecart > 225) && ($points_ecart < 236))
        {
            $pourcentage_gain = .79;
            $pourcentage_perte = .21;
        }
        else if (($points_ecart > 235) && ($points_ecart < 246))
        {
            $pourcentage_gain = .80;
            $pourcentage_perte = .20;
        }
        else if (($points_ecart > 245) && ($points_ecart < 257))
        {
            $pourcentage_gain = .81;
            $pourcentage_perte = .19;
        }
        else if (($points_ecart > 256) && ($points_ecart < 268))
        {
            $pourcentage_gain = .82;
            $pourcentage_perte = .18;
        }
        else if (($points_ecart > 267) && ($points_ecart < 279))
        {
            $pourcentage_gain = .83;
            $pourcentage_perte = .17;
        }
        else if (($points_ecart > 278) && ($points_ecart < 291))
        {
            $pourcentage_gain = .84;
            $pourcentage_perte = .16;
        }
        else if (($points_ecart > 290) && ($points_ecart < 303))
        {
            $pourcentage_gain = .85;
            $pourcentage_perte = .15;
        }
        else if (($points_ecart > 302) && ($points_ecart < 318))
        {
            $pourcentage_gain = .86;
            $pourcentage_perte = .14;
        }
        else if (($points_ecart > 317) && ($points_ecart < 329))
        {
            $pourcentage_gain = .87;
            $pourcentage_perte = .13;
        }
        else if (($points_ecart > 328) && ($points_ecart < 345))
        {
            $pourcentage_gain = .88;
            $pourcentage_perte = .12;
        }
        else if ($points_ecart > 345)
        {
            $pourcentage_gain = .89;
            $pourcentage_perte = .11;
        }
    
        if ($elo_uidb < $elo_uidn)
        {
            $espoir_gain_blanc = $pourcentage_perte;
            $espoir_gain_noir = $pourcentage_gain;    
        }
        else if ($elo_uidb > $elo_uidn)
        {
            $espoir_gain_blanc = $pourcentage_gain;
            $espoir_gain_noir = $pourcentage_perte;            
        }
        else
        {
            $espoir_gain_blanc = $pourcentage_gain;
            $espoir_gain_noir = $pourcentage_perte;
        }
    
        switch ($resultat)
        {
            case 1 :
                if ($elo_uidb > $elo_uidn)
                    $evolution_blanc = round($pourcentage_perte * $coefficient_uidb);
                else
                    $evolution_blanc = round($pourcentage_gain * $coefficient_uidb);
                
                $evolution_noir = -$evolution_blanc;            
             break;
            case 0.5 :
                $etape_un = round($pourcentage_gain * $coefficient_uidb);
                $etape_deux = -round($pourcentage_perte * $coefficient_uidb);
            
                if ($elo_uidb > $elo_uidn)
                    $evolution_blanc = -($etape_un + $etape_deux)/2;
                else
                    $evolution_blanc = ($etape_un + $etape_deux)/2;
            
                $evolution_noir = -$evolution_blanc;
                break;
            case 0 :
                if ($elo_uidb > $elo_uidn)
                    $evolution_blanc = -round($pourcentage_gain * $coefficient_uidb);
                else
                    $evolution_blanc = -round($pourcentage_perte * $coefficient_uidb);
            
                $evolution_noir = -$evolution_blanc;
                break;
        }

        $nouvel_elo_blanc = $elo_uidb + $evolution_blanc;
        $nouvel_elo_noir = $elo_uidn + $evolution_noir;
    
    
        $elo_retourne_blanc = round($nouvel_elo_blanc);
        $elo_retourne_noir = round($nouvel_elo_noir);

        // Connexion a la base de données
        try
        {
            $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        }
        Catch (PDOException $e)
        {
            die("Erreur ! : ".$e->getMessage());
        }
    
        $sql = "update login set elo = $elo_retourne_blanc where uid=$uidb";
        $resultat = $dbh->query($sql);
    
        $sql = "update login set elo = $elo_retourne_noir where uid=$uidn";
        $resultat = $dbh->query($sql);
   
    }

    private function calcul_coefficient($uid)
    {
        // Connexion a la base de données
        try
        {
            $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        }
        Catch (PDOException $e)
        {
            die("Erreur ! : ".$e->getMessage());
        }
        $retour = array();    

        $requete = "select elo, coefficient from login where uid=$uid";
        $resultat = $dbh->query($requete);
        $ligne = $resultat->fetch();
    
        $elo = $ligne['elo'];
        $coffiecient_enr = $ligne['coefficient'];
    
        $requete = "select count(*) as nombre from parties where uidb=$uid or uidn=$uid";
        $resultat = $dbh->query($requete);
        $ligne = $resultat->fetch();
    
        $nombre_parties = $ligne['nombre'];
    
        if ($nombre_parties < 101)
            $veritable_coefficient = 32;
        else if ($nombre_parties < 300)
            $veritable_coefficient = 24;
        else
            if ($elo < 2000)
                $veritable_coefficient = 16;
            else if (($elo > 1999) && ($elo < 2201))
                $veritable_coefficient = 12;
            else if ($elo > 2200)
                $veritable_coefficient = 10;
       
        if ($veritable_coefficient != $coffiecient_enr)
        {
            $sql = "update login set coefficient = $veritable_coefficient where uid=$uid";
            $resultat = $dbh->query($sql);
        }
            
        $retour['coefficient'] = $veritable_coefficient;
        $retour['elo'] = $elo;

        return $retour;
    }
}
?>