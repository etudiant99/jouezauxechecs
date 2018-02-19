<?php
include_once "./include/config.php";
include_once "./classes/Statistique.class.php";
//include_once "./include/Utilite.class.php";
include_once "./classes/StatistiqueManager.class.php";
include_once "./classes/Couleur.class.php";
include_once "./classes/Piece.class.php";
include_once "./classes/JoueurManager.class.php";
//include_once "./classes/Utilite.class.php";
include_once "./classes/Joueur.class.php";
$uid = $_GET['uid'];

$db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
$managerStatistiques = new StatistiqueManager($db);
$statistique = $managerStatistiques->get($uid);

$managerjoueurs = new JoueurManager($db);
$individu = $managerjoueurs->trouveJoueur($uid);

$pseudo = $individu->pseudo();
$elo = $individu->elo();
$age = $individu->age();
$description = $individu->description();
$photo = $individu->photo();
$monpays = $individu->pays();
$partiestotales = $statistique->partiestotales();

if (isset($monpays) and ($monpays != 'zz.png')) 
{
    $pays = './images/pays/'.$individu->pays();
    $imagepays = '<img border="2" width="40" src="'.$pays.'">';
}
else
    $imagepays = '';     
?>

        <body bgcolor="salmon">
        <table width="416" height="240" cellpadding="0" cellspacing="0">
            <tr>
                <td width="228" height="39" colspan="2"><font size="6px"><b><?php echo $pseudo ?></b></td>
                <td width="188" height="39" colspan="2" valign="middle" align="center"><span></span></font></td>
                
                <td width="188" height="29" colspan="2" valign="middle" align="center" style="text-align: center;"><?php echo $imagepays ?></td>
                
            </tr>
            <tr>
                <td width="113" height="29">Elo</td>
                <td width="103" height="29"><?php echo $elo ?></td>
                <td width="163" height="150" rowspan="4" align="center" valign="top"><img border="0" src="<?php echo $photo ?>" /></td>
                <td width="25" height="29" rowspan="4"></td>
                <td width="45" height="32" rowspan="4"></td>
            </tr>
            <tr>
                <td width="113" height="29">Age</td>
                <td width="103" height="29"><?php echo $age ?></td>
            </tr>
            <tr>
                <td width="113" height="29">Parties</td>
                <td width="103" height="29"><?php echo $statistique->partiestotales() ?></td></td>
            </tr>
            <tr>
                <td width="113" height="29">Gains</td>
                <td width="103" height="29"><?php echo $statistique->gainstotaux() ?></td></td>
            </tr>
            <tr>
                <td width="248" height="70" colspan="2"></td>
            </tr>
            <tr>
                <td width="416" height="80" colspan="5" class="gauche" valign="top"><?php echo nl2br($description) ?></td>
                <td></td>
            </tr>
            <tr>
                <td width="416" height="30" colspan="4"></td>
            </tr>
        </table>
