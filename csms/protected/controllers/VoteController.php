<?php
class VoteController extends Controller
{

	
	public function actionIndex()
	{
		$cs=Yii::app()->getClientScript();
		$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/datetimepicker/js/jquery-ui.min.js',CClientScript::POS_END);
		$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/datetimepicker/js/jquery.ui.timepicker.js',CClientScript::POS_END);
		$cs->registerCssFile(Yii::app()->baseUrl.'/js/datetimepicker/css/base.css');
		$cs->registerCssFile(Yii::app()->baseUrl.'/js/datetimepicker/css/redmond/jquery-ui.min.css');
		
		$gameType = Yii::app()->request->getParam('gameType');
		$questType = Yii::app()->request->getParam('questType');
		$chargeName = Yii::app()->request->getParam('chargeName');
		$consumerAccount = Yii::app()->request->getParam('consumerAccount');
		$orderNum = Yii::app()->request->getParam('orderNum');
		$startTime = Yii::app()->request->getParam('start_time');
		$endTime = Yii::app()->request->getParam('end_time');
		
		$model = new CsVote();
		$model->unsetAttributes();
		
		if(isset($_GET['CsVote']))
			$model->attributes=$_GET['CsVote'];
		
		if($orderNum == null){
			$orderNums = array();
			$csOrderModel = new CsOrder();
			if ($chargeName != '') {
				$basicUserModel = new BasicUser();
				$basicUserModel->username = $chargeName;
				$chargeId = $basicUserModel->getUserId();
				if($chargeId != 0)
					$csOrderModel->chargeid = $chargeId;
			}
			
			if($consumerAccount != '')
				$csOrderModel->consumeraccount = $consumerAccount;
			
			if($gameType != '')
				$csOrderModel->game_type = $gameType;
			
			if($questType != ''){
				$orderTypeId = CsOrdertype::model()->getByMongoId($questType);
				$csOrderModel->ordertypeid = $orderTypeId;
			}
			
			if($chargeName != '' || $consumerAccount != '' || $gameType != '' || $questType != ''){
				$model->order_num = $csOrderModel->getOrderNums();
				if(empty($model->order_num))
					$model->order_num = 1;
			}
		}else{
			$model->order_num = $orderNum;
		}
		
		$model->startTime = $startTime;
		$model->endTime = $endTime;
		
		$dataProvider = $model->search();
		$data = $dataProvider->getData();
		
		$rateData = array();
		foreach ($data as $value){
			$rate = $value->rate;
			if(!isset($rateData[$rate]))
				$rateData[$rate] = 1;
			else
				$rateData[$rate] = $rateData[$rate]+1;
		}
	
		$pieTitle = array_flip($this->__rateMap());
		$pieData = array();
		foreach ($pieTitle as $k=>$v){
			if(isset($rateData[$v]))
				$pieData[] = array($k,$rateData[$v]);
			else 
				$pieData[] = array($k,0);
		}
		
		$this->render('index',array(
			'model'		=>	$model,
			'rateMap'	=>	$this->__rateMap(),
			'pieData'	=>	json_encode($pieData),
			'gameType'	=>	$gameType,
			'questType'	=>	$questType,
			'orderNum'	=>	$orderNum,
			'chargeName'=>	$chargeName,
			'startTime'		 =>	$startTime,
			'endTime'		=>	$endTime,
			'consumerAccount'	=>	$consumerAccount,
		));
	}
	
	
	public function getRate($data,$row)
	{
		$map = $this->__rateMap();
		return $map[$data->rate];
	}
	
	
	private function __rateMap()
	{
		return array(
				1	=>	'很满意',
				2	=>	'满意',
				3	=>	'不满意 - 游戏设置不合理',
				4	=>	'不满意 - 客服服务态度不满意',
				5	=>	'公司运营政策不满意（如处理时间慢）',
		);
	}

}