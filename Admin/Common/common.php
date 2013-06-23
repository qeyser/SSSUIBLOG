<?php
include 'function.php';
function showAbstruct($content){
	return "<div class=\"Abstruct\">".strip_tags($content)."</div>";
}
/*新窗口显示图片*/
function showImg($src){
	return "<a href=\"#\" class=\"Abstruct\" type=\"img\">{$src}</a>";;
}
/* 推荐*/
function SF($type,$str){
	//model=star&field=renzheng&value=1&str0=推荐&str1=取消&id=121
	parse_str($str);
	$id = $id ? $id : 0;
	$str0 = $str0 ? $str0 : '推荐';
	$str1 = $str1 ? $str1 : '取消推荐';
	$field = $field ? $field : 'status';
	$model = $model ? $model : '';
	$str='str'.$type;
	$txt = '<a href="__URL__/push/id/'.$id.'/field/'.$field.'/model/'.$model.'/value/'.$value.'">'.$$str.'</a>';
	return $txt;
}
function GetNodeGroupName($id){
	return GTC($id,'Group','name');
}
function getGroupName($id){
	return GTC($id,'Role','name');
}
?>
