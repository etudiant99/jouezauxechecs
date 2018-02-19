<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />  <!-- sert entre autre à avoir les accents -->
    
    <title>Jouer aux échecs</title>
    
    <link rel="stylesheet" href="./css/monmeilleur.css" type="text/css" />
    <link rel="stylesheet" href="./css/mon.css" type="text/css" />

	<link rel="stylesheet" href="styles/demo.css" type="text/css" media="all" />    
	<link rel="stylesheet" href="../jquery.superbox.css" type="text/css" media="all" />
        
	<style type="text/css">
		/* Custom Theme */
		#superbox-overlay{background:#e0e4cc;}
		#superbox-container .loading{width:32px;height:32px;margin:0 auto;text-indent:-9999px;background:url(styles/loader.gif) no-repeat 0 0;}
		#superbox .close a{float:right;padding:0 5px;line-height:20px;background:#333;cursor:pointer;}
		#superbox .close a span{color:#fff;}
		#superbox .nextprev a{float:left;margin-right:5px;padding:0 5px;line-height:20px;background:#333;cursor:pointer;color:#fff;}
		#superbox .nextprev .disabled{background:#ccc;cursor:default;}
	</style>
    
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script type="text/javascript" src="../jquery.superbox-min.js"></script>
    <script type="text/javascript" src="../librairie.js"></script>
    
	<script type="text/javascript">
		$(function(){
			$.superbox.settings = {
                boxWidth: "700", // Largeur par défaut de la box
				closeTxt: "Fermer",
				loadTxt: "Chargement...",
				nextTxt: "Suivant",
				prevTxt: "Précédent"
			};
			$.superbox();
		});
	</script>

</head>


<?php
// Initialisation

function chargerClasse($classname)
{
    require './classes/'.$classname.'.class.php';
}

spl_autoload_register('chargerClasse');
session_start();

include_once ("./include/config.php");

include 'controleur.php';
?>