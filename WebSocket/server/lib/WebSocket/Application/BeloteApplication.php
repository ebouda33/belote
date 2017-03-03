<?php

namespace WebSocket\Application;

use Belote\Erreur\JeuxException;

use Belote\Metier\Joueur;

use Belote\Metier\Carte;

use Belote\Metier\Jeux;
/**
 * Websocket-Server Belote.
 * 
 * @author Eric Aboudaram <eric@aboudaram.net>
 */
class BeloteApplication extends Application
{
    private $_clients = array();
	private $_filename = '';
	private $jeux ;
	
	public function __construct(){
		//va lancer le service belote
		//construit le jeux et attendre les joueurs
		
		$this->jeux = new Jeux();
	}

	public function onConnect($client)
    {
		$id = $client->getClientId();
        $this->_clients[$id] = $client;	
        $banniere = "Vous êtes connecté sur le serveur de belote V".Jeux::VERSION;
//         $this->log($banniere);
        $this->__sendClient("banniere",$banniere,$client);
        
        
    }

    public function onDisconnect($client)
    {
        $id = $client->getClientId();	
        $joueur = $this->jeux->getJoueurs($id); 
        $this->jeux->retirerJoueur($id);	
		unset($this->_clients[$id]);     
		$this->sendAllClient("echoALL", array('message'=>$joueur->getPseudo()." a quitté la partie."));
    }

    public function onData($data, $client)
    {		
    	//utiliser le protocole de la belote
        $decodedData = $this->_decodeData($data);		
		if($decodedData === false)
		{
			// @todo: invalid request trigger error...
			$this->__actionEchoClient("Message incompris", $client);
			$this->__actionEchoClient($data, $client);
			return false;
		}
		
		$actionName = '_action' . ucfirst($decodedData['action']);	
// 		$actionName = '_actionEcho';	
		if(method_exists($this, $actionName))
		{			
			call_user_func(array($this, $actionName), $decodedData['data'],$client);
		}else{
			$this->__actionEchoClient("Action Protocol incomprise ".$decodedData['action'], $client);
		}
    }
	
	public function onBinaryData($data, $client)
	{		
		$filePath = substr(__FILE__, 0, strpos(__FILE__, 'server')) . 'tmp/';
		$putfileResult = false;
		if(!empty($this->_filename))
		{
			$putfileResult = file_put_contents($filePath.$this->_filename, $data);			
		}		
		if($putfileResult !== false)
		{
			
			$msg = 'File received. Saved: ' . $this->_filename;
		}
		else
		{
			$msg = 'Error receiving file.';
		}
		$client->send($this->_encodeData('echo', $msg));
		$this->_filename = '';
	}
	
	private function _actionOpen($data,$client){
		//controle pas plus de 4 joueurs
		if($this->jeux->getJoueurs()->count() < Jeux::NBJOUEURS){
			$this->__actionEchoClient("Serveur a reçu pseudo :".$data['pseudo'], $client);
			$token = $this->jeux->ajouterJoueur($data['pseudo'],$client->getClientId());
			$this->__actionEchoClient("Token :".$token, $client);
			$this->__actionEchoClient("Id CNX :".$client->getClientId(), $client);
			$this->__actionEchoClient(array("idCNX"=>$client->getClientId(),"token"=>$token), $client);
			$this->_actionSendListeJoueur();
			$this->sendAllClient("newPlayer", array("pseudo" => $data['pseudo']));
			$this->controleDepart();
		}else{
			$this->__actionEchoClient("Nombre Max de joueurs atteint ,prochaine version ", $client);
		}
	}
	
	
	private function _actionEcho($text)
	{		
		foreach($this->_clients as $client)
		{
			$this->__actionEchoClient($text, $client);
        }
	}
	
	
	private function __actionEchoClient($text,$client){
		$encodedData = $this->_encodeData('echo', $text);
		$client->send($encodedData);
	}
	
	private function __sendClient($action,$data,$client){
		$encodedData = $this->_encodeData($action, $data);
		$client->send($encodedData);
	} 
	
	private function _actionSetFilename($filename)
	{		
		if(strpos($filename, '\\') !== false)
		{
			$filename = substr($filename, strrpos($filename, '\\')+1);
		}
		elseif(strpos($filename, '/') !== false)
		{
			$filename = substr($filename, strrpos($filename, '/')+1);
		}		
		if(!empty($filename)) 
		{
			$this->_filename = $filename;
			return true;
		}
		return false;
	}
	
	
	private function _actionSendListeJoueur(){
		foreach($this->_clients as $client)
		{
			$this->_actionListeJoueur(null,$client);
		}
	}
	
	private function _actionListeJoueur($data,$client){
		$joueurs = $this->jeux->getJoueurs();
		$this->log("Demande de la liste de joueur");
		$liste = array();
		foreach ($joueurs as $joueur){
			array_push($liste, array("pseudo"=>$joueur->getPseudo(),"id"=>$joueur->getToken(),"position"=>$joueur->getPosition()));
		}
		$encodedData = $this->_encodeData('listeJoueur', $liste);
		
		$client->send($encodedData);
		
		
	}
	
	private function _actionJoue($data,$client){
		//prevenir tout le monde
		
		$carte = new Carte($data['ordre'], $data['couleur']);
		$id = $client->getClientId();
		$joueur = $this->jeux->getJoueurs($id);
		$joueur->aJouer();
		$this->sendAllClient('joue',array('idCNX'=>$client->getClientId(),'carte'=>$data,'string'=>$joueur->getPseudo()." joue " . $carte->__toString()));
	}
	
