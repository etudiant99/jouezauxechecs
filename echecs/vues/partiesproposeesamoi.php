<section></section>
<div class="titreveritable">
    partie(s) personelle(s) proposée(s)
</div>
<div class="decompte">
    <table width="700">
        <tr><th>Nb</th><th>proposée par</th><th>son elo</th><th>ma couleur</th><th>cadence (jrs)</th><th>réserve (jrs)</th><th>commentaire</th><th>oui</th><th>non</th></tr>
        <?php
            $compteur = 0;   
            
            foreach ( $mespartiesproposees as $partie ):
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
                    <td><a href="?action=traiter partie&amp;but=refuser&id=<?php echo $partie->gidp() ?>"><img src="./images/icons/effacer.png"  width="20" height="15" alt="refuser cette proposition" border="0" /></a></td>
                </tr>
            <?php
            endforeach;
            ?>
        </table>
</div>
    <div class="choix">
        <a href="?action=parties terminées">Mes parties terminées</a><br />
        <a href="?action=mes parties proposées&amp;folder=-1">Les parties que j'ai proposées</a><br />
        <a href="?action=parties proposées">Les parties proposées par les autres joueurs</a><br /><br />
    </div>

</section>