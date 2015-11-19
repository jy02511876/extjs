<?php
class QuestTableController extends Controller
{
	public function init(){
		parent::init();
		if(!in_array('官网自助配置', $this->premits))
			die('权限不足(permission denied)');
	}
	
	public function actionIndex()
	{
		$model = new QuestTable('search');
		$model->unsetAttributes();
		if(isset($_GET['QuestTable']))
			$model->attributes=$_GET['QuestTable'];
		
		$this->render('index',array(
				'model'		=>	$model,
		));
	}
	
	
	public function actionTables()
	{
		$page = intval(Yii::app()->request->getParam('page',1))-1;
		$name = trim(Yii::app()->request->getParam('name'));
		
		$cond = new EMongoCriteria();
		if($name != '')
			$cond->compare('name', $name,true);
		
		$count = QuestTable::model()->count($cond);
		
		$pages = new EMongoPagination($count);
		$pages->pageSize = self::PAGE_SIZE;
		$pages->currentPage = $page;
		$pages->applyLimit($cond);
		
		$data = QuestTable::model()->find($cond);
		$rows = array();
		foreach ($data as $value){
			$tmp = $value->getAttributes();
			$tmp['id'] = (string)$value->getPrimaryKey();
			unset($tmp['_id']);
			$rows[] = $tmp;
		}
		
		echo json_encode(array(
			'count'	=>	$count,
			'rows'	=>	$rows,
		));
	}
	
	public function actionCreate()
	{
		$model = new QuestTable();
			
		if(isset($_POST['QuestTable']))
			$this->__save($model);
		
		$this->render('create',array(
			'model'	=>	$model
		));
	}
	
		
	public function actionUpdate()
	{
		$id = Yii::app()->request->getParam('id');
		$model = QuestTable::model()->findBy_id($id);
		if(isset($_POST['QuestTable']))
			$this->__save($model);
		
		$this->render('update',array(
				'model'	=>	$model
		));
	}
	
	
	public function actionDelete()
	{
		echo json_encode(array('success'=>true));exit;
		$return = array('success'=>false);
		$id = Yii::app()->request->getParam('id');
		
		$bool = QuestTable::model()->deleteByPk($id);
		if($bool){
			$csOrderTypeModel = new CsOrdertype();
			$csOrderTypeModel->deleteAll('contenttable=:contenttable and game_type=:game_type',array(':contenttable'=>$id,':game_type'=>Yii::app()->params['gwzzGameType']));
			$return['success'] = true;
		}
		echo json_encode($return);
	}
	
	
	public function actionElement()
	{
		$id = Yii::app()->request->getParam('id');
		$model = QuestTable::model()->findBy_id($id);
		
		if(isset($_POST['QuestTable']['_id']))
		{
			$element = array();
			$count = count($_POST['e_id']);
			for ($i=0;$i<$count;$i++){
				$element[] = array(
					'e_id'		=>	$_POST['e_id'][$i],
					'required'	=>	$_POST['required'][$i],
					'tip'		=>	$_POST['tip'][$i],
					'type'		=>	$_POST['type'][$i],
					'error_msg'	=>	$_POST['error_msg'][$i],
				);
				$model->setAttribute('element', $element);
				if(!$model->save()){
					echo '更新失败';exit;
				}
			}
		}
		
		
		$this->render('element',array(
			'model'	=>	$model,	
		));
	}
	
	
	public function getDeptName($data,$row){
		$name = '';
		$depts = CHtml::listData(BasicDept::model()->getNames(), 'deptid', 'deptname');
		if (isset($depts[$data->dept]))
			$name = $depts[$data->dept];
		
		return $name;
	}
	
	
	
	private function __save($model)
	{
		$model->attributes=$_POST['QuestTable'];
		$uploadModel = new MPicture($model, 'des_pic');
		$uploadModel->hasThumb = false;
		$names = $uploadModel->getNames();
		if (!empty($names))
			$model->des_pic = $names;
		
		if($model->save()){
			$this->redirect(array('index'));
		}
	}
	
	
	public function actionAll()
	{
		$data = QuestTable::model()->find();
		$rows = array();
		foreach($data as $value)
			$rows[] = array('id'=>(string)$value->getPrimaryKey(),'name'=>$value->name);
		
		echo json_encode(array('rows'=>$rows));
	}
}