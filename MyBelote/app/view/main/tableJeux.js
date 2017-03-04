
Ext.define('MyBelote.view.main.tableJeux',{
	extend : 'Ext.panel.Panel',
	xtype : 'mainTableJeux',
	scrollable : true,
	tbar : [
	{
		xtype : 'label',
		text : 'Table de Jeux'
	},{
		xtype : 'tbseparator'
	},
	{
		xtype : 'button',
		text : 'Distribué le jeux',
		id : 'bt_distribution',
		disabled : true,
		enableToggle : true,
		listeners : {
			click : function (button,evt,eOpts){
				var ws = MyBelote.getApplication().ws;
				var etat = false;
				if(button.pressed){
    				button.setText('Annuler Partie');
    				etat = true;
				}else{
					button.setText('Distribué le jeux');
					etat = false;
				}
				
				ws.distributionJeux(etat);
			}
		}
	},{
		xtype : 'label',
		text : 'Score'
	},{
		xtype : 'label',
		text : 'Eux'
	},{
		xtype : 'tbseparator'
	},{
		xtype : 'label',
		text : 'Nous'
	}],
	height:500,
	minHeight : 400,
	items: [
	    {
        	xtype : 'panel',
        	scrollable : true,
        	fond : '#268008',
        	fondSurvol: '#124900',
        	
        	id:'centre',
    		region : 'center',
    		height:200,
    		minHeight : 100,
    		minWidth : 100,
    		layout: {
    	    	type : 'table',
    	        columns : 3,
    	        rows : 3,
    	        tableAttrs: {
    	            style: {
    	                width: '100%',
    	                height : '100%'
//	        	                background : '#268008',
    	            },
    	         tdAttrs : {
    	        	 textAlign : 'center'
    	         }   
    	        }
    	    },
    	    items : [
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"&nbsp;",zone:'vide'},
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"",zone:'north'},
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"&nbsp;",zone:'vide'},
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"",zone:'west'},
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"&nbsp;",zone:'centre'},
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"",zone:'east'},
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"&nbsp;",zone:'vide'},
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"",zone:'south'},
    	             {xtype : 'panel',bodyStyle: 'background:none;',width:'100%',height:'100%',html:"&nbsp;",zone:'vide'}
    	    ],
    		//dropzone.
    		dragger : false,
    		listeners: {
    			//on ne gere que le drapndrop du joueur sud
                boxready: function(component) {
                	component.setBodyStyle('background:'+component.fond+';');
                    component.dropZone  = new Ext.dd.DropZone( component.body, {
                        // Tell the zone what our target component is
                        getTargetFromEvent: function(event) {
                            return component;
                        },
                        // When the node is dropped, add a new instance to the
                        // the component via the supplied component clone
                        onNodeDrop: function(target, dd, e, data) {
                        	var ws = MyBelote.getApplication().ws;
                        	//on autorise le drag que si partie en cours et joueur autorise 
                        	if(ws.enCours){
                            	if(data.panel.carte !== undefined){
                                	var carte = data.panel.carte;
                                	var motif = MyBelote.getApplication().generateurHtmlCarte(carte);
                                	if(component.items.items[7].html === ""){
                                		//on permet ajout de carte
                                		component.insert(9,data.panel);
                                		component.items.items[7].setHtml(motif);
                                		component.remove(component.items.items[9]);
                                		MyBelote.getApplication().ws.joueCarte(carte);
                                	}
                                	component.updateLayout();
                                	target.setBodyStyle('background:'+target.fond+';');
                                	//prevenir serveur que joueur joue une carte
                                }
							  	
                                return true;
                        	}
                        	return false;
                        },
                     // On entry into a target node, highlight that node.
                        onNodeEnter : function(target, dd, e, data){
//	                            	console.log('Enter Drop');
                        	var ws = MyBelote.getApplication().ws;
                        	if(ws.enCours){
                        		target.setBodyStyle('background:'+target.fondSurvol+';');
                        	}
                        },

                        // On exit from a target node, unhighlight that node.
                        onNodeOut : function(target, dd, e, data){
//	                            	console.log('Out Drop');
                        	target.setBodyStyle('background:'+target.fond+';');
                        	
                        },

                        // While over a target node, return the default drop allowed class which
                        // places a "tick" icon into the drag proxy.
                        onNodeOver : function(target, dd, e, data){
                        	//????
                        	var ws = MyBelote.getApplication().ws;
                        	if(ws.enCours){
                        		return Ext.dd.DropZone.prototype.dropAllowed;
                        	}
                        	return Ext.dd.DropZone.prototype.dropNotAllowed;
                            
                        }
                    });
                }
            },
            getZoneCentre : function (){
            	var me = this;
            	var zone = me.items.items[4];
            	
            	return zone;
            	
            }
            
        },{
        	xtype : 'mainCarte',
        	scrollable : true,
        	region : 'north',
        	tools: [
                    { type:'refresh',tooltip : 'Rafraichit la liste des joueurs',callback: function(){
                    	var app= MyBelote.getApplication();
	              		  var vue = app.getMainView();
	              		  var panel = vue.setActiveTab(2);
	              		  panel.items.getAt(1).miseAJourNom();
                    } }
            ],
        	id:'north',
        	split:true,
        	minHeight : 100,
        	collapsible : true,
        	animCollapse: true,
        	title: 'En Attente...'
        },{
        	xtype : 'mainCarte',
        	scrollable : true,
        	region : 'east',
        	id:'east',
        	split:true,
        	minWidth : 100,
        	height : 100,
        	collapsible : true,
        	animCollapse: true,
        	title: 'En Attente...'
        },{
        	xtype : 'mainCarte',
        	scrollable : true,
        	region : 'west',
        	id:'west',
        	split:true,
        	minWidth : 100,
        	height : 200,
        	collapsible : true,
        	animCollapse: true,
        	title: 'En Attente...'
        },{
            xtype: 'mainCarte',
            scrollable : true,
            region : 'south',
            id:'south',
            height : 200,
            animCollapse: true,
//		        	width: 400,
           // height:200,	
            split:true,
            collapsible : true,
            title: 'En Attente...',
            listeners : {
            	beforerender : function(panel,eOpts){
            		
            		/*var jeux = [{ordre:1,couleur:1},
            		            {ordre:8,couleur:2},
            		            {ordre:3,couleur:3},
            		            {ordre:2,couleur:4},
            		            {ordre:6,couleur:1}];
            		panel.definirJeux(jeux);
            		*/
            	}
            }	
        }
    ],
    listeners :{
    	beforerender : function(panel,eOpts){
    		//recuperer les pseudos pour les afficher
    		var ws = MyBelote.getApplication().ws;
    		ws.definirTable(panel);
    		var store = ws.getJoueurs();
    		store.each(function (record){
    			if(record.get('id') == ws.token){
    				Ext.getCmp('south').setTitle(record.get('pseudo'));
    			}
    		},this);
    	}
    },
    miseAJourNom : function (){
    	var me = this;
    	var ws = MyBelote.getApplication().ws;
		var store = ws.getJoueurs();
		var liste = store.data.items
		var position = undefined;
		var compteurReelJoueur = 0;
		//recherche ou on est sur la table
		for(i=0;i< liste.length;i++){
			var record = liste[i].data;
			if(record.position !== undefined && record.position !== null){
				if(record.id == ws.token){
					position = record.position;
					compteurReelJoueur++;
				}
//	    				console.log(record.pseudo);
			}
		}
		//re-parcours la liste pour positionner
		for(i=0;i< liste.length;i++){
			var record = liste[i].data;
			if(record.position !== undefined && record.position !== null){
    			if(record.position !== position && record.position%2 == position%2){
    				Ext.getCmp('north').setTitle(record.pseudo);
    				compteurReelJoueur++;
    			}
    			if(position+1 == record.position || (position == 3 && record.position == 0)){
    				Ext.getCmp('east').setTitle(record.pseudo);
    				compteurReelJoueur++;
    			}
    			if(position-1 == record.position || (position == 0 && record.position == 3)){
    				Ext.getCmp('west').setTitle(record.pseudo);
    				compteurReelJoueur++;
    			}
			}
		}
		var button = me.getDockedItems('toolbar[dock="top"]')[0].items.items[2];
		if(compteurReelJoueur == 4 && ws.donneur == true){
			button.setDisabled(false);
			
		}else{
			button.setDisabled(true);
		}
		
		ws.demanderParole();
		ws.demandeCartes();
		
		//refresh de l ecran
		if(ws.carteAtout !== undefined){
			me.afficherAtout(ws.carteAtout);
		}
		
		//gestion du button donneur ?? lors du refresh
		if(ws.donneur && ws.carteAtout !== undefined){
			button.setText('Annuler Partie');
		}else{
			button.setText('Distribué le jeux');
		}
		
		
		
    },
    afficherAtout : function(carte){
    	var me = this;
    	var zoneAtout = me.items.items[0].getZoneCentre();
    	if(carte === undefined || carte === null){
    		zoneAtout.setHtml("&nbsp;");
    	}else{
    		var carteSVG =  MyBelote.getApplication().generateurHtmlCarte(carte);
    		zoneAtout.setHtml(carteSVG);
    	}
    },
    afficherJeux : function(jeux){
    	var me = this;
    	var panel = Ext.getCmp('south');
    	panel.definirJeux(jeux);
    	panel.afficherCarte();
    	var limite = jeux.length;
    	//afficher aux autres les cartes caches
    	jeux = [];
    	for(var i = 0 ; i < limite;i++){
    		jeux[i] = {ordre:0,couleur:0};
    	}
    	
    	panel = Ext.getCmp('west');
    	panel.definirJeux(jeux,true);
    	panel.afficherCarte();
    	
    	panel = Ext.getCmp('north');
    	panel.definirJeux(jeux,true);
    	panel.afficherCarte();
    	
    	panel = Ext.getCmp('east');
    	panel.definirJeux(jeux,true);
    	panel.afficherCarte();
    	
    }
}
);
        