<?php
require('pdf/fpdf.php');
 
class PDF extends FPDF{

	function Header(){
	
		$this->Image('../img/infoemcepc.jpg', 0, 0, $fpdf->w, $fpdf->h);
		$this->Image('../img/logo.jpg', 20, 20, 40, 15);
		$this->SetTextColor(255, 255, 255); 
	}
	
	function result_politicas($info_pdf, $cab_pdf, $val_fon, $fecha_fonds, $id_ent){
		
		$this->SetFillColor(155, 4, 39);
		$this->Image('../img/log_ent/'.$id_ent.'.jpg', 150, 20, 40, 15);
		
		$y = 75;		
		$cab_pdf = 'Políticas de Inversión Para: '.$cab_pdf;
		
		$this->SetTextColor(231, 60 ,60);
		$this->SetFont('Humanist521Lt','',15);
		
		$this->SetXY(10, 40);
		$this->Cell(1);
		$this->MultiCell(200, 8, (iconv('UTF-8', 'windows-1252', $cab_pdf)), 0 , 1);
		
		$this->SetXY(10, 60);
		$this->Cell(1);
		$this->MultiCell(200, 4, (iconv('UTF-8', 'windows-1252', $val_fon)), 0 , 1);
		
		$this->SetXY(10, 70);
		$this->Cell(1);
		$this->MultiCell(200, 4, (iconv('UTF-8', 'windows-1252', $fecha_fonds)), 0 , 1);
		
		$this->SetTextColor(255, 255 ,255);
		$this->SetFont('Humanist521Lt','',9);
		
		$cont_ti = 0;
		
		for($i = 0; $i<count($info_pdf); ++$i){
			
			if( $info_pdf[$i][4] != 'COP 0,00' && $info_pdf[$i][0] != '*' ){
			
			// NO
				$this->Rect(10, $y, 9, 10, 'F');
				$this->SetXY(10, $y);
				$this->Cell(1);
				$this->MultiCell(9, 4, (iconv('UTF-8', 'windows-1252', $info_pdf[$i][0])), 0 , 1);
				
			// NOMBRE
				$this->Rect(19, $y, 69, 10, 'F');
				$this->SetXY(19, $y);
				$this->Cell(1);
				$this->MultiCell(68, 4, (iconv('UTF-8', 'windows-1252', $info_pdf[$i][1])), 0 , 1);
				
			// MAX
				$this->Rect(88, $y, 15, 10, 'F');
				$this->SetXY(88, $y);
				$this->Cell(1);
				$this->MultiCell(14, 4, (iconv('UTF-8', 'windows-1252', $info_pdf[$i][2])), 0 , 1);
				
			// MIN
				$this->Rect(103, $y, 15, 10, 'F');
				$this->SetXY(103, $y);
				$this->Cell(1);
				$this->MultiCell(14, 4, (iconv('UTF-8', 'windows-1252', $info_pdf[$i][3])), 0 , 1);
				
			// VPN
				$this->Rect(118, $y, 44, 10, 'F');
				$this->SetXY(118, $y);
				$this->Cell(1);
				$this->MultiCell(43, 4, (iconv('UTF-8', 'windows-1252', $info_pdf[$i][4])), 0 , 1);
				
				$this->Rect(162, $y, 22, 10, 'F');
				$this->SetXY(162, $y);
				$this->Cell(1);
				$this->MultiCell(21, 4, (iconv('UTF-8', 'windows-1252', $info_pdf[$i][5])), 0 , 1);
				
				$this->Rect(184, $y, 24, 10, 'F');
				$this->SetXY(184, $y);
				$this->Cell(1);
				$this->MultiCell(23, 4, (iconv('UTF-8', 'windows-1252', $info_pdf[$i][6])), 0 , 1);
				
				$y = $y + 10;
				
				if( $info_pdf[$i][0] != '-' && $info_pdf[$i][0] != 'No' ){ $cont_ti ++; }
				
			}//else if( $info_pdf[$i][0] != '*' && $info_pdf[$i][0] != '-' ){ $cont_ti ++; }
			
			$ib = $i + 1;
			
			$this->SetTextColor(25, 25, 25); 
			
			if($info_pdf[$i][0] == '-'){
				
				if($info_pdf[$ib][0] == 'No'){ 
					$this->SetTextColor(255, 255, 255); 
					$this->SetFillColor(155, 4, 39); 
					$y += 20;
				}else{
					$this->SetFillColor(232, 230, 231);
				}
				
			}else if($info_pdf[$i][0]%2==0) $this->SetFillColor(232, 230, 231);
			else $this->SetFillColor(203, 202, 202);
			
			if($info_pdf[$ib][0] == '-' ){
				$this->SetFillColor(52, 52, 52);
				$this->SetTextColor(225, 225, 225);
			}
			
		/* 
			- impresion de titulos compociciones 
		*/
		
			if( $info_pdf[$i][0] == '*'){
				
				$this->SetTextColor(231, 60 ,60);
				$this->SetFont('Humanist521Lt','',15);
				
				$y += 10;
				
				$this->SetXY(10, $y);
				$this->Cell(1);
				$this->MultiCell(200, 8, (iconv('UTF-8', 'windows-1252', $info_pdf[$i][0].' '.$info_pdf[$i][1])), 0 , 1);
				
				$this->SetFont('Humanist521Lt','',9);
				
				if($cont_ti == 0){
					
					$this->SetFillColor(232, 230, 231);
					$this->SetTextColor(25, 25, 25); 
					
					$y -= 10;
					$this->Rect(10, $y, 187, 10, 'F');
					$this->SetXY(10, $y);
					$this->Cell(1);
					$this->MultiCell(190, 4, (iconv('UTF-8', 'windows-1252', ' ')), 0 , 1);
					
					$y += 10;
				}//else{ $cont_ti = 0; }
				
				$this->SetFillColor(155, 4, 39); 
				$this->SetTextColor(255, 255 ,255);
				
				$y += 10; 
				$cont_ti = 0;
				
			}
			
			if($y >= 260){
				$y = 45;
				$this->AddPage();
				$this->Image('../img/log_ent/'.$id_ent.'.jpg', 150, 20, 40, 15);
			}
		}
	}
	
