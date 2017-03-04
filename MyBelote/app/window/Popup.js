Ext.define("MyBelote.window.Popup", {
    	    extend : "Ext.window.MessageBox",
    	    xtype : 'popup',
    	    buttonText: {
    	        ok: "1",
    	        cancel: "2",
    	        yes: "3",
    	        no: "4"
    	    }
    	}
, function(MessageBox) {
    /**
     * @class Ext.MessageBox
     * @alternateClassName Ext.Msg
     * @extends Ext.window.MessageBox
     * @singleton
     * @inheritdoc Ext.window.MessageBox
     */
    // We want to defer creating Ext.MessageBox and Ext.Msg instances
    // until overrides have been applied.
    Ext.onInternalReady(function() {
    	MyBelote.Popup = MyBelote.Msg = new MessageBox();
    });
}
);


