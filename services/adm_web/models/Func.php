<?php

namespace app\models;

use Yii;

class Func
{
	
	public static function _genRandStr($ln) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $ln; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

	public static function _genPassword($ln) {
		$characters = '0123456789abcdefghijkmnpqrstuvwxyzACDEFGHJKLMNPRTUVWXY';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $ln; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

	public static function hideEmail($email)
	{
		$em1 = explode('.', $email);
		$em2 = explode('@', $email);

		$em3 = $em2[0];
		$ln = (int)(mb_strlen($em3, 'UTF-8')/3);
		$em4 = '';
		$em4 .= mb_substr($em3, 0, $ln, 'UTF-8');
		for ($j=0;$j<$ln*2;$j++) $em4 .= '*';

		/*for ($j=0;$j<$ln;$j++) $em4 .= '*';
		$em4 .= mb_substr($em3, $ln);*/

		$ret = $em4.'@'.$em2[1];

		return $ret;
	}

	public static function processExportAdminData($model, $query_params, $filter_function)
	{
		if (!isset($_POST['export-data-submit']))
			return;

		$items = [];

		$dataProvider = $model->search($query_params);

		$dataProvider->pagination = false;

		$items = $dataProvider->getModels();

		/*foreach($the_data as $item) {
			$items[] = $item;
		}*/

		$return = $filter_function($items);

		//echo count($return);
		//die;

		
		$image_name = uniqid().".csv";
		$upload_directory = \Yii::getAlias('@webroot').'/uploads/report/';
        $upload_directory .= $image_name;

		$fp = fopen($upload_directory, 'w');
		fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

		fputcsv($fp, $return['header'], ';');

		foreach ($return['data'] as $data) {
		
			fputcsv($fp, $data, ';');
		}

		fclose($fp);

		$name = uniqid().".csv";
		$content = file_get_contents($upload_directory);
		header('Content-Type: text/csv');
		header('Content-Length: '.strlen( $content ));
		header('Content-disposition: attachment; filename="' . $name . '"');
		header('Cache-Control: public, must-revalidate, max-age=0');
		header('Pragma: public');
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		echo $content;
		die;
		
		
		/*Yii::import('ext.phpexcel.XPHPExcel');

		XPHPExcel::init();

		$phpExcel = new PHPExcel();

		$objSheet = $phpExcel->setactivesheetindex(0);

		$row = 1;

		foreach ($return['header'] as $col => $c) {
			$objSheet->setCellValueByColumnAndRow($col, $row, $c);
			$objSheet->getStyle('1:1')->getFont()->setBold(true);
		}

		/*$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'HTML');
		$objWriter->setSheetIndex(0);
		$objWriter->save('php://output');

		die;*/

		/*$row++;

		foreach ($return['data'] as $data) {

			foreach ($data as $col => $c) {
				$objSheet->setCellValueByColumnAndRow($col, $row, $c);
			}

			if ($clName == 'Order') {
				$objSheet->getStyle('U'.$row)->getNumberFormat()->setFormatCode('0');
				$objSheet->getStyle('Q'.$row)->getNumberFormat()->setFormatCode('0');
			}

			$row++;
		}

		$fn = uniqid().'.xlsx';
		$fpath = Yii::getPathOfAlias('webroot').'/uploads/report/'.$fn;

		$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel2007");
		$objWriter->save($fpath);

		Yii::app()->controller->redirect('/backend/uploads/report/'.$fn);*/
	}

	public static function getAge($date) {

		$ret = intval(date('Y', time() - strtotime($date))) - 1970;
	
		return $ret;
	}

};