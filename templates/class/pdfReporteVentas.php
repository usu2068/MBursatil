<meta http-equiv="content-type" content="text/html; charset=utf-8">
<?php
/* 
ob_end_clean(); 
session_start();
error_reporting(0);
require('pdf/fpdf.php');
include('../includes/conectarse.php');
include('../php/numerosALetras.class.php'); 
 

if(ereg( "MSIE", $_SERVER['HTTP_USER_AGENT'])){  
	 header('Cache-Control: maxage=3600');  
	 header('Pragma: public');  
}	
		
class PDF extends FPDF{

	
	
	//Cabecera de página
	function Header(){
		
		$link=conectarse();
		
	}

	function estiloTextoNormal(){
	
	    $this->SetFont('helvetica','',11);
		$this->SetTextColor(25,25,25);
		
	}
	
	function estiloTextoPeque(){
	
	    $this->SetFont('helvetica','',5);
		$this->SetTextColor(25,25,25);
		
	}

	function estiloTituloLabelNormal(){
	
	    $this->SetFont('helvetica','',18);
		$this->SetTextColor(25,25,25);
		
	}

	function estiloTextoTituloPrincipal(){
	
	    $this->SetFont('helvetica','',14);
		$this->SetTextColor(102,153,204);

	}

	function encabezadoTablaContenido(){
	
		$this->SetDrawColor(0,0,0);
		$this->SetDrawColor(100);
		$this->SetFillColor(200);
		$this->SetLineWidth(.3);		
	}

	function entidad($Nombre,$Nit,$Telefono,$Direccion,$fechaEnv, $plazo){
	
		$fecIni = "01-01-1900";
		$fecFin = "01-01-1900";
		//echo($fechaEnv);
		if($fechaEnv == date('Y-m-d')){
			$fecIni = date('  d          m          Y');
				if($plazo == 30){
				setlocale(LC_TIME, "spanish");
				$fecFin = strftime(utf8_decode('%d de %B del %Y'), strtotime("+ 1 month"));
				}else {
				setlocale(LC_TIME, "spanish");
				$fecFin = strftime(utf8_decode('%d de %B del %Y'), strtotime("+ ".$plazo." day"));
				}
			}else{
				for($i = 0; $i<=31; ++$i){
				
				 if($fechaEnv == date('Y-m-d',strtotime("+".$i."day"))){
				 	if($i== 30 || $i == 31){
						$fecIni = date('  d          m          Y', strtotime("+ 1 month"));
						if($plazo == 30){
							setlocale(LC_TIME, "spanish");
							$fecFin = strftime(utf8_decode('%d de %B del %Y'), strtotime("+ 2 month"));
						}else {
							setlocale(LC_TIME, "spanish");
							$fecFin = strftime(utf8_decode('%d de %B del %Y'), strtotime("+ ".$plazo." day + 1 month"));
						}
					}else{
						$fecIni = date('  d          m          Y',strtotime("+ ".$i." day"));
						if($plazo == 30){
							setlocale(LC_TIME, "spanish");
							$fecFin = strftime(utf8_decode('%d de %B del %Y'), strtotime("+".$i."day + 1 month"));
						}else {
							$plazo = $plazo + $i;
							setlocale(LC_TIME, "spanish");
							$fecFin = strftime(utf8_decode('%d de %B del %Y'), strtotime("+ ".$plazo." day"));
							}
						}
					}
				}
			}
		
		$this->estiloTextoNormal();
		
		$this->SetY(60);
		$this->Cell(15);
	    $this->MultiCell(100,4,(iconv('UTF-8', 'windows-1252', utf8_decode($Nombre))),0,'J');
		
		$this->SetY(63);
		$this->Cell(155);
	    $this->Cell(28,4,(iconv('UTF-8', 'windows-1252', utf8_decode($fecIni))),0,0,'J');
		
		$this->SetY(73);
		$this->Cell(155);
	    $this->Cell(28,4,(iconv('UTF-8', 'windows-1252', utf8_decode($Nit))),0,0,'J');
		
		$this->SetY(83);
		$this->Cell(15);
	    $this->Cell(28,4,(iconv('UTF-8', 'windows-1252', utf8_decode($Telefono))),0,0,'J');
		
		$this->SetY(73);
		$this->Cell(15);
	    $this->Cell(28,4,(iconv('UTF-8', 'windows-1252', utf8_decode($Direccion))),0,0,'J');
		
		$this->SetY(83);
		$this->Cell(145);
	    $this->Cell(28,4,(iconv('UTF-8', 'windows-1252', utf8_decode($fecFin))),0,0,'J');

	}
	
	function Concepto($Concepto){
	
		$this->SetY(115);
		$this->Cell(15);
		$this->estiloTextoNormal();
		$this->MultiCell(160,4, (iconv('UTF-8', 'windows-1252', utf8_decode($Concepto))),0,'J'); // Cartera Colectiva
	}
	
	function Ventas($IVA,$subTotal,$totalNeto,$valorenLetras){
	
		$totalNeto = number_format($totalNeto,0,",",".");
		$subTotal = number_format($subTotal,0,",",".");
		$IVA = number_format($IVA,0,",",".");
	
		$this->estiloTextoNormal();
	
		$this->SetY(260);
		$this->Cell(167);
	    $this->Cell(28,4,(iconv('UTF-8', 'windows-1252', utf8_decode($IVA).",00")),0,0,'J');
		
		$this->SetY(250);
		$this->Cell(167);
	    $this->Cell(28,4,(iconv('UTF-8', 'windows-1252', utf8_decode($subTotal).",00")),0,0,'J');
		
		$this->SetY(270);
		$this->Cell(167);
	    $this->Cell(28,4,(iconv('UTF-8', 'windows-1252', utf8_decode($totalNeto).",00")),0,0,'J');
		
		$this->SetY(232);
		$this->Cell(8);
	    $this->MultiCell(160,4,(iconv('UTF-8', 'windows-1252', utf8_decode($valorenLetras)." PESOS M/CTE")),0,'J');

	}	
	


	//Contenido de la pagina
	function contenido($get_parametros){	
	
		$link=conectarse();
		mysql_select_db("usu2068_bdCartera",$link);
		
		$get_idFactura=$get_parametros;
		
		/****************************************************************************************************/
		/******************************************Query de Facturas*****************************************/
		/****************************************************************************************************/
		
		/*$sqlFactura="

			SELECT

				factura.*
						

			FROM

				factura

			WHERE

				id = '".$get_idFactura."'
			
		";
		//echo($sqlFactura); 
		$resultFactura=mysql_query($sqlFactura, $link);
		$rowFactura=mysql_fetch_array($resultFactura);
		
		/****************************************************************************************************/
		/***************************************** Query de Cliente *****************************************/
		/****************************************************************************************************/
		

		/*$sqlCliente="
				SELECT
					cliente.*,factura.*
					
				FROM
					cliente, factura
				WHERE
					cliente.trash=1 AND 
					factura.id = '".$get_idFactura."' AND
					factura.idCliente = cliente.id
		";	
		
		$resultCliente=mysql_query($sqlCliente, $link);
		$rowCliente=mysql_fetch_array ($resultCliente);
		
		/****************************************************************************************************/
		/***************************************** Query de Ventas ******************************************/
		/****************************************************************************************************/
		
		/*$sqlVentas="
			
			SELECT 
			
				ventas.*
				
			FROM
				
				ventas
				
			WHERE 
				
				idFactura = '".$get_idFactura."' AND
				trash = 1
				
		";
		
		$resultVentas=mysql_query($sqlVentas, $link);
		$rowVentas=mysql_fetch_array($resultVentas);
		
		/****************************************************************************************************/
		/************************************* Query de Concepto ********************************************/
		/****************************************************************************************************/
		
		/*$sqlConcepto = "
			
			SELECT 
			
				concepto.*
				
			FROM
			
				concepto
				
			WHERE
			
			   idFactura = '".$get_idFactura."' AND
			   trash = 1
			   
		
		";
		
		$resultConcepto=mysql_query($sqlConcepto, $link);
		$rowConcepto=mysql_fetch_array($resultConcepto);
		
		$valorLetras = num2letras($rowVentas['totalNeto']);
		

		$this->entidad($rowCliente['Nombre'],$rowCliente['Nit'],$rowCliente['Telefono'],$rowCliente['Direccion'],$rowFactura['FechaEnvio'], $rowFactura['plazo']);
		$this->Concepto($rowConcepto['Concepto']);
		$this->Ventas($rowVentas['IVA'],$rowVentas['subTotal'],$rowVentas['totalNeto'],$valorLetras);

	}
}
	
	$link=conectarse();
	mysql_select_db("usu2068_bdCartera",$link);
		
	$sql="
				SELECT
					cliente.*,factura.*
					
				FROM
					cliente, factura
				WHERE
					cliente.trash=1 AND 
					factura.id = '".$_GET["parametros"]."' AND
					factura.idCliente = cliente.id
		";	
		
		$result=mysql_query($sql, $link);
		$row=mysql_fetch_array ($result);
		
//Creación del objeto de la clase heredada
$pdf=new PDF(); // 'L'
$pdf->SetTopMargin(5);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->contenido($_GET["parametros"]);
$pdf->Output('fac_'.utf8_decode($row['Nombre']).date("d/m/Y").'.pdf','D');*/




	error_reporting(0);

	session_start();

	
	include('../includes/conectarse.php');
	
	$link=conectarse();
	mysql_select_db("dbcartera",$link);	

	$fechaIni = $_POST["fechaIni"];
	$fechaFin = $_POST["fechaFin"];
	

	$sql="
		
		SELECT
			 cliente.*, factura.*, ventas.*
		FROM
			 cliente, factura, ventas
		WHERE
			 factura.trash=1 AND 
             factura.idCliente = cliente.id AND
             ventas.idFactura = factura.id AND 
			 factura.tipoFactura = '".$_POST["tipo"]."' AND
			 factura.FechaEnvio >= '".date($fechaIni)."' AND
			 factura.FechaEnvio <= '".date($fechaFin)."'
		ORDER BY 
		
			factura.numeroFactura
		ASC
		
	";
	
	$result=mysql_query($sql, $link);
	$csvName = "ReporteVentas ". date('d'."-".'m'."-".'Y') .".csv";
	$f = fopen($csvName,"w");
	$sep = ";";
	
	header('Content-Type: application/csv; utf-8');
	
			$linea = "No. FACTURA" .$sep."VALOR" .$sep."VALOR RECIBIDO".$sep."CLIENTE" .$sep."ESTADO" .$sep."FECHA RADICADO".$sep."FECHA DE PAGO". $sep."BANCO"."\n";
			fwrite($f,$linea);
			$totalFacturado = 0;
			$totalRecibido = 0;
		while($row=mysql_fetch_array($result) ) {		
			if((utf8_decode($row['estado'])) == 1)$estadot ="Pendiente";
				else if((utf8_decode($row['estado'])) == 2) $estadot="Anulada";
				else if(((utf8_decode($row['estado'])) == 3)) $estadot="Paga";
				$totalFacturado = $totalFacturado + $row["subTotal"];
				$totalRecibido = $totalRecibido + $row["ValorRecibido"];
			$linea = utf8_decode($row ['numeroFactura']) .$sep.utf8_decode($row ['subTotal']) .$sep.utf8_decode($row ['ValorRecibido']) .$sep.utf8_decode($row ['Nombre']) .$sep.$estadot .$sep.utf8_decode($row ['FechaEnvio']) .$sep.utf8_decode($row['FechaPagado']). $sep.utf8_decode($row['bancoConsignacion'])."\n";
			fwrite($f,$linea);
		}
		
	$linea = "TOTAL =" .$sep.utf8_decode($totalFacturado) .$sep.utf8_decode($totalRecibido).$sep."-" .$sep."-" .$sep."-".$sep."-". $sep."-"."\n";
			fwrite($f,$linea);	
	
	fclose($f);
	//echo $linea;
	echo "Se a generado su reporte satisfactoriamente";

	
		

?>

<a href="../pdf/<?php echo $csvName ?>">de click aqui para descargarlo</a>
