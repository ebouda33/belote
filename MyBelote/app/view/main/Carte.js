Ext.define('MyBelote.view.main.Carte',{
	extend : 'Ext.panel.Panel',
	xtype : 'mainCarte',
//	resizable : true,
	border : false,
	bodyPadding : 0,
	
//	initComponent : function(){
//		var me = this;
//		/*var dd = Ext.create('Ext.dd.DD', me, 'carteDDGroup', {
//		    isTarget  : false
//		  });
//		  */
//	},
	
	 
	layout: {
//        type: 'hbox',
//        align: 'stretch',
//        padding:5,
		type : 'column'
    },
    height : 50,
    items : [],
    jeux : undefined,
    dosCarte : undefined,
	listeners : {
		afterrender : function(panel,eOpts){
			//ajouter les cartes au panel
			panel.afficherCarte();
		}
		
			
	},
	genererCarte : function (carte){
		var html = MyBelote.getApplication().generateurHtmlCarte(carte,this.dosCarte);
		var width = 100;
		var panel = {
				xtype : 'panel',
				html : html,
			    carte : carte
		}
		var draggable = {
	        moveOnDrag: false,
	        delegate: 'svg'
	    };
		var livedrag = true;
		if(this.dosCarte !== undefined){
			width = 50;
			
		}else{
			panel.liveDrag = true;
			panel.draggable = draggable;
		}
		panel.width = width;
		
		
		return panel;
	},
	afficherCarte : function(){
		var panel = this;
		panel.removeAll();
		if(panel.jeux !== undefined){
			for(i=0;i<panel.jeux.length;i++){
				var carte = panel.jeux[i];
				panel.insert(panel.items.length,panel.genererCarte(carte));
			}
//			panel.insert(0,panel.genererCarte({couleur:1,ordre:6}));
//			panel.insert(1,panel.genererCarte({couleur:2,ordre:6}));
//			panel.insert(2,panel.genererCarte({couleur:3,ordre:8}));
//			panel.insert(3,panel.genererCarte({couleur:4,ordre:2}));
//			panel.insert(4,panel.genererCarte({couleur:1,ordre:1}));
			
			panel.updateLayout();
		}
		
		
	},
	definirJeux : function(jeux,dosCarte){
		this.jeux = jeux;
		
		if(dosCarte){
			this.dosCarte = dosCarte;
		}
	}
});