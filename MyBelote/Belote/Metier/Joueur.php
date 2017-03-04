<?php
namespace Belote\Metier;

class Joueur {

	private $pseudo;
	
	private $cartes = array();

	private $token;
	
	private $idCNX;
	
	private $joue;
	
	private $position;
	
	private $nextPosition;
	
	/**
	 * @return the $idCNX
	 */
	public function getIdCNX() {
		return $this->idCNX;
	}

	/**
	 * @param field_type $idCNX
	 */
	public function setIdCNX($idCNX) {
		$this->idCNX = $idCNX;
	}

	public function __construct($pseudo = "joueur"){
		$this->pseudo = $pseudo;
		$this->token = md5(uniqid(rand()));
		$this->joue = false;
		$this->position = null;
	}
	
	
	public function ajoutCarte(Carte $carte){
		array_push($this->cartes, $carte);
	}
	
	public function nbCartes(){
		return count($this->cartes);
	}
	
	public function __toString(){
		return "Je suis le joueur ".$this->token ." sous le pseudo ".$this->pseudo;
	}
	
	public function main(){
		return $this->cartes;
	}
	/**
	 * @return the $pseudo
	 */
	public function getPseudo() {
		return $this->pseudo;
	}

	/**
	 * @return the $token
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * @param field_type $pseudo
	 */
	public function setPseudo($pseudo) {
		$this->pseudo = $pseudo;
	}

	/**
	 * @param string $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}

	public function doitJouer(){
		$this->joue;
	}
	
	public function aJouer(){
		$this->joue = true;
	}
	
	public function getPosition(){
		return $this->position;
	}
	
	public function setPosition($position){
		if($position >= IA::NORTH && $position <= IA::EAST ){
			$this->position = $position;
			$this->setSuivant();
		}
		
	}
	
	public function setSuivant(){
		$position = $this->position;
		$position++;
		if($position >= Jeux::NBJOUEURS ){
			$position = 0;
		}
		$this->nextPosition = $position;
	}
	
	public function getSuivant(){
		return $this->nextPosition;
	}
	
}

?>