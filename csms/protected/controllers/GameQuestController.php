<?php
class GameQuestController extends Controller
{
	public function init(){
		parent::init();
		if(!in_array('官网自助配置', $this->premits))
			die('权限不足(permission denied)');
	}
	
	public function actionIndex()
	{
		$model = new GameQuest('search');
		$model->unsetAttributes();
		if(isset($_GET['GameQuest']))
			$model->attributes=$_GET['GameQuest'];
		
		$this->render('index',array(
				'model'		=>	$model,
		));
	}
	
	
	public function actionCreate()
	{
		$model = new GameQuest();
		
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'GameQuest-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['GameQuest']))
		{
			$model->attributes=$_POST['GameQuest'];
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
		$model = GameQuest::model()->findBy_id($id);
		
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'GameQuest-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['GameQuest']))
		{
			$model->attributes=$_POST['GameQuest'];
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
		$this->ajax_return(GameQuest::model()->deleteByPk($id));
	}
	
	
	public function actionList()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$gameId = trim(Yii::app()->request->getParam('game_id'));
	
		$cond = new EMongoCriteria();
		$cond->sort = array('game_id'=>1);
		if($gameId != '')
			$cond->compare('game_id', $gameId);
	
		$count = GameQuest::model()->count($cond);
	
		$pages = new EMongoPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
	
		$data = GameQuest::model()->find($cond);
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
	
		if($gameId <= 0)
			$this->ajax_return(false,'游戏类型不能为空');
	
		if($tableId == '')
			$this->ajax_return(false,'表单问题不能为空');
	
		if($id != '')
			$model = GameQuest::model()->findBy_id($id);
	
		if(!isset($model) || $model == null){
			$model = new GameQuest();
			$model->scenario = 'create';
		}
		
		//去重判断
		if($model->game_id != $gameId || $model->table_id != $tableId || $model->scenario == 'create'){
			$cond = new EMongoCriteria();
			$cond->compare('game_id',$gameId);
			$cond->compare('table_id',$tableId);
			$count = GameQuest::model()->count($cond);
			if($count >= 1)
				$this->ajax_return(false,'重复的游戏类型和表单问题');
		}
		
		$model->game_id = $gameId;
		$model->table_id = $tableId;
	
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
		$model = GameQuest::model()->findBy_id($id);
	
		if($model == null)
			$this->ajax_return(false);
		else{
			$return = array(
					'success'	=>	true,
					'data'		=>	array(
							'id'	=>	(string)$model->getPrimaryKey(),
							'game_id'	=>	$model->game_id,
							'table_id'	=>	$model->table_id,
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
	
	
	public function getTable($data,$row){
		$tableName = '';
		$tables = CHtml::listData(QuestTable::model()->getTables(), 'id', 'name');
		if (isset($tables[$data->table_id]))
			$tableName = $tables[$data->table_id];
	
		return $tableName;
	}
}