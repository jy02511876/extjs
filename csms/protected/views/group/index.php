<script>
Ext.onReady(function(){
	//分页的隐藏域
	Ext.define('Admin.basic.searchForm.group',{
        extend: 'Admin.basic.searchForm',
        items:[{
          	xtype: 'hiddenfield',
            name: 'group_id',
            id:'group_id'
        },{
            xtype: 'hiddenfield',
            name: 'page',
            id:'page'
        }]
    });

	//搜索块
    var groupSearchForm = new Admin.basic.searchForm.group();
    
	//权限树
    var groupStore = Ext.create('Ext.data.TreeStore',{
    	proxy: {
            type: 'ajax',
            url : '<?php echo $this->createUrl('list')?>'
        },
        root:{
			text:'团队',
			id:'all',
			expanded:true
        }
    });
    
	var menuTree = Ext.create('Ext.tree.Panel', {
        title: '权限数',
        width : 300,
        height: 600,
        collapsible: true,
        split: true,
        useArrows:true,
        store : groupStore,
        rootVisible : false,
        listeners : {
            itemclick : function(tree, record, item, index, e, options) {
        		if(record.data.leaf == false) return;
            	var searchForm = groupSearchForm.getForm();
                searchForm.findField('group_id').setValue(record.data.id);
                searchForm.findField('page').setValue(1);
                storeReload(store,1);
            }
        }
    });
    
    var store = Ext.create('Ext.data.Store',{
        fields:['id','username','domain_name'],
        pageSize:20,
        proxy: {
            type: 'ajax',
            url : '<?php echo $this->createUrl('user')?>',
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

    var gridContainer = Ext.create('Ext.panel.Panel',{
		layout:{
				type:'vbox',
				align:'stretch',
			},
		margin:'0 0 0 10',
		width:690,
		height:600,
		items:[{
				xtype:'grid',
				flex:1,
				id:'dataGrid',
				selType:'checkboxmodel',
				title:'用户',
				columns:[
		            {text:'id',dataIndex:'id'},
		            {text:'名称',dataIndex:'name',flex:1}
		        ],
		        store:store,
		        tbar:[{
	                iconCls: "glyphicon glyphicon-trash",
	                glyph: null,
	                text:'批量删除',
	                handler : function(){
	                	var rows = Ext.getCmp('dataGrid').getSelection();
	                	if(rows.length == 0)
		                	tip('请选择需要删除的数据!');
	                	Ext.MessageBox.confirm('确认', '确定要删除?', function(btn){
	                        if(btn == 'yes'){
	                        	var ids = [];
	    	                	for(i=0;i<rows.length;i++)
	    		                	ids.push(rows[i].id);
	    		               	
	    	                	Ext.Ajax.request({
	    	                        url:'<?php echo $this->createUrl('userDel')?>',
	    	                        params:{
	    	                            ids:ids.toString()
	    	                        },
	    	                        success:function(response){
	    	                            var data = Ext.JSON.decode(response.responseText);
	    	                            if(data.success){
	    	                                store.reload();
	    	                                tip('操作成功！');
	    	                            }else{
	    	                                tip('操作失败！');
	    	                            }
	    	                        }
	    	                    });
	                        }
	                    }, this);
	                    
	                	
	                    
	                }
	            }],
				bbar: {
		            xtype: 'pagingtoolbar',
		            id: 'pagingbar',
		            pageSize: 20,
		            store: store,
		            displayInfo: true,
		            plugins: new Ext.ux.ProgressBarPager()
		        }
			}]
		})
		
	
	Ext.create('Ext.panel.Panel',{
	width:1000,
	layout:'column',
	renderTo:Ext.getBody(),
	viewModel:true,
	border:false,
	defaults:{
			height:600
		},
	items:[menuTree,gridContainer]
	})
   
});
</script>