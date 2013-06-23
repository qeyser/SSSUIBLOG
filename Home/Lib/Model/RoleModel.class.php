<?php
// +----------------------------------------------------------------------
// | 角色模型
// +----------------------------------------------------------------------
// | Author: 李乾坤 <261001642@qq.com>
// +----------------------------------------------------------------------
class RoleModel extends CommonModel {
	protected $_validate = array(
		array('name','require','名称必须'),
		);

	protected $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);

	function setGroupApps($groupId,$appIdList)
	{
		if(empty($appIdList)) {
			return true;
		}
		$id = implode(',',$appIdList);
		$where = 'a.id ='.$groupId.' AND b.id in('.$id.')';
		$result = $this->db->execute('INSERT INTO '.$this->tablePrefix.'access (role_id,node_id,pid,level) SELECT a.id, b.id,b.pid,b.level FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function getGroupAppList($groupId)
	{	$sql='select b.id,b.title,b.name from '.$this->tablePrefix.'access as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid=0 and a.role_id='.$groupId.' ';
		$rs = $this->db->query($sql);
		return $rs;
	}

	function delGroupApp($groupId)
	{
		$table = $this->tablePrefix.'access';
		$result = $this->db->execute('delete from '.$table.' where level=1 and role_id='.$groupId);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function delGroupModule($groupId,$appId)
	{
		$table = $this->tablePrefix.'access';
		$result = $this->db->execute('delete from '.$table.' where level=2 and pid='.$appId.' and role_id='.$groupId);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function getGroupModuleList($groupId,$appId)
	{
		$table = $this->tablePrefix.'access';
		$sql='select b.id,b.title,b.name from '.$table.' as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid='.$appId.' and a.role_id='.$groupId.' ';
		$rs = $this->db->query($sql);
		return $rs;
	}

	function setGroupModules($groupId,$moduleIdList)
	{
		if(empty($moduleIdList)) {
			return true;
		}
		if(is_array($moduleIdList)) {
			$moduleIdList = implode(',',$moduleIdList);
		}
		$where = 'a.id ='.$groupId.' AND b.id in('.$moduleIdList.')';
		$rs = $this->db->execute('INSERT INTO '.$this->tablePrefix.'access (role_id,node_id,pid,level) SELECT a.id, b.id,b.pid,b.level FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function delGroupAction($groupId,$moduleId)
	{
	    $table = $this->tablePrefix.'access';

	    $result = $this->db->execute('delete from '.$table.' where level=3 and pid='.$moduleId.' and role_id='.$groupId);
	    if($result===false) {
	        return false;
	    }else {
	        return true;
	    }
	}

	function getGroupActionList($groupId,$moduleId)
	{
	    $table = $this->tablePrefix.'access';
	    $rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid='.$moduleId.' and  a.role_id='.$groupId.' ');
	    return $rs;
	}

	function setGroupActions($groupId,$actionIdList)
	{
	    if(empty($actionIdList)) {
	        return true;
	    }
		if(is_array($actionIdList)) {
		    $actionIdList = implode(',',$actionIdList);
		}
	    $where = 'a.id ='.$groupId.' AND b.id in('.$actionIdList.')';
	    $rs = $this->db->execute('INSERT INTO '.$this->tablePrefix.'access (role_id,node_id,pid,level) SELECT a.id, b.id,b.pid,b.level FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where);
	    if($result===false) {
	        return false;
	    }else {
	        return true;
	    }
	}
	function delGroupArea($groupId,$actionId)
	{
		$table = $this->tablePrefix.'access';
		$sql='delete from '.$table.' where level=4 and pid='.$actionId.' and role_id='.$groupId;
		//echo $sql;
		$result = $this->db->execute($sql);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function getGroupAreaList($groupId,$actionId)
	{
		$table = $this->tablePrefix.'access';
		$rs = $this->db->query('select b.id,b.title,b.name from '.$table.' as a ,'.$this->tablePrefix.'node as b where a.node_id=b.id and  b.pid='.$actionId.' and  a.role_id='.$groupId.' ');
		return $rs;
	}

	function setGroupAreas($groupId,$areaIdList)
	{
		if(empty($areaIdList)) {
			return true;
		}
		if(is_array($areaIdList)) {
			$areaIdList = implode(',',$areaIdList);
		}
		$where = 'a.id ='.$groupId.' AND b.id in('.$areaIdList.')';
		$sql='INSERT INTO '.$this->tablePrefix.'access (role_id,node_id,pid,level,module) SELECT a.id, b.id,b.pid,b.level,b.name FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'node b WHERE '.$where;
		//echo $sql;
		$rs = $this->db->execute($sql);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}
	function getGroupUserList($groupId)
	{
		$table = $this->tablePrefix.'role_user';
		$rs = $this->db->query('select b.id,b.nickname,b.email from '.$table.' as a ,'.$this->tablePrefix.'user as b where a.user_id=b.id and  a.role_id='.$groupId.' ');
		return $rs;
	}

	function delGroupUser($groupId)
	{
		$table = $this->tablePrefix.'role_user';

		$result = $this->db->execute('delete from '.$table.' where role_id='.$groupId);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function setGroupUser($groupId,$userId) {
		$sql	=	"INSERT INTO ".$this->tablePrefix.'role_user (role_id,user_id) values ('.$groupId.','.$userId.')';
		$result	=	$this->execute($sql);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

	function setGroupUsers($groupId,$userIdList)
	{
		if(empty($userIdList)) {
			return true;
		}
		if(is_string($userIdList)) {
			$userIdList = explode(',',$userIdList);
		}
		array_walk($userIdList, array($this, 'fieldFormat'));
		$userIdList	 =	 implode(',',$userIdList);
		$where = 'a.id ='.$groupId.' AND b.id in('.$userIdList.')';
		$rs = $this->execute('INSERT INTO '.$this->tablePrefix.'role_user (role_id,user_id) SELECT a.id, b.id FROM '.$this->tablePrefix.'role a, '.$this->tablePrefix.'user b WHERE '.$where);
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}

    protected function fieldFormat(&$value)
    {
        if(is_int($value)) {
            $value = intval($value);
        } else if(is_float($value)) {
            $value = floatval($value);
        }else if(is_string($value)) {
            $value = '"'.addslashes($value).'"';
        }
        return $value;
    }


}
?>