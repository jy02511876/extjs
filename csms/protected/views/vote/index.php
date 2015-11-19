<?php 
$games = CHtml::listData(BasicGame::model()->getGames(), 'game_code', 'game_name');
$types = QuestTable::model()->getTypes();

?>
<style> 
	.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
    .ui-timepicker-div dl { text-align: left; }
    .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
    .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
    .ui-timepicker-div td { font-size: 90%; }
    .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
    #ui_tpicker_hour_time,#ui_tpicker_minute_time{
	    border:1px solid #C5DBEC;
    }
    .ui-datepicker {
        border:1px solid #c5dbec;
    }
    .ui-datepicker td a {
        text-align: center;
    }
    .inputDateTime {
        width:150px;
    }
	.error {
		border:1px solid #EE3C3C;
    }
</style>
<div class="content-box"><!-- Start Content Box -->
				
	<div class="content-box-header" style="height:10px;">
	</div>
				
	<div class="search-box-content">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	
	<div class="row">
		<?php echo CHtml::label('游戏类型', 'game_type');?>
		<?php echo CHtml::dropDownList('gameType',$gameType,$games,array('empty'=>'请选择...'));?>
		
		<?php echo CHtml::label('问题类型', 'questType');?>
		<?php echo CHtml::dropDownList('questType',$questType,$types,array('empty'=>'请选择...'));?>
	</div>
	<div class="row">
		<?php echo CHtml::label('服务单号', 'order_num')?>
		<?php echo CHtml::textField('orderNum',$orderNum);?>
		
		<?php echo CHtml::label('玩家账号', 'consumeraccount')?>
		<?php echo CHtml::textField('consumerAccount',$consumerAccount);?>
		<!-- 
		<?php echo CHtml::label('经办人姓名','chargeName');?>
		<?php echo CHtml::textField('chargeName',$chargeName);?>
		 -->
	</div>
	<div class="row">
		<?php echo CHtml::label('评分时间', 'date')?>
		<?php echo CHtml::textField('start_time',$startTime,array('class'=>'inputDateTime'));?> - <?php echo CHtml::textField('end_time',$endTime,array('class'=>'inputDateTime'))?>
	</div>
	<div class="row">
		<?php echo CHtml::label('满意度','rate',array('style'=>'padding-left:20px'))?>
		<?php echo $form->dropDownList($model,'rate',$rateMap,array('empty'=>'请选择...'));?>
	</div>
	
	<div class="row buttons" style="padding-left:600px;">
		<?php echo CHtml::submitButton('搜索',array('id'=>'search_submit','class'=>'button')); ?>
	</div>

<?php $this->endWidget(); ?>

	</div> <!-- End .content-box-content -->
	
</div> <!-- End .content-box -->


<div class="content-box"><!-- Start Content Box -->
				
	<div class="content-box-header">
		
		<h3></h3>
		
		<ul class="content-box-tabs">
			<li><a href="#tab1" class="default-tab">占比</a></li> <!-- href must be unique and match the id of target div -->
		</ul>
		
		<div class="clear"></div>
		
	</div> <!-- End .content-box-header -->
				
	<div class="content-box-content">
		<div id="rateChart"></div>

	</div> <!-- End .content-box-content -->
	
</div> <!-- End .content-box -->


<div class="content-box"><!-- Start Content Box -->
				
	<div class="content-box-header">
		
		<h3></h3>
		
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
					'name'	=>	'order_num',
					'value'	=>	'CHtml::link($data->order_num,"/syswork/dealwithcomplain/dealwithcomplain.php?orderid=".$data->order_num)',
					'type'	=>	'raw',
				),
				array(
					'name'	=>	'rate',
					'value'	=>	array($this,'getRate')
				),
				'suggest',
				'date',
			),
	)); 
	?>
	</div> <!-- End .content-box-content -->
	
</div> <!-- End .content-box -->
	
<script src="<?php echo Yii::app()->baseUrl?>/js/highcharts/highcharts.js" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
	$('#rateChart').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: '满意度统计'
        },
        tooltip: {
    	    pointFormat: '总数：<b>{point.y}</b>；  占比：<b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    color: '#000000',
                    connectorColor: '#000000',
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            type: 'pie',
            name: '占比：',
            data: <?php echo $pieData?>
        }]
    });

});
</script>
<script>
$(document).ready(function() {
	$(".inputDateTime").datetimepicker({
		 timeText: '时间',hourText: '小时',minuteText: '分钟',secondText: '秒',millisecText: '毫秒',currentText: '现在',closeText: '关闭 ',ampm: false});
	})
</script>