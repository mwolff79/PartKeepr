Ext.define("PartKeepr.PartTextValueComboBox",{
    extend:"Ext.form.field.ComboBox",
    alias: 'widget.PartTextValueComboBox',
    displayField: 'txtValue',
    valueField: 'txtValue',
    autoSelect: false,
    allowBlank: true,
    queryMode: 'local',
    triggerAction: 'all',
    forceSelection: false,
    editable: true,
    initComponent: function () {
		//this.store = PartKeepr.getApplication().getPartUnitStore();
    	
    	this.store = Ext.create("Ext.data.Store", {
    		fields: [{ name: 'txtValue' }],
    		proxy: {
    			type: 'ajax',
    			url: PartKeepr.getBasePath() + "/Part/getPartTextValueNames",
    			reader: {
    				type: 'json',
    				root: 'response.data'
    			}
    		}
    	});
    	
    	this.store.load();
		
		/* Workaround to remember the value when loading */
		this.store.on("beforeload", function () {
			this._oldValue = this.getValue();
		}, this);
		
		/* Set the old value when load is complete */
		this.store.on("load", function () {
			this.setValue(this._oldValue);
		}, this);
		
		this.callParent();
    }
});

