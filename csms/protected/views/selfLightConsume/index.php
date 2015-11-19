<div class="content-box"><!-- Start Content Box -->
				
	<div class="content-box-header">
		
		<h3>
		<?php echo CHtml::link('新增',array('create'))?>
		</h3>
		
		<ul class="content-box-tabs">
			<li><a href="#tab1" class="default-tab">列表</a></li> <!-- href must be unique and match the id of target div -->
		</ul>
		
		<div class="clear"></div>
		
	</div> <!-- End .content-box-header -->
				
	<div class="content-box-content">
					
	<?php 
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>$this->getId(),
		'dataProvider'=>$model->search(),
		'pager'	=>	array('class'=>'MyPager'),
		'pagerCssClass'	=>	'pagination',
		'cssFile'	=>	false,
		'emptyText'	=>	'no message found!',
		'summaryText'	=>	'共{count}条记录',
		'template'	=>	'{items}{summary}{pager}',
		'enableSorting'	=>	false,
		//'summaryText'	=>	'<select id="op"><option>请选择</option><option value="delete">批量删除</option></select>',
		'emptyText'	=>	'无数据',
		'columns'=>array(
				array(
					'name'	=>	'game_id',
					'value'	=>	array($this,'getGameName'),
				),
				'consume',
				array(
					'header'	=>	'操作',
					'class'=>'CButtonColumn',
					'template'	=>	'{update} {delete}',
					'updateButtonImageUrl'	=>	Yii::app()->baseUrl.'/images/csms/icons/pencil.png',
					'updateButtonLabel'		=>	'更新',
					'deleteButtonImageUrl'	=>	Yii::app()->baseUrl.'/images/csms/icons/cross.png',
					'deleteButtonLabel'		=>	'删除',
					'deleteConfirmation'	=>	'确定删除？',
				)
			),
	)); 
	?>
	</div> <!-- End .content-box-content -->
	
</div> <!-- End .content-box -->