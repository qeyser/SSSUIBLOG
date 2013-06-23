// Ajax for ThinkPHP
document.write("<div id='ThinkAjaxResult' class='ThinkAjax' style=\"position:absolute;right:0;\" ></div>");
var m = {
	'\b': '\\b',
	'\t': '\\t',
	'\n': '\\n',
	'\f': '\\f',
	'\r': '\\r'
};
var ThinkAjax = {
	method:'POST',			// 默认发送方法
	bComplete:false,			// 是否完成
	updateTip:'数据处理中...',	// 后台处理中提示信息
	image:['Admin/Tpl/default/Public/images/loading2.gif', 'Admin/Tpl/default/Public/images/ok.gif','Admin/Tpl/default/Public/images/update.gif'], // 依次是处理中 成功 和错误 显示的图片
	tipTarget:'ThinkAjaxResult',	// 提示信息对象
	showTip:true,	 // 是否显示提示信息，默认开启
	status:0, //返回状态码
	info:'',	//返回信息
	data:'',	//返回数据
	type:'', // JSON EVAL XML ...
	intval:0,
	options:{},
	debug:false,
	activeRequestCount:0,
	// Ajax连接初始化
	getTransport: true,
	tip:function (tips){
		this.options['tip']	=	tips;
		return this;
	},
	target:function (taget){
		this.options['target']	=	target;
		return this;
	},
	response:function (response){
		this.options['response']	=	response;
		return this;
	},
	url:function (url){
		this.options['url']	=	url;
		return this;
	},
	params:function (vars){
		this.options['var']	=	vars;
		return this;
	},
	loading:function (target,tips){
		if ($(target))
		{
			//var arrayPageSize = getPageSize();
			//var arrayPageScroll = getPageScroll();
			$("#"+target).css({"right" :'5px','top':'0px','display':'block'});
			// 显示正在更新
			if ($('loader'))
			{
				$('#loader').hide();
			}
			if ('' != this.image[0])
			{
				$("#"+target).html('<IMG SRC="'+this.image[0]+'"  BORDER="0" ALT="loading..." align="absmiddle"> '+tips);
			}else{
				$("#"+target).html(tips);
			}
			$("#"+target).delay(3000).fadeOut('fast');
		}
	},
	ajaxResponse:function(msg,response,target){
		this.status = msg.status;
		this.info	=	 msg.info;
		this.data	= msg.data;
		this.type	=	msg.type;
		
		if (this.type == 'EVAL' ){
			// 直接执行返回的脚本
			eval($this.info);
		}else{
			if (response != undefined){
				try	{ (response).apply(this,[this.status,this.info,this.type,this.data]);}
				catch (e){}
			}
		}
		if ($('#'+target)){
			if (this.showTip && this.info!= undefined && this.info!=''){
				if (this.status==1){
					if ('' != this.image[1]){
						$('#'+target).html('<IMG SRC="'+this.image[1]+'"  BORDER="0" ALT="success..." align="absmiddle"> <span style="color:blue">'+this.info+'</span>');
					}else{
						$('#'+target).html('<span style="color:blue">'+this.info+'</span>');
					}
					
				}else{
					if ('' != this.image[2]){
						$('#'+target).html('<IMG SRC="'+this.image[2]+'"  BORDER="0" ALT="error..." align="absmiddle"> <span style="color:red">'+this.info+'</span>');
					}else{
						$('#'+target).html('<span style="color:red">'+this.info+'</span>');
					}
				}
			}
			// 提示信息停留5秒
			if (this.showTip){
				$('#'+target).delay(5000).fadeOut("fast");
			}
		}

		
	},
	// 发送Ajax请求
	send:function(url,pars,response,target,tips)
	{
		//var xmlhttp = this.getTransport();
		url = (url == undefined)?this.options['url']:url;
		pars = (pars == undefined)?this.options['var']:pars;
		if (target == undefined){
			target = (this.options['target'])?this.options['target']:this.tipTarget;
		}
		if (tips == undefined) {
			tips = (this.options['tip'])?this.options['tip']: this.updateTip;
		}
		if (this.showTip){
			this.loading(target,tips);
		}
		if (this.intval){
			window.clearTimeout(this.intval);
		}
		this.activeRequestCount++;
		this.bComplete = false;
		var _self = this;
		$.ajax({
			url:url,
			type:this.method,
			data:pars,
			dataType:'json',
			error:function(){
				$('#'+target).html("服务器返回数据出错!");
				$('#'+target).fadeOut("fast");
			},
			success:function(msg){
				_self.bComplete = true;
				_self.activeRequestCount--;
				_self.ajaxResponse(msg,response,target);
			}
		});
	},
	// 发送表单Ajax操作，暂时不支持附件上传
	sendForm:function(formId,url,response,target)
	{
		vars =$("#"+formId).serialize();
		this.send(url,vars,response,target);
	},
	// 绑定Ajax到HTML元素和事件
	// event 支持根据浏览器的不同 
	// 包括 focus blur mouseover mouseout mousedown mouseup submit click dblclick load change keypress keydown keyup
	bind:function(source,event,url,vars,response,target,tips)
	{
		var _self = this;
	   $(source).addEvent(event,function (){_self.send(url,vars,response,target,tips)});
	},
	// 页面加载完成后执行Ajax操作
	load:function(url,vars,response,target,tips)
	{
		var _self = this;
	   window.addEvent('load',function (){_self.send(url,vars,response,target,tips)});
	},
	// 延时执行Ajax操作
	time:function(url,vars,time,response,target,tips)
	{
		var _self = this;
		myTimer =  window.setTimeout(function (){_self.send(url,vars,response,target,tips)},time);
	},
	// 定制执行Ajax操作
	repeat:function(url,vars,intervals,response,target,tips)
	{
		var _self = this;
		_self.send(url,vars,response,target);
		myTimer = window.setInterval(function (){_self.send(url,vars,response,target,tips)},intervals);
	},
	sendFile:function(id,url){
		var frame	=		this.createUploadIframe(id);
		var form	=		this.createUploadForm(id,url);
        if(form.attr('encoding')){
            form.attr('encoding','multipart/form-data');
        }else{
            form.attr('enctype' ,'multipart/form-data');
        }
        form.submit();
	},
	// 创建上传的IFrame
    createUploadIframe: function(id, uri)
	{
            var frameId = 'ThinkUploadFrame' + id;
			var io='<iframe id="' + frameId + '" name="' + frameId + '" src="javascript:false" style="position:absolute;top:-1000px;left:-1000px;display:none;"></iframe>';
			$('body').append(form);
			return $('#'+frameId);
    },
	// 创建上传表单
    createUploadForm: function(id,url)
	{
		//create form	
		var formId				=		'ThinkUploadForm' + id;
		var fileId				=		'ThinkUploadFile' + id;
		var form='<form id="ThinkUploadForm'+id+'" name="ThinkUploadForm'+id+'" action="'+url+'" method="POST" enctype="multipart/form-data" target="ThinkUploadFrame'+id+'" style="position:absolute;top:-1200px;left:-1200px;display:none;"><input type="file" /></form>';
		$('body').append(form);
		return $('#ThinkUploadForm'+id);
    }
}
