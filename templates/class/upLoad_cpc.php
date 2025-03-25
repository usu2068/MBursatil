<?php
include_once('consultas.php');
include_once('html.php');

$consult = new mysql(); // Creación objeto sql

$table_emis = array('jo33_FIC_UTLR_emisores'); // tabla de emisores
$valC_emis = array('active', 'trash'); // campos a buscar
$val_emis = array(1, 1); // parametos a buscar
$sql_emis = $consult -> sql('S', $table_emis, $val_emis, $valC_emis, $valU);

$fil_emi = 0;

while($row_emis = mysqli_fetch_array($sql_emis)){

	$emisores_cpc[$fil_emi][0] = $row_emis['nombre']; // nombre emisor
	$emisores_cpc[$fil_emi][1] = $row_emis['nit']; // nit emisor
	$emisores_cpc[$fil_emi][2] = $row_emis['sector']; // sector
	$emisores_cpc[$fil_emi][3] = $row_emis['cod_nemo']; // nemo
	$emisores_cpc[$fil_emi][4] = $row_emis['cod_issin']; // issin
	
	++ $fil_emi;
}

//print_r($emisores_cpc);

$upload_depo="true";
$id_ent = $_POST['ent'];
$file_name = 'formato_cpc';

$num_pos = 0;

$uploadedfile_size=$_FILES['arch_351'][size];
$fecha_fonds = $_POST['fech_dep'];


if ( $_FILES[arch_dep][size] > 25000000 ){
	$msg=$msg."Uno de los archivos es mayor que 25Mg, debe reduzcirlo antes de subirlo <br>";
	//$upload_351="false";
	$upload_depo="false";
}

/*if (!($_FILES[arch_351][type] =="text/plain")){
	$msg=$msg." El archivo de 351 tiene que ser txt. Otros archivos No son permitidos";
	$upload_351="false";
}

if (!($_FILES[arch_dep][type] =="application/vnd.ms-excel")){
	$msg=$msg." El archivo de los depositos tiene que ser csv. Otros archivos No son permitidos";
	$upload_depo="false";
}*/

$ruta_cpc = "../../planos/anna/".$file_name.".csv";

