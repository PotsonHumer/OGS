<?php

	// 詢價設定
	class ADMIN_INQUIRY extends OGSADMIN{
		protected static $func;
		
		function __construct($args){
			
			self::$func = array_shift($args);
			$temp_option = array("LEFT" => self::$temp.'ogs-admin-left-tpl.html');
			
			switch(self::$func){
				case "del":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::inquiry_process($args);
				break;
				case "detail":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-inquiry-detail-tpl.html');
					self::inquiry_detail($args);
				break;
				case "output":
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-msg-tpl.html');
					self::output();
				break;
				default:
					$temp_main = array("MAIN" => self::$temp.'ogs-admin-inquiry-list-tpl.html');
					self::inquiry_list();
				break;
			}
			
			if(is_array($temp_main)){
				$temp_option = array_merge($temp_option,$temp_main);
			}
			
			new VIEW(self::$temp.'ogs-admin-hull-tpl.html',$temp_option,false,ture);
		}

		//--------------------------------------------------------------------------------------
		
		// 列表
		private function inquiry_list(){
			
			$select = array (
				'table' => 'ogs_inquiry',
				'field' => "*",
				//'where' => '',
				//'order' => "",
				//'limit' => '',
			);

			$sql = DB::select($select);
			$sql = PAGE::handle($sql, CORE::$manage.'admin_inquiry/');
			$rsnum = DB::num($sql);

			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){

					VIEW::newBlock('TAG_INQUIRY_LIST');
					VIEW::assign(array(
						"VALUE_ROW_NUM" => ++$i,
						"VALUE_IQ_ID" => $row["iq_id"],
						"VALUE_IQ_NAME" => $row["iq_name"],
						"VALUE_IQ_COMPANY" => $row["iq_company"],
						"VALUE_IQ_TEL" => $row["iq_tel"],
						"VALUE_IQ_CELLPHONE" => $row["iq_cellphone"],
					));
				}
			}else{
				VIEW::newBlock("TAG_ROW_NONE");
			}
		}

		// 詳細顯示頁
		private function inquiry_detail($args){
			
			$select = array (
				'table' => 'ogs_inquiry',
				'field' => "*",
				'where' => "iq_id = '".$args[0]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				foreach($row as $field => $value){
					switch($field){
						case "iq_content":
							$value = CORE::content_handle($value,true);
						break;
					}
					VIEW::assignGlobal("VALUE_".strtoupper($field),$value);
				}
				
				self::inquiry_items($row["iq_id"]);
			}
		}
		
		// 詢價產品列表
		private static function inquiry_items($iq_id){

			$select = array (
				'table' => 'ogs_inquiry_items',
				'field' => "*",
				'where' => "iq_id = '".$iq_id."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				while($row = DB::fetch($sql)){
					VIEW::newBlock("TAG_IQI_LIST");
					self::inquiry_itmes_load($row);
				}
			}
		}
		
		// 取得詢價產品
		private static function inquiry_itmes_load(array $args){
			
			$select = array (
				'table' => $args["iqi_lang"].'_products',
				'field' => "*",
				'where' => "p_id = '".$args["p_id"]."'",
				//'order' => '',
				//'limit' => '',
			);
			
			$sql = DB::select($select);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				$row = DB::fetch($sql);
				
				VIEW::assign(array(
					"VALUE_P_NAME" => $row["p_name"],
					"VALUE_P_S_IMG" => $row["p_s_img"],
					"VALUE_IQI_NUM" => $args["iqi_num"],
				));
			}
		}
		
		// 各項處理
		private function inquiry_process($args=false){
			switch(self::$func){
				case "del":
					if(!empty($args)){
						DB::delete('ogs_inquiry',array('iq_id' => $args[0]));
						if(!empty(DB::$error)){
							CORE::notice(DB::$error,$_SESSION[CORE::$config["sess"]]['last_path']);
						}else{
							$rs = true;
						}
					}else{
						$rs = CRUD::delete('ogs_inquiry','iq',$_REQUEST["id"]);
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
			
			$sql_str = "SELECT * FROM ogs_inquiry_items as iqi 
						left join ogs_inquiry as iq on iq.iq_id = iqi.iq_id 
						WHERE iq.iq_createdate >= '".$_REQUEST["startdate"]."' and iq.iq_createdate <= '".$_REQUEST["enddate"]."' 
						ORDER BY iq.iq_createdate asc";

			$sql = DB::select(false,$sql_str);
			$rsnum = DB::num($sql);
			
			if(!empty($rsnum)){
				
				$xls = new PHPExcel();
				
				//宣告工作表
				$xls->setActiveSheetIndex(0);

				$xls->getActiveSheet()
					->setTitle("產品詢價資料") //頁籤名稱
					->setCellValue('A1', 'No.')
					->setCellValue('B1', '詢價產品名稱')
					->setCellValue('C1', '詢價數量')
					->setCellValue('D1', '名稱')
					->setCellValue('E1', '公司名稱')
					->setCellValue('F1', '職務')
					->setCellValue('G1', '電話')
					->setCellValue('H1', '行動電話')
					->setCellValue('I1', '傳真')
					->setCellValue('J1', '城市')
					->setCellValue('K1', '郵遞區號')
					->setCellValue('L1', '地址')
					->setCellValue('M1', '國家')
					->setCellValue('N1', 'E-mail')
					->setCellValue('O1', '網址')
					->setCellValue('P1', '內容')
					->setCellValue('Q1', '聯絡方式')
					->setCellValue('R1', '紀錄時間')
					;
	
				$xls->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$xls->getActiveSheet()->getColumnDimension('B')->setWidth(30);
				$xls->getActiveSheet()->getColumnDimension('C')->setWidth(10);
				$xls->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('J')->setWidth(10);
				$xls->getActiveSheet()->getColumnDimension('K')->setWidth(10);
				$xls->getActiveSheet()->getColumnDimension('L')->setWidth(40);
				$xls->getActiveSheet()->getColumnDimension('M')->setWidth(20);
				$xls->getActiveSheet()->getColumnDimension('N')->setWidth(30);
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
					
					$select = array (
						'table' => $row["iqi_lang"].'_products',
						'field' => "*",
						'where' => "p_id = '".$row["p_id"]."'",
						//'order' => '',
						//'limit' => '',
					);
					
					$p_sql = DB::select($select);
					$p_row = DB::fetch($p_sql);
					
					$xls->getActiveSheet()
						->setTitle("產品詢價資料") //頁籤名稱
						->setCellValue('A'.$i, ($i - 1))
						->setCellValueExplicit('B'.$i, $p_row["p_name"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('C'.$i, $row["iqi_num"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('D'.$i, $row["iq_name"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('E'.$i, $row["iq_company"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('F'.$i, $row["iq_position"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('G'.$i, $row["iq_tel"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('H'.$i, $row["iq_cellphone"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('I'.$i, $row["iq_fax"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('J'.$i, $row["iq_city"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('K'.$i, $row["iq_zip"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('L'.$i, $row["iq_address"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('M'.$i, $row["iq_country"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('N'.$i, $row["iq_email"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('O'.$i, $row["iq_url"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('P'.$i, $row["iq_content"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('Q'.$i, $row["iq_contact"], PHPExcel_Cell_DataType::TYPE_STRING)
						->setCellValueExplicit('R'.$i, $row["iq_createdate"], PHPExcel_Cell_DataType::TYPE_STRING)
						;
						
					$xls->getActiveSheet()->getStyle('A'.$i.':R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
				
				$output_status = true;
				
				//輸出
				if($output_status){
					$xls->setActiveSheetIndex(0);
					
					$savefilename = iconv("utf8","big5","inquiry-".date("Y-m-d").".xls");
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