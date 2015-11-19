<?php 
$element = CHtml::listData(
		QuestionElement::model()->getElemet(),
		'id', 
		'e_name');
$verify = array(
	'tel'		=>	'手机号',
	'id_card'	=>	'身份证号',
	'yes_only'	=>	'必须选是',
);
?>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/css/csms/reset.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/css/csms/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/css/csms/invalid.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/csms/simpla.jquery.configuration.js"></script>

<style type="text/css">
.row {
	margin-top:20px;
	margin-left:0px;
}
.row_label{
	line-height:22px;
	padding-right:10px;
}
form label {
	font-weight: normal;
}
</style>
<div class="content-box"><!-- Start Content Box -->

<div class="content-box-header">

<h3>
	<?php echo CHtml::link('返回',array('index'))?>
</h3>

<ul class="content-box-tabs">
<li><?php echo $model->name;?></li> <!-- href must be unique and match the id of target div -->
</ul>

			<div class="clear"></div>

	</div> <!-- End .content-box-header -->

	<div class="content-box-content">
<div class="search-box-content">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'element-form',
	'enableAjaxValidation'=>false,
)); ?>
<?php echo $form->hiddenField($model,'_id');?>
	<div id="element">
	<?php 
	if($model->element != null){
	foreach($model->element as $value) {?>
	<div class="row">
	<div class="row" style="margin-top:10px;">
		<label for="type" title="">元素名</label>
		<?php echo CHtml::dropDownList('e_id[]',$value['e_id'],$element); ?>
		
		<label for="required" title="" style="padding-left:20px">是否允许为空</label>
		<?php echo CHtml::dropDownList('required[]',$value['required'],array('yes'=>'是','no'=>'否')); ?>
		
		<label for="tip" title="" style="padding-left:20px">显示提示</label>
		<?php echo CHtml::textField('tip[]',$value['tip'],array('class'=>'text-input small-input')); ?>

	</div>
	<div class="row" style="margin-top:10px;">
		<label for="type" title="">验证类型</label>
		<?php echo CHtml::dropDownList('type[]',$value['type'],$verify,array('empty'=>'无')); ?>
		
		<label for="error_msg" title="" style="padding-left:20px">错误提示</label>
		<?php echo CHtml::textField('error_msg[]',$value['error_msg'],array('class'=>'text-input small-input')); ?>
		
		<span style="padding-left:20px;cursor: pointer" title="上移" class="up"><?php echo CHtml::image(Yii::app()->baseUrl.'/images/csms/icons/up.jpg','上移')?></span>
		<span style="padding-left:5px;cursor: pointer" title="下移" class="down"><?php echo CHtml::image(Yii::app()->baseUrl.'/images/csms/icons/down.jpg','下移')?></span>
		<span style="padding-left:5px;cursor: pointer" title="删除元素" class="delete_element"><?php echo CHtml::image(Yii::app()->baseUrl.'/images/csms/icons/cross.png','删除元素')?></span>
	</div>
<hr>
</div>
	<?php }}?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::button('增加元素',array('id'=>'add_element')); ?>
		<?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
	</div>
</div>


<div id="template" style="display:none">
<div class="row">
	<div class="row" style="margin-top:10px;">
		<label for="type" title="">元素名</label>
		<?php echo CHtml::dropDownList('e_id[]','',$element); ?>
		
		<label for="required" title="" style="padding-left:20px">是否允许为空</label>
		<?php echo CHtml::dropDownList('required[]','',array('yes'=>'是','no'=>'否')); ?>
		
		<label for="tip" title="" style="padding-left:20px">显示提示</label>
		<?php echo CHtml::textField('tip[]','',array('class'=>'text-input small-input')); ?>
		<!-- 
		<label for="sort" title="" style="padding-left:20px">排序号</label>
		<?php echo CHtml::textField('sort[]','0',array('class'=>'text-input','style'=>'width:20px;','maxlength'=>2)); ?>
		 -->
	</div>
	<div class="row" style="margin-top:10px;">
		<label for="type" title="">验证类型</label>
		<?php echo CHtml::dropDownList('type[]','',$verify,array('empty'=>'无')); ?>
		
		<label for="error_msg" title="" style="padding-left:20px">错误提示</label>
		<?php echo CHtml::textField('error_msg[]','',array('class'=>'text-input small-input')); ?>
		
		<span style="padding-left:20px;cursor: pointer" title="上移" class="up"><?php echo CHtml::image(Yii::app()->baseUrl.'/images/csms/icons/up.jpg','上移')?></span>
		<span style="padding-left:5px;cursor: pointer" title="下移" class="down"><?php echo CHtml::image(Yii::app()->baseUrl.'/images/csms/icons/down.jpg','下移')?></span>
		<span style="padding-left:5px;cursor: pointer" title="删除元素" class="delete_element"><?php echo CHtml::image(Yii::app()->baseUrl.'/images/csms/icons/cross.png','删除元素')?></span>
	</div>
<hr>
</div>

</div>
<script>
$(function(){
	$("#add_element").click(function(){
		html = $("#template").html();
		$("#element").append(html);
	})


	$(".delete_element").live('click',function(){
		$(this).parent().parent().remove();
	})

	$(".up").live('click',function(){
		move($(this).parent().parent(),"up")
	})
	
	$(".down").live('click',function(){
		move($(this).parent().parent(),"down")
	})


	function move(element,type){
		if(type == 'up'){
			side = element.prev();
			text = "上";
		}else{
			side = element.next();
			text = "下";
		}

		if(side.html() == null){
			alert("已经是最"+text+"层了！");
		}else{
			tmp = element.clone();
			element.remove();
			if(type == "up"){
				side.before(tmp);
			}else{
				side.after(tmp);
			}
		}
		
	}
})
</script>