if( $upload_depo == "true" ){
	
///**/ - /* CARGA ARCHIVO CPC */ - /**/ ▼▼▼▼
	
	if( move_uploaded_file ( $_FILES[arch_dep][tmp_name], $ruta_cpc ) ){
		$door = 1;	
		echo "Estamos creando el nuevo archivo, espere un momento por favor. <br><a href='../../planos/anna/formato_procesado_anna.csv'>descargar</a> ";
	}else{
		$door = 0;
		echo "Error al subir el archivo";
	}
	
	if($door == 1){
	
///**/ - /*INTERPRETACION ANNA Y CREACIÓN DEL ARCHIVO PARA SUBIR A CPC*/ - /**/	
	
// ABRE EL ARCHIVO CPC QUE SE HA CARGADO ▲▲▲▲
		if (($fichero_cpc = fopen($ruta_cpc, "r")) !== FALSE) {
			while (($datos = fgetcsv($fichero_cpc, 1000, ";")) !== FALSE) { // Recorre celdas del archivo csv - CPC
				
				$cod_fic_cpc[$num_pos] = $datos[0]; // guardamos el codigo del fondo
				$tact_cpc[$num_pos] = $datos[1]; // guardamos el tipo de activo
				$vinc_cpc[$num_pos] = $datos[2]; // guardamos vinculado
				$tinv_cpc[$num_pos] = $datos[6]; // guardamos el tipo de Inversion
				$vpn_cpc[$num_pos] = $datos[14]; // guardamos vpn_cpc
				
				$isins_cpc[$num_pos] = $datos[16]; // Guarda ISSIN
				$nemos_cpc[$num_pos] =  $datos[15];// Guarda NEMO
								
				++ $num_pos; // aumenta en uno la pocición de los arrays
				
			} // fin while
		} // fin if
//print_r($isins_cpc);
// ABRE EL ARCHIVO ANNA QUE SE HA CARGADO
		
		$ruta_anna = "../../planos/anna/formato_anna.csv";
		
		$cal_anna = array(
			0 => "A::35",
			1 => "A-::37",
			2 => "A+::28",
			3 => "AA::18",
			4 => "AA-::23",
			5 => "AA+::11",
			6 => "AAA::1",
			7 => "BB-::",// falta codigo cpc
			8 => "BRC1::14",
			9 => "BRC1+:: 4",
			10 => "BRC2:: 25",
			11 => "BRC3:: 30",
			12 => "CCC::", // falta codigo cpc
			13 => "F1::6",
			14 => "F1+::6",
			15 => "NACION::9",
			16 => "VrR1+::5",
			17 => "VrR2::", // falta codigo cpc
			18 => "MULTILATER::50"
		);
		
		$sep = ";"; //separador para el nuevo archivo
		
		$fil_cvs[0] = 
			'Codigo'.$sep.
			'Tipo de Activo'.$sep.
			'Vinculado o no'.$sep.
			'Tipo de Titulo'.$sep.
			'Emisor'.$sep.
			'NIT'.$sep.
			'Tipo Inversion'.$sep.
			'Tipo Registro'.$sep.
			'Sisitema TRansaccional'.$sep.
			'Tipo Moneda'.$sep.
			'Tipo Sector'.$sep.
			'Calificacion'.$sep.
			'Dias Vencimiento'.$sep.
			'Duracion'.$sep.
			'VPN'.$sep."\n";
		
		if (($fichero_anna = fopen($ruta_anna, "r")) !== FALSE) { // Abre el formato ANNA
		
			while (($datos = fgetcsv($fichero_anna, 1000, ";")) !== FALSE) { // Recorre celdas del archivo csv - CPC
				
				$n = 0;
				$i = 1;
				for($i = 1; $i < $num_pos; ++$i){ // Recorre los issin y busca los que se tienen en el archivo ANNA
				
					if( $datos[3] === $isins_cpc[$i] ){ // Si el codigo Issin existe

					// COLUMNA TIPO DE MONEDA					
						if(strpos($datos[12], "COP") !== FALSE) $tip_moneda = 1; // si el tipo de moneda es peso colombiano
						else if(strpos($datos[12], "USD") !== FALSE) $tip_moneda = 2; // si el tipo de moneda es dollar
						else if(strpos($datos[12], "UVR") !== FALSE) $tip_moneda = 1; // si el tipo de moneda es peso colombiano
						else if(strlen($datos[12]) == 3 ) $tip_moneda = 3; // otra divisa - tamaño de la palabra
						else $tip_moneda = $datos[12];

					// COLUMNA CALIFICACIÓN
						for( $k = 0; $k <= count($cal_anna); ++$k ){
							$cal_tit = explode( "::", $cal_anna[$k] ); // separa texto de la pocision $k del array 
							if( strpos( $datos[30], $cal_tit[0] ) !== FALSE ) $cal_cpc = $cal_tit[1];
						}
						
					// COLUMNAS DEL EMISOR
						
						if( substr($datos[2], 0, 1) === 'T' || substr($datos[2], 0, 3) === 'BPE'){
							
							$table_emi_gov = array('jo33_FIC_UTLR_emisores'); // tabla de emisores
							$valC_emi_gov = array('nit', 'active', 'trash'); // campos a buscar
							
							if(substr($datos[2], 0, 3) === 'TDA')$val_emi_gov = array('8001163987',1, 1); // parametos a buscar	
							else $val_emi_gov = array('8999990902',1, 1); // parametos a buscar
							
							$sql_emi_gov = $consult -> sql('S', $table_emi_gov, $val_emi_gov, $valC_emi_gov, $valU);
							$row_emi_gov = mysqli_fetch_array($sql_emi_gov);
							
							$nom_emi = $row_emi_gov['nombre'];
							$nit_emi = $row_emi_gov['nit'];
							$sector_emi = $row_emi_gov['sector'];
							
						}else{
							for($s = 0; $s <= count( $emisores_cpc ); ++$s){
								for( $j = 0; $j <= count( $emisores_cpc[$s] ); ++ $j){
									if(strpos(substr($datos[2], 1), $emisores_cpc[$s][3]) !== FALSE){
										if($emisores_cpc[$s][2] != 0){
											$nom_emi = $emisores_cpc[$s][0];
											$nit_emi = $emisores_cpc[$s][1];
											$sector_emi = $emisores_cpc[$s][2];
										}
									}
								}
							}
						}
					// COLUMNA TIPO DE Titulo
						$table_tip_tit = array('jo33_FIC_UTLR_tit_nemo_issin');
						$valC_tip_tit = array('issin');
						$val_tip_tit = array("'".$isins_cpc[$i]."'");
						$sql_tip_tit = $consult -> sql('S', $table_tip_tit, $val_tip_tit, $valC_tip_tit, $val_U);
						
						while($row_tip_tit = mysqli_fetch_array($sql_tip_tit)){
							$tip_tit = $row_tip_tit['parametro_cpc'];
						}
						
					
					// ARRAY CON FILAS DEL NUEVO FORMATO
						$fil_cvs[$i] = 
							$cod_fic_cpc[$i].$sep. // codigo FIC
							$tact_cpc[$i].$sep. // Tipo de Activo
							$vinc_cpc[$i].$sep. // Vinculado o no
							$tip_tit.$sep. // Tipo Titulo
							$nom_emi.$sep. // Nombre del Emisor
							$nit_emi.$sep. // Nit Emisor
							$tinv_cpc[$i].$sep. // Tipo Inversion
							'1'.$sep. // Tipo de Registro - siempre es RNVE
							'1'.$sep. // Sisitema Transaccional 
							$tip_moneda.$sep. // Tipo de Moneda
							$sector_emi.$sep. // Tipo Sector
							$cal_cpc.$sep. // Calificación
							$datos[11].$sep. // Días Vencimiento
							$datos[26].$sep. // Duración
							$vpn_cpc[$i].$sep."\n"; // VPN
							
							$nom_emi = '';
							$nit_emi = '';
							$sector_emi = '';
							
						//$i = $num_pos;
						
					}else if($isins_cpc[$i] == 'NA'){
						
						$fil_cvs[$i] = 
							$cod_fic_cpc[$i].$sep. // codigo FIC
							$tact_cpc[$i].$sep. // Tipo de Activo
							$vinc_cpc[$i].$sep. // Vinculado o no
							'-'.$sep. // Tipo Titulo
							'-'.$sep. // Nombre del Emisor
							'-'.$sep. // Nit Emisor
							'-'.$sep. // Tipo Inversion
							'-'.$sep. // Tipo de Registro - siempre es RNVE
							'-'.$sep. // Sisitema Transaccional 
							'-'.$sep. // Tipo de Moneda
							'-'.$sep. // Tipo Sector
							'-'.$sep. // Calificación
							'-'.$sep. // Días Vencimiento
							'-'.$sep. // Duración
							$vpn_cpc[$i].$sep."\n"; // VPN
						
						//$i = $num_pos;
						
					}else if($isins_cpc[$i] == '0'){
					
						$fil_cvs[$i] = 
							$cod_fic_cpc[$i].$sep. // codigo FIC
							'0'.$sep. // Tipo de Activo
							'0'.$sep. // Vinculado o no
							'0'.$sep. // Tipo Titulo
							'0'.$sep. // Nombre del Emisor
							'0'.$sep. // Nit Emisor
							'0'.$sep. // Tipo Inversion
							'0'.$sep. // Tipo de Registro - siempre es RNVE
							'0'.$sep. // Sisitema Transaccional
							'0'.$sep. // Tipo de Moneda
							'0'.$sep. // Tipo Sector
							'0'.$sep. // Calificación
							'0'.$sep. // Días Vencimiento
							'0'.$sep. // Duración
							$vpn_cpc[$i].$sep."\n"; // VPN
						
						//$i = $num_pos;
						
					}else if($isins_cpc[$i] == '' ){ // ANALISIS POR NEMOTECNICO
					
						if($nemos_cpc[$i] != ''){
							if( $nemos_cpc[$i] == $datos[2] ){ // Si el codigo Issin existe
							
							// COLUMNA TIPO DE MONEDA
								if(strpos($datos[12], "COP") !== FALSE) $tip_moneda = 1; // si el tipo de moneda es peso colombiano
								else if(strpos($datos[12], "USD") !== FALSE) $tip_moneda = 2; // si el tipo de moneda es dollar
								else if(strpos($datos[12], "UVR") !== FALSE) $tip_moneda = 1; // si el tipo de moneda es peso colombiano
								else if(strlen($datos[12]) == 3 ) $tip_moneda = 3; // otra divisa - tamaño de la palabra
								else $tip_moneda = $datos[12];

							// COLUMNA CALIFICACIÓN
								for( $k = 0; $k <= count($cal_anna); ++$k ){
									$cal_tit = explode( "::", $cal_anna[$k] ); // separa texto de la pocision $k del array 
									if( strpos( $datos[30], $cal_tit[0] ) !== FALSE ) $cal_cpc = $cal_tit[1];
								}
							$resto = substr ("abcdef", 1);
							// COLUMNAS DEL EMISOR
								for($s = 0; $s <= count( $emisores_cpc ); ++$s){
									for( $j = 0; $j <= count( $emisores_cpc[$s] ); ++ $j){
										if(strpos( substr($datos[2], 1), $emisores_cpc[$s][3]) !== FALSE){
											if($emisores_cpc[$s][2] != 0){
												$nom_emi = $emisores_cpc[$s][0];
												$nit_emi = $emisores_cpc[$s][1];
												$sector_emi = $emisores_cpc[$s][2];
											}
										}
									}
								}
								
							}
							
						// ARRAY CON FILAS DEL NUEVO FORMATO
							$fil_cvs[$i] = 
								$cod_fic_cpc[$i].$sep. // codigo FIC
								$tact_cpc[$i].$sep. // Tipo de Activo
								$vinc_cpc[$i].$sep. // Vinculado o no
								$tip_tit.$sep. // Tipo Titulo
								$nom_emi.$sep. // Nombre del Emisor
								$nit_emi.$sep. // Nit Emisor
								$tinv_cpc[$i].$sep. // Tipo Inversion
								'1'.$sep. // Tipo de Registro - siempre es RNVE
								'1'.$sep. // Sisitema Transaccional 
								$tip_moneda.$sep. // Tipo de Moneda
								$sector_emi.$sep. // Tipo Sector
								$cal_cpc.$sep. // Calificación
								$datos[11].$sep. // Días Vencimiento
								$datos[26].$sep. // Duración
								$vpn_cpc[$i].$sep."\n"; // VPN
								
								$nom_emi = '';
								$nit_emi = '';
								$sector_emi = '';
								//$i = $num_pos;
							
						}else{
							
							// ARRAY CON FILAS DEL NUEVO FORMATO
							$fil_cvs[$i] = 
								$cod_fic_cpc[$i].$sep. // codigo FIC
								'Titulo sin NEMO ni ISSIN'.$sep. // Tipo de Activo
								'Titulo sin NEMO ni ISSIN'.$sep. // Vinculado o no
								'Titulo sin NEMO ni ISSIN'.$sep. // Tipo Titulo
								'Titulo sin NEMO ni ISSIN'.$sep. // Nombre del Emisor
								'Titulo sin NEMO ni ISSIN'.$sep. // Nit Emisor
								'Titulo sin NEMO ni ISSIN'.$sep. // Tipo Inversion
								'Titulo sin NEMO ni ISSIN'.$sep. // Tipo de Registro - siempre es RNVE
								'Titulo sin NEMO ni ISSIN'.$sep. // Sisitema Transaccional
								'Titulo sin NEMO ni ISSIN'.$sep. // Tipo de Moneda
								'Titulo sin NEMO ni ISSIN'.$sep. // Tipo Sector
								'Titulo sin NEMO ni ISSIN'.$sep. // Calificación
								'Titulo sin NEMO ni ISSIN'.$sep. // Días Vencimiento
								'Titulo sin NEMO ni ISSIN'.$sep. // Duración
								$vpn_cpc[$i].$sep."\n"; // VPN
								
							//$i = $num_pos;
						}
					}
					
					if($fil_cvs[$i] == '') {
					// ARRAY CON FILAS DEL NUEVO FORMATO
						$fil_cvs[$i] = 
							$cod_fic_cpc[$i].$sep. // Codigo FIC
							'ISSIN no registrado en ANNA'.$sep. // Tipo de Activo
							'ISSIN no registrado en ANNA'.$sep. // Vinculado o no
							$datos[2].$sep. // Tipo Titulo
							'ISSIN no registrado en ANNA'.$sep. // Nombre del Emisor
							'ISSIN no registrado en ANNA'.$sep. // Nit Emisor
							'ISSIN no registrado en ANNA'.$sep. // Tipo Inversion
							'ISSIN no registrado en ANNA'.$sep. // Tipo de Registro - siempre es RNVE
							'ISSIN no registrado en ANNA'.$sep. // Sisitema Transaccional
							'ISSIN no registrado en ANNA'.$sep. // Tipo de Moneda
							'ISSIN no registrado en ANNA'.$sep. // Tipo Sector
							'ISSIN no registrado en ANNA'.$sep. // Calificación
							'ISSIN no registrado en ANNA'.$sep. // Días Vencimiento
							'ISSIN no registrado en ANNA'.$sep. // Duración
							$vpn_cpc[$i].$sep."\n"; // VPN
					}
				} // fin FOR
			} //fin WHILE
		} // fin IF

// CREA EL ARCHIVO FINAL

		$new_arch_cpc = fopen("../../planos/anna/formato_procesado_anna.csv","w"); // nuevo archivo - CPC EXPORTADO
		
		for( $j = 0; $j <= $num_pos; ++ $j){ // llega al numero de campos array creado ▲▲▲▲
			fwrite($new_arch_cpc,$fil_cvs[$j]); // escritura en el nuevo archivo
		}
		fclose($new_arch_cpc);
	}
	
}else{ echo $msg; }


?>