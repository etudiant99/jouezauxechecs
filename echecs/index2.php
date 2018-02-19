<?php

session_start();
include_once ("./include/config.php");
$uidActif = $_SESSION['uid'];

$db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
$requete = "select * from users where uid=$uidActif";
$q = $db->query($requete);
$donnees = $q->fetch(PDO::FETCH_ASSOC);

$la_photo = null;  
$photo = $donnees['photo'];

if ($photo == 'n')
    $la_photo = './images/joueurs/h0.jpg';
else
{
    $nombre_aleatoire = nombre_au_hazard();
    $la_photo = './images/joueurs/h'.$uidActif.'.jpg?'.$nombre_aleatoire; // pour recharger la véritable photo;
}
?>

<link rel="stylesheet" href="./css/mien.css" type="text/css" />

<body bgcolor="salmon">
    <div class="gauche">
        <b><u>Ma photo actuelle:</b></u><br /><br />
        <img style="width: 30%;" src="<?php echo $la_photo ?>" />
        <br /><br />Ajoutez ou modifiez votre photo
        <br /><br /><u>Attention:</u>
        <br />- seuls les fichiers au format <i>jpeg</i> (type .jpg ou .jpeg) sont acceptés.
        <br />- taille maximum du fichier = 200 ko
        <br />
        <form method="post" action="modification_photo.php" enctype="multipart/form-data"  target="_top"> 
            <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />     
            <input required="true" type="file" name="nom_du_fichier"  maxlength="80" size="60" />    
            <input type="submit" name="ok" value="Modifier" />
            <script type="text/javascript">
                $.superbox.close();
            </script>
        </form>
</div>
</body>

<?php
function nombre_au_hazard()
{
    //création d'un nombre aléatoire entre 1 et 100

    $nb_min = 1;
    $nb_max = 100;
    $nombre = mt_rand($nb_min,$nb_max);

    return $nombre;
}
?>
