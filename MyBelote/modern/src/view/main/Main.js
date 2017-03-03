/**
 * This class is the main view for the application. It is specified in app.js as the
 * "mainView" property. That setting causes an instance of this class to be created and
 * added to the Viewport container.
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */
Ext.define('MyBelote.view.main.Main', {
    extend: 'Ext.tab.Panel',
    xtype: 'app-main',

    requires: [
        'Ext.MessageBox',
//        'Ext.draw.engine.Svg',
        'MyBelote.view.main.MainController',
        'MyBelote.view.main.MainModel'
//        'MyBelote.view.Texte.Home'
    ],

    controller: 'main',
    viewModel: 'main',

    defaults: {
        tab: {
            iconAlign: 'top'
        },
        styleHtmlContent: true
    },

    tabBarPosition: 'bottom',

    items: [
        {
            title: 'Home',
            iconCls: 'x-fa fa-home',
            layout: 'fit'
            // The following grid shares a store with the classic version's grid as well!
//            items: [{
//                xtype: 'panel.texteHome'
//            }]
        },{
            title: 'Users',
            iconCls: 'x-fa fa-user',
            bind: {
                html: '{loremIpsum}'
            }
        },{
            title: 'Groups',
            iconCls: 'x-fa fa-users',
            bind: {
                html: '{loremIpsum}'
            }
        },{
            title: 'Settings modern',
            iconCls: 'x-fa fa-cog',
            bind: {
                html: '{loremIpsum}'
            }
        }
    ]
});
