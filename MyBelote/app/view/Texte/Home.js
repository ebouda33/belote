Ext.define('MyBelote.view.Texte.Home', {
	extend: 'Ext.panel.Panel',
    xtype : 'panel.texteHome',
    layout: 'auto',
    html: 'Bienvenue sur mon premier serveur de Belote Réalisé en websocket.'
    	
   ,listeners :{
	   beforerender : function(panel,eOpts){
		   panel.html = 'Bienvenue sur mon premier serveur de Belote Réalisé en websocket';
		   if(MyBelote.ws !== undefined ){
			   MyBelote.ws.definirBanniere(panel);
		   }
	   }
   } 	
});