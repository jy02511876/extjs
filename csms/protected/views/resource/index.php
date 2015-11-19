<script>
Ext.onReady(function(){
	//分页的隐藏域
	Ext.define('Admin.basic.searchForm.resource',{
        extend: 'Admin.basic.searchForm',
        items:[{
          	xtype: 'hiddenfield',
            name: 'resource_id',
            id:'resource_id'
        },{
            xtype: 'hiddenfield',
            name: 'page',
            id:'page'
        }]
    });

    //权限树
    var modelStore = Ext.create('Ext.data.TreeStore',{
    	proxy: {
            type: 'ajax',
            url : '<?php echo $this->createUrl('list')?>'
        },
        root:{
			text:'权限树',
			id:'all',
			expanded:true
        }
    });

    //用户数据列表
    var store = Ext.create('Ext.data.Store',{
        fields:['id','name'],
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
            	if(store.getProxy().url == '<?php echo $this->createUrl('url') ?>')
            		Ext.getCmp('dataGrid').setConfig({title:'用户'});
                else if(store.getProxy().url == '<?php echo $this->createUrl('group') ?>')
                	Ext.getCmp('dataGrid').setConfig({title:'团队'});
                else if(store.getProxy().url == '<?php echo $this->createUrl('dept') ?>')
                	Ext.getCmp('dataGrid').setConfig({title:'部门'});              
                var searchForm = Ext.getCmp('searchForm').getForm();
                searchForm.findField('page').setValue(store.currentPage);
                Ext.apply(store.getProxy().extraParams,searchForm.getValues());
            }
        }
    });
    
    var menuTree = Ext.create('Ext.tree.Panel', {
                title: '权限数',
                width : 300,
                height: 600,
                collapsible: true,
                split: true,
                useArrows:true,
                store : modelStore,
                rootVisible : false,
                listeners : {
                    itemclick : function(tree, record, item, index, e, options) {
                		if(record.data.leaf == false) return;
                    	var searchForm = resourceSearchForm.getForm();
                        searchForm.findField('resource_id').setValue(record.data.id);
                        searchForm.findField('page').setValue(1);
                        showUser();
                    }
                }
            });

    function showUser()
    {
        gridContainer.down("#user").disable();
        gridContainer.down("#group").enable();
        gridContainer.down("#dept").enable();
    	store.getProxy().setUrl('<?php echo $this->createUrl('user')?>');
    	store.currentPage = 1;
    	muliteStoreReload(store,1);
    }


    function showGroup()
    {
    	gridContainer.down("#user").enable();
        gridContainer.down("#group").disable();
        gridContainer.down("#dept").enable();
    	store.getProxy().setUrl('<?php echo $this->createUrl('group')?>');
    	store.currentPage = 1;
    	muliteStoreReload(store,1);
    }


    function showDept()
    {
    	gridContainer.down("#user").enable();
        gridContainer.down("#group").enable();
        gridContainer.down("#dept").disable();
    	store.getProxy().setUrl('<?php echo $this->createUrl('dept')?>');
    	store.currentPage = 1;
    	muliteStoreReload(store,1);
    }

    function muliteStoreReload(store,page){
        
        var searchForm = Ext.getCmp('searchForm').getForm();
        if(page == undefined)
        	searchForm.findField('page').setValue(store.currentPage);
        else
        	searchForm.findField('page').setValue(page);
        
        store.reload({params:searchForm.getValues()});
    }
    
   	var gridContainer = Ext.create('Ext.panel.Panel',{
			layout:{
					type:'vbox',
					align:'stretch',
				},
			margin:'0 0 0 10',
			width:690,
			height:600,
			items:[{
					xtype:'container',
					layout:'hbox',
					defaultType:'button',
					padding:10,
					items:[{
							itemId:'user',
							text:'用户',
							padding:5,
							margin:'0 10 5 0',
							cls:'btn btn-info',
							handler:showUser
						},{
							itemId:'group',
							text:'团队',
							padding:5,
							margin:'0 10 5 0',
							cls:'btn btn-info',
							handler:showGroup
						},{
							itemId:'dept',
							text:'部门',
							padding:5,
							margin:'0 10 5 0',
							cls:'btn btn-info',
							handler:showDept
						}]
				},{
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
				                        url:store.getProxy().url+'Del',
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
   	
   	//搜索块
    var resourceSearchForm = new Admin.basic.searchForm.resource();
});
</script>