<?php
class JoueurManager extends Piece
{
    public static $instance;
    

    public function __construct()
    {
        self::$instance=$this;
    }
    
  public function trouveJoueur($id)
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $id = (int) $id;
    $q = $db->query('SELECT * FROM login l, users u WHERE l.uid = '.$id.' and u.uid = '.$id);
    
    if (!$q)
        die("Tables inexistantes");

    $donnees = $q->fetch(PDO::FETCH_ASSOC);

    return new Joueur($donnees);
  }

  public function exists($id)
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = "SELECT * FROM login where pseudo='".$id."'";
    $q = $db->query($requete);
    $donnees = $q->fetch(PDO::FETCH_ASSOC);
    $uid = $donnees['uid'];
    
    if (count($uid) == 1)
        return $uid;
    else
        return 0;
  }
  
  public function countinscrits()
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = "SELECT COUNT(*) FROM login";

    return $db->query($requete)->fetchColumn();
  }

  public function countenligne()
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = "SELECT COUNT(*) FROM login WHERE connecte=1";

    return $db->query($requete)->fetchColumn();
  }


  public function count($personne)
  {
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    $requete = "SELECT COUNT(*) FROM parties where finalisation=0 and (uidb=$personne or uidn=$personne)";

    return $db->query($requete)->fetchColumn();
  }

    public function getList()
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $joueurs = array();
        
        $requete = 'SELECT * FROM login l,users u WHERE l.uid = u.uid order by l.elo desc, l.date_inscription, l.elo';
        
        $q = $db->query($requete);
        if (!$q)
            die("Table inexistante");

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $joueurs[] = new Joueur($donnees);
        }
        
        return $joueurs;
    }
    
    public function countrecemmentconecte()
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $connection = array();
        $jours = 2;
        
        $requete = "SELECT date_connection FROM users WHERE date_connection != ''";
        $q = $db->query($requete);
        if (!$q)
            die("Table users inexistante");

        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $temps = $donnees['date_connection'];
            
            $resultat = $this->duree_precise($temps);
            $nbjours = $resultat['jours'];
            if ($nbjours < $jours)
                $connection[] = $nbjours;
        }

        return  count($connection);
    }
    
    public function motdepasse($uidActif)
    {
        $_SESSION['uid'] = $uidActif;
        ?>
        <script type="text/javascript">
            $(document).ready((function() {
            $("#lenom").focus();
            }))			
        </script>
        <script>
            var _mouseX, _mouseY;
            document.onmousemove=function(event){getMouse(event);}
        </script>
        <div class="profil">
            <div class="icones">
                <img src="./images/icons/lock.png" alt="Mot de passe" border="0" />
                <a href="index.php?action=usager"><img src="./images/icons/user_green.png" alt="Mes informations" border="0" /></a>
                <a href="index2.php" rel="superbox[iframe][600x500]"><img src="./images/icons/photo.png" alt="Ma photo" border="0" /></a>
            </div>
            <div class="gauche">
                <b><u>Mot de passe</b></u>
                <a href="javascript:" onclick="_toggle_help('Mot de passe','Peut être utilisé pour remplacer un mot de passe temporaire, ou simplement pour un besoin de sécurité.<br /><br />Il est à noter que vous devrez confirmer le mot de passe entré en le spécifiant à deux occasions.<br /><br />La validation devra être effectuée en pressant sur le bouton <b>Modifier</b>.'); return(false);"><img src="./images/help.png" border="0" alt="aide" /></a>
                <span id="helpid" style="position: absolute; z-index: 20; display: none;"></span>
                <br /><br />Utilisez le formulaire ci-contre pour modifier votre mot de passe.
                <br /> Après modification, vous serez déconnecté et devrez vous reconnecter avec le nouveau mot de passe.
            </div>
            <form>
                <p class="error"></p>
                <input type="hidden" name="action" value="profil" />
                <p>Nouveau mot de passe:<br />
                <input required="true" id="lenom" type="password" name="pw1" size="15" maxlength="10" style="text-align: left;" value="" />
                </p>
                <p>Retapez votre mot de passe:<br />
                <input required="true" type="password" name="pw2" size="15" maxlength="10" style="text-align: left;" value="" />
                </p>
                <p>
                <input style="text-align: center; font-size: 16px;" type="submit" value="Modifier" />
            </form>
        </div>
        <?php
    }
    
    public function revisioninfos($uidActif)
    {
        $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
        $requete = "select * from users where uid=$uidActif";
        $q = $db->query($requete);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
        
        $sexe = $donnees['sexe'];
        $pays = $donnees['pays'];
        $naissance = $donnees['naissance'];
        $laphoto = $donnees['photo'];
        $jour = substr($naissance,8,2);
        $mois = substr($naissance,5,2);
        $annee = substr($naissance,0,4);
        
        $description = $donnees['description'];
        ?>
        <script type="text/javascript">
            $(document).ready((function() {
                $("#mesinfos").focus();
            }))			
        </script>
        <script>
            var _mouseX, _mouseY;
            document.onmousemove=function(event){getMouse(event);}
        </script>
        <div class="profil">
            <div class="icones">
                <a href="index.php?action=profil"><img src="./images/icons/lock.png" alt="Mot de passe" border="0" /></a>
                <img src="./images/icons/user_green.png" alt="Mes informations" border="0" />
                <a href="index2.php" rel="superbox[iframe][600x500]"><img src="./images/icons/photo.png" alt="Ma photo" border="0" /></a>
            </div>
        
        
        <div class="gauche">
            <br />
            <b><u>Complétez ou corrigez les informations ci-contre</b></u>
            <a href="javascript:" onclick="_toggle_help('Informations ci-contre','Devant vous apparaît les informations affichées aux gens qui choisiront de consulter votre profil.<br /><br />Vous avez le loisir de modifier celles-ci, et valider le changement en pressant sur le bouton <b>Modifier</b>.'); return(false);"><img src="./images/help.png" border="0" alt="aide" /></a>
            <span id="helpid" style="position: absolute; z-index: 20; display: none;"></span>
            <br /><br />
            
            <form> 

            <?php
            $les_sexes = array
            (
                'h'=>'homme',
                'f'=>'femme'
            );
            $les_pays = array
            (
                'zz.png'=>'-',
                'dz.png'=>'Algérie',
                'be.png'=>'Belgique',
                'bu.png'=>'Bulgarie',
                'ca.png'=>'Canada',
                'us.png'=>'Etats-Unis',
                'fr.png'=>'France',
                'lb.png'=>'Liban',
                'lu.png'=>'Luxembourg',
                'ma.png'=>'Maroc',
                'me.png'=>'Mexique',
                'qu.png'=>'Québec',
                'ch.png'=>'Suisse',
                'tn.png'=>'Tunisie'
            );
            $le_jour_du_mois = array
            (
                '00'=>'-',
                '01'=>'1',
                '02'=>'2',
                '03'=>'3',
                '04'=>'4',
                '05'=>'5',
                '06'=>'6',
                '07'=>'7',
                '08'=>'8',
                '09'=>'9',
                '10'=>'10',
                '11'=>'11',
                '12'=>'12',
                '13'=>'13',
                '14'=>'14',
                '15'=>'15',
                '16'=>'16',
                '17'=>'17',
                '18'=>'18',
                '19'=>'19',
                '20'=>'20',
                '21'=>'21',
                '22'=>'22',
                '23'=>'23',
                '24'=>'24',
                '25'=>'25',
                '26'=>'26',
                '27'=>'27',
                '28'=>'28',
                '29'=>'29',
                '30'=>'30',
                '31'=>'31'
            );

            $le_mois = array
            (
                '00'=>'-',
                '01'=>'janvier',
                '02'=>'février',
                '03'=>'mars',
                '04'=>'avril',
                '05'=>'mai',
                '06'=>'juin',
                '07'=>'juillet',
                '08'=>'août',
                '09'=>'septembre',
                '10'=>'octobre',
                '11'=>'novembre',
                '12'=>'décembre',
            );
    
            $les_annees = array
            (
                '0000'=>'-',
                '2013'=>'2013',
                '2012'=>'2012',
                '2011'=>'2011',
                '2010'=>'2010',
                '2009'=>'2009',
                '2008'=>'2008',
                '2007'=>'2007',
                '2006'=>'2006',
                '2005'=>'2005',
                '2004'=>'2004',
                '2003'=>'2003',
                '2002'=>'2002',
                '2001'=>'2001',
                '2000'=>'2000',
                '1999'=>'1999',
                '1998'=>'1998',
                '1997'=>'1997',
                '1996'=>'1996',
                '1995'=>'1995',
                '1994'=>'1994',
                '1993'=>'1993',
                '1992'=>'1992',
                '1991'=>'1991',
                '1990'=>'1990',
                '1989'=>'1989',
                '1988'=>'1988',
                '1987'=>'1987',
                '1986'=>'1986',
                '1985'=>'1985',
                '1984'=>'1984',
                '1983'=>'1983',
                '1982'=>'1982',
                '1981'=>'1981',
                '1980'=>'1980',
                '1979'=>'1979',
                '1978'=>'1978',
                '1977'=>'1977',
                '1976'=>'1976',
                '1975'=>'1975',
                '1974'=>'1974',
                '1973'=>'1973',
                '1972'=>'1972',
                '1971'=>'1971',
                '1970'=>'1970',
                '1969'=>'1969',
                '1968'=>'1968',
                '1967'=>'1967',
                '1966'=>'1966',
                '1965'=>'1965',
                '1964'=>'1964',
                '1963'=>'1963',
                '1962'=>'1962',
                '1961'=>'1961',
                '1960'=>'1960',
                '1959'=>'1959',
                '1958'=>'1958',
                '1957'=>'1957',
                '1956'=>'1956',
                '1955'=>'1955',
                '1954'=>'1954',
                '1953'=>'1953',
                '1952'=>'1952',
                '1951'=>'1951',
                '1950'=>'1950',
                '1949'=>'1949',
                '1948'=>'1948',
                '1947'=>'1947',
                '1946'=>'1946',
                '1945'=>'1945',
                '1944'=>'1944',
                '1943'=>'1943',
                '1942'=>'1942',
                '1941'=>'1941',
                '1940'=>'1940',
                '1939'=>'1939',
                '1938'=>'1938',
                '1937'=>'1937',
                '1936'=>'1936',
                '1935'=>'1935',
                '1934'=>'1934',
                '1933'=>'1933',
                '1932'=>'1932',
                '1931'=>'1931',
                '1930'=>'1930',
                '1929'=>'1929',
                '1928'=>'1928',
                '1927'=>'1927',
                '1926'=>'1926',
                '1925'=>'1925',
                '1924'=>'1924',
                '1923'=>'1923',
                '1922'=>'1922',
                '1921'=>'1921',
                '1920'=>'1920',
                '1919'=>'1919',
                '1918'=>'1918',
                '1917'=>'1917',
                '1916'=>'1916',
                '1915'=>'1915',
                '1914'=>'1914',
                '1913'=>'1913',
            );

            ?>
            <input type="hidden" name="action" value="usager" />
            Vous êtes un(e): <select name="sexe">
            
            <?php

            foreach ($les_sexes as $cle=>$valeur) 
            {
                if ($sexe == $cle)
                {
                    echo '<option value="'.$cle.'" selected>'.$valeur.'</option>';
            
                }
                else
                    echo '<option value="'.$cle.'">'.$valeur.'</option>';
            }
            ?>
        
            </select>
            <br />Votre pays: <select name="pays">
    
            <?php
            foreach ($les_pays as $cle=>$valeur) 
            {
                if ($pays == $cle)
                {
                    echo '<option value="'.$cle.'" selected>'.$valeur.'</option>';
            
                }
                else
                    echo '<option value="'.$cle.'">'.$valeur.'</option>';
            }
            ?>
            </select>
            <br /></b>Votre date de naissance: <select name="jour">
    
            <?php
            foreach ($le_jour_du_mois as $cle=>$valeur) 
            {
                if ($jour == $cle)
                {
                    echo '<option value="'.$cle.'" selected>'.$valeur.'</option>';
            
                }
                else
                    echo '<option value="'.$cle.'">'.$valeur.'</option>';
            }
            ?>
            </select>
            <select name="mois">
    
            <?php

            foreach ($le_mois as $cle=>$valeur) 
            {
                if ($mois == $cle)
                {
                    echo '<option value="'.$cle.'" selected>'.$valeur.'</option>';
            
                }
                else
                    echo '<option value="'.$cle.'">'.$valeur.'</option>';
            }
            ?>
    
            </select>
            <select name="annee">
            <?php
            foreach ($les_annees as $cle=>$valeur) 
            {
                if ($annee == $cle)
                {
                    echo '<option value="'.$cle.'" selected>'.$valeur.'</option>';
            
                }
                else
                    echo '<option value="'.$cle.'">'.$valeur.'</option>';
            }
            ?>
            </select>
            <?php
            if ($laphoto == 'o')
                echo '<br /><input type="checkbox" name="photo" value="o" checked />Photo';
            ?>
            <br /><br />
            Description <br />
            
            <textarea name="description" id="mesinfos" cols="60" rows="8"><?php echo $description ?></textarea></p>
            <p><input style="text-align: center; font-size: 16px;" type="submit" value="Modifier" />
            </form>
        </div>
    
        <?php        
        
        ?>
        
        </div>
        </div>
        <?php
    }
    
    public function revisionphoto($uidActif)
    {
        $joueur = $this->get($uidActif);
        $la_photo = $joueur->photo();
        
        if (isset($_POST['envoi']))
        {
            $resultat = $_FILES['fichier']['name'];
            echo $resultat;
        }

        ?>
        <div class="profil">
            <div class="icones">
                <a href="index.php?action=profil"><img src="./images/icons/lock.png" alt="Mot de passe" border="0" /></a>
                <a href="index.php?action=usager"><img src="./images/icons/user_green.png" alt="Mes informations" border="0" /></a>
            </div>
            <div class="gauche">
                <b><u>Ma photo actuelle:</b></u> <img style="width: 15%;" src="<?php echo $la_photo ?>" alt="" border="0" />
                <br /><br />Ajoutez ou modifiez votre photo
                <br /><br /><u>Attention:</u>
                <br />- seuls les fichiers au format <i>jpeg</i> (type .jpg ou .jpeg) sont acceptés.
                <br />- taille maximum du fichier = 200 ko
                <br />
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="MAX_FILE_SIZE" value="250000" />
                    <input required="true" type="file" name="fichier" maxlength="80" size="60" /><br />
                    <input type="submit" value="modifier" name="envoi" />
                </form>
                <?php
                ?>
            </div>
        </div>
        <?php        
    }
    
    public function changephoto($taille_maximum,$fichier,$uidActif)
    {
        $_FILES['file'] = $fichier;
        $_FILES['size'] = $taille_maximum;

        $types = '{jpg,jpeg}';
        $nom_fichier = $_FILES['file']['name'];

        $nom_temporaire = $_FILES['file']['tmp_name'];
        $nom_destination = null;
        $nom_destination = 'h'.$uidActif.'.jpg';

        $type_fichier = $_FILES['file']['type'];
 
        if (($taille_image < $taille_maximum) && ($type_fichier == "image/jpeg"))
        {
            $resultat = move_uploaded_file($nom_temporaire,$nom_destination);
            $filename = $nom_destination;
    
    // Définition de la largeur et de la hauteur maximale
    $width = 200;
    $height = 200;

    // Content type
    header('Content-Type: image/jpeg');

    // Cacul des nouvelles dimensions
    list($width_orig, $height_orig) = getimagesize($filename);

    $ratio_orig = $width_orig/$height_orig;

    if ($width/$height > $ratio_orig)
        $width = $height*$ratio_orig;
    else
        $height = $width/$ratio_orig;

    // Redimensionnement
    $image_p = imagecreatetruecolor($width, $height);
    $image = imagecreatefromjpeg($filename);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

    imagejpeg($image_p,$nom_destination);

    imagedestroy($image_p);
    imagedestroy($nom_destination);
    
    }

    if ($resultat)
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
        
        $sql = "update users set photo = 'o' where uid='".$uidActif."'";
            
        $resultat = $dbh->query($sql);

        // Fermeture de la connection
        $dbh = NULL;
    }

    header('Location: index.php?module=parties&action=frame_profil.php&but=photo');

    }
    
    private function mysql_bind($sql)
    {
        // Génère : Hll Wrld f PHP
        $vowels = array("'");
        $onlyconsonants = str_replace($vowels, "\'", $sql);

        return $onlyconsonants;
    }


    public function traiterinfos($sexe,$pays,$naissance,$description)
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

        $uidActif = $_SESSION['uid'];
        $ladescription = $this->mysql_bind($description);
        
        $requete = "update users set sexe = '$sexe', pays = '$pays', naissance = '$naissance', description = '$ladescription' where uid=$uidActif";
        
        $dbh->query($requete);
        
        return;
    }

    public function traiterpassword($pw1,$pw2)
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

        $uidActif = $_SESSION['uid'];
        $nouveau_mot_de_passe = str_replace("'", "", $dbh->quote($pw1));
        $confirmation_mot_de_passe = str_replace("'", "", $dbh->quote($pw2));

        if ($nouveau_mot_de_passe != $confirmation_mot_de_passe)
            header('Location: index.php?action=profil');
        else
        {
            $password_crypte = md5($nouveau_mot_de_passe);
            $requete = "update login set bidon = '$password_crypte' where uid='$uidActif'";
            
            $resultat = $dbh->query($requete);
            if ($resultat == true)
            
            // Fermeture de la connection
            $dbh = NULL;
            
            unset($password_crypte);  
            unset($reception_action);
            
            header('Location: ../index.php?action=deconnection');
        }
        
    }  // fin du isset pour le post

    protected function duree_precise($time)
    {
        $retour = array();
    
        // calcul du temps écoulé en secondes
        $diff = time() - strtotime($time);
  
        $diff_jour = floor($diff/60/60/24);
        $diff -= $diff_jour*60*60*24;
  
        $diff_heure = floor($diff/60/60);
        $diff -= $diff_heure*60*60;
  
        $diff_min = floor($diff/60);
        $diff -= $diff_min*60;
  
        $diff_sec = $diff;
  
        $temp_ecoule = $diff_jour;
  
        $retour['jours'] = $diff_jour;
        $retour['heures'] = $diff_heure;
        $retour['minutes'] = $diff_min;
        $retour['secondes'] = $diff_sec;
  
    
        return $retour;
    }

}
?>