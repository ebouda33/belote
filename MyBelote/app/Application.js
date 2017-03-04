/**
 * The main application class. An instance of this class is created by app.js when it
 * calls Ext.application(). This is the ideal place to handle application launch and
 * initialization details.
 */
Ext.define('MyBelote.Application', {
    extend: 'Ext.app.Application',
    
    name: 'MyBelote',

    stores: [
        // TODO: add global / shared stores here
    ],
    requires : ['Ext.fx.Anim','MyBelote.window.Popup'],
    
    init: function(application){
    	application.ws = new MyBelote.Object.Websocket('10.184.49.142');
//    	MyBelote.ws = new MyBelote.Object.Websocket('192.168.0.13');
    	var pseudo;
    	Ext.Msg.prompt('Pseudo Pour vous connecter', 'Votre Pseudo:', function(btn, text){
    	    if (btn == 'ok'){
    	    	application.ws.connect(text);
    	    }
    	});
    	
    	
    	
    	
    },
    
    launch: function () {
    	
    	
        // TODO - Launch the application remove wait div
		if(document.getElementById('maskLoader')){
                    var node = Ext.getDom('maskLoader');
                    Ext.create('Ext.fx.Anim', {
                      target: node,
                      duration: 500,
                      to : {
                        opacity : 0
                      }
                      ,callback : function(){
                        Ext.removeNode(node);
                      }
                    });
		}
    },
    
    popupParole : function (message){
    	//ouverture fenetre
    	Ext.Msg.show({
    		title : 'Prise?',
    		message : 'Souhaitez-vous prendre : '+message+'?',
    		buttons : Ext.Msg.YESNO,
    		icon : Ext.Msg.QUESTION,
    		closable : false,
    		closeAction : 'destroy',
    		fn : function (button){
    			if(button === 'yes' ){
    				//envoyer prise
    				var app= MyBelote.getApplication();
    				var ws = app.ws;
//    				console.log("prise");
    				ws.prendreAtout(true);
    			}else if(button === 'no'){
    				//passe au suivant
    				var app= MyBelote.getApplication();
    				var ws = app.ws;
//    				console.log("pas prise");
    				ws.prendreAtout(false);
    			}
    		}
    	});
    },
    popupParole2 : function (message,couleur){
    	//ouverture fenetre
    	const PIQUES = 1;
    	const CARREAUX = 2;
    	const COEUR = 3;
    	const TREFLES = 4;
    	var couleurs = ["","Piques","Carreau","Coeur","Trèfles"];
    	couleurs[couleur] = "2";
//    	Ext.Msg
    	
    	MyBelote.Popup.OK = PIQUES;
    	MyBelote.Popup.YES = CARREAUX;
    	MyBelote.Popup.NO = COEUR;
    	MyBelote.Popup.CANCEL = TREFLES;
    	
    	MyBelote.Popup.show({
    		buttonText: {
    	        ok: couleurs[PIQUES],
    	        yes: couleurs[CARREAUX],
    	        no: couleurs[COEUR],
    	        cancel: couleurs[TREFLES]
    	    },
    		title : 'Prise?',
    		message : 'Carte sur le jeux : '+message+'?',
    		buttons : MyBelote.Popup.YESNOCANCEL,
    		icon : MyBelote.Popup.QUESTION,
    		closable : false,
    		closeAction : 'destroy',
    		fn : function (button){
    			eric = button;
    			buttonText = {
        	        ok: couleurs[PIQUES],
        	        yes: couleurs[CARREAUX],
        	        no: couleurs[COEUR],
        	        cancel: couleurs[TREFLES]
        	    };
    			var value = false;
    			var couleurChoisi = -1;
    			if(buttonText[button] === "2"){
    				value = false;
    			}else{
    				value = true;
    				couleurChoisi = MyBelote.Popup[button.toUpperCase()];
    			}
    			
    			var app= MyBelote.getApplication();
				var ws = app.ws;
				ws.prendreAtout(value,couleurChoisi);
    		}
    	});
    	
    	
    },
    generateurHtmlCarte : function(carte,dosCarte){
		if(carte !== undefined){
			var mouse = 'onmouseover ="MyBelote.getApplication().carteOver(this);" onmouseout ="MyBelote.getApplication().carteOut(this);"';
			if(dosCarte !== undefined){
				mouse = '';
			}
    		return '<svg height="100" width="100" >'+
    	    '<foreignObject height="100" width="100" >'+
    	    '<img height="50" width="50" src=\'resources/cartes/'+carte.couleur+'/'+carte.ordre+'.svg\'' 
    	    +mouse+
    	    '/></foreignObject>'+
    	    '</svg>';
		}
		return '&nbsp;';
	},
	
	carteOver :function(element){
//		console.log(element);
		element.width = element.width*2;
		element.height = element.height*2;
	},
	carteOut : function(element){
//		console.log(element);
		element.width = element.width/2;
		element.height = element.height/2;
	},
    
    onAppUpdate: function () {
       /* Ext.Msg.confirm('Application Update', 'This application has an update, reload?',
            function (choice) {
                if (choice === 'yes') {
                    window.location.reload();
                }
            }
        );*/
    }
});
