Ext.define('MyBelote.Object.Websocket',{
  extend :'Object',
  flux : undefined,
  gridJoueur : undefined,
  table : undefined,
  banniere : undefined,
  donneur : false,
  carteAtout : undefined,
  enCours : undefined,
  constructor : function(ip){
	  this.ip = ip;
	  this.enCours = false;
  },
  connect : function(pseudo){
	  var me = this;
	  me.pseudo = pseudo;
	  try{
		   me.ws = new WebSocket('ws://'+me.ip+':8000/belote');
		  me.ws.onopen = me.open;
		  me.ws.onmessage = me.receive;
		  me.ws.onclose = me.close;
		  me.ws.onerror = me.error;
	  }catch(exc){
		  console.log(exc);
	  }
  },
  toString : function(){
    return "Un ws";
  },
  statics:{
    
  },
  open : function (){
	  //dans le websocket
	  var me = this;
	  me.send(Ext.JSON.encode({action:"open",data:{pseudo:MyBelote.getApplication().ws.pseudo}}));
  },
  envoyer : function(data){
	  var me =this;
	  me.ws.send(Ext.JSON.encode(data));
  },
  demandeListeJoueurs : function(){
	  var me = this;
	  me.laListeJoueurs = undefined;
	  me.envoyer({action:'listeJoueur',data:''}); 
	  
  },
  receive : function(event){
	  //dans le websocket
	  //attention ici this = ws
	  var me = MyBelote.getApplication().ws;
	  var json = Ext.JSON.decode(event.data);
	  
	  me.gestionProtocol(json.action,json.data);
	  
  },
  afficheBanniere : function (text){
	  var me = this;
	  if(text !== undefined){
		  if(me.banniere !== undefined){
			  if(me.flux !== undefined){
				  text = me.flux+"<br>"+text;
				  me.flux = undefined;
			  }else{
				  text = "<br>"+text;
			  }
			  if(me.banniere.html === undefined){
				  me.banniere.html = '';
			  }
			  me.banniere.setHtml(me.banniere.html+ text);
		  	
		  }else{
			  var app= MyBelote.getApplication();
	  		  var vue = app.getMainView();
	  		  var panel = vue.setActiveTab(0);
			  me.banniere = panel;
			  if(me.flux === undefined){
				  me.flux = text;  
			  }
			  
		  }
	  }else{
		  console.log('reception banniere vide');
	  }
  },
  definirBanniere : function (panel){
	  var me = this;
	  me.banniere = panel;
  },
  definirGridJoueurs : function (grid){
	  var me = this;
	  me.gridJoueur = grid;
  },
  getJoueurs : function(){
	var me = this;
	if(me.gridJoueur !== undefined){
		return me.gridJoueur.getStore();
	} else{
		return me.listeJoueur;
	} 
	return undefined;
  },
  close : function(){
	//dans le websocket
	  var me = MyBelote.getApplication().ws;
	  me.afficheBanniere("Fermeture de la connection par le serveur" );
	  if(this.readyState < this.CLOSING){
		  this.close();
	  }
  }
  ,error : function(error){
	//dans le websocket
	  var me = MyBelote.getApplication().ws;
	  me.afficheBanniere("une erreur est survenue." );
  },
  gestionProtocol : function(action,data){
  	var me = MyBelote.getApplication().ws;
	  if(action === 'banniere'){
			  me.afficheBanniere(data);
	  }else if(action === 'echo'){
		  if(typeof data === "string" ){
			  me.afficheBanniere("Message du serveur => "+data);
		  }else{
			  for(var prop in data){
				  me[prop] = data[prop];
			  }
		  }
  	  }else if(action === 'listeJoueur'){
  		var store = Ext.create('Ext.data.Store', {
  		    fields: [
  		         { name: 'id', type: 'string' },
  		         { name: 'pseudo', type: 'string' }
  		     ],
  			data: { items: data},
  		    proxy: {
  		        type: 'memory',
  		        reader: {
  		            type: 'json',
  		            rootProperty: 'items'
  		        }
  		    }
  		});
  		  if(me.gridJoueur !== undefined){
  			  me.gridJoueur.remplirGrid(store);
  		  }else{
  			  me.listeJoueur = store;
  		  }
  		  //prevenir la table d arrivee de joueur
//  		  if(me.table !== undefined){
//  			  me.table.miseAJourNom();
//  		  }
  		 me.afficheBanniere("Message du serveur => Recuperation liste joueurs");
  		  
  	  }else if(action === 'joue'){
  		me.ecrireTchatJoueurs(data.string);
  		
  	  }
	  else if(action === 'newPlayer'){
		  me.ecrireTchatJoueurs("Arrivée de "+data.pseudo);
	  }else if(action === 'echoALL'){
		  me.ecrireTchatJoueurs(data.message,data.qui);
	  }else if(action === 'donneur'){
		  if(me.token == data.token){
			  me.donneur = true;
		  }else{
			  me.donneur = false;
		  }
//		  if(me.table !== undefined){
//			  me.table.miseAJourNom();
//		  }
	  }else if (action === 'distribution'){
		  var app= MyBelote.getApplication();
  		  var vue = app.getMainView();
  		  var panel = vue.setActiveTab(2);
  		  panel.items.getAt(1).afficherJeux(data);
	  }else if(action === "distribution_atout"){
		  me.carteAtout = data;
		  var app= MyBelote.getApplication();
  		  var vue = app.getMainView();
  		  var panel = vue.setActiveTab(2);
  		  panel.items.getAt(1).afficherAtout(me.carteAtout);
	  }else if(action === 'parole'){
		  //ouvrir popup de parole
		  var app= MyBelote.getApplication();
		  var vue = app.getMainView();
		  var panel = vue.setActiveTab(2);
		  if(me.carteAtout === undefined){
			  panel.items.getAt(1).miseAJourNom();
		  }else{
			  MyBelote.getApplication().popupParole(data.message);
		  }
	  }else if(action === 'parole2'){
		  var app= MyBelote.getApplication();
		  var vue = app.getMainView();
		  var panel = vue.setActiveTab(2);
		  if(me.carteAtout === undefined){
			  panel.items.getAt(1).miseAJourNom();
		  }else{
			  MyBelote.getApplication().popupParole2(data.message,data.couleur);
		  }
	  } 
  	  else{
		  console.log(action,data);
	  }
	  
	  console.log('reception:'+action);
  },
  joueCarte : function(carte){
  	var me = this;
  	// console.log('carte joue',carte);
  	me.envoyer({action:'joue',data:carte});
  },
  distributionJeux : function(etat){
	  var me = this;
	  me.envoyer({action:'distribution',data:{etat:etat}});
	  
  },
  ecrireTchatJoueurs : function(message,qui){
	  var html = Ext.getCmp('messageJeux').message;
	  if(qui === undefined || qui === null){
		  qui = "Système";
	  }
		if(html === undefined){
			html = '';
		}else{
			html += '<br>';
		}
		var date = new Date();
		var minutes = date.getMinutes();
		if((minutes*1) < 10){
			minutes = '0'+minutes;
		}
		var heures = date.getHours();
		if((heures*1) < 10){
			heures = '0'+heures;
		}
		var strheure = heures+':'+minutes;
		Ext.getCmp('messageJeux').message = html+qui+" "+strheure+" > "+message;
		Ext.getCmp('messageJeux').setHtml(Ext.getCmp('messageJeux').message);
  },
  affecterEquipe : function(equipe){
	  var me = this;
	  equipe = equipe+'';
	  me.envoyer({action:'equipe',data:equipe});
  },
  definirTable : function (table){
	  var me = this;
	  me.table = table;
  },
  demandeCartes : function(){
	  var me = this;
	  me.envoyer({action:'distributionPerso',data:''});
  },
  demanderParole : function(){
	  var me = this;
	  me.envoyer({action:'parole',data:''});
  },
  prendreAtout : function(accord,couleur){
	  var me = this;
	  me.envoyer({action:'prise',data:{"accord":accord , "couleur": couleur}});
  }
 
});