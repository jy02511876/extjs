<?php
class SelfGameAlertController extends Controller
{
	public function init(){
		parent::init();
		if(!in_array('官网自助配置', $this->premits))
			die('权限不足(permission denied)');
	}
	
	public function actionIndex()
	{
		$model = new SelfGameAlert('search');
		$model->unsetAttributes();
		if(isset($_GET['SelfGameAlert']))
			$model->attributes=$_GET['SelfGameAlert'];
		
		$this->render('index',array(
				'model'		=>	$model,
		));
	}
	
	
	public function actionCreate()
	{
		$model = new SelfGameAlert();
		
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'selfGameAlert-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['SelfGameAlert']))
		{
			$model->attributes=$_POST['SelfGameAlert'];
			if($model->save())
				$this->redirect(array('index'));
		}
				
		$this->render('create',array(
			'model'	=>	$model
		));
	}
	
		
	public function actionUpdate()
	{
		$id = Yii::app()->request->getParam('id');
		$model = SelfGameAlert::model()->findBy_id($id);
		
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'selfGameAlert-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['SelfGameAlert']))
		{
			$model->attributes=$_POST['SelfGameAlert'];
			if($model->save())
				$this->redirect(array('index'));
		}
		
		$this->render('update',array(
				'model'	=>	$model
		));
	}
	
	
	public function actionDelete()
	{
		$id = Yii::app()->request->getParam('id');
		$this->ajax_return(SelfGameAlert::model()->deleteByPk($id));
	}
	
	
	public function actionList()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$gameId = trim(Yii::app()->request->getParam('game_id'));
	
		$cond = new EMongoCriteria();
		$cond->sort = array('game_id'=>1);
		if($gameId != '')
			$cond->compare('game_id', $gameId);
	
		$count = SelfGameAlert::model()->count($cond);
	
		$pages = new EMongoPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
	
		$data = SelfGameAlert::model()->find($cond);
		$rows = array();
		$games = CHtml::listData(BasicGame::model()->getGames(), 'game_code', 'game_name');
		$tableRs = QuestTable::model()->find();
		$tables = array();
		foreach ($tableRs as $value)
			$tables[(string)$value->getPrimaryKey()] = $value->name;
		
		foreach ($data as $value){
			$tmp = $value->getAttributes();
			$tmp['game'] = @isset($games[$tmp['game_id']]) ? $games[$tmp['game_id']] : '';
			$tmp['table'] = @isset($tables[$tmp['table_id']]) ? $tables[$tmp['table_id']] : '';
			$tmp['alert'] = mb_substr($tmp['alert'], 0,100,'UTF8');
			$tmp['id'] = (string)$value->getPrimaryKey();
			unset($tmp['_id']);
			$rows[] = $tmp;
		}
	
		echo json_encode(array(
				'count'	=>	$count,
				'rows'	=>	$rows,
		));
	}
	
	
	public function actionSave()
	{
		$id = trim(Yii::app()->request->getParam('id'));
		$gameId = intval(Yii::app()->request->getParam('game_id'));
		$tableId = trim(Yii::app()->request->getParam('table_id'));
		$alert = trim(Yii::app()->request->getParam('alert'));
		
		if($gameId <= 0)
			$this->ajax_return(false,'游戏类型不能为空');
		if($tableId == '')
			$this->ajax_return(false,'表单问题不能为空');
		if($alert == '')
			$this->ajax_return(false,'注意事项不能为空');
	
		if($id != '')
			$model = SelfGameAlert::model()->findBy_id($id);
	
		if(!isset($model) || $model == null){
			$model = new SelfGameAlert();
			$model->scenario = 'create';
		}
		
		//去重判断
		if($model->game_id != $gameId || $model->table_id != $tableId || $model->scenario == 'create'){
			$cond = new EMongoCriteria();
			$cond->compare('game_id',$gameId);
			$cond->compare('table_id',$tableId);
			$count = SelfGameAlert::model()->count($cond);
			if($count >= 1)
				$this->ajax_return(false,'重复的游戏类型和表单问题');
		}
		
		$model->game_id = $gameId;
		$model->table_id = $tableId;
		$model->alert = $alert;
		
		if($model->validate() && $model->save())
			$this->ajax_return(true,'操作成功');
		else{
			$msg = '';
			foreach ($model->getErrors() as $value)
				$msg .= implode('<br>', $value).'<br>';
				
			$this->ajax_return(false,substr($msg, 0,-4));
		}
	
	}
	
	
	public function actionFind()
	{
		$id = Yii::app()->request->getParam('id');
		$model = SelfGameAlert::model()->findBy_id($id);
	
		if($model == null)
			$this->ajax_return(false);
		else{
			$return = array(
					'success'	=>	true,
					'data'		=>	array(
							'id'	=>	(string)$model->getPrimaryKey(),
							'game_id'	=>	$model->game_id,
							'table_id'	=>	$model->table_id,
							'alert'		=>	$model->alert,
					),
			);
			echo json_encode($return);
			Yii::app()->end();
		}
	}
	

	
	public function getGameName($data,$row){
		$gameName = '';
		$games = CHtml::listData(BasicGame::model()->getGames(), 'game_code', 'game_name');
		if (isset($games[$data->game_id]))
			$gameName = $games[$data->game_id];
		
		return $gameName;
	}
}