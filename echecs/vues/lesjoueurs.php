<div class="titreveritable">
    Les joueurs inscrits
</div>
<div class="decompte">
    <table width="600">
    <?php
    if ($_SESSION['pseudo'] == 'etudiant')
    { ?>
        <tr><th>rang</th><th style="text-align: left;">pseudo</th><th></th><th>elo</th><th>connecté(e) le</th><th>parties en cours</th><th>inscrit le</th></tr>
    <?php
    }
    else
    {
        ?>
        <tr><th>rang</th><th style="text-align: left;">pseudo</th><th>elo</th><th>connecté(e) le</th><th>parties en cours</th><th>inscrit le</th></tr>
    <?php
    }
        
    $compteur = 0;
    foreach ($lesjoueurs as $joueur)
    {
        $compteur++;
        $detail = $joueur->detailJoueur($joueur->uid());
        $nb_parties = $managerJoueurs->count($detail['uid']);
        if ($_SESSION['pseudo'] == 'etudiant')
        {
            ?>
            <tr>
                <td><?php echo $compteur ?></td>
                <td style="text-align: left;"><?php echo $detail['pseudostylise'] ?></td>
                <?php
                if ($joueur->pseudo() == 'etudiant')
                {
                    ?>
                    <td><img border="0" src="./images/vide.gif" width="20" height="15" /></td>
                <?php
                }
                else
                {
                    ?>
                <td style='text-align: left;'><a href="./include/effaceindividu.php?uid=<?php echo $joueur->uid() ?> " 
                onclick="if(!confirm('Voulez-vous vraiment effacer  <?php echo $joueur->pseudo() ?> ?')) return false;"><img border="0" src="./images/effacer.png" width="20" height="15" /></a></td>
                <?php } ?>
                <td><?php echo $detail['elo'] ?></td>
                <td><?php echo $detail['date_connection'] ?></td>
                <td><?php echo $nb_parties ?></td>
                <td><?php echo $detail['date_inscription'] ?></td>
            </tr>
            <?php
        }
        else
        {
            ?>
            <tr>
                <td><?php echo $compteur ?></td>
                <td style="text-align: left;"><?php echo $detail['pseudostylise'] ?></td>
                <td><?php echo $detail['elo'] ?></td>
                <td><?php echo $detail['date_connection'] ?></td>
                <td><?php echo $nb_parties ?></td>
                <td><?php echo $detail['date_inscription'] ?></td>
            </tr>
            <?php          
        }
        
    }
?>
</table>
</div>