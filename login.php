<?php
session_start();

include_once "./echecs/include/config.php";
include_once "./echecs/classes/ConnectionManager.class.php";
include_once "./echecs/classes/Connection.class.php";

$managerconnections = new ConnectionManager;

$pseudo = $_SESSION['pseudo'];
$usager = $managerconnections->get($pseudo);

$uid = $usager->uid();
$elo = $usager->elo();
$_SESSION['uid'] = $uid;
$_SESSION['elo'] = $elo;

$managerconnections->add($uid);
header('Location: ./echecs/index.php');
?>