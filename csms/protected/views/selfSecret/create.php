<div class="content-box"><!-- Start Content Box -->

<div class="content-box-header">

<h3>
	<?php echo CHtml::link('返回',array('index'))?>
</h3>

<ul class="content-box-tabs">
			<li><a href="#tab1" class="default-tab">添加</a></li> <!-- href must be unique and match the id of target div -->
			</ul>

			<div class="clear"></div>

	</div> <!-- End .content-box-header -->

	<div class="content-box-content">
<?php $this->renderPartial('_form', array('model'=>$model)); ?>
	</div>
</div>