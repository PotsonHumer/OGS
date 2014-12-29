<?php

	// 管理員設定
	class ADMIN_CONTACT extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::contact_process($args);
				break;
				case "detail":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-contact-detail-tpl.html');
					self::contact_detail($args);
				break;
				case "output":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::output();
				break;
				default:
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-contact-list-tpl.html');
					self::contact_list();
				break;
			}
			
			if(is_array($temp_main)){
				$temp_option = array_merge($temp_option,$temp_main);
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}

		//--------------------------------------------------------------------------------------
		
		// 關於我們列表
		private function contact_list(){
			$select = array (
				'table' => 'ogs_contact',
				'field' => "*",
				//'where' => '',
				//'order' => "",
				//'limit' => '',
			);

			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_contact/');
			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){

					VIEW::newBlock('TAG_CONTACT_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_CT_ID" => $row["ct_id"],
						"VALUE_CT_NAME" => $row["ct_name"],
						"VALUE_CT_COMPANY" => $row["ct_company"],
						"VALUE_CT_TEL" => $row["ct_tel"],
						"VALUE_CT_CELLPHONE" => $row["ct_cellphone"],
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}

		// 詳細顯示頁
		private function contact_detail($args){
			
			$select = array (
				'table' => 'ogs_contact',
				'field' => "*",
				'where' => "ct_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "ct_content":
							$value = CORE::content_handle($value,true);
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
			}
		}
		
		// 介紹頁各項處理
		private function contact_process($args=false){
			switch(self::$func){
				case "del":
					if(!empty($args)){
						DB::delete('ogs_contact',array('ct_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						$rs = CRUD::delete('ogs_contact','ct',$_REQUEST["id"]);
					}
				break;
			}
			
			if($rs){
				CORE::notice('處理完成',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
		//--------------------------------------------------------------------------------------
		
		// Excel輸出
		function output(){
			
			$select = array (
				'table' => 'ogs_contact',
				'field' => "*",
				'where' => "ct_createdate >= '".$_REQUEST["startdate"]."' and ct_createdate <= '".$_REQUEST["enddate"]."'",
				'order' => 'ct_createdate desc',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				
				$xls = new PHPExcel();
				
				//宣告工作表
				$xls->setActiveSheetIndex(0);
	
				$xls->getActiveSheet()
					->setTitle("聯絡我們資料") //頁籤名稱
					->setCellValue('A1', 'No.')
					->setCellValue('B1', '名稱')
					->setCellValue('C1', '公司名稱')
					->setCellValue('D1', '職務')
					->setCellValue('E1', '電話')
					->setCellValue('F1', '行動電話')
					->setCellValue('G1', '傳真')
					->setCellValue('H1', '城市')
					->setCellValue('I1', '郵遞區號')
					->setCellValue('J1', '地址')
					->setCellValue('K1', '國家')
					->setCellValue('L1', 'E-mail')
					->setCellValue('M1', '網址')
					->setCellValue('N1', '公司類型')
					->setCellValue('O1', '詢問類別')
					->setCellValue('P1', '內容')
					->setCellValue('Q1', '聯絡方式')
					->setCellValue('R1', '紀錄時間')
					;
	
				$xls->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$xls->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('H')->setWidth(10);
				$xls->getActiveSheet()->getColumnDimension('I')->setWidth(10);
				$xls->getActiveSheet()->getColumnDimension('J')->setWidth(40);
				$xls->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('L')->setWidth(30);
				$xls->getActiveSheet()->getColumnDimension('M')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('N')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('O')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('P')->setWidth(40);
				$xls->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('R')->setWidth(20);
				$xls->getActiveSheet()->getStyle('A1:R1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$xls->getActiveSheet()->getStyle('A1:R1')->getFill()
					->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					->getStartColor()->setRGB('00B0F0');
					
				$xls->getActiveSheet()->getStyle('A1:R1')->getFont()->getColor()->setRGB('FFFFFF');
				
				$i = 1;
				while($row = DB::fetch($sql)){
					$i++;
					
					$xls->getActiveSheet()
						->setTitle("聯絡我們資料") //頁籤名稱
						->setCellValue('A'.$i, ($i - 1))
						->setCellValueExplicit('B'.$i, $row["ct_name"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('C'.$i, $row["ct_company"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('D'.$i, $row["ct_position"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('E'.$i, $row["ct_tel"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('F'.$i, $row["ct_cellphone"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('G'.$i, $row["ct_fax"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('H'.$i, $row["ct_city"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('I'.$i, $row["ct_zip"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('J'.$i, $row["ct_address"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('K'.$i, $row["ct_country"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('L'.$i, $row["ct_email"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('M'.$i, $row["ct_url"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('N'.$i, $row["ct_type"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('O'.$i, $row["ct_quest"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('P'.$i, $row["ct_content"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('Q'.$i, $row["ct_contact"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('R'.$i, $row["ct_createdate"], PHPExcel_Cell_DataType::TYPE_STRING)
						;
						
					$xls->getActiveSheet()->getStyle('A'.$i.':R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
				
				$output_status = true;
				
				//輸出
				if($output_status){
					$xls->setActiveSheetIndex(0);
					
					$savefilename = iconv("utf8","big5","contact-".date("Y-m-d").".xls");
					header('Content-Type: application/vnd.ms-excel');
					header("Content-Disposition: attachment;filename=\"".$savefilename."\"");
					header('Cache-Control: max-age=0');
	
					$objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
					$objWriter->save('php://output');
				}
			}else{
				CORE::notice('查無資料',$_SESSION[CORE::$config["sess"]]['last_path']);
			}
		}
		
	}

?>