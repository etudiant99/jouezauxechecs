<?php

/**
 * @author Denis Boucher
 * @copyright 2011
 */
 
//include_once "config.php";


function infos_usager($numero)
{
    $retour = array();
    
    // Connexion a la base de données
    try
    {
        $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    }
    Catch (PDOException $e)
    {
        die("Erreur ! : ".$e->getMessage());
    }
    
    $requete = "select pseudo, elo from login where uid=$numero";
    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    
    $pseudo = utf8_encode($ligne['pseudo']);
    $elo = $ligne['elo'];


    $requete = "select sexe, pays, naissance, description, photo from users where uid=$numero";
    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    
    $sexe = $ligne['sexe'];
    $pays = $ligne['pays'];
    $naissance = $ligne['naissance'];
    $description = $ligne['description'];
    $photo = null;
    $photo = $ligne['photo'];
    
    $age_personne = Age($naissance);
    
    if ($sexe == "h")
        $le_sexe = "homme";
    else
        $le_sexe = "femme";
    
    
    $jour = substr($naissance,8,2);
    $mois = substr($naissance,5,2);
    $annee = substr($naissance,0,4);
    
    $la_photo = null;
    if ($photo == 'o')
        $la_photo = 'h'.$numero.'.jpg';
    else
        $la_photo = 'h0.jpg';

    $retour['pseudo'] = $pseudo;
    $retour['elo'] = $elo;
    $retour['sexe'] = $le_sexe;
    $retour['pays'] = $pays;
    $retour['jour'] = $jour;
    $retour['mois'] = $mois;
    $retour['annee'] = $annee;
    $retour['age'] = $age_personne;
    $retour['description'] = $description;
    $retour['photo'] = $photo;
    $retour['la_photo'] = $la_photo;
    


    // Fermeture de la connection
    $dbh = NULL;

       
    return $retour;

}


