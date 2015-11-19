<?php
 class SelfLightConsumeController extends Controller
{
	public function actionIndex()
	{
		$model = new SelfLightConsume('search');
		$model->unsetAttributes();
		if(isset($_GET['SelfLightConsume']))
			$model->attributes=$_GET['SelfLightConsume'];
		
		$this->render('index',array(
				'model'		=>	$model,
		));
	}
	
	
	public function actionCreate()
	{
		$model = new SelfLightConsume();
		
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'selfLightConsume-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['SelfLightConsume']))
		{
			$model->attributes=$_POST['SelfLightConsume'];
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
		$model = SelfLightConsume::model()->findBy_id($id);
		
		if (isset($_POST['ajax']) && $_POST['ajax'] === 'selfLightConsume-form'){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['SelfLightConsume']))
		{
			$model->attributes=$_POST['SelfLightConsume'];
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
		$bool = SelfLightConsume::model()->deleteByPk($id);
		if($bool)
			$this->redirect(array('index'));
		else
			echo '操作失败';exit;
	}
	

	
	public function getGameName($data,$row){
		$gameName = '';
		$games = CHtml::listData(BasicGame::model()->getGames(), 'game_code', 'game_name');
		if (isset($games[$data->game_id]))
			$gameName = $games[$data->game_id];
		
		return $gameName;
	}
}