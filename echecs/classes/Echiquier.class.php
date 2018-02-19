<?php
class Echiquier extends JoueurManager
{
    protected $_position = array();
    protected $_pieces = array('piece' => array(), 'couleur' => array(), 'endroit' => array());
    protected $_nombrepiecesblanches;
    protected $_nombrepiecesnoires;
    protected $_nbcases;
    protected $_contenucase;
    protected $_trait;
    protected $_bascule;
    protected $_lastmove;
    private $_positiondepart;
    protected $_promotion;
    private $_situationpriseenpassant;
    
    public function __construct()
    {
        $this->positiondepart();
        //$this->piecesEchiquier();
    }

    public function getPositionDepart()
    {
        return $this->_position;
    }

    public function positiondepart()
    {            
        $this->_lastmove = '';
        $position = array();
        
        $position[0] = 'R';
        $position[1] = 'N';
        $position[2] = 'B';
        $position[3] = 'Q';
        $position[4] = 'K';
        $position[5] = 'B';
        $position[6] = 'N';
        $position[7] = 'R';
        
        for ($i=8;$i<16;$i++) $position[$i]  = 'P';
        for ($i=16;$i<48;$i++) $position[$i] = '';
        for ($i=48;$i<56;$i++) $position[$i] = 'p';
        
        $position[56] = 'r';
        $position[57] = 'n';
        $position[58] = 'b';
        $position[59] = 'q';
        $position[60] = 'k';
        $position[61] = 'b';
        $position[62] = 'n';
        $position[63] = 'r';

        for ($i=0;$i<64;$i++)
        {
            $this->_position[$i] = $position[$i];
        }
    }

    public function piecesEchiquier()
    {   
        $pieces = array('1' => array(),'0' => array());
        $endroit = array();
        $couleur = array();
        $retour = array();
        $position = $this->_position;
        
        for ($i=0;$i<64;$i++)
        {
            if ($position[$i] != '')
            {
                if ($position[$i] == strtoupper($position[$i]))
                    $pieces['1'][$i] = $position[$i];
                else
                    $pieces['0'][$i] = $position[$i];
            }
        }
        $this->_nombrepiecesblanches = count($pieces['1']);
        $this->_nombrepiecesnoires = count($pieces['0']);
    }

    public function position()
    {
        return $this->_position;
    }
    
    private function contenuCase($indice)
    {
        $position = $this->position();
        return $position[$indice];
    }
    
    public function getSituationPriseEnPassant()
    {
        return $this->_situationpriseenpassant;
    }
    
    public function NombrePiecesBlanches()
    {
        return $this->_nombrepiecesblanches;
    }
    
    public function NombrePiecesNoires()
    {
        return $this->NombrePiecesNoires();
    }

