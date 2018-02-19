<?php
$db = new PDO(SQL_DSN, SQL_USERNAME, SQL_PASSWORD);
$statistiques = new StatistiqueManager($db);
?>

<div class="titreveritable">
    Mes statistiques
</div>

<p><b>Vos parties:</b>
<br /><br />
Vous avez <?php echo $partiesencours ?> <a href="?action=mes parties"><?php echo $message_pour_parties  ?></a> (<b><?php echo $partiesavecblancs ?></b> avec les Blancs, <b><?php echo $partiesavecnoirs ?></b> avec les Noirs).
<br /><br />
Vous avez <b><?php echo $statistique->partiestotales() ?></b><?php echo $message_parties_jouees  ?>depuis le <?php echo $statistiques->formate_date($joueur->date_inscription()) ?><br />
(<?php echo $statistique->partiesavecblancs() ?> avec les Blancs, <?php echo $statistique->partiesavecnoirs() ?> avec les Noirs).</p>
<p>Vos résultats: <u><strong><?php echo $statistique->pourcentagegains() ?> parties gagnées</u></strong>.
<br />

<table align="center" cellpadding="0" cellspacing="0"class="listtable" style="margin-top: 10px;">
<tr style="height: 40px; background-image:url('images/bg/table_header_bg.png'); background-repeat:repeat-x;">
<td width="100" height="40"></td>
<td width="60" align="center">Blancs</td>
<td width="60" align="center">Noirs</td>
</tr>
<tr>
<td align="center" height="30">Gains:</td>
<td align="center"><?php echo $statistique->gains_b() ?></td>
<td align="center"><?php echo $statistique->gains_n() ?></td>
</tr>
<tr>
<td align="center" height="30">Nulles:</td>
<td align="center"><?php echo $statistique->nulles_b() ?></td>
<td align="center"><?php echo $statistique->nulles_n() ?></td>
</tr>
<tr>
<td align="center" height="30">Défaites:</td>
<td align="center"><?php echo $statistique->pertes_b() ?></td>
<td align="center"><?php echo $statistique->pertes_n() ?></td>
</tr>
</table></p>

</div>