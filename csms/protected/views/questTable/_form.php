<?php
$element = QuestionElement::model()->findAll(array(
		'select'=>'e_type,e_name',
		'order'=>'id ASC'));
$label = array(
		'0'=>'其它类',
		'1'=>'注册资料修改类',
		'2'=>'密保解绑类',
);
$deptName = '';
if ($model->dept != ''){
	$deptModel = BasicDept::model()->getDeptById($model->dept);
	if ($deptModel != null)
		$deptName = $deptModel->deptname;
}

$updateDeptName = '';
if ($model->update_dept != ''){
	$deptModel = BasicDept::model()->getDeptById($model->update_dept);
	if ($deptModel != null)
		$updateDeptName = $deptModel->deptname;
}

$delayDeptName = '';
if ($model->delay_dept != ''){
	$deptModel = BasicDept::model()->getDeptById($model->delay_dept);
	if ($deptModel != null)
		$delayDeptName = $deptModel->deptname;
}

?>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/css/csms/reset.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/css/csms/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/css/csms/invalid.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/csms/simpla.jquery.configuration.js"></script>

<style type="text/css">
.row {
	margin-top:20px;
}
.row_label{
	line-height:22px;
	padding-right:10px;
}
form label {
	font-weight: normal;
}
</style>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'news-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'	=>array('enctype'=>'multipart/form-data'),
)); ?>


	<?php echo $form->errorSummary($model); ?>
<!-- 
	<div class="row">
		<?php echo $form->labelEx($model,'label',array('class'=>'row_label align-left')); ?>
		<?php echo $form->dropDownList($model,'label',$label); ?>
	</div>
 -->
	<div class="row">
		<?php echo $form->labelEx($model,'type',array('class'=>'row_label align-left')); ?>
		<?php echo $form->textField($model,'type',array('size'=>60,'maxlength'=>120,'class'=>'text-input small-input')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name',array('class'=>'row_label align-left')); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>120,'class'=>'text-input small-input')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'dept',array('class'=>'row_label align-left')); ?>
		<?php echo CHtml::textField('dept',$deptName,array('readonly'=>true,'onclick'=>'selectdept()','class'=>'text-input small-input'))?>
		<?php echo $form->hiddenField($model,'dept',array('id'=>'deptID')); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'update_dept',array('class'=>'row_label align-left')); ?>
		<?php echo CHtml::textField('update_dept',$updateDeptName,array('readonly'=>true,'onclick'=>'updatedept()','class'=>'text-input small-input'))?>
		<?php echo $form->hiddenField($model,'update_dept',array('id'=>'updateDeptID')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'level',array('class'=>'row_label align-left')); ?>
		<?php echo $form->dropDownList($model,'level',array('50'=>'低','100'=>'高')); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'sort',array('class'=>'row_label align-left')); ?>
		<?php echo $form->textField($model,'sort',array('size'=>20,'maxlength'=>20,'class'=>'text-input','style'=>'width:100px;')); ?>越大越在前
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'limit_time',array('class'=>'row_label align-left')); ?>
		<?php echo $form->textField($model,'limit_time',array('size'=>20,'maxlength'=>20,'class'=>'text-input','style'=>'width:100px;')); ?>工作日
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'consume_point',array('class'=>'row_label align-left')); ?>
		<?php echo $form->textField($model,'consume_point',array('size'=>20,'maxlength'=>20,'class'=>'text-input','style'=>'width:100px;')); ?>巨人点
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'consume_time_limit',array('class'=>'row_label align-left')); ?>
		<?php echo $form->textField($model,'consume_time_limit',array('size'=>20,'maxlength'=>20,'class'=>'text-input','style'=>'width:100px;')); ?>小时
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'delay_hour',array('class'=>'row_label align-left')); ?>
		<?php echo $form->textField($model,'delay_hour',array('size'=>20,'maxlength'=>20,'class'=>'text-input','style'=>'width:100px;')); ?>小时
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'delay_dept',array('class'=>'row_label align-left')); ?>
		<?php echo CHtml::textField('delay_dept',$delayDeptName,array('readonly'=>true,'onclick'=>'delaydept()','class'=>'text-input small-input'))?>
		<?php echo $form->hiddenField($model,'delay_dept',array('id'=>'delayDeptID')); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'status',array('class'=>'row_label align-left')); ?>
		<?php echo $form->checkBox($model,'status'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'admin_status',array('class'=>'row_label align-left')); ?>
		<?php echo $form->checkBox($model,'admin_status'); ?>是
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'app_status',array('class'=>'row_label align-left')); ?>
		<?php echo $form->checkBox($model,'app_status'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'game_alert',array('class'=>'row_label align-left')); ?>
		<?php echo $form->checkBox($model,'game_alert'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'consume',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'consume'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'equipment_return_times',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'equipment_return_times'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'bind_secret',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'bind_secret'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'role_level',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'role_level'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'role_exit',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'role_exit'); ?>是
	</div>
	<!-- 
	<div class="row">
		<?php echo $form->labelEx($model,'role_lock',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'role_lock'); ?>是
	</div>
	 -->
	<div class="row">
		<?php echo $form->labelEx($model,'account_lock',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'account_lock'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'response_show',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'response_show'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'role_frozen',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'role_frozen'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'account_updating',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'account_updating'); ?>是
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'repeat_submit',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'repeat_submit'); ?>禁止 (同一账号,同一游戏，同一类型的订单未处理完之前，不允许重复的提交)
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'repeat_submit_account',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'repeat_submit_account'); ?>禁止 (同一账号的订单未处理完之前，不允许重复的提交)
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'des_pic',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'des_pic_show_admin'); ?>客服可见
		<?php echo $form->checkBox($model,'des_pic_show_user'); ?>玩家可见
	</div>
	<div class="row">
		<?php
			$this->widget('CMultiFileUpload',array(
					'model'	=>	$model,
					'attribute'	=>	'des_pic',
					'accept'	=>	'jpg|gif|png',
					'max'		=>	10,
			));
		?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'des_text',array('class'=>'row_label align-left'))?>
		<?php echo $form->checkBox($model,'des_text_show_admin'); ?>客服可见
		<?php echo $form->checkBox($model,'des_text_show_user'); ?>玩家可见
	</div>
	
	<div class="row">
		<?php echo $form->textArea($model,'des_text',array('rows'=>6)); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<script type="text/javascript" src="<?php echo Yii::app()->baseUrl?>/js/richeditor/fckeditor.js"></script>
