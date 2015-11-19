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
    
    var store = Ext.create('Ext.data.Store',{
        fields:['id','game','game_id','consume'],
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
                xtype: 'numberfield',
                id: 'consume',
                name: 'consume',
                fieldLabel: '消耗',
                minValue:0,
                maxValue:999999999,
                allowBlank: false,
                width:200
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
        width:400,
        items:dataForm
    });

    
    Ext.define('Admin.basic.searchForm.consume',{
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

    Ext.define('Admin.basic.grid.consume',{
        extend:'Admin.basic.grid',
        columns:[
            {text:'游戏类型',dataIndex:'game',flex:1},
            {text:'等级',dataIndex:'consume',flex:1}
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
                var rows = consumeGrid.getSelection();
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
                            editForm.findField('consume').setValue(data.data.consume);
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
                        var rows = consumeGrid.getSelection();
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
    var consumeSearchForm = new Admin.basic.searchForm.consume();
    consumeSearchForm.render(Ext.getBody());
    //列表
    var consumeGrid = new Admin.basic.grid.consume();
    consumeGrid.render(Ext.getBody());
    //初始化数据集
    storeReload(store,1);	
});
</script>