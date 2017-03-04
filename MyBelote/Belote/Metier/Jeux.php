<?php
namespace Belote\Metier;


use Belote\Erreur\JeuxException;

use STANDARD\Logger\Logger;

use \SplObjectStorage;

class Jeux {

	const VERSION = "0.1";
	
	const NBCARTES = 32;
	const NBCARTE_COULEUR = 8;
	
	const NBCARTEMAIN = 5;
	
	const NBJOUEURS = 4;
	
	
	
	
	private $couleurs = array(Carte::PIQUES,Carte::CARREAUX,Carte::COEUR,Carte::TREFLES);
	private $ordreAtout = array(Carte::cVALET=> 8,Carte::cNEUF=> 7,Carte::cAS=>6,Carte::cDIX=>5,Carte::cROI=>4,Carte::cDAME=>3,Carte::cHUIT=>2,Carte::cSEPT=>1);
	
	private $points = array(Carte::cVALET=> 2,Carte::cNEUF=> 0,Carte::cAS=>11,Carte::cDIX=>10,Carte::cROI=>4,Carte::cDAME=>3,Carte::cHUIT=>0,Carte::cSEPT=>0);
	private $pointsAtout = array(Carte::cVALET=> 20,Carte::cNEUF=> 14,Carte::cAS=>11,Carte::cDIX=>10,Carte::cROI=>4,Carte::cDAME=>3,Carte::cHUIT=>0,Carte::cSEPT=>0);
	
	private $jeux = null;
	private $jeuxOrigine = null;
	
	private $logger ;
	
	private $joueurs = array();
	
	private $paquetNeuf = true;
	
	public $atout= null;
	private $couleurAtout = null;
	
	private $donneur = null;
	
	private $preneur = null;
	
	private $refusAtout = 0;
	
	private $pli = array();
	
	private $parole ;
	
	/**
	 * @var array
	 * @description $table permet de connaitre la position des joueurs sur la table
	 * sous forme de matrice à une dimension
	 */
	private $table = array();
	private $tableFantome = array();
	
	public function __construct($logger=null){
		$this->initialize();
		if(empty($logger)){
			$this->logger = new Logger();
		}
		$this->table = array();
	}
	
	private function initialize(){
		//genere le jeux de carte propre neuf
		$this->reset();
		if($this->jeux->count() !== self::NBCARTES){
			$this->logger->err('Nombre de cartes incohérentes.. '.$this->jeux->count() ." pour ".self::NBCARTES." demandées");
		}		
		//initialize les joueurs
		$this->initializeJoueurs();
	}
	
	private function initializeJoueurs(){
		$this->joueurs = new SplObjectStorage();
// 		for($i=0;$i< self::NBJOUEURS;$i++){
// 			$this->joueurs->attach(new Joueur("Joueur_".($i+1)));
// 		}
	}
	
	
	public function getPointsCarte($valeur,$isAtout){
		$points = $this->points[$valeur];
		if($isAtout){
			$poins = $this->pointsAtout[$valeur];
		}
		
		return $points;
	}
	
	
	public function getJeux(){
		return $this->jeux;
		
	}
	
	public function getJoueurs($idcnx = null){
		$liste = $this->joueurs;
		if(is_null($idcnx)){
			$liste = $this->joueurs;
		}else{
			$this->joueurs->rewind();
// 			for($i=0;$i< self::NBJOUEURS;$i++){
			while($this->joueurs->valid()){
				$joueur = $this->joueurs->current();
				if(!is_null($joueur) && $joueur instanceof Joueur){
					if($joueur->getIdCNX() == $idcnx){
						$liste = $joueur;
					}
				}
				$this->joueurs->next();
			}
		}
		
		return $liste;
	}
	
	public function distribution($nbMelange = 1,$coupe = 1,$tour=3){
		//controle coherence des joueurs 
		if(count($this->table) == self::NBJOUEURS){
			//affecter les cartes au joueurs
			$pile = $this->getJeux();
			$joueurs = $this->table;
			//on a 32 cartes c est le demarrage
			if($pile->count() == self::NBCARTES){
				//prendre aleatoirement des cartes dans une pile
				//melange le jeux
				if($this->paquetNeuf){
					$this->melange($nbMelange);
					$pile = $this->getJeux();
				}
				//toujours faire
				$this->coupeJeu($coupe);
				
				
				$this->jeuxOrigine = $pile;
				
				//pour etre sur
				$pile->rewind();
		
				$continue =  true;
				while($pile->valid() && $continue){
					//valable pour le premier tour
					foreach ($joueurs as $joueur){
						if($joueur->nbCartes() == 0){
							self::distribueJoueurCarte($pile,$tour, $joueur);
						}
					}
					//on distribue le reste 2+3 ou 3+2
					foreach ($joueurs as $joueur){
						if($joueur->nbCartes() < self::NBCARTEMAIN){
							self::distribueJoueurCarte($pile,self::NBCARTEMAIN, $joueur);
						}
					}
					$continue = false;
				}
				$this->getCarteAtout($pile);
				$this->paquetNeuf = false;
				$this->jeux = $pile;
			}else{
				//la carte atout à ete valide on continue 
				if(!empty($this->preneur)){
// 					echo "distri le reste";
					$this->listeContenuPaquet($pile);
					//on distribue le reste 
					foreach ($joueurs as $joueur){
						if($joueur->nbCartes() < self::NBCARTE_COULEUR){
							self::distribueJoueurCarte($pile,self::NBCARTE_COULEUR, $joueur);
						}
					}
				}else{
					//on change le donneur et permet de ditribue a nouveau sans annule la partie.
					$this->jeux = $this->jeuxOrigine;
					$this->changerDonneur();
					
				}
			}
		}else{
			throw new JeuxException('Vous n\'êtes pas le nombre de joueurs suffisant.');
		}
		
	}
	
