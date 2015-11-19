<?php
class ResourceController extends Controller
{
	public function init(){
		parent::init();
		if(!in_array('权限管理', $this->premits))
			die('权限不足(permission denied)');
	}
	
	
	public function actionIndex()
	{
		$this->render('index');	
	}
	
	public function actionList()
	{
		$cond = new CDbCriteria();
		$cond->order = 'orderby asc';
		$cond->with = array('resource');
		$model = BasicModel::model()->findAll($cond);
		
		$rows = array();
		foreach ($model as $value){
			$children = array();
			foreach ($value['resource'] as $val){
				$children[] = array(
					'text'	=>	mb_convert_encoding($val->resourcename, 'UTF8','GBK'),
					'id'	=>	$val->resourceid,
					'leaf'	=>	TRUE,
				);
			}
			$rows[] = array(
				'text'	=>	mb_convert_encoding($value->modelname, 'UTF8','GBK'),
				'id'	=>	$value->modelid,
				'leaf'	=>	FALSE,
				'children'	=>	$children
			);
		}
		echo json_encode($rows);
	}
	
	
	public function actionUser()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$resourceId = intval(Yii::app()->request->getParam('resource_id'));
		
		$cond = new CDbCriteria();
		$cond->compare('resourceid', $resourceId);
		$cond->order = 'id asc';
		$cond->with = array('user');
		$count = BasicResource2user::model()->count($cond);
		
		$pages = new CPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
		
		$data = BasicResource2user::model()->findAll($cond);
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
	
	
	public function actionGroup()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$resourceId = intval(Yii::app()->request->getParam('resource_id'));
		
		$cond = new CDbCriteria();
		$cond->compare('resourceid', $resourceId);
		$cond->order = 'id asc';
		$cond->with = array('group');
		$count = BasicResource2group::model()->count($cond);
	
		$pages = new CPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
	
		$data = BasicResource2group::model()->findAll($cond);
		$rows = array();
		foreach ($data as $value){
			$rows[] = array(
					'id'	=>	$value->id,
					'name'	=>	mb_convert_encoding($value->group->groupname, 'UTF8','GBK'),
			);
		}
	
		echo json_encode(array(
				'count'	=>	$count,
				'rows'	=>	$rows,
		));
	}
	
	
	public function actionDept()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$resourceId = intval(Yii::app()->request->getParam('resource_id'));
		
		$cond = new CDbCriteria();
		$cond->compare('resourceid', $resourceId);
		$cond->order = 'id asc';
		$cond->with = array('dept');
		$count = BasicResource2dept::model()->count($cond);
	
		$pages = new CPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
	
		$data = BasicResource2dept::model()->findAll($cond);
		$rows = array();
		foreach ($data as $value){
			$rows[] = array(
					'id'	=>	$value->id,
					'name'	=>	mb_convert_encoding($value->dept->deptname, 'UTF8','GBK'),
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
		
		$rs = BasicResource2user::model()->findAll($cond);
		if(BasicResource2user::model()->deleteAll($cond)){
			//添加删除日志
			$model = new AuthLog();
			$auth = Yii::app()->session->get('CAuthorize');
			foreach ($rs as $value){
				$model->unsetAttributes();
				$model->isNewRecord = true;
				$model->resourceid = $value->resourceid;
				$model->userid = $value->userid;
				$model->op = 'del';
				$model->op_user = $auth['user']->mUserid;
				$model->op_time = date('Y-m-d H:i:s');
				$model->save();
			}
			$this->ajax_return(true);
		}
		$this->ajax_return(false);
	}
	
	
	public function actionGroupDel()
	{
		$ids = explode(',', trim(Yii::app()->request->getParam('ids')));
	
		$cond = new CDbCriteria();
		$cond->addInCondition('id', $ids);
	
		$rs = BasicResource2group::model()->findAll($cond);
		if(BasicResource2group::model()->deleteAll($cond)){
			//添加删除日志
			$model = new AuthLog();
			$auth = Yii::app()->session->get('CAuthorize');
			foreach ($rs as $value){
				$model->unsetAttributes();
				$model->isNewRecord = true;
				$model->resourceid = $value->resourceid;
				$model->groupid = $value->groupid;
				$model->op = 'del';
				$model->op_user = $auth['user']->mUserid;
				$model->op_time = date('Y-m-d H:i:s');
				$model->save();
			}
			$this->ajax_return(true);
		}
		$this->ajax_return(false);
	}
	
	
	public function actionDeptDel()
	{
		$ids = explode(',', trim(Yii::app()->request->getParam('ids')));
	
		$cond = new CDbCriteria();
		$cond->addInCondition('id', $ids);
	
		$rs = BasicResource2dept::model()->findAll($cond);
		if(BasicResource2dept::model()->deleteAll($cond)){
			//添加删除日志
			$model = new AuthLog();
			$auth = Yii::app()->session->get('CAuthorize');
			foreach ($rs as $value){
				$model->unsetAttributes();
				$model->isNewRecord = true;
				$model->resourceid = $value->resourceid;
				$model->deptid = $value->deptid;
				$model->op = 'del';
				$model->op_user = $auth['user']->mUserid;
				$model->op_time = date('Y-m-d H:i:s');
				$model->save();
			}
			$this->ajax_return(true);
		}
		$this->ajax_return(false);
	}
}