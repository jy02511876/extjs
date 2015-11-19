//form弹窗
Ext.define('Admin.basic.win',{
    extend: 'Ext.window.Window',
    xtype: 'basic-window',
    width: 400,
    title: '新增',
    scrollable: true,
    bodyPadding: 10,
    autoShow:false,
    closeAction:'hide',
    constrain: true
});
//搜索区域
Ext.define('Admin.basic.searchForm',{
    extend: 'Ext.form.Panel',
    xtype: 'form-hboxlayout',
    title: '搜索',
    bodyPadding: 5,
    id : 'searchForm',  
    border:false,
    defaults: {
        border: false,
        flex: 1,
        defaultType: 'textfield'
    },
    layout: 'vbox'
});

//数据展示框
Ext.define('Admin.basic.grid',{
    extend:'Ext.grid.Panel',
    xtype: 'progress-bar-pager',
    frame: true,
    width: '100%',
    title: '数据列表',
    changeRenderer: function(val) {
        if (val > 0) {
            return '<span style="color:green;">' + val + '</span>';
        } else if(val < 0) {
            return '<span style="color:red;">' + val + '</span>';
        }
        return val;
    },
    pctChangeRenderer: function(val){
        if (val > 0) {
            return '<span style="color:green;">' + val + '%</span>';
        } else if(val < 0) {
            return '<span style="color:red;">' + val + '%</span>';
        }
        return val;
    }
});


function storeReload(store,page){
    var form = Ext.getCmp('searchForm').getForm();
    if(page != "undefined")
        form.findField('page').setValue(store.currentPage);

    store.reload({params:form.getValues()});
}