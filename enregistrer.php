<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />  <!-- sert entre autre à avoir les accents -->
    <link rel="stylesheet" href="./css/vien.css" type="text/css" />
    <script type="text/javascript" src="jquery-1.3.2.js"></script>

    <script type="text/javascript">
        $(document).ready((function() {
            $("#pseudo").focus();
        }))			
    </script>
</head>

<body>
    <?php
    include_once "./echecs/include/config.php";
    include_once "./echecs/include/tableaux.php";
    include_once "./echecs/classes/Inscription.class.php";

    date_default_timezone_set('America/Montreal');
    $valeur_confirmation = '';
    $type_confirmation = "hidden";
    $type_pseudo = "hidden";
    $valeur_pseudo = '';
    $pseudo = '';
    $courriel = '';
    $confirmation_courriel = '';
    $dbh = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);

    if (isset($_POST['envoi']))
    {
        /* Récupération des données du formulaire */
        $pseudo = isset($_POST['pseudo']) ? str_replace("'", "", $dbh->quote($_POST['pseudo'])) : "";
        $courriel = isset($_POST['courriel']) ? str_replace("'", "", $dbh->quote($_POST['courriel'])) : "";
        $confirmation_courriel = isset($_POST['confirmation']) ? str_replace("'", "", $dbh->quote($_POST['confirmation'])) : "";
        $jour = $_POST['jour'];
        $mois = $_POST['mois'];
        $annee = $_POST['annee'];
        $sexe = $_POST['sexe'];
        $prenom = isset($_POST['prenom']) ? $dbh->quote($_POST['prenom']) : "";
        $nom = isset($_POST['nom']) ? $dbh->quote($_POST['nom']) : "";
        $pays = $_POST['pays'];
        
        $validation = new Inscription;
        $resultat = $validation->enregistrer_joueur($pseudo,$courriel,$confirmation_courriel,$jour,$mois,$annee,$sexe,$prenom,$nom,$pays);
        $valeur_pseudo = $resultat['pseudo'];
        $type_pseudo = $resultat['type_pseudo'];
        $valeur_confirmation = $resultat['confirmation'];
        $type_confirmation = $resultat['type_confirmation'];
    }
    
    ?>
    <header>
        <img id="center-logo" src="monlogo.gif" />
        <div id="center-titre">Inscription au site</div>
    </header>
    <div class="petitecran">
        <form method="post">
            <fieldset>
                <legend>Élements obligatoires</legend>
                <div class="div-items">
                    <p><label for="pseudo">Choisissez un pseudonyme (5 à 20 caractères)</label><span class="erreur"><?php if ($type_pseudo == 'text') echo $valeur_pseudo; ?></span></span></p>
                    <p><label for="courriel">Votre adresse courriel</label><br />
                    <span class="small">votre mot de passe vous y sera envoyé. Cette adresse ne sera pas visible par les autres utilisateurs</span></p>
                    <p><label for="confirmation">Confirmation adresse courriel</label><span class="erreur"><?php if ($type_confirmation == 'text') echo $valeur_confirmation; ?></span></span></p>
                </div>
                <div class="div-reponses">
                    <p><input type="text" name="pseudo" id="pseudo" size="22" maxlength="20" placeholder="pseudonyme" required value="<?php echo $pseudo ?>" /></p>
                    <p><input type="email" name="courriel" id="courriel" placeholder="courriel" required value="<?php echo $courriel ?>" /><br /><span class="small">&nbsp;</span></p>
                    <p><input type="email" name="confirmation" id="confirmation" placeholder="confirmation" required value="<?php echo $confirmation_courriel ?>" /><br />
                </div>
            </fieldset>
            <fieldset>
                <legend>Élements optionels</legend>
                <div class="div-items">
                    <p><label for="jour">Date de naissance</label></p>
                    <p><label for="sexe">Vous êtes un / une</label></p>
                    <p><label for="prenom">Prénom</label></p>
                    <p><label for="nom">Nom</label></p>
                    <p><label for="pays">Votre pays</label></p>
                </div>
                <div class="div-reponses">
                    <p><select name="jour" id="jour">
                    <?php
                    for ($i=0;$i<count($les_jours);$i++){
                        if ($jour == $i)
                            echo '<option value="'.$i.'" selected>'.$les_jours[$i].'</option>';
                        else
                            echo '<option value="'.$i.'">'.$les_jours[$i].'</option>';
                    }
                    ?>
                    </select>
                    <select name="mois">
                    <?php
                    for ($i=0;$i<count($les_mois);$i++) {
                        if ($mois == $i)
                            echo '<option value="'.$i.'" selected>'.$les_mois[$i].'</option>';
                        else
                            echo '<option value="'.$i.'">'.$les_mois[$i].'</option>';
                    }
                    ?>
                    </select>
                    <select name="annee">
                    <?php
                    for ($i=0;$i<count($les_annees);$i++) {
                        if ($annee == $i)
                            echo '<option value="'.$i.'" selected>'.$les_annees[$i].'</option>';
                        else
                            echo '<option value="'.$i.'">'.$les_annees[$i].'</option>';
                    }
                    ?>
                    </select></p>
                    <p><select name="sexe" id="sexe">
                        <?php
                        for ($i=0;$i<count($les_sexes);$i++) {
                            if ($sexe == $i)
                                echo '<option value="'.$i.'" selected>'.$les_sexes[$i].'</option>';
                            else
                                echo '<option value="'.$i.'">'.$les_sexes[$i].'</option>';
                        }  
                        ?>
                    </select></p>
                    <p><input type="text" name="prenom" id="prenom" placeholder="prénom" /></p>
                    <p><input type="text" name="nom" id="nom" placeholder="nom" /></p>
                    <p><select name="pays" id="pays">
                    <?php
                    for ($i=0;$i<count($les_pays);$i++){
                        if ($pays == $i)
                            echo '<option value="'.$i.'" selected>'.$les_pays[$i].'</option>';
                        else
                            echo '<option value="'.$i.'">'.$les_pays[$i].'</option>';
                    }
                    ?>
                    </select></p>
                </div>
            </fieldset>
            <p><input style="text-align: center; font-size: 16px;" type="submit" value="S'inscrire" name="envoi" /></p>
        </form>
    </div>
    </body>
</html>