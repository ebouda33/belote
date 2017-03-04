/**
 * This view is an example list of people.
 */
Ext.define('MyBelote.view.main.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'mainlist',

//    requires: ['MyBelote.model.Joueur' ],

    title: 'Liste Joueurs',
    tools: [
            { type:'refresh',tooltip : 'Rafraichit la liste des joueurs',callback: function(){
            	grid = this.getBubbleParent().getBubbleParent();
            	grid.refreshStore();
            } }
    ],
    
    columns: [
        { text: 'id',  dataIndex: 'id',hidden: true },
        { text: 'Pseudo', dataIndex: 'pseudo', flex: 1 },
        { text: 'Email', dataIndex: 'email', flex: 1 },
        { text: 'Equipe', dataIndex: 'position', flex: 1,
        	field: {
	            xtype: 'combobox',
	            typeAhead: true,
	            triggerAction: 'all',
	            selectOnTab: true,
	            store: [
	                [0,'Equipe 1'],
	                [1,'Equipe 2']
	            ],
	            lazyRender: true,
	            listClass: 'x-combo-list-small'
	        } ,
	        renderer : function(value, metaData, record, rowIndex, colIndex, store, view){
	        	var position =  ['Equipe 1','Equipe 2'];
	        	if(value === null || value === undefined){
	        		return '';
	        	}
	        	return position[value%2];
	        }
        }
    ],
    plugins: [Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    })],
    listeners: {
        select: 'onItemSelected',
        beforerender : function(grid,eOpts){
        	grid.refreshStore();
        },
        beforeedit : function(editor,context,eOpts){
        	var ws = MyBelote.getApplication().ws;
        	if(context.record.data.id !== ws.token){
        		context.cancel = true;
        	}
        } ,
        validateedit : function(editor,context,eOpts){
        	var ws = MyBelote.getApplication().ws;
        	if(context.record.data.id != ws.token){
        		context.cancel = true;
        	}
        },
        edit : function (editor,context,eOpts){
        	var p = context.record.data.position;
        	var ws = MyBelote.getApplication().ws;
        	ws.affecterEquipe(p);
        }
    },
    refreshStore : function (){
    	var me = this;
    	if(MyBelote.getApplication().ws !== undefined){
    		//recherche la liste des joueurs
    		MyBelote.getApplication().ws.definirGridJoueurs(me);
    		MyBelote.getApplication().ws.demandeListeJoueurs();
    	}
    },
    remplirGrid : function(store){
    	var me = this;
    	me.setStore(store);
    }
});
