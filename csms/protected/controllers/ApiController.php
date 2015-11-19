<?php
class ApiController extends CController
{
	public function actionGettable()
	{
		$id = trim(Yii::app()->request->getParam('id'));
		$rs = QuestTable::model()->findBy_id($id);
		
		if($rs != null){
			$data = array(
				'name'					=>	$rs['name'],
				'consume_point'			=>	intval($rs['consume_point']),
				'consume_time_limit'	=>	intval($rs['consume_time_limit']),
			);
			echo json_encode($data);
		}else 
			echo '';
	}
}