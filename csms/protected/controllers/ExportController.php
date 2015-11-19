<?php
require_once 'config.php';

class ExportController extends Controller
{
	public function init(){
		parent::init();
		if(!in_array('所有接待日志', $this->premits))
			die('权限不足(permission denied)');
	}
	
	public function actionPollRating()
	{
		$orderIds = trim(Yii::app()->request->getParam('orderIds'));
		//获取数据
		$data = PollRating::model()->getExportData($orderIds);
		//获取附件
		$attachments = FileUpload::model()->getFiles($orderIds);
		$pics = array();
		foreach ($attachments as $value)
			$pics[$value['orderid']][] = $value['file_path'];
		
		Yii::$enableIncludePath = false;
		//Yii::import('application.extension.PHPExcel', 1);
		$objPHPExcel = new PHPExcel();
		
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', '处理日期')
		->setCellValue('B1', '产品类型')
		->setCellValue('C1', '开始时间')
		->setCellValue('D1', '结束时间')
		->setCellValue('E1', '处理时间')
		->setCellValue('F1', '账号')
		->setCellValue('G1', '订单号')
		->setCellValue('H1', '申请服务')
		->setCellValue('I1', '具体内容')
		->setCellValue('J1', '问题描述')
		->setCellValue('K1', '处理情况')
		->setCellValue('L1', '备注')
		->setCellValue('M1', '处理人员')
		->setCellValue('N1', '附件');
		
		//最多支持5张附件图片
		$picColumn = array('N','O','P','Q','R');
		$i = 2;
		foreach ($data as $value){
			$endTime =empty($value["updatetime"])?time():strtotime($value["updatetime"]);
			$startTime = empty($value["intitime"])?0:strtotime($value["intitime"]);
			$subTime = $endTime - $startTime;
			
			$remark="";
			$res=unserialize(base64_decode($value['content']));
			$remarkKey = mb_convert_encoding('备注_', 'GBK','UTF8');
			if(isset($res[$remarkKey]))
				$remark= mb_convert_encoding($res[$remarkKey],'UTF8','GBK');
			
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$i, strstr($value["intitime"],' ',true))
				->setCellValueExplicit('B'.$i, mb_convert_encoding($value['game_name'],'UTF8','GBK'),PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValue('C'.$i, trim(strstr($value["intitime"],' ')))
				->setCellValueExplicit('D'.$i, $value['updatetime'],PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValueExplicit('E'.$i, $subTime,PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValue('F'.$i, $value['consumeraccount'])
				->setCellValueExplicit('G'.$i, $value['ordernum'],PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValue('H'.$i, $value['typename'])
				->setCellValue('I'.$i, $value['typedetail'])
				->setCellValueExplicit('J'.$i, $remark,PHPExcel_Cell_DataType::TYPE_STRING)
				->setCellValue('K'.$i, mb_convert_encoding($value['status'],'UTF8','GBK'))
				->setCellValue('L'.$i, mb_convert_encoding($value['remark'],'UTF-8','GBK'))
				->setCellValue('M'.$i, mb_convert_encoding($value['username'],'UTF8','GBK'));

			//图片
			if (isset($pics[$value['orderid']])){
				foreach ($pics[$value['orderid']] as $key=>$pic){
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setPath(_PATH_UPLOAD.DIRECTORY_SEPARATOR.$pic);
					$objDrawing->setCoordinates($picColumn[$key].$i);
					$objDrawing->setName($pic);
					$objDrawing->setHeight(60);
					$objDrawing->setWidth(60);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				}
			}
			
			//设置行高
			$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
			
			$i++;
		}
		
		$filename = mb_convert_encoding('所有接待日志', 'GBK','UTF8').date('YmdHis');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}