<script>
Ext.onReady(function(){
	var gameStore = Ext.create('Ext.data.JsonStore',{
        fields:['game_code','game_name'],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '<?php echo $this->createUrl('game/all')?>',
            reader: {
                type : 'json',
                rootProperty : 'rows'
            }
        }
    });

    var tableStore = Ext.create('Ext.data.JsonStore',{
		fields:['id','name'],
		autLoad:true,
		proxy:{
			type:'ajax',
			url:'<?php echo $this->createUrl('questTable/all')?>',
			reader:{
				type:'json',
				rootProperty:'rows'
			}
		}
    });
    
    var store = Ext.create('Ext.data.Store',{
        fields:['id','game','table','alert'],
        pageSize:20,
        proxy: {
            type: 'ajax',
            url : '<?php echo $this->createUrl('list')?>',
            reader: {
                type : 'json',
                totalProperty: 'count',
                rootProperty : 'rows'
            }
        },
        listeners:{
            beforeload:function(store,records,options){
                var searchForm = Ext.getCmp('searchForm').getForm();
                searchForm.findField('page').setValue(store.currentPage);
                Ext.apply(store.getProxy().extraParams,searchForm.getValues());
            }
        }
    });

    var dataForm = {
            xtype: 'form',
            bodyPadding:10,
            id:'dataForm',
            items: [
            {
                xtype: 'hiddenfield',
                id:'id',
                name: 'id'
            }, {
                xtype: 'combobox',
                name: 'game_id',
                id: 'game_id',
                fieldLabel: '游戏类型',
                queryMode:'remote',
                store:gameStore,
                triggerAction:'all',
                editable:false,
                valueField:'game_code',
                displayField:'game_name',
                width:300
            }, {
                xtype: 'combobox',
                name: 'table_id',
                id: 'table_id',
                fieldLabel: '表单名称',
                queryMode:'remote',
                store:tableStore,
                triggerAction:'all',
                editable:false,
                valueField:'id',
                displayField:'name',
                width:300
            }, {
                xtype: 'htmleditor',
                id: 'alert',
                name: 'alert',
                fieldLabel: '注意事项',
                allowBlank: false,
                fontFamilies:HtmlEditorFontFamilies,
                width:700
            }],
            buttons: [{
                text : '重置',
                handler : function(){
                    this.up('form').reset();
                }
            },{
                text: '确认',
                formBind: true,
                handler: function(){
                    submitForm();
                }
            }],
            listeners:{
                keydown:{
                    element:'body',
                    fn:function(e){
                        if(e.keyCode == 13){
                            submitForm();
                        }
                    }
                }
            }
        };

        function submitForm(){
            var form = Ext.getCmp('dataForm');
            if(form.isValid()){
                form.submit({
                    waitMsg:'正在提交数据，请稍后。。。',
                    url:'<?php echo $this->createUrl('save')?>',
                    success: function(form, action){
                        tip(action.result.msg);
                        form.reset();
                        dataFormWin.hide();
                        storeReload(store);
                    },
                    failure: function(from, action){
                    	tip(action.result.msg);
                    }
                });
            }
        }
        
    Ext.define('Admin.basic.win.data',{
        extend: 'Admin.basic.win',
        width:800,
        items:dataForm
    });

    
    Ext.define('Admin.basic.searchForm.alert',{
        extend: 'Admin.basic.searchForm',
        items: [{
            items:[{
                xtype: 'hiddenfield',
                name: 'page',
                id:'page'
            }]
        },{
            layout: 'hbox',
            items: [{
            	xtype: 'combobox',
                name: 'game_id',
                fieldLabel: '游戏类型',
                queryMode:'remote',
                store:gameStore,
                triggerAction:'all',
                editable:false,
                valueField:'game_code',
                displayField:'game_name',
                emptyText:'请选择',
                width:300
            }]
        }],
        buttons: [
            '->',
        {
            text: '重置',
            handler : function(){
                this.up('form').reset();
            }
        }, {
            text: '搜索',
            handler : function(){
                storeReload(store,1);
            }
        },'->']
    });

    Ext.define('Admin.basic.grid.alert',{
        extend:'Admin.basic.grid',
        columns:[
            {text:'游戏类型',dataIndex:'game',width:300},
            {text:'表单名称',dataIndex:'table',width:300},
            {text:'注意事项',dataIndex:'alert',flex:1}
        ],
        store:store,
        bbar: {
            xtype: 'pagingtoolbar',
            id: 'pagingbar',
            pageSize: 20,
            store: store,
            displayInfo: true,
            plugins: new Ext.ux.ProgressBarPager()
        },
        tbar:[{
            iconCls: "glyphicon glyphicon-plus",
            glyph: null,
            text:'新增',
            handler : function(){
                Ext.getCmp('dataForm').reset();
                dataFormWin.setConfig('title',this.text).show();
            }
        },'-',
        {
            iconCls: "glyphicon glyphicon-pencil",
            glyph: null,
            text:'编辑',
            handler : function(){
                var rows = alertGrid.getSelection();
                if(rows.length != 1){
                    tip('请选择要编辑的数据！');
                    return;
                }
                var title = this.text;
                Ext.Ajax.request({
                    url:'<?php echo $this->createUrl('find')?>',
                    params:{
                        id:rows[0].get('id')
                    },
                    success:function(response){
                        var data = Ext.JSON.decode(response.responseText);
                        if(data.success){
                            var editForm = Ext.getCmp('dataForm').getForm();
                            editForm.findField('id').setValue(data.data.id);
                            editForm.findField('game_id').setValue(data.data.game_id);
                            editForm.findField('table_id').setValue(data.data.table_id);
                            editForm.findField('alert').setValue(data.data.alert);
                            dataFormWin.setConfig('title',title).show();
                        }else{
                            tip('您选择的数据不存在了！','错误');
                        }
                    }
                });
            }
        },'-',
        {
            iconCls: "glyphicon glyphicon-trash",
            glyph: null,
            text:'删除',
            handler : function(){
                Ext.MessageBox.confirm('确认', '确定要删除?', function(btn){
                    if(btn == 'yes'){
                        var rows = alertGrid.getSelection();
                        if(rows.length != 1){
                            tip('请选择数据！');
                            return;
                        }
                        Ext.Ajax.request({
                            url:'<?php echo $this->createUrl('delete')?>',
                            params:{
                                id:rows[0].get('id')
                            },
                            success:function(response){
                                var data = Ext.JSON.decode(response.responseText);
                                storeReload(store);
                                if(data.success){
                                    tip('删除成功！');
                                }else{
                                	tip('删除失败！');
                                }
                            }
                        });
                    }
                }, this);
            }
	    }]
	});
    
    //数据窗体
    var dataFormWin = new Admin.basic.win.data();
    //搜索块
    var alertSearchForm = new Admin.basic.searchForm.alert();
    alertSearchForm.render(Ext.getBody());
    //列表
    var alertGrid = new Admin.basic.grid.alert();
    alertGrid.render(Ext.getBody());
    //初始化数据集
    storeReload(store,1);	
});
</script>