<?php
class GroupController extends Controller
{
	public function init(){
		parent::init();
		if(!in_array('管理用户组', $this->premits))
			die('权限不足(permission denied)');
	}
	
	public function actionIndex()
	{
		$this->render('index');
	}
	
	public function actionList()
	{
		$cond = new CDbCriteria();
		$cond->order = 'groupid asc';
		$model = BasicGroup::model()->findAll($cond);
		
		$rows = array();
		foreach ($model as $value){
			$rows[] = array(
					'text'	=>	mb_convert_encoding($value->groupname, 'UTF8','GBK'),
					'id'	=>	$value->groupid,
					'leaf'	=>	TRUE
			);
		}
		echo json_encode($rows);
	}
	
	
	public function actionUser()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$groupId = intval(Yii::app()->request->getParam('group_id'));
		
		$cond = new CDbCriteria();
		$cond->compare('groupid', $groupId);
		$cond->order = 'id asc';
		$cond->with = array('user');
		$count = BasicUseringroup::model()->count($cond);
		
		$pages = new CPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
		
		$data = BasicUseringroup::model()->findAll($cond);
		$rows = array();
		foreach ($data as $value){
			if(!isset($value->user->username)) continue;
			$rows[] = array(
					'id'	=>	$value->id,
					'name'	=>	mb_convert_encoding($value->user->username, 'UTF8','GBK'),
			);
		}
		
		echo json_encode(array(
				'count'	=>	$count,
				'rows'	=>	$rows,
		));
	}
	
	
	public function actionUserDel()
	{
		$ids = explode(',', trim(Yii::app()->request->getParam('ids')));
		$cond = new CDbCriteria();
		$cond->addInCondition('id', $ids);
		$this->ajax_return(BasicUseringroup::model()->deleteAll($cond));
	}
}