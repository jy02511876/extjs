Ext.onReady(function(){
    var companyStore = Ext.create('Ext.data.Store',{
        fields:['id','name','price','lastChange'],
        pageSize:20,
        proxy: {
            type: 'ajax',
            url : 'data/company.php',
            reader: {
                type : 'json',
                totalProperty:'recordCount',
                rootProperty : 'data'
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

    var companyForm = {
        xtype: 'form',
        bodyPadding:10,
        id:'companyForm',
        items: [
        {
            xtype: 'hiddenfield',
            id:'id',
            name: 'id'
        }, {
            xtype: 'textfield',
            name: 'name',
            id: 'name',
            fieldLabel: '公司名',
            allowBlank: false
        }, {
            xtype: 'textfield',
            id: 'price',
            name: 'price',
            fieldLabel: '价格',
            allowBlank: false
        }, {
            xtype: 'datefield',
            id: 'lastChange',
            name: 'lastChange',
            fieldLabel: '日期',
            format:'Y-m-d H:i:s',
            maxValue: new Date()
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
        var form = Ext.getCmp('companyForm');
        if(form.isValid()){
            form.submit({
                waitMsg:'正在提交数据，请稍后。。。',
                url:'data/save.php',
                success: function(form, action){
                    Ext.Msg.alert('提示',action.result.msg);
                    form.reset();
                    companyFormWin.hide();
                    storeReload(companyStore);
                },
                failure: function(from, action){
                    Ext.Msg.alert('错误提示',action.result.msg);
                }
            });
        }
    }

    Ext.define('Admin.basic.searchForm.company',{
        extend: 'Admin.basic.searchForm',
        id:'searchForm',
        items: [{
            items:[{
                xtype: 'hiddenfield',
                name: 'page',
                id:'page'
            }]
        },{
            layout: 'hbox',
            items: [{
                fieldLabel: 'Name',
                name: 'name',
                labelWidth: 80,
                width: 300
            },{
                fieldLabel: 'Price',
                name: 'price',
                labelWidth: 80,
                width: 300,
                margin: '0 0 5 10'
            }]
        }, {
            layout: 'hbox',
            items: [{
                xtype: 'datefield',
                fieldLabel: '起始日期',
                name: 'start_date'
            },{
                xtype: 'datefield',
                fieldLabel: '结束日期',
                name: 'end_date',
                margin: '0 0 5 10'
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
                storeReload(companyStore,1);
            }
        },'->']
    });

    Ext.define('Admin.basic.win.company',{
        extend: 'Admin.basic.win',
        items:companyForm
    });


    Ext.define('Admin.basic.grid.company',{
        extend:'Admin.basic.grid',
        columns:[
            {text:'Name',dataIndex:'name'},
            {text:'Price',dataIndex:'price',flex:1},
            {text:'Last Change',dataIndex:'lastChange',flex:1}
        ],
        store:companyStore,
        bbar: {
            xtype: 'pagingtoolbar',
            id: 'pagingbar',
            pageSize: 20,
            store: companyStore,
            displayInfo: true,
            plugins: new Ext.ux.ProgressBarPager()
        },
        tbar:[{
                iconCls: "glyphicon glyphicon-plus",
                glyph: null,
                text:'新增',
                handler : function(){
                    Ext.getCmp('companyForm').reset();
                    companyFormWin.show();
                }
            },'-',
            {
                iconCls: "glyphicon glyphicon-pencil",
                glyph: null,
                text:'编辑',
                handler : function(){
                    var rows = companyGrid.getSelection();
                    if(rows.length != 1){
                        Ext.Msg.alert('错误提示','请选择要编辑的数据！');
                        return;
                    }
                    Ext.Ajax.request({
                        url:'data/find.php',
                        params:{
                            id:rows[0].get('id'),
                        },
                        success:function(response){
                            var data = Ext.JSON.decode(response.responseText);
                            if(data.success){
                                var editForm = Ext.getCmp('companyForm').getForm();
                                editForm.findField('id').setValue(data.data.id);
                                editForm.findField('name').setValue(data.data.name);
                                editForm.findField('price').setValue(data.data.price);
                                editForm.findField('lastChange').setValue(data.data.last_change);
                                companyFormWin.show();
                            }else{
                                Ext.Msg.alert('错误提示','您选择的数据不存在了！');
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
                            var rows = companyGrid.getSelection();
                            if(rows.length != 1){
                                Ext.Msg.alert('错误提示','请选择数据！');
                                return;
                            }
                            Ext.Ajax.request({
                                url:'data/delete.php',
                                params:{
                                    id:rows[0].get('id'),
                                },
                                success:function(response){
                                    var data = Ext.JSON.decode(response.responseText);
                                    storeReload(companyStore);
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
    });
    

    //form弹窗
    var companyFormWin = new Admin.basic.win.company();
    //搜索块
    var companySearchForm = new Admin.basic.searchForm.company();
    companySearchForm.render(Ext.getBody());
    //列表
    var companyGrid = new Admin.basic.grid.company();
    companyGrid.render(Ext.getBody());
    //初始化数据集
    storeReload(companyStore,1);
});