	function result_prohibiciones($info_pdf_pr, $cab_pdf, $val_fon, $fecha_fonds, $id_ent){
		
		$this->SetFillColor(155, 4, 39);
		
		$y = 60;
		$cab_pdf = 'Prohibiciones y Conflicto de Intereses Para: '.$cab_pdf;
		
		$this->SetTextColor(231, 60 ,60);
		$this->SetFont('Humanist521Lt','',15);
		$this->AddPage();
		$this->Image('../img/log_ent/'.$id_ent.'.jpg', 150, 20, 40, 15);
		
		$this->SetXY(10, 40);
		$this->Cell(1);
		$this->MultiCell(200, 8, (iconv('UTF-8', 'windows-1252', $cab_pdf)), 0 , 1);
		
		$this->SetXY(10, 60);
		$this->Cell(1);
		$this->MultiCell(200, 4, (iconv('UTF-8', 'windows-1252', $val_fon)), 0 , 1);
		
		$this->SetXY(10, 70);
		$this->Cell(1);
		$this->MultiCell(200, 4, (iconv('UTF-8', 'windows-1252', $fecha_fonds)), 0 , 1);
		
		$this->SetTextColor(255, 255 ,255);
		$this->SetFont('Humanist521Lt','',9);
		
		
		for($i = 0; $i<count($info_pdf_pr); ++$i){
		
			if( $info_pdf_pr[$i][0] != '-'){
			// NO
				$this->Rect(10, $y, 9, 15, 'F');
				$this->SetXY(10, $y);
				$this->Cell(1);
				$this->MultiCell(9, 10, (iconv('UTF-8', 'windows-1252', $info_pdf_pr[$i][0])), 0 , 1);
			// NOMBRE
				$this->Rect(19, $y, 69, 15, 'F');
				$this->SetXY(19, $y);
				$this->Cell(1);
				$this->MultiCell(68, 4, (iconv('UTF-8', 'windows-1252', $info_pdf_pr[$i][1])), 0 , 1);
			// MAX
				$this->Rect(88, $y, 15, 15, 'F');
				$this->SetXY(88, $y);
				$this->Cell(1);
				$this->MultiCell(14, 10, (iconv('UTF-8', 'windows-1252', $info_pdf_pr[$i][2].'%')), 0 , 1);
			// MIN
				$this->Rect(103, $y, 15, 15, 'F');
				$this->SetXY(103, $y);
				$this->Cell(1);
				$this->MultiCell(14, 10, (iconv('UTF-8', 'windows-1252', $info_pdf_pr[$i][3].'%')), 0 , 1);
			// VPN
				
				//$vpn = money_format($fmt, $info_pdf_pr[$i][6]);
				
				$this->Rect(118, $y, 44, 15, 'F');
				$this->SetXY(118, $y);
				$this->Cell(1);
				$this->MultiCell(43, 10, (iconv('UTF-8', 'windows-1252', $info_pdf_pr[$i][4])), 0 , 1);
				
				$this->Rect(162, $y, 22, 15, 'F');
				$this->SetXY(162, $y);
				$this->Cell(1);
				$this->MultiCell(21, 10, (iconv('UTF-8', 'windows-1252', $info_pdf_pr[$i][5])), 0 , 1);
				
				$this->Rect(184, $y, 24, 15, 'F');
				$this->SetXY(184, $y);
				$this->Cell(1);
				$this->MultiCell(23, 10, (iconv('UTF-8', 'windows-1252', $info_pdf_pr[$i][6])), 0 , 1);
				
				$y = $y + 15;
				$ib = $i + 1;
			}
			
			$this->SetTextColor(25, 25, 25); 
			
			if($info_pdf_pr[$i][0] == '-'){ 
				
				$this->SetTextColor(255, 255, 255); 
				$this->SetFillColor(155, 4, 39); 
				$y += 15; 				
				
			}else if($info_pdf_pr[$i][0]%2==0) $this->SetFillColor(232, 230, 231);
			else $this->SetFillColor(203, 202, 202);
			
			if($y >= 260){
				$y = 40;
				$this->AddPage();
				$this->Image('../img/log_ent/'.$id_ent.'.jpg', 150, 20, 40, 15);
			}	
		}
	}
	
