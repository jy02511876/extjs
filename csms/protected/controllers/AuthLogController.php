<?php
class AuthLogController extends Controller
{
	public function init(){
		parent::init();
		if(!in_array('权限操作日志', $this->premits))
			die('权限不足(permission denied)');
	}
	
	public function actionIndex(){
		//获取所有的组
		$groupRs = BasicGroup::model()->findAll(array(
						'select'	=>	'groupid,groupname',
						'order'		=>	'groupid asc',
					));
		$group = array();
		foreach ($groupRs as $value){
			$group[] = array(
					'id'	=>	$value['groupid'],
					'name'	=>	mb_convert_encoding($value['groupname'],'UTF-8','GBK')
			);
		}
		
		//获取所有的团队
		$deptRs = BasicDept::model()->findAll(array(
						'select'	=>	'deptid,deptname',
						'order'		=>	'deptid asc',
					));
		$dept = array();
		foreach ($deptRs as $value){
			$dept[] = array(
					'id'	=>	$value['deptid'],
					'name'	=>	mb_convert_encoding($value['deptname'],'UTF-8','GBK')
			);
		}
		$this->render('index',array(
			'group'	=>	$group,
			'dept'	=>	$dept,
		));	
	}
	
	public function actionList()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$userId = intval(Yii::app()->request->getParam('user_id'));
		$groupId = intval(Yii::app()->request->getParam('group_id'));
		$deptId = intval(Yii::app()->request->getParam('dept_id'));
		$op = trim(Yii::app()->request->getParam('op'));
		$opUser = intval(Yii::app()->request->getParam('op_user'));
		$startDate = trim(Yii::app()->request->getParam('start_date'));
		$endDate = trim(Yii::app()->request->getParam('end_date'));
	
		$cond = new CDbCriteria();
		$cond->with = array('user','group','resource','oper');
		$cond->order = 'id desc';
		if($userId > 0) $cond->compare('`t`.userid', $userId);
		if($groupId > 0) $cond->compare('`t`.groupid', $groupId);
		if($deptId > 0) $cond->compare('`t`.deptid', $deptId);
		if(in_array($op,array('add','del'))) $cond->compare('op', $op);
		if($opUser >0 ) $cond->compare('op_user',$opUser);
		if($startDate == '') $startDate = '2000-01-01';
		if($endDate == '') $endDate = date('Y-m-d');
		$cond->addBetweenCondition('op_time', $startDate.' 00:00:00', $endDate.' 23:59:59');
		
		$count = AuthLog::model()->count($cond);
		
		$pages = new CPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
	
		$data = AuthLog::model()->findAll($cond);
		$rows = array();
		foreach ($data as $value){
			$rows[] = array(
				'id'	=>	$value->getPrimaryKey(),
				'resource'	=>	isset($value->resource['resourcename']) ? mb_convert_encoding($value->resource['resourcename'],'utf-8','gbk') : '',
				'user'		=>	isset($value->user['username']) ? mb_convert_encoding($value->user['username'],'utf-8','gbk') : '',
				'group'		=>	isset($value->group['groupname']) ? mb_convert_encoding($value->group['groupname'],'utf-8','gbk') : '',
				'dept'		=>	isset($value->dept['deptname']) ? mb_convert_encoding($value->dept['deptname'],'utf-8','gbk') : '',
				'op'		=>	$value->op,
				'oper'		=>	isset($value->oper['username']) ? mb_convert_encoding($value->oper['username'],'utf-8','gbk') : '',
				'optime'	=>	$value->op_time,
			);
		}
	
		echo json_encode(array(
				'count'	=>	$count,
				'rows'	=>	$rows,
		));
	}
	
	
	//所有操作人
	public function actionOper()
	{
		$users = Yii::app()->cache->get('authlog_oper');
		if($users == null){
			$cond = new CDbCriteria();
			$cond->select = 'op_user';
			$cond->distinct = true;
			
			$rs = AuthLog::model()->findAll($cond);
			$userIds = array();
			foreach($rs as $value)
				$userIds[] = $value['op_user'];
			
			$cond = new CDbCriteria();
			$cond->select = 'userid,username';
			$cond->order = 'userid asc';
			$cond->addInCondition('userid', $userIds);
			$rs = BasicUser::model()->findAll($cond);
			
			$users = array();
			foreach ($rs as $value){
				$users[] = array(
					'id'	=>	$value['userid'],
					'name'	=>	mb_convert_encoding($value['username'],'UTF-8','GBK')
				);
			}
			Yii::app()->cache->add('authlog_oper',$users);
		}
		echo json_encode(array('rows'=>$users));
	}
}