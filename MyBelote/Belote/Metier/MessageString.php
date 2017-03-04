<?php
namespace Belote\Metier;

class MessageString {

	private static $strCouleur = array(
			Carte::CARREAUX => "CARREAUX",
			Carte::COEUR => "COEUR",
			Carte::TREFLES => "TREFLES",
			Carte::PIQUES => "PIQUES",
			);
	
	private static $strCarte = array(
			Carte::cAS => "As",
			Carte::cROI => "Roi",
			Carte::cDAME => "Dame",
			Carte::cVALET => "Valet",
			Carte::cDIX => "Dix",
			Carte::cNEUF => "Neuf",
			Carte::cHUIT => "Huit",
			Carte::cSEPT => "Sept"
			);
	
	
	public static function getNomCarte($valeur){
		
		return self::$strCarte[$valeur];
	}
	
	public static function getCouleurCarte($valeur){
	
		return self::$strCouleur[$valeur];
	}
}

?>