function variables_rejouer($partie, $uidActif)
{
    
    $retour = array();
    
    // Connexion a la base de données
    try
    {
        $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    }
    Catch (PDOException $e)
    {
        die("Erreur ! : ".$e->getMessage());
    }

    $requete = "select coups from coups where cip=$partie";
    $resultat = $dbh->query($requete);

    $les_coups = " ";

    while($ligne = $resultat->fetch())
    {
        
        $coup = $ligne['coups'];
    
        if (strlen($coup > 6))
            $les_coups .= $coup;
        else
            $les_coups .= $coup." ";
        $coup = "";
    }
    
    $ign = $les_coups;

    $requete = "select 
       blancs.uid as uid_blancs,
       noirs.uid as uid_noirs,
       blancs.pseudo as pseudoblancs, 
       noirs.pseudo as pseudonoirs, 
       ublancs.sexe as sexeblancs,
       unoirs.sexe as sexenoirs,
       blancs.connecte as blancsconnectes,
       noirs.connecte as noirsconnectes,
       p.uidb,
       p.uidn,
       p.cadencep,
       p.reservep,
       p.reserve_uidb,
       p.reserve_uidn,
       p.date_debut,
       p.date_fin,
       p.finalisation,
       p.date_dernier_coup,
       curdate() - date_dernier_coup as maintenant
       from parties p, login blancs, login noirs, users ublancs, users unoirs  
       where p.uidb=blancs.uid and p.uidn=noirs.uid and blancs.uid=ublancs.uid and noirs.uid=unoirs.uid and p.gid=$partie";


    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    
    $uid_blancs = $ligne['uid_blancs'];
    $uid_noirs = $ligne['uid_noirs'];
    $pseudo_blancs = $ligne['pseudoblancs'];
    $pseudo_noirs = $ligne['pseudonoirs'];
    $sexe_blancs = $ligne['sexeblancs'];
    $sexe_noirs = $ligne['sexenoirs'];
    $blancs_connectes = $ligne['blancsconnectes'];
    $noirs_connectes = $ligne['noirsconnectes'];
    $uidb = $ligne['uidb'];
    $uidn = $ligne['uidn'];
    //$trait = $ligne['trait'];
    $cadence = $ligne['cadencep'];
    $reserve = $ligne['reservep'];
    $reserve_uidb = $ligne['reserve_uidb'];
    $reserve_uidn = $ligne['reserve_uidn'];
    $date_debut = $ligne['date_debut'];
    $date_fin = $ligne['date_fin'];
    $dernier_coup = $ligne['date_dernier_coup'];
    $finalisation = $ligne['finalisation'];
    $cadence = $ligne['cadencep'];

    //$duree_b = $cadence;
    //$duree_n = $cadence;
    $temps_maximum_en_secondes = strtotime($dernier_coup)+(86400*$cadence);
    $nombre_secondes_maintenant = time();
    $changement_b = 0;
    $changement_n = 0;
    
    if ($temps_maximum_en_secondes > $nombre_secondes_maintenant)
    {
        $reste = $temps_maximum_en_secondes - $nombre_secondes_maintenant;
                
        $nombre_jours_restant = floor($reste/3600/24);
        $reste = $reste % (3600*24);
        $nombre_heures_restant = floor($reste/3600);
        $reste = $reste % 3600;
        $nombre_minutes_restant = floor($reste/60);
        $reste = $reste % 60;
        $nombre_secondes_restant = floor($reste);
    }
    else
    {
        $nombre_jours_restant = 0;
        $nombre_heures_restant = 0;
        $nombre_minutes_restant = 0;
        $nombre_secondes_restant = 0;
                    
        $reste = $temps_maximum_en_secondes - $nombre_secondes_maintenant;
        $rnombre_jours_restant = $reste/3600/24;
    
    /*
        if ($trait == $uid_blancs)
        {
            $changement_b = $reserve_uidb + $rnombre_jours_restant;   
            $duree_b = $nombre_jours_restant."j ".$nombre_heures_restant."h ".$nombre_minutes_restant."min ".$nombre_secondes_restant."s";            
        }
        else
        {
            */
            $changement_n = $reserve_uidn + $rnombre_jours_restant;
            $duree_n = $nombre_jours_restant."j ".$nombre_heures_restant."h ".$nombre_minutes_restant."min ".$nombre_secondes_restant."s";           

          //  $sql = "update parties set reserve_uidn = $changement where gid=$numero_partie";
      //  }
        
        
        //$resultat = $dbh->query($sql);
    }
    
    $duree = $nombre_jours_restant."j ".$nombre_heures_restant."h ".$nombre_minutes_restant."min ".$nombre_secondes_restant."s";

    $la_date_debut = substr($date_debut,8,2)."/".substr($date_debut,5,2)."/".substr($date_debut,0,4);
    $la_date_fin = substr($date_fin,8,2)."/".substr($date_fin,5,2)."/".substr($date_fin,0,4);

    if ($sexe_blancs == "h")
        $vrai_sexe_blancs = "homme";
    else
        $vrai_sexe_blancs = "femme";

    if ($sexe_noirs == "h")
        $vrai_sexe_noirs = "homme";
    else
        $vrai_sexe_noirs = "femme";
        
    if ($blancs_connectes == true)
        $image_connection_blancs = '<img src="./images/icons/active.gif" border="0">';
    else
        $image_connection_blancs = '';

    if ($noirs_connectes == true)
        $image_connection_noirs = '<img src="./images/icons/active.gif" border="0">';
    else
        $image_connection_noirs = '';
    
    /*    
    if ($trait == $uidb)
        $le_trait = "blancs";
    else
    */
        $le_trait = "noirs";
    
    /*
    if ($trait == $uidActif)
        $mon_tour = '1';
    else
    */
        $mon_tour = '0';
        
    if ($uidActif == $uidb)
        $flip = '0';
    else
        $flip = '1';
        
    if ($dernier_coup != '0000-00-00')
        $date_joue = substr($dernier_coup,8,2)."/".substr($dernier_coup,5,2)."/".substr($dernier_coup,0,4);
    else
        $date_joue = '';
        
    switch ($finalisation) 
    {
        case 1:
            $la_fin = "Nulle sur proposition des blancs";
            break;
        case 2;
            $la_fin = "Nulle sur proposition des noirs";
            break;
        case 3;
            $la_fin = "Les blancs abandonnent";
            break;
        case 4;
            $la_fin = "Les noirs abandonnent";
            break;
        case 5:
            $la_fin = "Les blancs gagnent au temps";
            break;
        case 6;
            $la_fin = "Les noirs gagnent au temps";
            break;
        case 7:
            $la_fin = "Les blancs gagnent(mat)";
            break;
        case 8:
            $la_fin = "Les noirs gagnent(mat)";
            break;
        default:
            $la_fin = "Fin de partie anormale";
    }
    
    // ici
    
    if ($uidActif == $uid_blancs)
        $adversaire = $uid_noirs;
    else
        $adversaire = $uid_blancs;

    $retour['ign'] = $ign;
    $retour['uid_blancs'] = $uid_blancs;
    $retour['uid_noirs'] = $uid_noirs;
    $retour['pseudoblancs'] = $pseudo_blancs;
    $retour['pseudonoirs'] = $pseudo_noirs;
    $retour['sexeblancs'] = $vrai_sexe_blancs;
    $retour['pseudo_blanc_stylise'] = '<font class="'.$vrai_sexe_blancs.'">'.$pseudo_blancs.'</font>';
    $retour['sexenoirs'] = $vrai_sexe_noirs;
    $retour['pseudo_noir_stylise'] = '<font class="'.$vrai_sexe_noirs.'">'.$pseudo_noirs.'</font>';
    $retour['blancsconnectes'] = $image_connection_blancs;
    $retour['noirsconnectes'] = $image_connection_noirs;
    //$retour['trait'] = $le_trait;
    $retour['cadence'] = $cadence;
    $retour['reserve'] = $reserve;
    $retour['flip'] = $flip;
    $retour['montour'] = $mon_tour;
    $retour['derniercoup'] = $date_joue;
    $retour['date_debut'] = $la_date_debut;
    $retour['date_fin'] = $la_date_fin;
    $retour['finalisation'] = $la_fin;
    $retour['duree'] = $duree;
    $retour['changement_b'] = $changement_b;
    $retour['changement_n'] = $changement_n;
    $retour['reserve_uidb'] = $reserve_uidb;
    $retour['reserve_uidn'] = $reserve_uidn;
    $retour['image_blancs'] = $image_connection_blancs;
    $retour['image_noirs'] = $image_connection_noirs;
    $retour['adversaire'] = $adversaire;

    // Fermeture de la connection
    $dbh = NULL;

       
    return $retour;
}


    function Genere_Password($size)
    {
        $password = '';        
        
        // Initialisation des caractères utilisables
        $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        for($i=0;$i<$size;$i++)
        {
            $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
        }
        return $password;
    }

    function EnvoiCourriel($adresse_email,$nom_utilisateur,$pascrypte)
    {
	       // Preparation du mail

        $nom = "Adminstrateur du site";
        $mail = "denis@etudiant99.com";

 
        /////voici la version Mine 
        $headers = "MIME-Version: 1.0\r\n"; 

        //////ici on détermine le mail en format text 
        $headers .= "Content-type: text/html; charset=UTF-8\r\n"; 
        
        $headers .= "From: $nom <$mail>\r\nX-Mailer:PHP";
        // dans le From tu mets l'expéditeur du mail 

        $sujet="Validation inscription"; 
        $subject="$sujet"; 
        
        $destinataire=  $nom_utilisateur.' <'.$adresse_email.'>';

        $adresse = $_SERVER['PHP_SELF'];
        $adresseServeur = $_SERVER['SERVER_ADDR'];

        $corps = "<p>Bonjour, ".$nom_utilisateur."</p>";
        $corps .= "<p>Votre inscription au site <b><u>jouezauxechecs</b></u> a été réalisée.<br />";
        $corps.= "Un mot de passe temporaire vous est envoyé maintenant.<br />";
        $corps.= "Il permettra de vous connecter au site, et de jouer des parties d'échecs.</p>";
        
        $corps.= "<p>Je veux vous préciser que ce mot de passe est connu de vous seul.<br />";
        $corps.= "Il a été créé automatiquement. Alors, même l'administrateur du site<br />";
        $corps.= "ne connaît pas ce mot de passe qui vous a été envoyé.</p>";
        
        $corps.= "<p>De plus, ce mot de passe temporaire a été ajouté, automatiquement, à une <br />";
        $corps.= "base de données, mais de facon cryptée, donc illisible par l'administrateur aussi.</p>";
        
        $corps.= "<p>Alors, pour vous connecter au site,</p>";
        
        $corps.="<p>&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Pseudo:</b></u>  ".$nom_utilisateur."</p>";
        $corps.="<p>&nbsp;&nbsp;&nbsp;&nbsp;<b><u>Mot de passe:</u></b>  ".$pascrypte."</p>";
        
        //$corps.='<p><a href="http://etudiant99.com/jouezauxechecs/">Jouer aux echecs</a></p>';
        $corps.='<p><a href="http://localhost/jouezauxechecs/">Jouer aux echecs</a></p>'; // en local
        $corps.="<p>Vous pouvez modifier votre mot de passe par le menu <b><u>profil</b></u>.<br />";
        $corps.="Vous serez déconnecté et devrez vous reconnecter avec le nouveau mot de passe "."</p>";
        $corps.="<p>::::::::::::::: MAIL AUTOMATIQUE - NE PAS Y RÉPONDRE :::::::::::::::</p>";

        mail($destinataire,$subject,$corps,$headers);       
        
        Return;
    }

    function MotDePassePerdu()
    {
        echo "<h2>Mot de passe perdu</h2>";
        echo "<br />";
        echo "<p><b />Vérifiez vos courriels, vous recevrez un nouveau mot de passe.</p></b>";
        echo "<br />";
        // "formulaire_connexion" est l'ID unique du formulaire
        $form_password_perdu = new Form('formulaire_mot_perdu');

        $form_password_perdu->method('POST');

        $form_password_perdu->add('Text', 'pseudo')
                    ->label("Votre pseudonyme")
                    ->required(true);

        $form_password_perdu->add('Email', 'courriel')
                    ->label("Adresse courriel fournie")
                    ->required(true);

        $form_password_perdu->add('Submit', 'submit')
                    ->value("Envoyer");

        // Pré-remplissage avec les valeurs précédemment entrées (s'il y en a)
        $form_password_perdu->bound($_POST);

        // Création d'un tableau des erreurs
        $erreurs_inscription = array();
        
        if ($form_password_perdu->is_valid($_POST))
        {
            list($pseudo, $adresse_courriel) =
            $form_password_perdu->get_cleaned_data('pseudo', 'courriel');
            
            if (!$coordonnees = $this->LocalisePseudo($pseudo))
            {
                echo "<ul><li>une erreur s'est glissée. Ce pseudonyme n'existe pas</ul></li>";
                
            }
            else
            {
                if ($coordonnees[1] != $adresse_courriel)
                {
                    echo "<ul><li>Prenez soin d'indiquer le même courriel déja fourni</ul></li>";
                }
                else
                {
                    $pascrypte = $this->Genere_Password(5);
                    $crypte = md5($pascrypte); 
                    $this->EnvoiCourriel($adresse_courriel,$pseudo,$pascrypte);
            
                    $reponse = $this->LocalisePseudo($pseudo);
                    $item = $reponse[2];
            
                    $this->MiseAJourPassword($item,$crypte);
                    header("Location: index.php?module=usagers&action=deconnexion");
                }
            }                        
        }
        
        echo $form_password_perdu;
        
    }

    function MiseAJourPassword($id_utilisateur,$password)
    {
        $pdo = PDO2::getInstance();

        $requete = $pdo->prepare("UPDATE membres SET
                dernier_acces = NOW(),
                mot_de_passe = '$password'
                WHERE
                id = :id_utilisateur");
  
        $requete->bindValue(':id_utilisateur', $id_utilisateur);
  
        return $requete->execute();        
    }



function le_delais($numero)
{
        try
    {
        $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    }
    Catch (PDOException $e)
    {
        die("Erreur ! : ".$e->getMessage());
    }

    $requete = "select cadencep, curdate() - date_dernier_coup as maintenant from parties where gid=".$numero;
    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    
    $le_jour = $ligne['maintenant'];

    $cadence = $ligne['cadencep'];
    
    $temp_restant = $cadence - $le_jour;

    $la_reponse = $temp_restant." jours";
        

    
    return $la_reponse;
}


function statistiques_personnelles($uid_joueur)
{
    
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

    
    $retour = array();
    
    // Connexion a la base de données
    try
    {
        $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    }
    Catch (PDOException $e)
    {
        die("Erreur ! : ".$e->getMessage());
    }
    
    $requete_en_cours = "select count(*) as nombre_b from parties where uidb=$uid_joueur and date_fin = '0000-00-00'";
    $resultat = $dbh->query($requete_en_cours);
    $ligne = $resultat->fetch();
    $nombreb = $ligne['nombre_b'];

    $requete_en_cours = "select count(*) as nombre_n from parties where uidn=$uid_joueur and date_fin = '0000-00-00'";
    $resultat = $dbh->query($requete_en_cours);
    $ligne = $resultat->fetch();
    $nombren = $ligne['nombre_n'];    
    
    $requete_insc = "select date_inscription from users where uid=$uid_joueur";
    $resultat = $dbh->query($requete_insc);
    $ligne = $resultat->fetch();
    
    $date_inscription = $ligne['date_inscription'];
    $mois = substr($date_inscription,5,2);
    $inscriptin = substr($date_inscription,8,2)." ".$le_mois[$mois]." ".substr($date_inscription,0,4);


    $requete = "select gains_b, pertes_b, nulles_b, gains_n, pertes_n, nulles_n from statistiques where uid=$uid_joueur";
    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    
    $gains_b = $ligne['gains_b'];
    $pertes_b = $ligne['pertes_b'];
    $nulles_b = $ligne['nulles_b'];
    $gains_n = $ligne['gains_n'];
    $pertes_n = $ligne['pertes_n'];
    $nulles_n = $ligne['nulles_n'];

    
     $retour['date_inscription'] = $inscriptin;
     $retour['nombreb'] = $nombreb;
     $retour['nombren'] = $nombren;
     $retour['gains_b'] = $gains_b;
     $retour['pertes_b'] = $pertes_b;
     $retour['nulles_b'] = $nulles_b;
     $retour['gains_n'] = $gains_n;
     $retour['pertes_n'] = $pertes_n;
     $retour['nulles_n'] = $nulles_n;
     
     
    // Fermeture de la connection
    $dbh = NULL;

       
    return $retour;

    
}


function statistiques_generales()
{

    $retour = array();
    
    // Connexion a la base de données
    try
    {
        $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    }
    Catch (PDOException $e)
    {
        die("Erreur ! : ".$e->getMessage());
    }
    
    $requete = "select count(*) as nombre_inscrit from login";
    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    $nombre_inscrit = $ligne['nombre_inscrit'];
    
    
    $requete = "select count(*) as nombre_en_ligne from login where connecte=1";
    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    $nombre_en_lligne = $ligne['nombre_en_ligne'];
    
    
    $requete = "select count(*) as nombre_parties from parties where date_fin = '0000-00-00'";
    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    $nombre_parties = $ligne['nombre_parties'];
    
    $requete = "select date_connection from users";
    $resultat = $dbh->query($requete);
    
    $connection = array();
    
    while ($ligne = $resultat->fetch())
    {
        $connection[] = $ligne['date_connection'];    
    }
    
    $compteur = 0;
    foreach ($connection as $cle=>$valeur) 
    {
        $reponse = duree_precise($valeur);
        $jours = $reponse['jours'];
        if ($jours < 1)
            $compteur++;
    }
    
    $retour['nombre_inscrit'] = $nombre_inscrit;
    $retour['nombre_en_ligne'] = $nombre_en_lligne;
    $retour['nombre_parties'] = $nombre_parties;
    $retour['connecte_dans_journee'] = $compteur;

    // Fermeture de la connection
    $dbh = NULL;

       
    return $retour;


}


function Age($date_naissance)
{

    $arr1 = explode('-', $date_naissance);
    $arr2 = explode('/', date('Y/m/d'));
    
    if ($arr1[0] == '0000')
        return "";

    if(($arr1[1] < $arr2[1]) || (($arr1[1] == $arr2[1]) && ($arr1[2] <= $arr2[2])))
        return intval($arr2[0]) - intval($arr1[0])." ans";

    return (intval($arr2[0]) - intval($arr1[0]) - 1)." ans";
}
 

 
/* PETIT PLUS  */
// J'ai rajouter un ptit if si nous sommes bien le jour & le moi de la date de naissence
// on lui souhaite un bonne anniversaire
// SI TU UTILISE LE FORMAT 08-05-1988 ou 08/05/1988 Il TE SUFFIE DE REMPLACER LES / PAR -
/* 
if($date_d_naissence.'/'.$date_m_naissence == date("d").'/'.date("m"))
{
    $date_anniversaire = 'Joyeux anniversaire ';
}
 
echo $date_anniversaire.'vous avez '.$age.' ans'; 
*/

function duree_precise($time)
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

function calcul_coefficient($uid)
{
    $retour = array();

    // Connexion a la base de données
    try
    {
        $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    }
    Catch (PDOException $e)
    {
        die("Erreur ! : ".$e->getMessage());
    }
    

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
        
    // Fermeture de la connection
    $dbh = NULL;
    
    $retour['coefficient'] = $veritable_coefficient;
    $retour['elo'] = $elo;

    return $retour;
    
}


function calcule_elo($uidb, $uidn, $resultat)
{
    $infos_uidb = calcul_coefficient($uidb);
    $infos_uidn = calcul_coefficient($uidn);
    
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


function pieces_mangees($numero)
{
       
    $retour = array();
    $pieces_blanches = array();
    $pieces_noires = array();
    
    // Connexion a la base de données
    try
    {
        $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    }
    Catch (PDOException $e)
    {
        die("Erreur ! : ".$e->getMessage());
    }
    
    /*
    $requete = "select blancsmanges, noirsmanges from parties where gid=$numero";
    $resultat = $dbh->query($requete);
    $ligne = $resultat->fetch();
    */

    
    $blancs_manges = '';
    $noirs_manges = '';
    
    /*
    for ($i=0;$i<strlen($blancs_manges);$i++)
    {
        $piece = 'w';
        $piece .= substr($blancs_manges,$i,1);
        $piece .= '_21.gif';
        $pieces_blanches[] = $piece;
    }

    for ($i=0;$i<strlen($noirs_manges);$i++)
    {
        $piece = 'b';
        $piece .= substr($noirs_manges,$i,1);
        $piece .= '_21.gif';
        $pieces_noires[] = $piece;
    }
    */

    $retour['blanc'] = $pieces_blanches;
    $retour['noir'] = $pieces_noires;

    // Fermeture de la connection
    $dbh = NULL;

       
    return $retour;
   
}

function dateDiff($interval,$dateTimeBegin,$dateTimeEnd)
{
  $dateTimeBegin=strtotime($dateTimeBegin);

  if($dateTimeBegin === -1)
  {
    return("..begin date Invalid" );
  }

  $dateTimeEnd=strtotime($dateTimeEnd);

  if($dateTimeEnd === -1)
  {
    return("..end date Invalid" );
  }

  $dif = $dateTimeEnd - $dateTimeBegin;

  switch($interval)
  {
    case "s"://seconds
        return($dif);
    case "n"://minutes
        return(floor($dif/60)); //60s=1m
    case "h"://hours
        return(floor($dif/3600)); //3600s=1h
    case "d"://days
        return(floor($dif/86400)); //86400s=1d
    case "ww"://Week
        return(floor($dif/604800)); //604800s=1week=1semana
    case "m": //similar result "m" dateDiff Microsoft
        $monthBegin=(date("Y",$dateTimeBegin)*12)+date("n",$dateTimeBegin);
        $monthEnd=(date("Y",$dateTimeEnd)*12)+date("n",$dateTimeEnd);
        $monthDiff=$monthEnd-$monthBegin;
        return($monthDiff);
    case "yyyy": //similar result "yyyy" dateDiff Microsoft
        return(date("Y",$dateTimeEnd) - date("Y",$dateTimeBegin));
    default:
        return(floor($dif/86400)); //86400s=1d
  }
} 


function validation_infos_entrees()
{
    
    $tablo = array(array());
    
    // Verification si champs vides
    if (empty($_SESSION['pseudo']))
    {
	   $tablo['vide'][] = "pseudo";	
    }

    
    return $tablo;    

}

function formate_date($date)
{
    $date_formatee = substr($date,8,2)."/".substr($date,5,2)."/".substr($date,0,4);
    
    if ($date == '')
        return '';
    else
        return $date_formatee;
}


function messages_non_lus($individu)
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

    $requete = "select count(*) as nombre from messages where destinataire = $individu and lu=0";
    $resultat1 = $dbh->query($requete);
    $ligne = $resultat1->fetch();
    $nombre = $ligne['nombre'];
    
    $dbh = null;
    
    switch ($nombre)
    {
        case 0:
            $invite = "";
            break;
        case 1:
            $invite = "<b>".$nombre." nouveau message</b>";
            break;
        default:
            $invite = "<b>".$nombre." nouveaux messages</b>";
    } 


    return $invite;
    
}

function nombre_au_hazard()
{
    //création d'un nombre aléatoire entre 1 et 100

    $nb_min = 1;
    $nb_max = 100;
    $nombre = mt_rand($nb_min,$nb_max);

    return $nombre;
}
        
function date_lisible($date)
{
    $date_lisible = $date;
    $jour = substr($date,0,2);
    $mois = substr($date,3,2);
    
    switch ($jour) {
        case 1:
            $jour = "1";
            break;
        case 2:
            $jour = "2";
            break;
        case 3:
            $jour = "3";
            break;
        case 4:
            $jour = "4";
            break;
        case 5:
            $jour = "5";
            break;
        case 6:
            $jour = "6";
            break;
        case 7:
            $jour = "7";
            break;
        case 8:
            $jour = "8";
            break;
        case 9:
            $jour = "9";
            break;        
    }
    
    switch ($mois) {
    case 1:
        $date_lisible = "janvier";
        break;
    case 2:
        $date_lisible = "février";
        break;
    case 3:
        $date_lisible = "mars";
        break;
    case 4:
        $date_lisible = "avril";
        break;
    case 5:
        $date_lisible = "mai";
        break;
    case 6:
        $date_lisible = "juin";
        break;
    case 7:
        $date_lisible = "juillet";
        break;
    case 8:
        $date_lisible = "août";
        break;
    case 9:
        $date_lisible = "septembre";
        break;
    case 10:
        $date_lisible = "octobre";
        break;
    case 11:
        $date_lisible = "novembre";
        break;
    case 12:
        $date_lisible = "décembre";
        break;
    }
    
    return $jour." ".$date_lisible;

}

?>