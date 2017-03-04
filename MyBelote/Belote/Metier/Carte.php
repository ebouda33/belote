<?php
namespace Belote\Metier;

use STANDARD\Logger\LogWriterWeb;

use STANDARD\Logger\Logger;

class Carte {

	private $valeur;
	private $couleur;
	private $etat;
	
	const cAS = 8;
	const cROI = 7;
	const cDAME = 6;
	const cVALET = 5;
	const cDIX = 4;
	const cNEUF = 3;
	const cHUIT = 2;
	const cSEPT = 1;
	
	const PIQUES = 1;
	const CARREAUX = 2;
	const COEUR = 3;
	const TREFLES = 4;
	
	private static $STR_COULEUR= array(0=>"",1=>"Piques",2=>"Carreaux",3=>"Coeur",4=>"Trèfles");
	
	private $logger ;
	public function __construct($valeur,$couleur){
		$this->setValeur($valeur);
		$this->setCouleur($couleur);
		$this->setEtat(true);
		
		$this->logger = new Logger();
		
	}
	/**
	 * @return the $valeur
	 */
	public function getValeur() {
		return $this->valeur;
	}

	/**
	 * @return the $couleur
	 */
	public function getCouleur() {
		return $this->couleur;
	}

	/**
	 * @return the $etat
	 */
	public function getEtat() {
		return $this->etat;
	}

	/**
	 * @param field_type $valeur
	 */
	public function setValeur($valeur) {
		$this->valeur = $valeur;
	}

	/**
	 * @param field_type $couleur
	 */
	public function setCouleur($couleur) {
		$this->couleur = $couleur;
	}

	/**
	 * @param field_type $etat
	 */
	public function setEtat($etat) {
		$this->etat = $etat;
	}
	
	
	public function __toString(){
		return MessageString::getNomCarte($this->getValeur()) ." de ".MessageString::getCouleurCarte($this->getCouleur());
	}
	
	public static function getSTR_COULEUR(){
		return self::$STR_COULEUR;
	}

	
	
}

?>