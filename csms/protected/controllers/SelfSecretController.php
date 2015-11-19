<?php
class SelfSecretController extends Controller
{
	public function init(){
		parent::init();
		if(!in_array('官网自助配置', $this->premits))
			die('权限不足(permission denied)');
	}
	
	public function actionIndex()
	{
		$model = new SelfSecret('search');
		$model->unsetAttributes();
		if(isset($_GET['SelfSecret']))
			$model->attributes=$_GET['SelfSecret'];
		
		$this->render('index',array(
				'model'		=>	$model,
		));
	}
	
	
	public function actionCreate()
	{
		$model = new SelfSecret();
		
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'selfSecret-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['SelfSecret']))
		{
			$model->attributes=$_POST['SelfSecret'];
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
		$model = SelfSecret::model()->findBy_id($id);
		
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'selfSecret-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['SelfSecret']))
		{
			$model->attributes=$_POST['SelfSecret'];
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
		$this->ajax_return(SelfSecret::model()->deleteByPk($id));
	}
	
	
	public function actionList()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$gameId = trim(Yii::app()->request->getParam('game_id'));
	
		$cond = new EMongoCriteria();
		if($gameId != '')
			$cond->compare('game_id', $gameId);
	
		$count = SelfSecret::model()->count($cond);
	
		$pages = new EMongoPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
	
		$data = SelfSecret::model()->find($cond);
		$rows = array();
		$games = CHtml::listData(BasicGame::model()->getGames(), 'game_code', 'game_name');
		foreach ($data as $value){
			$tmp = $value->getAttributes();
			$tmp['game'] = isset($games[$tmp['game_id']]) ? $games[$tmp['game_id']] : '';
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
		
		if($gameId <= 0)
			$this->ajax_return(false,'游戏类型不能为空');
	
		if($id != '')
			$model = SelfSecret::model()->findBy_id($id);
	
		if(!isset($model) || $model == null){
			$model = new SelfSecret();
			$model->scenario = 'create';
		}
		$model->game_id = $gameId;
	
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
		$model = SelfSecret::model()->findBy_id($id);
	
		if($model == null)
			$this->ajax_return(false);
		else{
			$return = array(
					'success'	=>	true,
					'data'		=>	array(
							'id'	=>	(string)$model->getPrimaryKey(),
							'game_id'	=>	$model->game_id,
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