<script>
Ext.onReady(function(){
	var operStore = Ext.create('Ext.data.JsonStore',{
        fields:['id','name'],
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '<?php echo $this->createUrl('oper')?>',
            reader: {
                type : 'json',
                rootProperty : 'rows'
            }
        }
    });

    var userStore = Ext.create('Ext.data.JsonStore',{
			fields:['id','name'],
			autoLoad:true,
			proxy:{
				type:'ajax',
				url:'<?php echo $this->createUrl('user/all')?>',
				reader:{
					type:'json',
					rootProperty:'rows'
					}
			}
        });

    var groupStore = Ext.create('Ext.data.Store',{
    		fields:['id','name'],
    		autoLoad:true,
			data:<?php echo json_encode($group)?>
        });
        
    var deptStore = Ext.create('Ext.data.Store',{
		fields:['id','name'],
		autoLoad:true,
		data:<?php echo json_encode($dept)?>
    });
    
    var store = Ext.create('Ext.data.Store',{
        fields:['id','resource','user','group','dept','op','oper','optime'],
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

    
    Ext.define('Admin.basic.searchForm.authLog',{
        extend: 'Admin.basic.searchForm',
        width:'90%',
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
                name: 'user_id',
                id: 'user_id',
                fieldLabel: '用户',
                labelWidth:40,
                width:200,
                store:userStore,
                displayField:'name',
                valueField:'id',
                queryMode:'remote',
                triggerAction:'all',
                queryParam:'keyword',
                queryDelay:300,
                minChars:1,
                emptyText:'请选择',
                forceSelection:true,
                typeAhead:true,
                listConfig:{
					loadingText:'正在加载员工信息',
					emptyText:'未找到匹配的员工',
					maxHeight:200
                }
            },{
            	xtype: 'combobox',
                name: 'group_id',
                id: 'group_id',
                padding:'0 0 0 20',
                fieldLabel: '团队',
                labelWidth:40,
                width:200,
                store:groupStore,
                displayField:'name',
                valueField:'id',
                queryMode:'local',
                triggerAction:'all',
                queryDelay:300,
                minChars:1,
                emptyText:'请选择',
                forceSelection:true,
                typeAhead:true,
                listConfig:{
					loadingText:'正在加载团队信息',
					emptyText:'未找到匹配的团队',
					maxHeight:200
                }
            },{
            	xtype: 'combobox',
                name: 'dept_id',
                id: 'dept_id',
                fieldLabel: '部门',
                padding:'0 0 0 20',
                labelWidth:40,
                width:200,
                store:deptStore,
                displayField:'name',
                valueField:'id',
                queryMode:'local',
                triggerAction:'all',
                queryDelay:300,
                minChars:1,
                emptyText:'请选择',
                forceSelection:true,
                typeAhead:true,
                listConfig:{
					loadingText:'正在加载部门信息',
					emptyText:'未找到匹配的部门',
					maxHeight:200
                }
            },{
            	xtype: 'combobox',
                name: 'op',
                id: 'op',
                fieldLabel: '操作方式',
                padding:'0 0 0 20',
                labelWidth:60,
                width:150,
                emptyText:'请选择',
                store:new Ext.data.ArrayStore({
						fields:['value','op'],
						data:[['add','新增'],['del','删除']]
                    }),
                queryMode:'local',
                displayField:'op',
                valueField:'value'
            },{
            	xtype: 'combobox',
                name: 'op_user',
                id: 'op_user',
                fieldLabel: '操作人',
                padding:'0 0 0 20',
                labelWidth:60,
                width:150,
                store:operStore,
                queryMode:'remote',
                displayField:'name',
                emptyText:'请选择',
                valueField:'id'
            },{
                xtype: 'datefield',
                labelWidth:60,
                width:200,
                padding:'0 0 0 20',
                id: 'start_date',
                name: 'start_date',
                fieldLabel: '起始日期',
                format:'Y-m-d',
                dateRange:{begin:'start_date',end:'end_date'},
                vtype:'dateRange',
                maxValue: new Date()
            },{
            	xtype: 'datefield',
            	labelWidth:60,
                width:200,
                padding:'0 0 0 20',
                id: 'end_date',
                name: 'end_date',
                fieldLabel: '结束日期',
                format:'Y-m-d',
                dateRange:{begin:'start_date',end:'end_date'},
                vtype:'dateRange',
                maxValue: new Date()
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
                if(this.up('form').getForm().isValid())
                	storeReload(store,1);
            }
        },'->']
    });

    Ext.define('Admin.basic.grid.authLog',{
        extend:'Admin.basic.grid',
        columns:[
            {text:'id',dataIndex:'id',flex:1},
            {text:'用户',dataIndex:'user',flex:1},
            {text:'团队',dataIndex:'group',flex:1},
            {text:'部门',dataIndex:'dept',flex:1},
            {
                text:'操作',
                dataIndex:'op',
                flex:1,
                renderer:function(val){
					if(val == 'add'){
						return '<span style="color:green">新增</span>';
					}else if(val == 'del'){
						return '<span style="color:red">删除</span>';
					}
                }
            },
            {text:'权限',dataIndex:'resource',flex:1},
            {text:'操作人',dataIndex:'oper',flex:1},
            {text:'操作时间',dataIndex:'optime',flex:1}
        ],
        store:store,
        bbar: {
            xtype: 'pagingtoolbar',
            id: 'pagingbar',
            pageSize: 20,
            store: store,
            displayInfo: true,
            plugins: new Ext.ux.ProgressBarPager()
        }
	});
    
    //搜索块
    var authLogSearchForm = new Admin.basic.searchForm.authLog();
    authLogSearchForm.render(Ext.getBody());
    //列表
    var authLogGrid = new Admin.basic.grid.authLog();
    authLogGrid.render(Ext.getBody());
    //初始化数据集
    storeReload(store,1);	
});
</script>