	private function sendClient($action,$data,$client){
		$encodedData = $this->_encodeData($action, $data);
		$client->send($encodedData);
	}
	
	
	private function sendAllClient($action,$data,$qui=null){
		$data['qui'] = $qui;
		foreach($this->_clients as $client)
		{
			$this->sendClient($action,$data,$client);
		}
	}
	
	private function _actionEquipe($data,$client){
		$joueur = $this->jeux->getJoueurs($client->getClientId());
// 		suivant equipe affecte la bonne position sur la table
		$this->jeux->setPosition($joueur,$data);
		//prevenir tout le monde de la position du joueur
		$this->_actionSendListeJoueur();
		
		$this->controleDepart();
		
	}
	
	private function controleDepart(){
		//verifier qu ils sont quatre
		if($this->jeux->nbJoueurActif() == Jeux::NBJOUEURS){
			$donneur = $this->jeux->getDonneur();
			if($donneur instanceof Joueur){
				$this->sendAllClient('echoALL',array('message'=>'Le donneur est :'.$donneur->getPseudo()));
				$this->sendAllClient('donneur',array('token'=>$donneur->getToken()));
			}else{
				$this->sendAllClient('echoALL',array('message'=>'Pas de donneur !!!??'));
			}
		}
	}
	
	private function _actionDistribution($data,$client){
		//verification que l on est le donneur.
		if($data['etat']){
			$donneur = $this->jeux->getDonneur();
			if($donneur instanceof Joueur && $donneur->getIdCNX() == $client->getClientId()){
				//on va distribuer le jeux
				try{
					$this->jeux->distribution(1,1);
					$this->sendCartesAllPlayers();
					$this->sendCarteAtout();
					$this->sendDonneParole();
				}catch (JeuxException $exc){
					$this->sendClient("echoALL",array('message'=> $exc));
				}
			}else{
				$this->sendClient("echoALL", array('message'=>'Vous n\'êtes pas le donneur'), $client);
			}
			//distribue dans l'ordre les mains
		}else{
			$this->jeux->reset();
		}
	}
	
	private function _actionPrise($data,$client){
		$joueurCourant = $this->jeux->getJoueurs($client->getClientId());
		$couleur = (array_key_exists('couleur', $data))?$data['couleur']:null;
		try{
			$this->jeux->priseAtout($joueurCourant, $data['accord'],$couleur);
			if($data['accord']){
				$this->sendQuiPrend($data,$client);
			}else{
				$this->sendDonneParole();
			}
		}catch(JeuxException $exc){
			$this->log($exc);
		}
		
	}
	
	private function sendQuiPrend($data,$client){
		//??a quoi ca sert
		$joueurCourant = $this->jeux->getJoueurs($client->getClientId());
		$prise = $this->jeux->getCarteAtout();
		if(isset($data['couleur'])){
			$tab = Carte::getSTR_COULEUR();
			$prise = $tab[$data['couleur']];
			unset($tab);
		}
		$this->sendAllClient("echoALL",array('message'=>'j\'ai pris à '.$prise.'.'),"Joueur ".$joueurCourant->getPseudo());
		//rebalancer les cartes a chacun sans la carte d atout ...
		$this->jeux->distribution();
		$this->sendCartesAllPlayers();
	}
	
	private function _actionDistributionPerso($data,$client){
		//verification que l on est le donneur.
		$joueur = $this->jeux->getJoueurs($client->getClientId());
		$main = array();
		foreach($joueur->main() as $carte){
			array_push($main, array("ordre"=>$carte->getValeur(),"couleur"=>$carte->getCouleur()));
		}
		$this->sendClient("echoALL",array('message'=>'main distribue de '.count($main).' cartes.'),$client);
		$this->sendClient("distribution", $main, $client);
		if(count($main) == Jeux::NBCARTEMAIN && count($this->jeux->getPli()) === 0){
			$carte = $this->jeux->getCarteAtout();
			$this->sendClient("distribution_atout",array("ordre"=> $carte->getValeur() ,"couleur"=>$carte->getCouleur()),$client);
// 			$this->sendDonneParole();
		}else{
			$this->sendClient("distribution_atout",null,$client);
		}
	}
	
	private function sendCarteAtout(){
		$carte = $this->jeux->getCarteAtout();
		if(!empty($carte)){
			$this->sendAllClient("distribution_atout",array("ordre"=> $carte->getValeur() ,"couleur"=>$carte->getCouleur()));
		}
	}
	private function sendCartesAllPlayers(){
		foreach($this->_clients as $client)
		{
			$this->_actionDistributionPerso(null, $client);
		}
	}
	
	
	private function _actionParole($data,$client){
		//on demande la parole ou on pousse la parole a la bonne personne ?
		$this->log("client ".$client->getClientId() ." demande la parole");
		$joueur = $this->jeux->getPreneur();
		if(!$joueur instanceof Joueur){
			$this->sendDonneParole();
		}
		
	}
	
	private function sendDonneParole(){
		//
		$joueur = $this->jeux->getParole();
		$client = $this->_clients[$joueur->getIdCNX()];
		$carte = $this->jeux->getCarteAtout();
		$carteString = "Inconnu";
		if($carte instanceof  Carte){
			$carteString = $carte->__toString();
			$this->log("Atout :".$this->jeux->atout);
			if($this->jeux->getRefusAtout() < Jeux::NBJOUEURS){
				$this->sendClient("parole", array('message'=>$carteString), $client);
			}else if($this->jeux->getRefusAtout() < (Jeux::NBJOUEURS*2)){
				$this->sendClient("parole2", array('message'=>$carteString,'couleur'=>$carte->getCouleur()), $client);
			}
			$this->log("Parole envoye à ".$client->getClientId());
		}
		
		
	}
	
	private function _actionResetPartie($data,$client){
		$this->jeux->reset();
		
	}
}