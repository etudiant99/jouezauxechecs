<section></section>
<div class="titreveritable">
    Les parties en cours
</div>
<?php
if (count($lesparties) == 0)
{
?>
    <div class="decompte">
        Il n'y a aucune partie en cours
    </div>
<?php
}
else
{
?>
<div class="decompte">
    <table width="550">
        <tr><th>Nb</th><th>adversaires</th><th>#partie</th><th>démarrée le</th><th>coups</th><th>tour</th></tr>
    <?php
    $joueurs = new JoueurManager;
    $compteur = 0;
    
    foreach ($lesparties as $partie)
    {
        $lesBlancs = $joueurs->trouveJoueur($partie->uidb());
        $lesNoirs = $joueurs->trouveJoueur($partie->uidn());
        $compteur++;
        ?>
        <tr>
            <td><?php echo $compteur ?></td>
            <td style="text-align: left;"><?php echo $lesBlancs->pseudostylise() ?> - <?php echo $lesNoirs->pseudostylise() ?></td>
            <td><?php echo $partie->gid() ?></td>
            <td><?php echo $partie->datedebut() ?></td>
            <td><?php echo $partie->getNbCoups() ?></td>
            <td><a href="?action=rejouer&amp;gid=<?php echo $partie->gid() ?>"><?php echo $partie->getSituation() ?></a></td>
        </tr>
        <?php
    }
    ?>
    </table>
</div>
</section>
<?php  
}