	function result_emisores($info_pdf_emi_inv, $info_pdf_emi_dep, $val_fon, $fecha_fonds, $id_ent){
	
		$this->SetFillColor(155, 4, 39);
		
		$y = 60;
		$num = 0;
	/*
	
		* EMISORES POR INVERSION
	*/
		
		$cab_pdf = 'Composición del Portafolio de Inversiones por Emisor';
		
		$this->SetTextColor(231, 60 ,60);
		$this->SetFont('Humanist521Lt','',15);
		$this->AddPage();
		$this->Image('../img/log_ent/'.$id_ent.'.jpg', 150, 20, 40, 15);
		
		$this->SetXY(10, 40);
		$this->Cell(1);
		$this->MultiCell(200, 8, (iconv('UTF-8', 'windows-1252', $cab_pdf)), 0 , 1);
		
		$this->SetXY(10, 60);
		$this->Cell(1);
		$this->MultiCell(200, 4, (iconv('UTF-8', 'windows-1252', $val_fon)), 0 , 1);
		
		$this->SetXY(10, 70);
		$this->Cell(1);
		$this->MultiCell(200, 4, (iconv('UTF-8', 'windows-1252', $fecha_fonds)), 0 , 1);
		
		$this->SetTextColor(255, 255 ,255);
		$this->SetFont('Humanist521Lt','',9);
		
	/*
		* EMISORES POR INVERSIONES
	*/
		
		for($i = 0; $i<count($info_pdf_emi_inv); ++$i){
		
		// NOMBRE DEL EMISOR
		
			$this->Rect(10, $y, 87, 12, 'F');
			$this->SetXY(10, $y);
			$this->Cell(1);
			$this->MultiCell(86, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_inv[$i][0])), 0 , 1);
			
		// MAXIMO EMISOR
			
