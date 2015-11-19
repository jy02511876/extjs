<script>
Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store',{
        fields:['id','type','name','status','admin_status','app_status','sort'],
        pageSize:20,
        proxy: {
            type: 'ajax',
            url : '<?php echo $this->createUrl('tables')?>',
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

    Ext.define('Admin.basic.searchForm.table',{
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
                fieldLabel: '问题名称',
                name: 'name',
                labelWidth: 80,
                width: 300
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

    Ext.define('Admin.basic.grid.table',{
        extend:'Admin.basic.grid',
        columns:[
            {text:'问题类型',dataIndex:'type',flex:1},
            {text:'问题名称',dataIndex:'name',flex:1},
            {	
            	xtype:'booleancolumn',
                text:'用户可见',
                dataIndex:'status',
                trueText:'<span class="red">是</span>',
                falseText:'<span class="green">否</span>'
            },
            {
            	xtype:'booleancolumn',
                text:'客服可见',
                dataIndex:'admin_status',
                trueText:'<span class="red">是</span>',
                falseText:'<span class="green">否</span>'
            },
            {
                xtype:'booleancolumn',
                text:'APP可见',
                dataIndex:'app_status',
                trueText:'<span class="red">是</span>',
                falseText:'<span class="green">否</span>'
            },
            {text:'排序',dataIndex:'sort'},
            {
                text:'操作',
                sortable:false,
                xtype:'actioncolumn',
                width:150,
                align:'center',
                items:[{
                	iconCls:'element',
                	handler:function(grid,rowIndex,colIndex){
						var rec = grid.getStore().getAt(rowIndex);
						window.location.href="<?php echo $this->createUrl('element')?>&id="+rec.get('id');
                   	}
                },'-',{
					iconCls:'edit',
					handler:function(grid,rowIndex,colIndex){
						var rec = grid.getStore().getAt(rowIndex);
						window.location.href="<?php echo $this->createUrl('update')?>&id="+rec.get('id');
                   	}
                },'-',{
                	iconCls:'delete',
                	handler:function(grid,rowIndex,colIndex){
                		Ext.MessageBox.confirm('确认', '确定要删除?', function(btn){
                            if(btn == 'yes'){
                            	var rec = grid.getStore().getAt(rowIndex);
                                Ext.Ajax.request({
                                    url:'<?php echo $this->createUrl('delete')?>',
                                    params:{
                                        id:rec.get('id')
                                    },
                                    success:function(response){
                                        var data = Ext.JSON.decode(response.responseText);
                                        storeReload(store);
                                        if(data.success){
                                            Ext.Msg.alert('提示','删除成功！');
                                        }else{
                                            Ext.Msg.alert('提示','删除失败！');
                                        }
                                    }
                                });
                            }
                        }, this);

                   	}
                }]
            }
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
            	window.location.href="<?php echo $this->createUrl('create')?>";
            }
        }]
    });
    

    //搜索块
    var tableSearchForm = new Admin.basic.searchForm.table();
    tableSearchForm.render(Ext.getBody());
    //列表
    var tableGrid = new Admin.basic.grid.table();
    tableGrid.render(Ext.getBody());
    //初始化数据集
    storeReload(store,1);
});
</script>