	public function getPreneur(){
		return  $this->preneur;
	}
	
	public function reset(){
		$this->jeux = new SplObjectStorage();
		for($couleur=1;$couleur<5;$couleur++){
			for($valeur=1;$valeur<=self::NBCARTE_COULEUR;$valeur++){
				$this->jeux->attach(new Carte($valeur, $couleur));
			}
		}
		
		$this->pli = array();
		$this->donneur = null;
		$this->parole = null;
		$this->refusAtout = 0;
		$this->preneur = null;
		$this->atout = null;
		
		$this->definirDonneur();
	}
	
	
	public function getPli(){
		return $this->pli;
	}
	
	private function definirDonneur(){
		if(is_null($this->donneur) && count($this->table) == self::NBJOUEURS){
			$zone = rand(0,3);
			$this->donneur = $this->table[$zone];
			$this->parole = $this->donneur->getSuivant();
		}
	}
	
	public function getParole(){
		return $this->table[$this->parole];
	} 
	
		
	private function changerDonneur(){
		$position = $this->donneur->getSuivant();
		$this->donneur = $this->table[$position];
		$this->refusAtout = 0;
		$this->preneur = null;
		$this->atout = null;
	}
	
	
	public function getCarteAtout($paquet=null){
		if(empty($this->atout) && !is_null($paquet)){
			$carte = $paquet->current();
			$paquet->detach($carte);
			
			$this->atout = $carte;
		}
		return $this->atout;
		
	}
	
	private function coupeJeu($coupe){
		$i = 0;
		$paquet = $this->jeuToArray();
		while($i < $coupe){
			$zone = rand(0,31);
			$tmp1 = array_slice($paquet, $zone);
			$tmp2 = array_slice($paquet, 0,$zone);
			$paquet = array_merge($tmp1,$tmp2);
			
			$i++;
		}
		
		$this->arrayToJeu($paquet);
		
// 		$this->listeContenuPaquet();
		
	}
	
	
	/**
	 * 
	 * @param SplObjectStorage $pile cartes a jouer
	 * @param int $nb nombre de carte que doit avoir le joueur
	 * @param Joueur $joueur
	 */
	private static function distribueJoueurCarte(&$pile,$nb,Joueur $joueur){
		$pile->rewind();
		while($joueur->nbCartes() < $nb){
			$carte = $pile->current();
			$joueur->ajoutCarte($carte);
			$pile->next();
			$pile->detach($carte);
		}
	}
	
	private function melange($nbMelange){
		$listetmp = $this->jeuToArray();
		
		$i = 0;
		while($i < $nbMelange){
			if(!shuffle($listetmp)){
				$this->logger->err('melange impossible du jeux recommencez');
			}
			$i++;
		}

		//reaffecte le jeux
		$this->arrayToJeu($listetmp);
	}
	
	private function jeuToArray(){
		$listetmp = array();
		$pile = $this->getJeux();
		$pile->rewind();
		while($pile->valid()){
			$carte = $pile->current();
			array_push($listetmp, $carte);
			$pile->next();
		}
		return $listetmp;
	}
	
	private function arrayToJeu($listetmp){
		$this->jeux = new SplObjectStorage();
		foreach ($listetmp as $carte){
			$this->jeux->attach($carte);
		}
	}
	
	
	public function listeContenuPaquet($pile=null){
		if(empty($pile)){
			$pile = $this->getJeux();
		}
		$pile->rewind();
		$compteur = 1;
		while($pile->valid()){
			$carte = $pile->current();
// 			echo $compteur."#". $carte ."<br>";
			$pile->next();
			$compteur++;
		}
	}
	
	
	public function ajouterJoueur($pseudo,$idCNX){
		$joueur = new Joueur($pseudo);
		$joueur->setIdCNX($idCNX);
		$this->joueurs->attach($joueur);
		
		$this->setPosition($joueur);
		return $joueur->getToken(); 
	}
	
