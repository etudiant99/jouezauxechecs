<section></section>
<div class="titreveritable">
    Mes parties terminées
</div>
<div class="decompte">
    <?php
    $joueurs = new JoueurManager;
    if (count($lesparties) == 0)
    {
        ?>
        Vous n'avez aucune partie terminée
    <?php
    }
    else
    {
        ?>
        <table width="450">
        <tr><th>moi</th><th>#partie</th><th>adversaire:</th><th>résultat:</th><th>effacer</th></tr>
        <?php
        foreach ($lesparties as $partie):
            $adversaire = $joueurs->trouveJoueur($partie->getAdversaire());
            $finalisationStylisee = '<a href="?action=rejouer&amp;gid='.$partie->gid().'">'.$partie->finalisation().'</a>';
            ?>
            <tr>
                <td><?php echo $partie->getImageMaCouleur() ?></td>
                <td><?php echo $partie->gid() ?></td>
                <td style="text-align: left;"><?php echo $adversaire->pseudostylise() ?></td>
                <td style="text-align: left;"><?php echo $finalisationStylisee ?></td>
                <td><a href="?action=effacer partie&amp;no=<?php echo $partie->gid() ?>"><img src="./images/icons/effacer.png"  width="16" height="16" alt="supprimer cette partie" style="margin-left: 2px;" /></td>
            </tr>
        <?php
        endforeach;
        ?>
        </table>
<?php  
}
?>
</div>
<div class="choix">
    <a href="?action=mes parties">Mes parties en cours</a><br />
    <a href="?action=mes parties proposées&amp;folder=-1">Les parties que j'ai proposées</a><br />
    <a href="?action=parties proposées">Les parties proposées par les autres joueurs</a><br /><br />
</div>
