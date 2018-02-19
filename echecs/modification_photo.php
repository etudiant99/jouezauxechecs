<?php

/**
 * @author Denis Boucher
 * @copyright 2012
 */

session_start();
include_once ("./include/config.php");

$uidActif = $_SESSION['uid'];
$resultat = false;

//header('Content-type: image/jpeg');

$taille_maximum = $_POST['MAX_FILE_SIZE'];
$taille_image = $_FILES['nom_du_fichier']['size'];

//    echo 'taille image: '.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$taille_image.'<br />';
//    echo 'taille maximum: '.$taille_maximum;
//exit();

$types = '{jpg,jpeg}';

$nom_fichier = $_FILES['nom_du_fichier']['name'];
$nom_temporaire = $_FILES['nom_du_fichier']['tmp_name'];
$nom_destination = null; 
$nom_destination = './images/joueurs/'.'h'.$uidActif.'.jpg';

$type_fichier = $_FILES['nom_du_fichier']['type'];

if (($taille_image < $taille_maximum) && ($type_fichier == "image/jpeg"))
{
    $resultat = move_uploaded_file($nom_temporaire,$nom_destination);
    $filename = $nom_destination;
    
    // Définition de la largeur et de la hauteur maximale
    $width = 200;
    $height = 200;

    // Content type
    //header('Content-Type: image/jpeg');

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
    //imagedestroy($nom_destination);
    
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
?>

<?php
if ($taille_image > $taille_maximum || $taille_image == 0)
{
    ?>
        <script type="text/javascript">
        alert('image trop grande');
        window.location.assign("index.php?action=profil");
        </script>
    <?php
}
else
    header('Location: index.php?action=les joueurs');

?>