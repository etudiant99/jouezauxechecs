<div class="titreveritable">
    Les parties proposées
</div>
<?php
if (count($lespartiesproposees) == 0)
{
?>
    <div class="decompte">
        Il n'y a pas de partie libre proposée
    </div>
<?php
}
else
{
?>
    <div class="decompte">
        <table width="700">
            <tr><th>Nb</th><th>proposée par</th><th>son elo</th><th>ma couleur</th><th>cadence (jrs)</th><th>réserve (jrs)</th><th>commentaire</th><th>accepter</th></tr>

    </div>
            <?php
            $compteur = 0;   
            
            foreach ( $lespartiesproposees as $partie ):
                $compteur++;
                $detail = $partie->detailPartieproposee($partie->gidp());
                ?>
                <tr>
                    <td><?php echo $compteur ?></td>
                    <td><?php echo $detail['pseudostylise'] ?></td>
                    <td><?php echo $detail['elo'] ?></td>
                    <td><?php echo $detail['tacouleur'] ?></td>
                    <td><?php echo $detail['cadence'] ?></td>
                    <td><?php echo $detail['reserve'] ?></td>
                    <td><?php echo $detail['commentaire'] ?></td>
                    <td><a href="?action=traiter partie&amp;but=accepter&id=<?php echo $partie->gidp() ?>"><img src="./images/icons/accept.png" alt="accepter cette proposition" border="0" /></a></td>
                </tr>
            <?php
            endforeach;
            ?>
        </table>
    </div>
<?php  
}
?>
<div class="choix">
    <a href="?action=mes parties&amp;folder=-1">Mes parties en cours</a><br />
    <a href="?action=mes parties proposées&amp;folder=-1">Les parties que j'ai proposées</a><br /><br />
    <a href="?action=proposer partie">Proposer une partie</font></a>
</div>