<?php
$games = CHtml::listData(BasicGame::model()->getGames(), 'game_code', 'game_name');
$quests = CHtml::listData(QuestTable::model()->getTables(), 'id', 'name');

?>

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
.error {
    background: #ffcece url('');
    border-color: #df8f8f;
    color: #665252;
}
.errorMessage {
	padding-left:50px;
	color:red;
}
.success {
    background: #d5ffce url('');
    border-color: #9adf8f;
    color: #556652;
}
</style>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'GameQuest-form',
	'enableAjaxValidation'=>true,
)); ?>


	<div class="row">
		<?php echo $form->labelEx($model,'game_id',array('class'=>'row_label align-left')); ?>
		<?php echo $form->dropDownList($model,'game_id',$games); ?>
		<?php echo $form->error($model,'game_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'table_id',array('class'=>'row_label align-left')); ?>
		<?php echo $form->dropDownList($model,'table_id',$quests); ?>
		<?php echo $form->error($model,'table_id'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->