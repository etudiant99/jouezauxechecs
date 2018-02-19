
<?php

    
    $uidActif = $_SESSION['uid'];
    $db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
    
    $managerPartieproposee = new PartieproposeeManager($db);
    $mespartiesproposees = $managerPartieproposee->getListmespp($uidActif);

    ?>
    <div class="titreveritable">
        Mes parties proposées
    </div>

    <?php

    if (count($mespartiesproposees) == 0)
    {
    ?>
        <div class="decompte">
            Vous n'avez proposé aucune partie
        </div>
    <?php  
    }
    else
    {
        ?>
        <div class="decompte">
            <table width="700" align="center" align="center" class="listtable" cellpadding="0" cellspacing="0">
                <tr><th>proposée à</th><th>son élo</th><th>ma couleur</th><th>cadence<br />(jours)</th><th>réserve<br />(jours)</th><th>commentaire</th><th>Effacer</th></tr>
        <?php
        foreach ($mespartiesproposees as $partie):
            $detail = $partie->detailMapartieproposee($partie->gidp());
            ?>
                <tr>
                    <td><?php echo $detail['pseudostylise'] ?></td>
                    <td><?php echo $detail['elo'] ?></td>
                    <td><?php echo $detail['macouleur'] ?></td>
                    <td><?php echo $detail['cadence'] ?></td>
                    <td><?php echo $detail['reserve'] ?></td>
                    <td><?php echo $detail['commentaire'] ?></td>
                    <td><a href="?action=traiter partie&amp;but=effacer&id=<?php echo $partie->gidp() ?>"><img src="./images/icons/effacer.png"  width="20" height="15" alt="supprimer cette partie" border="0" style="margin-left: 2px;" /></a></td>
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
        <a href="?action=parties proposées">Les parties proposées par les autres joueurs</a><br /><br />
        <a href="?action=proposer partie">Proposer une partie</a>
    </div>