<script type="text/javascript">
window.onload = function()
{
	/*
	var oFCKeditor = new FCKeditor('QuestTable_content');
	oFCKeditor.BasePath = '<?php echo Yii::app()->baseUrl?>/js/richeditor/';
	oFCKeditor.Height = 500;
	oFCKeditor.ReplaceTextarea() ;
	*/
	var oFCKeditor2 = new FCKeditor('QuestTable_des_text');
	oFCKeditor2.BasePath = '<?php echo Yii::app()->baseUrl?>/js/richeditor/';
	oFCKeditor2.Height = 300;
	oFCKeditor2.ReplaceTextarea() ;
}

function selectdept()
{
	deptdata = showModalDialog("../../managesystem/selectdept.php",window,'dialogWidth:350px; dialogHeight:400px;help:0;status:0;resizeable:1;');
	if (deptdata != null)
	{
		//alert(deptdata["id"]+"="+deptdata["name"]);
		$("#dept").val(deptdata["name"]);
		$("#deptID").val(deptdata["id"]);
	}
}

function updatedept()
{
	deptdata = showModalDialog("../../managesystem/selectdept.php",window,'dialogWidth:350px; dialogHeight:400px;help:0;status:0;resizeable:1;');
	if (deptdata != null)
	{
		//alert(deptdata["id"]+"="+deptdata["name"]);
		$("#update_dept").val(deptdata["name"]);
		$("#updateDeptID").val(deptdata["id"]);
	}
}

function delaydept()
{
	deptdata = showModalDialog("../../managesystem/selectdept.php",window,'dialogWidth:350px; dialogHeight:400px;help:0;status:0;resizeable:1;');
	if (deptdata != null)
	{
		//alert(deptdata["id"]+"="+deptdata["name"]);
		$("#delay_dept").val(deptdata["name"]);
		$("#delayDeptID").val(deptdata["id"]);
	}
}
</script>