	public function retirerJoueur($idCNX){
		$joueur = $this->getJoueurs($idCNX);
		if(count($this->table)>0 && $joueur instanceof Joueur){
			$position = $joueur->getPosition();
			if(!is_null($position)){
				$this->tableFantome[$position] = $this->table[$position];
				unset($this->table[$position]);
			}
		}
		if($joueur instanceof Joueur){
			$this->joueurs->detach($joueur);
		}
		
	}
	
	public function setPosition(Joueur $joueur , $equipe = null){
		//1 retire le joueur si present
		if(count($this->table)>0){
			$position = $joueur->getPosition();
			if(!is_null($position)){
				$joueurPrec = $this->table[$position];
				foreach ($joueurPrec->main() as $carte){
					$joueur->ajoutCarte($carte);
				}
				unset($this->table[$position]);
				$joueur->setPosition(null);
				//redonne la meme main
			}
		}
		//2 recherche premier emplacement vide pour le joueur de l equipe
		if(count($this->table) <= self::NBJOUEURS){
			$position = null;
			if(is_null($equipe)){
				//affectation a la premiere place libre de la table
				$position = 0;
				while($position< self::NBJOUEURS && array_key_exists($position,$this->table)){
					$position++;
				}
				
			}else{
// 				$this->logger->info('Reception Equipe :'.$equipe);
				//validation suivant l equipe sinon on le retire de l equipe
				//equipe 1 => 0;
				//equipe 2 => 1;
				$position = $equipe%2;
				while($position< self::NBJOUEURS && array_key_exists($position,$this->table)){
					$position += 2;
				}
			}
			if(!is_null($position) && $position < self::NBJOUEURS){
				//3 affecter joueur a la table ainsi trouve
				$this->table[$position] = $joueur;
				$joueur->setPosition($position);
				//si fantome existe lui redonner les cartes
				if(array_key_exists($position, $this->tableFantome) && $this->tableFantome[$position] instanceof Joueur){
					$joueurPrec = $this->tableFantome[$position];
					foreach ($joueurPrec->main() as $carte){
						$joueur->ajoutCarte($carte);
					}
					unset($this->tableFantome[$position]);
				}
			}else{
				$joueur->setPosition(null);
			}
			
		}else{
			//on force a null la position pour etre sur
			$joueur->setPosition(null);
		}
		
		
		
		//si ils sont quatre definir Donneur
		//si donneur existe ne rien faire
		$this->definirDonneur();
		
		//attention le donneur existe mais il est parti redefinir le donneur
		if($this->donneur instanceof  Joueur){
			$position = $this->donneur->getPosition();
			$trouve = false;
			foreach ($this->table as $joueur){
				if($joueur->getIdCNX() == $this->donneur->getIdCNX()){
					$trouve = true;
				}
			}
			if(!$trouve){
				$this->donneur = $joueur;
				$this->table[$position] = $this->donneur;
			}
		} 
		
		
		
	}
	
	
	public function nbJoueurActif(){
		return count($this->table);
	}
	
	public function getDonneur(){
		return $this->donneur;
	}
	
	public function priseAtout(Joueur $joueur,$accepte ,$couleur = null){
		if(is_null($this->preneur)){
			if($this->refusAtout >= self::NBJOUEURS){
				var_dump($couleur);
				if($accepte && !empty($couleur)){
					$joueur->ajoutCarte($this->atout);
					$this->couleurAtout =  $couleur;
					$this->preneur = $joueur;
	// 				$this->log('Preneur :'.$joueur .' à '.$this->couleurAtout);
				}else{
					if($accepte){
						if(empty($couleur)){
							throw new JeuxException("Erreur lors de la prise pas de couleur demandé.");
						}
					}
					$this->refusAtout++;
					$this->setParole($joueur->getSuivant());
				}
			}else {
				if($accepte){
					if(!empty($this->atout)){
						$joueur->couleurAtout = $this->atout->getCouleur();
						$joueur->ajoutCarte($this->atout);
						$this->preneur = $joueur;
	// 					$this->log('Preneur :'.$joueur);
					}else{
						throw new JeuxException("Erreur lors de la prise pas d atout retourné.");
					}
					
				}else{
					$this->refusAtout++;
					$this->setParole($joueur->getSuivant());
				}
			}
		}else{
			throw new JeuxException("Il existe un preneur...");
		}
		
		
		//si on a un preneur on distribue le reste de cartes.
		if(!is_null($this->preneur)){
			$this->distribution();
		}
	}
	
	
	private function setParole($position){
		if(is_int($position)){
			$this->parole = $position;
		}
	}
	
	public function getRefusAtout(){
		return $this->refusAtout;
	}
	
}

?>