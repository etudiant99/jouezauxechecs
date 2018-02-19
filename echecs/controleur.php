<?php

$uidActif = $_SESSION['uid'];
$pseudo = $_SESSION['pseudo'];

$prive = array('mes parties' => 'Mes parties',
               'parties proposées' => 'Parties proposées',
               'mes statistiques' => 'Mes statistiques',
               'profil' => 'Profil',
               'deconnection' => 'deconnection');

$publique = array('les parties' => 'Les parties',
                  'les joueurs' => 'Les joueurs',
                  'statistiques' => 'Statistiques');

?>
<header>
    <div class="petitecran">
        <img src="monlogo.gif" />
    </div>
    <div class="petitecran">
        <div id="messagebienvenue">
        Bienvenue 
        <?php echo $pseudo ?>
        </div>
        <div id="messages"></div>
        </div>
    </div>
</header>
<br />
<div class="petitecran">
    <nav>
        <div class="espace">
        Espace membre
        </div>
            <ul id="espacepublic">
                <?php
                foreach ($prive as $key => $value):
                ?>
                    <li><a href="?action=<?php echo $key ?>"><?php echo $value ?></a></li>
                <?php
                endforeach;
                ?>
            </ul>
            <br />
        <div class="espace">
        Espace publique
        </div>
            <ul id="espacepublic">
                <?php
                foreach ($publique as $key => $value):
                ?>
                    <li><a href="?action=<?php echo $key ?>"><?php echo $value ?></a></li>
                <?php
                endforeach;
            ?>
            </ul>
        <form>
    
            <?php
            if (isset($_GET['action']))
                $retour = $_GET['action'];
            else
            {
                ?>
                </nav>
                <section>
                <?php
                $retour = '';
                include './vues/acceuil.php';
            }
                
            switch ($retour)
            {
                case 'mes parties':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $changementB = 0;
                    $changementN = 0;
                    $uidActif = $_SESSION['uid'];
                    $managerPartieproposee = new PartieproposeeManager;

                    $mespartiesproposees = $managerPartieproposee->getListpppersonnelles($uidActif);
                    if (count($mespartiesproposees) > 0)
                        include './vues/partiesproposeesamoi.php';
                    else
                    {
                        if (isset($_GET['gid']))
                            $nopartie = $_GET['gid'];
                        if (isset($_GET['chb']))
                            $changementB = $_GET['chb'];
                        if (isset($_GET['chn']))
                            $changementN = $_GET['chn'];
                        if (isset($_GET['nulle']))
                            $nulle = $_GET['nulle'];
                    
                        $managerParties = new PartieManager;

                        if (isset($_GET['lecoup']))
                        {
                            if (isset($nulle))
                                $managerParties->indiquernulle($nopartie);
                                
                            $managerParties->jouer($nopartie,$_GET['lecoup'],$changementB,$changementN);
                        }
                    
                        $mesparties = $managerParties->setListePartiesActif($uidActif);
                        $mesparties = $managerParties->getListepartiesActif();
                        
                        include './vues/mesparties.php';
                    }
                    break;
                case 'terminer partie':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $gid = $_GET['gid'];
                    $nopartie = $gid;

                    $parties = new PartieManager;
                    $parties->terminerPartie($uidActif,$nopartie);
                    break;
                case 'parties proposées':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $managerPartieproposee = new PartieproposeeManager;
                    $lespartiesproposees = $managerPartieproposee->getList($uidActif);
                    include './vues/partiesproposees.php';
                    break;
                case 'mes parties proposées':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $uidActif = $_SESSION['uid'];
    
                    $managerPartieproposee = new PartieproposeeManager;
                    $mespartiesproposees = $managerPartieproposee->getListmespp($uidActif);

                    include './vues/mespartiesproposees.php';
                    break;
                case 'proposer partie':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $proposeur = 0;
                    if (isset($_GET['proposeur']))
                        $proposeur = $_GET['proposeur'];
                    if (isset($_GET['color']))
                        $color = $_GET['color'];
                    if (isset($_GET['cadence']))
                        $cadence = $_GET['cadence'];
                    if (isset($_GET['reserve']))
                        $reserve = $_GET['reserve'];
                    if (isset($_GET['commentaire']))
                        $commentaire = $_GET['commentaire'];
                                    
                    $partiesproposees = new PartieproposeeManager;
                    $joueur = new JoueurManager;
                    $uid = $joueur->exists($proposeur);
                    
                    if (isset($color))
                        $partiesproposees->add($uid,$uidActif,$color,$cadence,$reserve,$commentaire);

                    $partiesproposees->proposerpartie();
                    break;
                case 'mes statistiques':
                    ?>
                    </nav>
                    <section>                    
                    <?php
                    $managerParties = new PartieManager;
                    $joueur = $managerParties->trouveJoueur($uidActif);
                    $managerStatistiques = new StatistiqueManager;
                    $lesstatistiques = $managerStatistiques->getList();
                    $statistique = $managerStatistiques->get($uidActif);
                    $gainsblancs = $statistique->gains_b();
                    $pertesblancs = $statistique->pertes_b();
                    $nullesblancs = $statistique->nulles_b();
                    $partiesavecblancs = $managerParties->countpartiesblancs($uidActif);
                    $partiesavecnoirs = $managerParties->countpartiesnoirs($uidActif);
                    $partiesencours = $partiesavecblancs+$partiesavecnoirs;
                    
                    if ($partiesencours < 2)
                        $message_pour_parties = 'partie en cours';
                    else
                        $message_pour_parties = 'parties en cours';
                    if ($statistique->partiestotales() < 2)
                        $message_parties_jouees = ' partie terminée ';
                    else
                        $message_parties_jouees = ' parties terminées ';
                        
                    include './vues/messtatistiques.php';
                    break;
                case 'deconnection':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $managerConnections = new ConnectionManager;
                    $connection = $managerConnections->quitter($uidActif);
                    break;
                case 'les parties':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $managerParties = new PartieManager;
                    $lesparties = $managerParties->setListeParties();
                    $lesparties = $managerParties->getListeParties();
                    include './vues/lesparties.php';
                    
                    break;
                case 'les joueurs':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $managerJoueurs = new JoueurManager;
                    
                    $lesjoueurs = $managerJoueurs->getList();
                    include_once './vues/lesjoueurs.php';
                    break;
                case 'statistiques':
                    ?>
                    </nav>
                    <section>
                    <?php
                    
                    $statistiques = new StatistiqueManager;
                    $nbpartiesjouees = $statistiques->nbpartiesjouees();
                    $managerJoueurs = new JoueurManager;
                    $managerParties = new PartieManager;
                    $joueursinscrits = $managerJoueurs->countinscrits();
                    $joueursconnectes = $managerJoueurs->countenligne();
                    $joueursrecemmentconnectes = $managerJoueurs->countrecemmentconecte();
                    $partiesencours = $managerParties->countencours();

                    if ($joueursrecemmentconnectes > 1)
                        $phrase_joueur_connecte = " joueurs se sont connectés ";
                    else
                        $phrase_joueur_connecte = " joueur s'est connecté ";
                    if ($joueursconnectes > 1)
                        $phrase_joueur_en_ligne = ' joueurs en ligne.';
                    else
                        $phrase_joueur_en_ligne = ' joueur en ligne.';
                    if ($partiesencours > 1)
                        $messagenbparties = " parties ";
                    else
                        $messagenbparties = " partie ";
                    
                    include_once './vues/statistiques.php';
                        break;
                        
                case 'montrer partie':
                    ?>
                    </nav>
                    <section>
                    <div class="titreveritable">
                    
                    </div>
                    <?php
                    if (isset($_GET['but']))
                        $abandon = $_GET['but'];
                    if (isset($_GET['nulle']))
                        $lanulle = $_GET['nulle'];
                    $gid = $_GET['gid'];
                    $nopartie = $gid;
                    
                    $parties = new PartieManager;
                    $lapartie = $parties->get($nopartie);
                    
                    if (isset($lanulle))
                        $parties->annulernulle($nopartie);

                    if ($lapartie->cliquable() == true)
                        if ($parties->verifiernulle($nopartie))
                        {
                            ?>
                            <div id="overlayid" style="position: absolute; z-index: 10; border: 1px solid #000; background-color: rgb(255, 250, 198); display: none;"></div>
                            
                            
                             language="javascript">
                                annulerConfirm(<?php echo $gid ?>, 1);
                            </script>
                           <?php
                        }
                        
                    if (isset($abandon))
                        $parties->abandonner($uidActif,$nopartie);
                    
                    $parties->dessinerEchiquier($lapartie);              
                    break;
                case 'rejouer':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $gid = $_GET['gid'];
                    $_SESSION['nopartie'] = $gid;
                    $option = 'rejouer';
                    
                    $cliquable = 0;
                    $usercolor = 1;
                    
                    $parties = new  PartieManager;
                    $partie = $parties->get($gid);
                    
                    $ign = $parties->Ign();
                    $uidb = $partie->uidb();
                    $finalisation = $partie->finalisation();
                    $qtecoups = $partie->getNbCoups();
                    $flip = $partie->getFlipBase();
                    if ($finalisation != 'Partie non terminée')
                        $option = 'parties terminees';
                                                            
                    $parties->visualiser($partie,$qtecoups,$flip,$ign,$option);
                    break;
                case 'debut':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $gid = $_GET['id'];
                    $flip = $_GET['f'];
                    
                    $option = 'rejouer';
                    $cliquable = 0;
                    $usercolor = 1;

                    $parties = new PartieManager;
                    $partie = $parties->get($gid);
                    
                    $uidb = $partie->uidb();
                    $finalisation = $partie->finalisation();
                    $totalcoups = $partie->getNbCoups();
                    
                    $ign = '';                    
                    $qtecoups = 0;
                    
                    if ($finalisation != 'Partie non terminée')
                        $option = 'parties terminees';
                    
                    $parties->visualiser($partie,$qtecoups,$flip,$ign,$option);
                    break;

                case 'precedent':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $id = $_GET['id'];
                    $flip = $_GET['f'];
                    $choix = $_GET['t'];

                    $option = 'rejouer';
                    $cliquable = 0;
                    $usercolor = 1;

                    $parties = new PartieManager;
                    $partie = $parties->get($id);
                    
                    $ign = $partie->getIgn();
                    $uidb = $partie->uidb();
                    $finalisation = $partie->finalisation();
                    
                    $choix--;
                    
                    if ($choix <= 0)
                    {
                        $choix = 0;
                        $ign = "";
                    }
                    
                    if ($finalisation != 'Partie non terminée')
                        $option = 'parties terminees';
                                       
                    $parties->visualiser($partie,$choix,$flip,$ign,$option);
                    break;
                case 'suivant':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $id = $_GET['id'];
                    $flip = $_GET['f'];
                    $choix = $_GET['t'];

                    $option = 'rejouer';
                    $cliquable = 0;
                    $usercolor = 1;

                    $parties = new PartieManager;
                    $partie = $parties->get($id);
                    
                    $ign = $partie->getIgn();
                    $uidb = $partie->uidb();
                    $finalisation = $partie->finalisation();
                    $totalcoups = $partie->getNbCoups();

                    $choix++;
                    
                    if ($choix >= $totalcoups)
                        $choix = $totalcoups;
                        
                    if ($finalisation != 'Partie non terminée')
                        $option = 'parties terminees';
                                        
                    $parties->visualiser($partie,$choix,$flip,$ign,$option);
                    break;
                case 'dernier':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $gid = $_GET['id'];
                    $flip = $_GET['f'];
                    
                    $option = 'rejouer';
                    $cliquable = 0;
                    $usercolor = 1;

                    $parties = new PartieManager;
                    $partie = $parties->get($gid);
                    
                    $ign = $partie->getIgn();
                    $uidb = $partie->uidb();
                    $finalisation = $partie->finalisation();
                    $qtecoups = $partie->getNbCoups();
                    
                    if ($finalisation != 'Partie non terminée')
                        $option = 'parties terminees';
                                            
                    $parties->visualiser($partie,$qtecoups,$flip,$ign,$option);
                    break;
                case 'tourner':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $id = $_GET['id'];
                    $flip = $_GET['f'];
                    $choix = $_GET['t'];
                    
                    $option = 'rejouer';
                    $cliquable = 0;
                    $usercolor = 1;

                    $parties = new PartieManager;
                    $partie = $parties->get($id);
                    
                    $ign = $partie->getIgn();
                    $uidb = $partie->uidb();
                    $finalisation = $partie->finalisation();

                    if($flip == 0)
                        $flip = 1;
                    else
                        $flip = 0;

                    if ($choix <= 0)
                    {
                        $choix = 0;
                        $ign = "";
                    }
                    
                    if ($finalisation != 'Partie non terminée')
                        $option = 'parties terminees';
                                        
                    $parties->visualiser($partie,$choix,$flip,$ign,$option);
                    break;
                case 'traiter partie':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $but = $_GET['but'];
                    $nopartie = $_GET['id'];
                    if ($but == 'accepter')
                    {
                        $managerParties = new PartieManager;
                        $lesparties = $managerParties->acepter($nopartie,$uidActif);
                    }
                    if ($but == 'refuser')
                    {
                        $managerParties = new PartieproposeeManager;
                        $lesparties = $managerParties->refuser($nopartie,$but);
                    }
                    if ($but == 'effacer')
                    {
                        $managerParties = new PartieproposeeManager;
                        $lesparties = $managerParties->refuser($nopartie,$but);
                    }
                    break;

                case 'parties terminées':
                    ?>
                    </nav>
                    <section>
                    <?php
                    $managerParties = new PartieManager;
                    $lesparties = $managerParties->getListPartiesterminees($uidActif);
                    
                    include './vues/mespartiesterminees.php';
                    break;
                case 'traiter abandon':
                    $nopartie = $_GET['gid'];
                    $managerParties = new PartieManager;
                    $managerParties->nulle($nopartie);
                    break;
                case 'effacer partie':
                    $nopartie = $_GET['no'];
                    $managerParties = new PartieManager;
                    $managerParties->effacerpartie($nopartie);
                    break;
                case 'profil':
                    ?>
                    </nav>
                    <section>
                    <div class="titreveritable">
                        Votre profil
                    </div>
                    <?php
                    if (isset($_GET['pw1']))
                        $pw1 = $_GET['pw1'];
                    if (isset($_GET['pw2']))
                        $pw2 = $_GET['pw2'];
                    
                    $joueurs = new JoueurManager;
                    if (isset($pw1) & isset($pw2))
                    {
                        $joueurs->traiterpassword($pw1,$pw2);
                    }
                    
                    $joueurs->motdepasse($uidActif);
                    break;
                case 'usager':
                    ?>
                    </nav>
                    <section>
                    <div class="titreveritable">
                        Votre profil
                    </div>
                    <?php
                    
                    $sexe = null;
                    if (isset($_GET['sexe']))
                        $sexe = $_GET['sexe'];
                    if (isset($_GET['pays']))
                        $pays = $_GET['pays'];
                    if (isset($_GET['jour']))
                        $jour = $_GET['jour'];
                    if (isset($_GET['mois']))
                        $mois = $_GET['mois'];
                    if (isset($_GET['annee']))
                        $annee = $_GET['annee'];
                    if (isset($_GET['naissance']))
                        $naissance = $_GET['naissance'];
                    if (isset($_GET['description']))
                        $description = $_GET['description'];
                    if (isset($_GET['photo']))
                        $photo = $_GET['photo'];
                        
                    $uidActif = $_SESSION['uid'];
                    $joueurs = new JoueurManager;
                    $joueurs->revisioninfos($uidActif);
                    if (isset($sexe))
                    {
                        $naissance = $annee.'-'.$mois.'-'.$jour;
                        $joueurs->traiterinfos($sexe,$pays,$naissance,$description);
                        ?>
                        <script type="text/javascript">
                            window.location.assign('index.php?action=les joueurs');
                            exit();
                        </script>
                        <?php
                    }
                    
                    break;
            }
            ?>
            </form>
</div>