			$this->Rect(97, $y, 15, 12, 'F');
			$this->SetXY(97, $y);
			$this->Cell(1);
			$this->MultiCell(14, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_inv[$i][1])), 0 , 1);
			
		// VALOR VPN
			
			$this->Rect(112, $y, 40, 12, 'F');
			$this->SetXY(112, $y);
			$this->Cell(1);
			$this->MultiCell(39, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_inv[$i][2])), 0 , 1);
			
		// PARTICIPACIÓN 
			
			$this->Rect(152, $y, 15, 12, 'F');
			$this->SetXY(152, $y);
			$this->Cell(1);
			$this->MultiCell(14, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_inv[$i][3].' %')), 0 , 1);
			
		// CALIFICACIÓN
			
			$this->Rect(167, $y, 30, 12, 'F');
			$this->SetXY(167, $y);
			$this->Cell(1);
			$this->MultiCell(29, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_inv[$i][4])), 0 , 1);
			
			$y = $y + 12;	
			
			$this->SetTextColor(25, 25, 25); 
			
			if($num%2==0) $this->SetFillColor(232, 230, 231);
			else $this->SetFillColor(203, 202, 202);
			++$num;
			/*if($info_pdf_pr[$i][0] == '-'){ 
				
				$this->SetTextColor(255, 255, 255); 
				$this->SetFillColor(155, 4, 39); 
				$y += 15; 				
				
			}else if($info_pdf_pr[$i][0]%2==0) $this->SetFillColor(232, 230, 231);
			else $this->SetFillColor(203, 202, 202);*/
			
			if($y >= 268){
				$y = 40;
				$this->AddPage();
				$this->Image('../img/log_ent/'.$id_ent.'.jpg', 150, 20, 40, 15);
			}
		}
		
	/*
		* EMISORES POR DEPOSITOS
	*/
		
		$this->SetFillColor(155, 4, 39);
		$y = 60;
		
		$cab_pdf = 'Composición del Portafolio de Depositos por Emisor';
		
		$this->SetTextColor(231, 60 ,60);
		$this->SetFont('Humanist521Lt','',15);
		$this->AddPage();
		$this->Image('../img/log_ent/'.$id_ent.'.jpg', 150, 20, 40, 15);
		
		$this->SetXY(10, 40);
		$this->Cell(1);
		$this->MultiCell(200, 8, (iconv('UTF-8', 'windows-1252', $cab_pdf)), 0 , 1);
		
		$this->SetXY(10, 60);
		$this->Cell(1);
		$this->MultiCell(200, 4, (iconv('UTF-8', 'windows-1252', $val_fon)), 0 , 1);
		
		$this->SetTextColor(255, 255 ,255);
		$this->SetFont('Humanist521Lt','',9);
		
		for($i = 0; $i<count($info_pdf_emi_dep); ++$i){
		
		// NOMBRE DEL EMISOR
		
			$this->Rect(10, $y, 87, 12, 'F');
			$this->SetXY(10, $y);
			$this->Cell(1);
			$this->MultiCell(86, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_dep[$i][0])), 0 , 1);
			
		// MAXIMO EMISOR
			
			$this->Rect(97, $y, 15, 12, 'F');
			$this->SetXY(97, $y);
			$this->Cell(1);
			$this->MultiCell(14, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_dep[$i][1])), 0 , 1);
			
		// VALOR VPN
			
			$this->Rect(112, $y, 40, 12, 'F');
			$this->SetXY(112, $y);
			$this->Cell(1);
			$this->MultiCell(39, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_dep[$i][2])), 0 , 1);
			
		// PARTICIPACIÓN 
			
			$this->Rect(152, $y, 15, 12, 'F');
			$this->SetXY(152, $y);
			$this->Cell(1);
			$this->MultiCell(14, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_dep[$i][3].' %')), 0 , 1);
			
		// CALIFICACIÓN
			
			$this->Rect(167, $y, 30, 12, 'F');
			$this->SetXY(167, $y);
			$this->Cell(1);
			$this->MultiCell(29, 12, (iconv('UTF-8', 'windows-1252', $info_pdf_emi_dep[$i][4])), 0 , 1);
			
			$y = $y + 12;	
			
			$this->SetTextColor(25, 25, 25); 
			
			if($num%2==0) $this->SetFillColor(232, 230, 231);
			else $this->SetFillColor(203, 202, 202);
			++$num;
			
			if($y >= 268){
				$y = 40;
				$this->AddPage();
				$this->Image('../img/log_ent/'.$id_ent.'.jpg', 150, 20, 40, 15);
			}
		}
		
	}
	
	function contenido($info_pdf, $info_pdf_pr, $cab_pdf, $info_pdf_emi_inv, $info_pdf_emi_dep, $val_fon, $fecha_fonds, $id_ent){
	
		$this->result_politicas($info_pdf, $cab_pdf, $val_fon, $fecha_fonds, $id_ent);
		$this->result_prohibiciones($info_pdf_pr, $cab_pdf, $val_fon, $fecha_fonds, $id_ent);
		$this->result_emisores($info_pdf_emi_inv, $info_pdf_emi_dep, $val_fon, $fecha_fonds, $id_ent);
		
		//print_r($info_pdf_pr);
	}
	
	function array_recibe($url_array) {
	
		$tmp = stripslashes($url_array);
		$tmp = urldecode($tmp);
		$tmp = unserialize($tmp);

		return $tmp;
	}
	
} // FIN Class PDF


$pdf = new PDF('P');

$info_pdf = $pdf -> array_recibe($_POST['info_pdf']);
$info_pdf_pr = $pdf -> array_recibe($_POST['info_pdf_pr']);
$info_pdf_emi_inv = $pdf -> array_recibe($_POST['info_pdf_emi_inv']);
$info_pdf_emi_dep = $pdf -> array_recibe($_POST['info_pdf_emi_dep']);

/*print_r($info_pdf_emi_inv);
echo'<br>';
print_r($info_pdf_emi_dep);*/

$cab_pdf = $_POST['cab_pdf'];
$id_ent = $_POST['id_ent'];
$val_fon = $_POST['val_fon'];
$fecha_fonds = $_POST['fecha_fonds'];

$pdf->AddPage();

$pdf->AddFont('Humanist521Lt','','humanist.php');
$pdf->SetFont('Humanist521Lt','',8);

$pdf->SetTextColor(255, 255, 255);

$pdf->contenido($info_pdf, $info_pdf_pr, $cab_pdf, $info_pdf_emi_inv, $info_pdf_emi_dep, $val_fon, $fecha_fonds, $id_ent);
$pdf->Output($cab_pdf.$fecha_fonds.'.pdf','D', 'L'); //Salida al navegador

?>