    public function dessinerEchiquier($partie)
    {
        $positiontemp = array();
        $affichagePromotion = null;
        $position = $this->getPositionDepart();
        $trait = $this->Montrait();
        $lescasesattaquees = $this->trouveCasesAttaquees($position,$trait);
        
        $lettres = array('a','b','c','d','e','f','g','h');
        $joueur = $this->trouveJoueur($partie->getAdversaire());
        $blancs = $partie->uidb();
        $noirs = $partie->uidn();
        $joueur = $this->trouveJoueur($blancs);
        $pseudoblancs = $joueur->pseudostylise();
        $joueur = $this->trouveJoueur($noirs);
        $adversaire = $joueur->pseudostylise();
        $flip = $partie->getFlipBase();

        $cliquable = $partie->getCliquable();
        $lastmove = $this->Lastmove();
        $partie->duree_restante();
        $mangeaille = $partie->getMangeaille();
        if ($trait == 1)
            $reservepersonnelle = $partie->reserve_uidb();
        else
            $reservepersonnelle = $partie->reserve_uidn();

         if($lastmove != '')
         {
            $derniercoup = -1;
            $lmove = explode("-",$lastmove);
            $start = $lmove[0];
            $end = $lmove[1];
            if (($position[$end] == 'p' && $trait == 1) || ($position[$end] == 'P' && $trait == -1))
                $derniercoup = $end;
         }
         else
         {
            $start = -1;
            $end = -1;
            $derniercoup = -1;
         }

        $cell1 = -1;
        $cell2 = -1;

        if (isset($_GET['depart']))
            $cell1 = $_GET['depart'];
            
        if (isset($_GET['arrivee']))
            $cell2 = $_GET['arrivee'];

        if ($cell2 != -1 && $cell2 == $cell1)
        {
            $cell1 = -1;
            $cell2 = -1;
        }

        if ($cell1 != -1)
        {
            if ($cell1>47 && $position[$cell1] == 'P')
            {
                $positiontemp = $position;
                $this->setPromotion('Q');
                $affichagePromotion = $this->dessinerPiecesPromotion('1',$partie->gid(),$cell1,$cell2);
            }
  
            if ($cell1<16 && $position[$cell1] == 'p')
            {
                $positiontemp = $position;
                $this->setPromotion('q');
                $affichagePromotion = $this->dessinerPiecesPromotion('-1',$partie->gid(),$cell1,$cell2);
            }
            
            if (isset($_GET['piece']))
            {
                $mapiece = $_GET['piece'];
                $this->setPromotion($mapiece);
            }
            else
                $mapiece = '';
                
            $lapiece = $this->trouve($position[$cell1]);
            $endroitsPossibles =  $lapiece->endroitsPossibles($position,$cell1,$trait,$derniercoup);
            
            if (isset($lapiece))
            {
                $lapiece->deplacer($position,$cell1,$trait,$derniercoup);
                $positions = $lapiece->positionsPossibles();
                $piecesattaquees = $lapiece->piecesAttaquees();
                $piecesdefendues = $lapiece->piecesDefendues();
            }   
        }
        
        $couleur = 1;
        ?>
        
        <div class="titre">
            <b>Ma partie</b> #</b>&nbsp;<?php echo $partie->gid(); ?>:&nbsp;&nbsp;&nbsp;<?php echo $pseudoblancs ?> - <?php echo $adversaire ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br /><br /><br />
            <div class="infosdroite">
                Date du dernier coup:<?php echo $this->dateenlettres($partie->datederniercoup()) ?>
                <br /><br />
                <?php
                if ($cliquable == true)
                {
                    if ($trait == 1)
                        $reservepersonnelle = '(+ réserve blanche de '.number_format($partie->getreserveBlanche(),2);
                    else
                        $reservepersonnelle = '(+ réserve noire de '.number_format($partie->getreserveNoire(),2);
                    ?>
                    <u>Le temps restant:</u>&nbsp;&nbsp;<b><?php echo $partie->tempsRestant() ?></b><br /><?php echo $reservepersonnelle ?> jours)<br /><br />
                    Réserve des Blancs: <?php echo $partie->pourcentage_a_blancs() ?>%
                    <?php
                }
                else
                {
                    if ($trait == 1)
                        $reservepersonnelle = '(+ réserve blanche de '.number_format($partie->getreserveBlanche(),2);
                    else
                        $reservepersonnelle = '(+ réserve noire de '.number_format($partie->getreserveNoire(),2);
                    ?>
                    <u>Le temps restant:</u>&nbsp;&nbsp;<b><?php echo $partie->tempsRestant() ?></b><br /><?php echo $reservepersonnelle ?> jours)<br /><br />
                    Réserve des Blancs: <?php echo $partie->pourcentage_a_blancs() ?>%
                    <?php
                }
                ?>
                <br />
                <table style="width: 100%;">
                    <tr>
                        <div style="background-color:#f00;">
                        <div style="width:<?php echo $partie->pourcentage_a_blancs() ?>%;background-color:#ff0;">&nbsp;</div>
                        </div>
                    </tr>
                </table>
                <br />
                Réserve des Noirs: <?php echo $partie->pourcentage_a_noirs() ?>%
                <br />
                <table style="width: 100%;">
                    <tr>
                        <div style="background-color:#f00;">
                            <div style="width:<?php echo $partie->pourcentage_a_noirs() ?>%;background-color:#ff0;">&nbsp;</div>
                        </div>
                    </tr>
                </table>
                <br />
                <?php
                echo '<b>'.$this->nbCoupsPossibles($partie->gid(),$derniercoup).'</b>';
                if ($cliquable == true)
                {   
                    ?>
                    <a href="?action=montrer partie&amp;gid=<?php echo $partie->gid() ?>&amp;but=abandonner" 
                    onclick="if(!confirm('Voulez-vous vraiment abandonner ?')) return false;">Abandonner</a>
                    <br />
                <?php
                echo $affichagePromotion;
                }
                ?>            
                <br /><br />
                <?php
                $lecoup = '';
                $resultat = false;
        
                if ($partie->getMat())
                    $this->mat($partie->actif(),$partie->gid());
            
                if ($partie->getPartieNulle())
                    $this->nulle($partie->gid());
                
                if ($cell1 != -1 and $cell2 != -1)
                {
                    $positiontemp = $position;
                    $position[$cell2] = $this->getPromotion();
                    
                    if ($derniercoup != -1 && $derniercoup+8 == $cell2 && $position[$cell2] == '')
                    {
                        $cell2 = $derniercoup+8;
                        $positiontemp = $position;
                    }
                    
                    if ($derniercoup != -1 && $derniercoup-8 == $cell2 && $position[$cell2] == '')
                    {
                        $cell2 = $derniercoup-8;
                        $positiontemp = $position;
                    }

                        
                    $p = $position[$cell1];
                    $lecoup = $this->moveToText($cell1).$this->moveToText($cell2).$this->getPromotion();
                    $lapiece = $this->trouve($p);
                    
                    // lecoup est les coordonnées du coup, sous la forme a7a5
                    // la position est le contenu de chacune des 64 cases
                    // cell1 est le numéro de la case de départ pour le coup
                    // cell2 est le numéro de la case d'arrivée pour le coup
                    // derniercoup est le numéro de la case d'arrivée pour le dernier coup de l'adversaire
                    // lapiece est le nom/couleur de la pièce se trouvant sur la case de départ de la pièce à bouger
                    
                    $coupvalide = $lapiece->legal($positiontemp,$cell1,$cell2,$derniercoup);
                    
                    if ($coupvalide)
                    {
                        ?>
                        <div class="acceptercoup">
                        <form>
                            <input type="hidden"   name="action"  value="mes parties" />
                            <input type="hidden"   name="lecoup"  value="<?php echo $lecoup ?>" />
                            <input type="hidden"   name="gid"  value="<?php echo $partie->gid() ?>" />
                            <input type="hidden"   name="chb"  value="<?php echo $partie->changementB() ?>" />
                            <input type="hidden"   name="chn"  value="<?php echo $partie->changementN() ?>" />
                            <input type="checkbox" style="border: 0px; background-color: transparent; width: 30px" name="nulle" value="1"  />Proposer partie nulle<br />
                            <input type="submit" value="Jouer" /><?php echo ' '.$lecoup ?>
                        </form>
                        </div>
                        <?php
                    }
                    else
                        echo 'Coup invalide';
                }
                ?>
            </div>
        </div>
        <div class="leschiffres">
            <div>
            <?php
            if ($flip == false)
                for ($j=8;$j>0;$j--)
                {
                    ?>
                    <div class="chiffres"><?php echo $j; ?></div>
                    <?php
                }
            else
                for ($j=1;$j<9;$j++)
                {
                    ?>
                    <div class="chiffres"><?php echo $j; ?></div>
                    <?php
                }
            ?>
            </div>
        </div>
        <div class="echiquier">
            <?php
            for($ligne=0; $ligne<8; $ligne++)
            {
                for ($colonne=0;$colonne<8;$colonne++)
                {
                    if ($flip == false)
                        $i = (7-$ligne)*8 + $colonne;
                    else
                        $i = $ligne*8+(7-$colonne);
                    
                    if ($couleur == 1)
                        $bgcolor = "white";
                    else
                        $bgcolor = "lightblue";
                    
                    // Pour les cases attaquees
                    if (isset($lescasesattaquees) && $cell1 == -1)
                        for ($t=0;$t<count($lescasesattaquees);$t++)
                        {
                            if ($i == $lescasesattaquees[$t])
                                $bgcolor = "#00effc";
                        }
                    
                    // Pour le lastmove
                    if ($i == $start || $i == $end)
                        $bgcolor = "#baffbf";
                    
                    // Pour les endroits ou l'on peut jouer la piece
                    if (isset($positions) && $cell2 == -1)
                        for ($po=0;$po<count($positions);$po++)
                        {
                            if ($i == $positions[$po])
                                $bgcolor = "#99fff3";   
                        }
                    
                    if (isset($piecesattaquees))
                        for ($pi=0;$pi<count($piecesattaquees);$pi++)
                        {
                            if ($i == $piecesattaquees[$pi])
                                $bgcolor = "#eba2a2";
                        }

                    // Pour les pieces que je defends avec la piece que je veux jouer
                    if ($cell1 != -1)
                    {
                        if (isset($piecesdefendues) && $cell2 == -1)
                            for ($pi=0;$pi<count($piecesdefendues);$pi++)
                            {
                                if ($i == $piecesdefendues[$pi])
                                    $bgcolor = "#fbe479";
                            }
                    }           

                    // Pour indiquer ou l'on veut déplacer une pièce
                    if ($i == $cell1 or $i == $cell2)
                        $bgcolor = "#ffff35";

                    $lapiece = $this->trouve($position[$i]);
                    $imagepiece = $lapiece->image();
                    $couleurcase = $lapiece->Couleur($position[$i]);
                    
                    $contenucase = '<div style="background-color: '.$bgcolor.';" class="unecase">'.$imagepiece.'</div>';
                    if ($cliquable)
                    {
                        if ($position[$i] != '' and $couleurcase == $trait)
                            if ($cell1 == -1)
                            {
                                $lapiece = $this->trouve($position[$i]);
                                $quantite = $lapiece->nbEndroitsPossibles($position,$i,$partie->getTrait(),$derniercoup);
                                if ($quantite > 0)
                                    $contenucase = '<div style="background-color: '.$bgcolor.';" class="unecase"><a href="index.php?action=montrer partie&amp;depart='.$i.'&amp;gid='.$partie->gid().'">'.$imagepiece.'</a></div>';
                                else
                                    $contenucase = '<div style="background-color: '.$bgcolor.';" class="unecase">'.$imagepiece.'</div>';
                            }
                        
                        if ($cell1 != -1)
                            for ($po=0;$po<count($positions);$po++)
                            {
                                if ($positions[$po]  == $i)
                                    $contenucase = '<div style="background-color: '.$bgcolor.';" class="unecase"><a href="index.php?action=montrer partie&amp;depart='.$cell1.'&amp;arrivee='.$i.'&amp;gid='.$partie->gid().'">'.$imagepiece.'</a></div>';
                            }
                    }
                    echo $contenucase;
                    $couleur = -$couleur;
                }
                $couleur = -$couleur;
            }
            ?>
        </div>
        <div class="leslettres">
            <?php
            if ($flip == false)
                for ($j=0;$j<8;$j++)
                {
                    ?>
                    <div class="lettres"><?php echo $lettres[$j]; ?></div>
                    <?php
                }
            else
                for ($j=7;$j>=0;$j--)
                {
                    ?>
                    <div class="lettres"><?php echo $lettres[$j]; ?></div>
                    <?php
                }
            ?>
        </div>
        <div class="piecesblanchessmangees">
        <?php
        if (isset($mangeaille))
        {
            foreach($mangeaille['blancs'] as $item)
                echo $item;
            if (count($mangeaille['blancs']) == 0)
                echo '<img src="./pieces/vide_21.gif">';
        }
        else
            echo '<img src="./pieces/vide_21.gif">';
        ?>
        </div>
        <div class="piecesnoiressmangees">
        <?php
        if (isset($mangeaille))
        {
            foreach($mangeaille['noirs'] as $item)
                echo $item;
            if (count($mangeaille['noirs']) == 0)
                echo '<img src="./pieces/vide_21.gif">';
        }
        else
            echo '<img src="./pieces/vide_21.gif">';
        ?>
        </div>
        <?php
    }
    
    public function visualiser($partie,$qtecoups,$flip,$vraiign,$option)
    {
        $lettres = array('a','b','c','d','e','f','g','h');
        $cadence = $partie->cadencep();
        $reserve = $partie->reservep();
        $datedebut = $partie->datedebut();
        $datederniercoup = $partie->datederniercoup();
        $datefin = $partie->datefin();
        $finalisation = $partie->finalisation();
        $nbcoups = $partie->getNbCoups();
        
        $blancs = $partie->uidb();
        $noirs = $partie->uidn();
        $joueur = $this->trouveJoueur($blancs);
        $pseudoblancs = $joueur->pseudostylise();
        $joueur = $this->trouveJoueur($noirs);
        $adversaire = $joueur->pseudostylise();
        $trait = $partie->getTrait();
        if ($trait == -1)
            $letrait = "noirs";
        else
            $letrait = "blancs";
        
        $this->positionarbitraire($partie->getIgn(),$qtecoups);
        $mangeaille = $this->getMangeaille();
        $position = $this->position();
        
        $lastmove = $this->Lastmove();
        
         if($lastmove != '')
         {
            $lmove = explode("-",$lastmove);
            $start = $lmove[0];
            $end = $lmove[1];
         }
         else
         {
            $start = -1;
            $end = -1;
         }

        $couleur = 1;
        ?>
        <div class="titre">
            <?php
            $phrase = '';
            if ($option == 'rejouer')
                $phrase = '<b>Partie en cours # '.$partie->gid().'</b>:&nbsp;'.$pseudoblancs.' - '.$adversaire;
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                $phrase .= '<br />Trait aux '.$letrait.' - Cadence: '.$cadence.' jours/coup<br />Reserve = '.$reserve.' jours';
            if ($option == 'parties terminees')
            {
                $phrase = '<b>Partie terminee # '.$partie->gid().'</b>:&nbsp;&nbsp;&nbsp;'.$pseudoblancs.' - '.$adversaire;
                $phrase .= '<br />Cadence: <b>'.$cadence.'</b> jours/coup, Reserve = <b>'.$reserve.'</b> jours <br />';
                $phrase .= 'Commencee le <b>'.$this->formate_date($datedebut).'</b>, terminee le <b>'.$this->formate_date($datefin).'<br />';
                $phrase .= '<b>'.$finalisation.'</b>, apres '.$nbcoups.' coups';
            }
            echo $phrase;
            ?>
            <div class="coupsdroite">
                <?php
                if ($nbcoups > 0)
                    echo '<input type="button" value="Imprimer les coups" onClick="impressioncoups()"<br /><br /><br >';
                if ($vraiign != '')
                {
                    $nombrefou = -15;
                    if (isset($_GET['t']))
                        $nombrefou =  $_GET['t'];
                    $coups = explode(" ",$vraiign);  
                    $sortie = null;
                                                                        
                    $this->positionactuelle($vraiign);
                    $sortie = $this->getTousLesCoups();
                    $_SESSION['lescoups'] = $sortie;
                    $i = 0;
                    $yvan = $qtecoups;
                                    
                    foreach($coups as $item)
                    {
                        if($i == $yvan-1)
                            $bg = 'color: rgb(0, 0, 255)';
                        else
                            $bg = '';

                        $i++;
                        if ($i%2 == 0)
                        {
                            $truc = '<span style="width: 70px; text-align: left; '.$bg.'">'.$item.'</span>';
                            echo $truc.'<br />';
                        }
                        else
                        {
                            $truc = '<span style="width: 70px; text-align: left; '.$bg.'">'.$item.'</span>';
                            $n = $this->parseInt($i/2)+1;
                            echo $n.'. '.$truc.'&nbsp;&nbsp;';
                        } 
                    }
                }
                ?>
            </div>
        </div>
        <div class="leschiffres">
            <div>
            <?php
            if ($flip == false)
                for ($j=8;$j>0;$j--)
                {
                    ?>
                    <div class="chiffres"><?php echo $j; ?></div>
                    <?php
                }
            else
                for ($j=1;$j<9;$j++)
                {
                    ?>
                    <div class="chiffres"><?php echo $j; ?></div>
                    <?php
                }
            ?>
            </div>
        </div>
        <div class="echiquier">
            <?php
            for($ligne=0; $ligne<8; $ligne++)
            {
                for ($colonne=0;$colonne<8;$colonne++)
                {
                    if ($flip == false)
                        $i = (7-$ligne)*8 + $colonne;
                    else
                        $i = $ligne*8+(7-$colonne);
                    
                    if ($couleur == 1)
                        $bgcolor = "white";
                    else
                        $bgcolor = "lightblue";
                    
                    // Pour le lastmove
                    if ($i == $start || $i == $end)
                        $bgcolor = "#baffbf";
                        
                    if (isset($positions))
                        for ($po=0;$po<count($positions);$po++)
                        {
                            if ($i == $positions[$po])
                                $bgcolor = "#99fff3";   
                        }
                    
                    if (isset($piecesattaquees))
                        for ($pi=0;$pi<count($piecesattaquees);$pi++)
                        {
                            if ($i == $piecesattaquees[$pi])
                                $bgcolor = "#eba2a2";
                        }

                    $lapiece = $this->trouve($position[$i]);
                    $imagepiece = $lapiece->image();
                    $couleurcase = $lapiece->Couleur($position[$i]);
                    
                    $contenucase = '<div style="background-color: '.$bgcolor.';" class="unecase">'.$imagepiece.'</div>';
                    echo $contenucase;
                    
                    $couleur = -$couleur;
                }
                $couleur = -$couleur;
            }
            ?>
        </div>
        <div class="leslettres">
            <?php
            if ($flip == false)
                for ($j=0;$j<8;$j++)
                {
                    ?>
                    <div class="lettres"><?php echo $lettres[$j]; ?></div>
                    <?php
                }
            else
                for ($j=7;$j>=0;$j--)
                {
                    ?>
                    <div class="lettres"><?php echo $lettres[$j]; ?></div>
                    <?php
                }
            ?>
        </div>
        <div class="piecesblanchessmangees">
        <?php
        if (isset($mangeaille))
        {
            foreach($mangeaille['blancs'] as $item)
                echo $item;
            if (count($mangeaille['blancs']) == 0)
                echo '<img src="./pieces/vide_21.gif">';
        }
        else
            echo '<img src="./pieces/vide_21.gif">';
        ?>
        </div>
        <div class="piecesnoiressmangees">
        <?php
        if (isset($mangeaille))
        {
            foreach($mangeaille['noirs'] as $item)
                echo $item;
            if (count($mangeaille['noirs']) == 0)
                echo '<img src="./pieces/vide_21.gif">';
        }
        else
            echo '<img src="./pieces/vide_21.gif">';
        ?>
        </div>
        <div>
            <a href="?action=debut&amp;id=<?php echo $partie->gid() ?>&amp;f=<?php echo $flip ?>"><img src="./images/icons/set_first.png" border="0"/></a>
            <a href="?action=precedent&amp;id=<?php echo $partie->gid() ?>&amp;t=<?php echo $qtecoups ?>&amp;f=<?php echo $flip ?>"><img src="./images/icons/set_previous.png" border="0"/></a>
            <a href="?action=suivant&amp;id=<?php echo $partie->gid() ?>&amp;t=<?php echo $qtecoups ?>&amp;f=<?php echo $flip ?>"><img src="./images/icons/set_next.png" border="0"/></a>
            <a href="?action=dernier&amp;id=<?php echo $partie->gid() ?>&amp;f=<?php echo $flip ?>"><img src="./images/icons/set_last.png" border="0"/></a>&nbsp;
            <a href="?action=tourner&amp;id=<?php echo $partie->gid() ?>&amp;t=<?php echo $qtecoups ?>&amp;f=<?php echo $flip ?>"><img src="./images/icons/flip.png" border="0"/></a>
        </div>
        <?php
    }
    
    private function dateenlettres($date)
    {
        switch(substr($date,5,2))
        {
            case '01':
                $mois = 'janvier';
                break;
            case '02':
                $mois = 'février';
                break;
            case '03':
                $mois = 'mars';
                break;
            case '04':
                $mois = 'avril';
                break;
            case '05':
                $mois = 'mai';
                break;
            case '06':
                $mois = 'juin';
                break;
            case '07':
                $mois = 'juillet';
                break;
            case '08':
                $mois = 'août';
                break;
            case '09':
                $mois = 'septembre';
                break;
            case '10':
                $mois = 'octobre';
                break;
            case '11':
                $mois = 'novembre';
                break;
            case '12':
                $mois = 'décembre';
                break;
            default:
                $mois = '';
        }
        
        $dateformatee = substr($date,8,2).' '.$mois;
        
        return $dateformatee;
    }

    private function moveToText($move)
    {
        $sortie = '';
        $ligne = $this->parseInt($move/8);
	    $colonne = $move - $ligne*8;
        
        if($colonne == 0) $sortie .= 'a';
        else if($colonne == 1) $sortie .= 'b';
        else if($colonne == 2) $sortie .= 'c';
        else if($colonne == 3) $sortie .= 'd';
        else if($colonne == 4) $sortie .= 'e';
        else if($colonne == 5) $sortie .= 'f';
        else if($colonne == 6) $sortie .= 'g';
        else if($colonne == 7) $sortie .= 'h';
        
        $ligne ++;
        $sortie .= $ligne;
        
        return $sortie;
    }
    
    protected function formate_date($date)
    {
        $date_formatee = substr($date,8,2)."/".substr($date,5,2)."/".substr($date,0,4);
    
        if ($date == '')
            return '';
        else
            return $date_formatee;
    }
    
    public function getPromotion()
    {
        return $this->_promotion;
    }

    protected function Lastmove()
    {
        return $this->_lastmove;
    }
    
    public function Montrait()
    {
        return $this->_trait;
    }
    
    public function setPromotion($id)
    {
        $this->_promotion = $id;
    }

}
?>