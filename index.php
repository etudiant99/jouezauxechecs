<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />  <!-- sert entre autre à avoir les accents -->
   	<meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="stylesheet" href="./css/lemien.css" type="text/css" />
    
    <script type="text/javascript" src="jquery-1.3.2.js"></script>
    <script>
        $(document).ready((function(){ $("#lenom").focus();}))
    </script>

	<title>Jouer aux échecs</title>
</head>

<body>

<?php
session_start();

    // on cherche un user-agent apparenté à une plateforme mobile dans la variable
    $iphone = strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone');
    $ipad = strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
    $android = strpos($_SERVER['HTTP_USER_AGENT'], 'Android');
    $blackberry = strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry');

    // puis on détermine si une chaîne de caractères a été trouvée
    if($iphone || $ipad || $android || $blackberry > -1){
        header('Location: http://etudiant99.com/lesechecs/'); // si c'est le cas, on redirige
        die; // puis on arrête le chargement de la page actuelle
    }

require_once "./echecs/include/config.php";
include_once "./echecs/classes/ConnectionManager.class.php";
include_once "./echecs/classes/Connection.class.php";
include_once "./echecs/classes/Courriel.class.php";

$managerconnections = new ConnectionManager();

$pseudo_entre = "&nbsp;";
$mot_de_passe = "&nbsp;";

if (isset($_POST['envoi']))
{
    $pseudo_entre = "&nbsp;";
    $mot_de_passe = "&nbsp;";
    
    /* Récupération des données du formulaire */
    $pseudo = isset($_POST['pseudonyme']) ? trim($_POST['pseudonyme']) : "";
    $mot_de_passe = isset($_POST['mot_de_passe']) ? trim($_POST['mot_de_passe']) : "";
    $pseudo = trim($_POST['pseudonyme']);
    $mot_de_passe =  $_POST['mot_de_passe'];
    $_SESSION['password'] = md5($mot_de_passe);
    if ($pseudo == "")
        $pseudo_entre = "champ obligatoire";
    else
    {
        $_SESSION['pseudo'] = $pseudo;
        $lasortie = $managerconnections->exists($pseudo);

        if ($lasortie == '0')
            $pseudo_entre = "pseudonyme inexistant";
        else
        {
            $crypte = md5($mot_de_passe);
            
            if ($lasortie != $_SESSION['password'])
                $mot_de_passe = "mot de passe invalide";
            else
            {
                $_SESSION['pseudo'] = $pseudo;
                header('Location: login.php');
            }
        }
    }
}
?>
    <header>
        <div class="petitecran">
            <img src="monlogo.gif" />
            <form id="formulairecontact" method="post" action="<?php echo ($_SERVER['PHP_SELF']); ?>">
                <label>Pseudonyme:</label><input required="true" id="lenom" type="text" name="pseudonyme" />
                <label>mot de passe:</label><input required="true" id="lepassword" type="password" name="mot_de_passe" />
                <input type="submit" name="envoi" value="Envoyer" />
            </form>
        </div>
    </header>

    <subheader>
        <div class="petitecran">
            <div id="infos">
                Jouer aux échecs, est un site de base pour jouer aux echecs:
            </div>
            <div id="details">
                Il s'améliorera avec le temps, laissez la chance au coureur
            </div>
            <div id="inscription">
                Pour vous <a href="./enregistrer.php">inscrire</a>
            </div>
        </div>
    </subheader>

</body>
</html>