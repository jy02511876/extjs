Ext.onReady(function(){
	var items = {
	        xtype: 'form',
	        reference: 'form',
	        bodyPadding:10,
	        height:100,
	        id:'loginForm',
	        items: [{
	            xtype: 'textfield',
	            name: 'username',
	            fieldLabel: '用户名',
	            allowBlank: false
	        }, {
	            xtype: 'textfield',
	            name: 'password',
	            inputType: 'password',
	            fieldLabel: '密&nbsp;&nbsp;&nbsp;码',
	            allowBlank: false,
	            enableKeyEvents:true
	        }, {
	            xtype: 'displayfield',
	            hideEmptyLabel: false,
	            value: ''
	        }],
	        buttons: [{
        		text : '重置',
        		handler : function(){
        			this.up('form').reset();
        		}
        	},{
	            text: '登录',
	            formBind: true,
	            handler: function(){
	            	verify();
	            }
	        }],
	        listeners:{
	        	keydown:{
	        		element:'body',
	        		fn:function(e){
	        			if(e.keyCode == 13){
	        				verify();
	        			}
	        		}
	        	}
	        }
	    };
	
	function verify(){
		var form = Ext.getCmp('loginForm');
    	if(form.isValid()){
    		form.submit({
    			waitMsg:'正在进行登录验证，请稍后。。。',
    			url:"data/login.php",
    			success: function(form, action){
    				window.location.href="main.html";
    			},
    			failure: function(from, action){
    				Ext.Msg.alert('错误提示',action.result.msg);
    				form.reset();
    			}
    		});
    	}
	}

	Ext.create('Ext.window.Window', {
	    bodyPadding: 10,
	    title: '管理员登录',
	    closable: false,
	    autoShow: true,
	    items: items,
	    renderTo: Ext.getBody()
	});
	
	
});