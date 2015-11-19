//全局常量
var HtmlEditorFontFamilies = ['微软雅黑','宋体','隶属','黑体','Arial'];
//form弹窗
Ext.define('Admin.basic.win',{
    extend: 'Ext.window.Window',
    xtype: 'basic-window',
    width: 400,
    scrollable: true,
    bodyPadding: 10,
    autoShow:false,
    closeAction:'hide',
    constrain: true,
    modal:true
});
//搜索区域
Ext.define('Admin.basic.searchForm',{
    extend: 'Ext.form.Panel',
    id:'searchForm',
    xtype: 'form-hboxlayout',
    title: '搜索',
    bodyPadding: 5,
    margin:'10 10 0 10',
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
    title: '数据列表',
    margin:'10 30 0 10',
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

//自定义验证类型：时间比较
Ext.apply(Ext.form.field.VTypes,{
	dateRange:function(val,field){
		var beginDate = null,		//开始日期
			beginDateCmp = null,	//开始日期组件
			endDate = null,			//结束日期
			endDateCmp = null,		//结束日期组件
			validStatus = true; 	//验证状态
		
		if(field.dateRange){
			//获取开始时间
			if(!Ext.isEmpty(field.dateRange.begin)){
				beginDateCmp = Ext.getCmp(field.dateRange.begin);
				beginDate = beginDateCmp.getValue();
			}
			
			if(!Ext.isEmpty(field.dateRange.end)){
				endDateCmp = Ext.getCmp(field.dateRange.end);
				endDate = endDateCmp.getValue();
			}
		}
		
		if(!Ext.isEmpty(beginDate) && !Ext.isEmpty(endDate)){
			validStatus = beginDate <= endDate;
		}
		return validStatus;
	},
	dateRangeText:'开始日期不能大于结束日期，请重新选择。'
})

//提示框
function tip(html,title){
	if(title == undefined) title = "提示";
	Ext.toast({
		html:html,
		title:title,
		width:300,
		minWidth:300,
		alwaysOnTop:true,
		slideInDuration:400,
		closable:false,
		align:'t'
	});
}

//重新加载数据
function storeReload(store,page){
    var searchForm = Ext.getCmp('searchForm').getForm();
    if(page == undefined)
    	searchForm.findField('page').setValue(store.currentPage);
    else
    	searchForm.findField('page').setValue(page);
    
    store.reload({params:searchForm.getValues()});
}