/**
 * This class is the main view for the application. It is specified in app.js as the
 * "mainView" property. That setting automatically applies the "viewport"
 * plugin causing this view to become the body element (i.e., the viewport).
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */

Ext.define('MyBelote.view.main.Main', {
    extend: 'Ext.tab.Panel',
    xtype: 'app-main',

    requires: [
        'Ext.plugin.Viewport',
        'Ext.window.MessageBox',
        'MyBelote.view.main.MainModel',
        'MyBelote.view.main.List',
        'MyBelote.view.main.Carte',
        'MyBelote.view.main.tableJeux',
        'MyBelote.view.Texte.Home'
        
    ],

//    controller: 'main',
    viewModel: 'main',

    ui: 'navigation',
    tabBarHeaderPosition: 1,
    titleRotation: 0,
    tabRotation: 0,
    header: {
        layout: {
            align: 'stretchmax'
        },
        title: {
            bind: {
                text: '{name}'
            },
            flex: 0
        },
        iconCls: 'fa-th-list'
    },

    tabBar: {
        flex: 1,
        layout: {
            align: 'stretch',
            overflowHandler: 'none'
        }
    },
    responsiveConfig: {
        tall: {
            headerPosition: 'left'
        },
        wide: {
            headerPosition: 'left'
        }
    },

    defaults: {
        bodyPadding: 20,
        tabConfig: {
            plugins: 'responsive',
            responsiveConfig: {
                wide: {
                    iconAlign: 'left',
                    textAlign: 'left',
                    height:50
                },
                tall: {
                    iconAlign: 'top',
                    textAlign: 'center',
                    width: 120
                }
            }
        }
    },
	scrollable : true,
    items: [{
        title: 'Home',
        iconCls: 'fa-home',
        
        items: [{
            xtype: 'panel.texteHome'
        }]

    	}, {
        title: 'Joueurs',
        iconCls: 'fa-user',
        items: [{
            xtype: 'mainlist'
        }]
        
    	}, 
    	{
        title: 'Table',
        iconCls: 'fa-users',
        scrollable : true,
        items: [{
        	xtype:'panel',
        	scrollable : true,
        	minHeight : 100,
        	maxHeight : 200,
        	split:true,
        	title : 'message',
        	id :'messageJeux',
        	collapsible : true
        	
        	},{
        		xtype :'mainTableJeux',
        		layout : 'border'
        	}]
    	
    } , {
        title: 'Settings classic',
        iconCls: 'fa-cog',
        bind: {
            html: '{loremIpsum}'
        }
    }]
});
