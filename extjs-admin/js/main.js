Ext.onReady(function(){
    var menuStore = Ext.create('Ext.data.TreeStore',{
        root :{
            expanded:true,
            children:[
                    {leaf:false,text:"App管理",expanded:true,children:[
                            {leaf:true,text:'App列表',url:'grid.html'},
                            {leaf:true,text:'开发者列表',url:'form.html'},
                            {leaf:true,text:'类别列表',url:'hello.html'}
                        ]
                    },
                    {leaf:false,text:'任务管理',expanded:false,children:[
                           {leaf:true,text:'任务列表'}
                        ]
                    },
                    {leaf:false,text:'广告管理',children:[
                            {leaf:true,text:'App内广告位'}
                        ]
                    },
                    {leaf:false,text:'礼包管理',children:[
                            {leaf:true,text:'礼包列表'}
                        ]
                    },
                    {leaf:false,text:'新闻管理',children:[
                            {leaf:true,text:'新闻列表'}
                        ]
                    },
                    {leaf:false,text:'兑换管理',children:[
                            {leaf:true,text:'礼品列表'},
                            {leaf:true,text:'分类列表'},
                            {leaf:true,text:'兑换记录'}
                        ]
                    }
            ]
        }
    });
    
    var menuTree = Ext.create('Ext.tree.Panel', {
                title: '菜单',
                region : 'west',
                margins : '0 5 0 0',
                width : 200,
                collapsible: true,
                split: true,
                useArrows:true,
                store : menuStore,
                rootVisible : false,
                listeners : {
                    itemclick : function(tree, record, item, index, e, options) {
                        var nodeText = record.data.text, 
                        tabPanel = viewport.items.get(2), 
                        tabBar = tabPanel.getTabBar(), 
                        tabIndex;
                        for (var i = 0; i < tabBar.items.length; i++) {
                            if (tabBar.items.get(i).getText() === nodeText) {
                                tabIndex = i;
                            }
                        }
                        if (Ext.isEmpty(tabIndex)) {
                            tabPanel.add({
                                        title : record.data.text,
                                        html : "<iframe width='100%' height='100%' src='"+record.data.url+"'/>"
                                    });
                            tabIndex = tabPanel.items.length - 1;
                        }
                        tabPanel.setActiveTab(tabIndex);
                    }
                }
            });
        
        var viewport = Ext.create('Ext.container.Viewport', {
                layout : 'border',
                renderTo: Ext.getBody(),
                items : [menuTree, {
                            xtype : 'tabpanel',
                            region : 'center',
                            defaults: {
                                closable : true
                            }
                        }]
            });
            
 
});