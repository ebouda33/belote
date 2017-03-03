<?php

namespace Belote;
/** test intelligence carte **/

use Belote\Erreur\JeuxException;

use Belote\Metier\Joueur;

use STANDARD\Logger\Logger;

use Belote\Metier\Carte;
use Belote\Metier\Jeux;

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Autoloader.php';

//definition des namespaces 
$config = array("ns"=>array(
		"Belote"=>dirname(__FILE__).DIRECTORY_SEPARATOR,
		"STANDARD"=>dirname(__FILE__).DIRECTORY_SEPARATOR.'Belote'.DIRECTORY_SEPARATOR
		));
\Autoloader::register($config);

// $carte = new Carte(Jeux::cROI,Jeux::CARREAUX);

// echo $carte;
$log = new Logger();


$jeux = new Jeux();



$joueurs = $jeux->getJoueurs();


// $jeux->listeContenuPaquet();


/*
//liste joueur
$log->debug("Liste joueurs");
$joueurs->rewind();
while($joueurs->valid()){
	$j = $joueurs->current();
	echo $j ."<br>";
	$joueurs->next();
}
*/

echo "<br>";



// $joueurs->rewind();
// $joueurCourant = null;
// while($joueurs->valid()){
// 	$j = $joueurs->current();
// 	$joueurCourant = $j;
// // 	echo $j."<br>";
// 	foreach($j->main() as $carte){
// 		$log->debug($carte);
// 	}
// 	$joueurs->next();
// }


//carte d atout
// $carte = $jeux->getCarteAtout();
// $log->notice("Atout ".$carte);

//carte restante
// $pile = $jeux->getJeux();
// $log->info("Cartes restantes".$pile->count());
// while($pile->valid()){
// 	$carte = $pile->current();
// 	echo $carte ."<br>";
// 	$pile->next();
// }


$jeux->ajouterJoueur("nabou", 000);
$joueurCourant = $jeux->getJoueurs(000);
$jeux->setPosition($joueurCourant,0);

echo "#######<br>";

$jeux->setPosition($joueurCourant,1);

echo "#######<br>";

$jeux->setPosition($joueurCourant,0);

$jeux->ajouterJoueur("toto", 001);
$joueurCourant = $jeux->getJoueurs(001);
$jeux->setPosition($joueurCourant,0);

$jeux->ajouterJoueur("toto2", 002);
$joueurCourant = $jeux->getJoueurs(002);
$jeux->setPosition($joueurCourant);

$jeux->ajouterJoueur("toto3", 003);
$joueurCourant = $jeux->getJoueurs(003);
$jeux->setPosition($joueurCourant,1);

$jeux->ajouterJoueur("toto4", 004);
$joueurCourant = $jeux->getJoueurs(004);
$jeux->setPosition($joueurCourant,1);
// var_dump($joueurCourant);

echo "le donneur est ".$jeux->getDonneur();
echo "<br>";

try{
	$jeux->distribution(1,1);
}catch (JeuxException $exc){
	echo $exc;
}

$joueurs->rewind();
$joueurCourant = null;
while($joueurs->valid()){
	$j = $joueurs->current();
// 	echo $j."<br>";
	
	$log->debug($j->getPseudo() .' position table '. $j->getPosition());
	foreach($j->main() as $carte){
		$log->debug($carte);
	}
	echo "#######<br>";
	$joueurs->next();
}


echo "Atout >".$jeux->getCarteAtout();
echo "<br>";


$joueur = $jeux->getJoueurs(0);

$jeux->priseAtout($joueur, false);

$joueur = $jeux->getJoueurs(1);
$jeux->priseAtout($joueur, false);

$joueur = $jeux->getJoueurs(2);
$jeux->priseAtout($joueur, false);

$joueur = $jeux->getJoueurs(3);
$jeux->priseAtout($joueur, true);


// echo $jeux->listeContenuPaquet();
$jeux->distribution();

$joueurs->rewind();
while($joueurs->valid()){
	$j = $joueurs->current();
	// 	echo $j."<br>";

	$log->debug($j->getPseudo() .' position table '. $j->getPosition());
	foreach($j->main() as $carte){
		$log->debug($carte);
	}
	echo "#######<br>";
	$joueurs->next();
}

echo "<br>";
echo $jeux->listeContenuPaquet();

// $jeux->distribution();


echo "<br>";
echo $jeux->listeContenuPaquet();

