function showTip(info){
	$('tips').innerHTML	=	info;
}
function add(id){
	if (id)
	{
		 window.location  = URL+"/add/id/"+id;
	}else{
		 window.location= URL+"/add/";
	}
}
function edit(id){
	var keyValue;
	if (id){
		keyValue = id;
	}else{
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue){
		alert('请选择编辑项！');
		return false;
	}
	window.location =  URL+"/edit/id/"+keyValue;
}
function doDelete(status){
	if (status==1){
		$('.row').find(':checkbox').each(function(){
			if($(this).attr('checked')=='checked'){
				$(this).parent().parent().empty();
			}
		})
	}
}
function del(id){
    var keyValue;
    if (id)
    {
        keyValue = id;
    }else {
        keyValue = getSelectCheckboxValues();
    }
    if (!keyValue)
    {
        alert('请选择删除项！');
        return false;
    }
    if (window.confirm('确实要删除选择项吗？'))
    {
        ThinkAjax.send(URL+"/delete/","id="+keyValue+'&ajax=1',doDelete);
    }
}
function getSelectCheckboxValues(){
	var val=new Array();
	$('.row').find(':checkbox').each(function(){
		if($(this).attr('checked')=='checked'){
			val.push($(this).val());
		}
	})
	return val.join(',');
}
function changrowbgcolor(e){
	var checked=true;
	$(':checkbox[name="key"]').each(function(){
		if($(this).attr('checked')=='checked'){color='#c2efc9';}else{color='#fff';checked=false;}
		$(this).parent().parent().css('background',color);
	});
	if (false!==e){
		$('#check').attr('checked',checked);
	}
}
function CheckAll(id){
	$(':checkbox[name="key"]').attr('checked',id.checked);
	changrowbgcolor(false);
}


function adv(o){
	if($('#SearchC').css('display')=='none'){
		$('#SearchC').css('display','inline');
		$('#SearchA').attr('class','adv left');
		o.value='隐藏';
		$(o).parent().attr("class","btn adv2");
	}else{
		$('#SearchC').css('display','none');;
		$('#SearchA').attr('class','adv');
		o.value='高级';
		$(o).parent().attr("class","btn adv1");
	}
}

function fleshVerify(){
	//重载验证码
	var timenow = new Date().getTime();
	$('verifyImg').src= APP+'/Public/verify/'+timenow;
}
function sortBy (field,sort){
	location.href = URL+"/index/_order/"+field+"/_sort/"+sort;
}

function sendForm(formId,action,response,target){
	// Ajax方式提交表单
	if (CheckForm($(formId),'ThinkAjaxResult'))//表单数据验证
	{
		ThinkAjax.sendForm(formId,action,response,target);
	}
}
function getPageScroll(){ 
	var yScroll; 
	if (self.pageYOffset) { 
		yScroll = self.pageYOffset; 
	} else if (document.documentElement && document.documentElement.scrollTop){ // Explorer 6 Strict 
		yScroll = document.documentElement.scrollTop; 
	} else if (document.body) {// all other Explorers 
		yScroll = document.body.scrollTop; 
	} 
	arrayPageScroll = new Array('',yScroll) 
	return arrayPageScroll; 
} 



function sort(id){
	var keyValue;
	keyValue = getSelectCheckboxValues();
	location.href = URL+"/sort/sortId/"+keyValue;
}

function cache(){
	ThinkAjax.send(URL+'/cache','ajax=1');
}
function child(id){
	location.href = URL+"/index/pid/"+id;
}
function action(id){
	location.href = URL+"/action/groupId/"+id;
}

function access(id){
	location.href= URL+"/access/id/"+id;
}
function app(id){
	location.href = URL+"/app/groupId/"+id;
}

function module(id){
	location.href = URL+"/module/groupId/"+id;
}

function user(id){
	location.href = URL+"/user/id/"+id;
}
function read(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/read/id/"+keyValue;
}