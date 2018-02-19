<section></section>
<div class="titreveritable">
    Mes parties en cours
</div>
<div class="decompte">
    <?php
    $joueurs = new JoueurManager;
    if (count($mesparties) == 0)
    {
        echo "Vous n'avez aucune partie en cours";
    }
    else
    {
        ?>
        
            <table width="600">
            <tr><th>Nb</th><th>#partie</th><th>ma couleur</th><th>adversaire</th><th style="text-align: left;">temps restant</th><th>coups</th><th>tour</th></tr>
        <?php
        $compteur = 0;
        foreach ($mesparties as $partie)
        {
            $joueur = $joueurs->trouveJoueur($partie->getAdversaire());
            $partie->duree_restante();
            
            $compteur++;
            ?>
            <tr>
            <td><?php echo $compteur ?></td>
            <td><?php echo $partie->gid() ?></td>
            <td><?php echo $partie->getImageMaCouleur() ?></td>
            <td><?php echo $joueur->pseudostylise() ?></td>
            <td><?php echo $partie->tempsRestant() ?></td>
            <td><?php echo $partie->getNbCoups() ?></td>
            <td><?php echo $partie->getTour() ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </table>
    </div>

    <div class="choix">
        <a href="?action=parties terminées">Mes parties terminées</a><br />
        <a href="?action=mes parties proposées&amp;folder=-1">Les parties que j'ai proposées</a><br />
        <a href="?action=parties proposées">Les parties proposées par les autres joueurs</a><br /><br />
    </div>

</section>