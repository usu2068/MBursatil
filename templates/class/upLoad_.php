<?php
include_once('consultas.php');
include_once('html.php');
//include_once('pdf.php');

/*
	*
	*
	
	include('conectarse.php');

	$link = conectarse();
	mysql_select_db("aplicati_FIC",$link);

	$sql = '
	SELECT 
		jo33_FIC_UTLR_titulos_pol_fon.id_compocicion_pol_fon, 
		jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon.*
	FROM 
		`jo33_FIC_UTLR_titulos_pol_fon`, 
		`jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon`
	WHERE 
		`jo33_FIC_UTLR_titulos_pol_fon`.`id_Nom_tiulo` =97 AND 
		`jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon`.`id_titulos_pol_fon` = `jo33_FIC_UTLR_titulos_pol_fon`.`id`
	';

	$result = mysql_query($sql, $link)or die('Error:'.mysql_error());

	while($row = mysql_fetch_array($result)){

		$sql_ins = 'INSERT INTO `jo33_FIC_UTLR_titulos_pol_fon`(`max`, `min`, `active`, `trash`, `id_compocicion_pol_fon`, `id_Nom_tiulo`) VALUES (0,0,1,1,'.$row[0].',162)';
		$result_ins = mysql_query($sql_ins, $link)or die('Error titulo:'.mysql_error());
		
		$sql_ult = 'SELECT MAX(id) AS id FROM `jo33_FIC_UTLR_titulos_pol_fon`';
		$result_ult = mysql_query($sql_ult, $link)or die('Error ultimo:'.mysql_error());
		$row_ult = mysql_fetch_array($result_ult);
		
		$ult = $row_ult[0];
		
		$sql_insgr = 'INSERT INTO `jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon`(`id_grupos_fon`, `id_titulos_pol_fon`) VALUES ('.$row[1].','.$ult.')';
		$result_insgr = mysql_query($sql_insgr, $link)or die('Error Grupo:'.mysql_error());
		
		echo 'titulo: '.$ult.'<br>';
	}
	
	*
	*
*/
$cuerpo = new body();
$head = $cuerpo -> header('administradorutlr','../../');
$footer = $cuerpo -> footer('administradorutlr');
	
echo $head;
	
//$upload_351="true";
$upload_depo="true";
$id_ent = $_POST['ent'];
$file_name = $_POST['ent_n'];

$uploadedfile_size=$_FILES['arch_351'][size];
$fecha_fonds = $_POST['fech_dep'];


if ($_FILES[arch_dep][size]>25000000){
	$msg=$msg."Uno de los archivos es mayor que 25Mg, debes reduzcirlo antes de subirlo";
	//$upload_351="false";
	$upload_depo="false";
}

/*if (!($_FILES[arch_351][type] =="text/plain")){
	$msg=$msg." El archivo de 351 tiene que ser txt. Otros archivos No son permitidos";
	$upload_351="false";
}*/

/*if (!($_FILES[arch_dep][type] =="application/vnd.ms-excel")){
	$msg=$msg." El archivo de los depositos tiene que ser csv. Otros archivos No son permitidos";
	$upload_depo="false";
}*/

//$add_351="../../plaNos/351_".$file_name.".txt";
$add_depo="../../planos/".$file_name.".csv";

if($upload_depo=="true"){
	if(move_uploaded_file ($_FILES[arch_dep][tmp_name], $add_depo)){
		
		$conslt = new mysql();
		$table_ent = array('jo33_FIC_UTLR_entidad');
		$valC_ent = array('ubicacion_depo');
		$val_ent = array($add_depo);
		/*$valC_ent = array('ubicacion_351', 'ubicacion_depo');
		$val_ent = array($add_351, $add_depo);*/
		$valU_ent = array('id ='.$id_ent);
		$sql_ent = $conslt -> sql('U',$table_ent, $val_ent, $valC_ent, $valU_ent);
		
		$emisores = emisores($add_depo, $id_ent, 1);
		$emisores_pdf_inv = emisores($add_depo, $id_ent, '2I');
		$emisores_pdf_dep = emisores($add_depo, $id_ent, '2D');
		$grp_tot = emisores($add_depo, $id_ent, '2G');		
		$analisis = analisis($add_depo, $id_ent, $emisores_pdf_inv, $emisores_pdf_dep, $fecha_fonds, $grp_tot);
		
		/*$g_emisores = guardar_emisores($add_depo);*/
		//echo $g_emisores;
		echo $analisis[0];
		echo $emisores;
		echo '	
			<script type="text/javascript">
				$("#fech_repo").datetimepicker({
					format: "yyyy-MM-dd",
					language: "pt-ES"
				});
			</script>';
		
		//$content = $analisis[1];
	
		/*$pdf->DisplayPreferences('HideWindowUI'); 
		$pdf->AddPage();
		$pdf->WriteHTML($html); 
		$pdf->Output('doc.pdf','I'); */
		

		/*$html2pdf = new HTML2PDF('P','A4','fr');
		$html2pdf->WriteHTML($content);
		$html2pdf->Output('Reporte_Fondo_'.$row_sql[13].'.pdf','I');*/
			//" Realizando Analisis del portafolio.";
		
		//header("Location: http://aplicativojuridico.com/utlr/index.php/administrador");
		
	}else{echo "Error al subir el archivo";}
}else{echo $msg;}

/*
	* INICIO ANALISIS DEL FORMATO ECXEL
*/

function analisis($ruta, $id_ent, $emisores_pdf_inv, $emisores_pdf_dep, $fecha_fonds, $grp_tot){

	$i = 0;
	
	$inf_grpeco_inv_ult_fic = array();
	$inf_grpeco_dep_ult_fic = array();
	$inf_grpeco_tot_ult_fic = array();
	
	$rnve = array();
	$tsisneg = array();
	$tmoneda = array();
	$ttit = array();
	$tsec = array();
	$tact = array();
	$tot = array();
	$composicion = array();
	$tot_calificaciones = array();
	$n_tit = 0;
	$consult = new mysql();
	//$pdf = new PDF();
	$pro_actual = 0;
	
	$grp_eco_inv = $grp_tot[0];
	$grp_eco_dep = $grp_tot[1];
	
	/*
		* SQL PARA SMLV
	*/
	
	$table_smlv = array('jo33_FIC_UTLR_SMLV');
	$valC_smlv = array('id');
	$val_smlv = array(1);

	$sql_smlv = $consult -> sql('S', $table_smlv, $val_smlv, $valC_smlv, $valU_smlv);
	$row_smlv = mysql_fetch_array($sql_smlv);
	
	$max_smlv = $row_smlv['valor']*2600;
	
/*
	*
	* SQL PARA TRAER LA INFORMACUIÓN DE LAS POLITICAS
	*
*/
	
	$table_comp = array('jo33_FIC_UTLR_nom_compocicion');
	$valC_comp = array('trash','active');
	$val_comp = array(1,1);
	$sql_comp = $consult -> sql('S', $table_comp, $val_comp, $valC_comp, $valU);
	
	while($row_comp = mysql_fetch_array($sql_comp)) $composicion[$row_comp[0]] = $row_comp[1];
	
	$table_tot = array('jo33_FIC_UTLR_nom_tot');
	$valC_tot = array('active');
	$val_tot = array(1);
	$sql_tot = $consult -> sql('S', $table_tot, $val_tot, $valC_tot, $valU);
	
	while($row_tot = mysql_fetch_array($sql_tot)) $totales[$row_tot[0]] = $row_tot[1];
	
	$table_cons = array(
		'jo33_FIC_UTLR_entidad',
		'jo33_FIC_UTLR_fondos',
		'jo33_FIC_UTLR_politicas_fon',
		'jo33_FIC_UTLR_compocicion_pol_fon',
		'jo33_FIC_UTLR_titulos_pol_fon',
		'jo33_FIC_UTLR_nom_tiulo', 
		'jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon',
		'jo33_FIC_UTLR_grupos_fon');
		
	$valC = array(
		'jo33_FIC_UTLR_entidad.id',
		'jo33_FIC_UTLR_fondos.id_entidad',
		'jo33_FIC_UTLR_politicas_fon.id_fondos',
		'jo33_FIC_UTLR_compocicion_pol_fon.id_politicas_fon',
		'jo33_FIC_UTLR_titulos_pol_fon.id_compocicion_pol_fon',
		'jo33_FIC_UTLR_titulos_pol_fon.id_Nom_tiulo',
		'jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon.id_titulos_pol_fon',
		'jo33_FIC_UTLR_grupos_fon.id',
		'jo33_FIC_UTLR_titulos_pol_fon.active',	
		'jo33_FIC_UTLR_titulos_pol_fon.trash',
		'jo33_FIC_UTLR_fondos.active',
		'jo33_FIC_UTLR_fondos.trash');
		
	$val = array(
		$id_ent,
		'jo33_FIC_UTLR_entidad.id',
		'jo33_FIC_UTLR_fondos.id',
		'jo33_FIC_UTLR_politicas_fon.id',
		'jo33_FIC_UTLR_compocicion_pol_fon.id',
		'jo33_FIC_UTLR_nom_tiulo.id',
		'jo33_FIC_UTLR_titulos_pol_fon.id',
		'jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon.id_grupos_fon',
		1, 1,
		1, 1);
		
	$sql = $consult -> sql('S', $table_cons, $val, $valC, $valU);	
	
	$fil = 0;
	$cod_fon = 0;
	
	
/*
	*
	* SQL PARA TRAER LA INFORMACUIÓN DE LAS PROHIBICIONES
	*
*/

	$table_proh = array(
		'jo33_FIC_UTLR_entidad',
		'jo33_FIC_UTLR_fondos',
		'jo33_FIC_UTLR_prohibiciones_fon',
		'jo33_FIC_UTLR_compocicion_pro_fon',		
		'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon',
		'jo33_FIC_UTLR_nom_compocicion_pro',
		'jo33_FIC_UTLR_titulos_pro_fon',
		'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon',
		'jo33_FIC_UTLR_nom_titulo_pro'
		);
		
	$valC_proh = array(
		'jo33_FIC_UTLR_entidad.id',		
		'jo33_FIC_UTLR_fondos.id_entidad',		
		'jo33_FIC_UTLR_prohibiciones_fon.id_fondos',		
		'jo33_FIC_UTLR_compocicion_pro_fon.id_prohibiciones_fon',		
		'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon.id_compocicion_pro_fon',		
		'jo33_FIC_UTLR_nom_compocicion_pro.id',		
		'jo33_FIC_UTLR_titulos_pro_fon.id_compocicion_pro_fon',		
		'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon.id_titulos_pro_fon',		
		'jo33_FIC_UTLR_nom_titulo_pro.id',
		'jo33_FIC_UTLR_fondos.active',
		'jo33_FIC_UTLR_fondos.trash');
		
	$val_proh = array(
		$id_ent,		
		'jo33_FIC_UTLR_entidad.id',		
		'jo33_FIC_UTLR_fondos.id',
		'jo33_FIC_UTLR_prohibiciones_fon.id',		
		'jo33_FIC_UTLR_compocicion_pro_fon.id',		
		'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon.id_nom_compocicion_pro',		
		'jo33_FIC_UTLR_compocicion_pro_fon.id',		
		'jo33_FIC_UTLR_titulos_pro_fon.id',		
		'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon.id_nom_titulo_pro',	
		1, 1);

	$sql_proh = $consult -> sql('S', $table_proh, $val_proh, $valC_proh, $valU);

/*
	*
	* LECTURA DE EL ARCHIVO PLANo CSV
	*
*/
	
	if (($fichero = fopen($ruta, "r")) !== FALSE) {
	
		while (($datos = fgetcsv($fichero, 1000, ";")) !== FALSE) {
		
		/*
			1. Sumatoria los valores para RNVE
			2. Sumatoria de los valores Sistema de Negociación
			3. Tipo de Moneda
			4. Tipo de Inversión
			5. Tipo de titulo
			6. Tipo Sector
			7. Tipo Activo
			8. Resultado 
		*/
		
			if($datos[0] != $cod_fon){
				$cod_fon = $datos[0];
				$n_tit = 0;
				++ $fil;
			}
			
			/*Orden valores dentro del Array $pro_actual*/
			
			/*4.*/ $ttit[$fil][$datos[3]] = $ttit[$fil][$datos[3]] + $datos[14];
			
			$dias_vto_tit[$fil][$n_tit] = $datos[12];
			$valor_titulo[$fil][$n_tit] = $datos[14];
			$n_tit ++;
			
			if($datos[3]!=77){
				
				/*0.*/ $rnve[$fil][$datos[7]] = $rnve[$fil][$datos[7]] + $datos[14];
				/*1.*/ $tsisneg[$fil][$datos[8]] = $tsisneg[$fil][$datos[8]] + $datos[14];
				/*2.*/ $tmoneda[$fil][$datos[9]] = $tmoneda[$fil][$datos[9]] + $datos[14];
				/*3.*/ $tinv[$fil][$datos[6]] = $tinv[$fil] [$datos[6]] + $datos[14];
				
				/*5.*/ $tsec[$fil][$datos[10]] = $tsec[$fil][$datos[10]] + $datos[14];
				/*6.*/ $tact[$fil][$datos[1]] = $tact[$fil][$datos[1]] + $datos[14];
				/*7.*/ $cal[$fil][$datos[11]] = $cal[$fil][$datos[11]] + $datos[14];
				
				/*8.*/ $tot[$fil] = $tot[$fil] + $datos[14];
				
				if($datos[1] == 2)$depo[$fil] = $depo[$fil] + $datos[14];
				else if($datos[1] == 1 )$invers[$fil] = $invers[$fil] + $datos[14];
				
			/* RANGOS DE DÍAS */
				if( $datos[12] >= 1 && $datos[12] <= 30 ) $dias_vto[$fil][0] += $datos[14];
				if( $datos[12] >= 31 && $datos[12] <= 90 ) $dias_vto[$fil][1] += $datos[14];
				if( $datos[12] >= 91 && $datos[12] <= 180 ) $dias_vto[$fil][2] += $datos[14];
				if( $datos[12] >= 181 && $datos[12] <= 365 ) $dias_vto[$fil][3] += $datos[14];
				if( $datos[12] >= 366 && $datos[12] <=  1095 ) $dias_vto[$fil][4] += $datos[14];
				if( $datos[12] >= 1096 && $datos[12] <= 1825 ) $dias_vto[$fil][5] += $datos[14];
				if( $datos[12] >= 1826 && $datos[12] <= 3650 ) $dias_vto[$fil][6] += $datos[14];
				if( $datos[12] >= 3651 && $datos[12] <= 7300 ) $dias_vto[$fil][7] += $datos[14];
				if( $datos[12] >= 7301 && $datos[12] <= 10950 ) $dias_vto[$fil][8] += $datos[14];
				if( $datos[12] >= 10951 ) $dias_vto[$fil][9] += $datos[14];
			}
			
			/*
				* TOTAL DE LA SUMA DE LOS TOTALES
			/
				//print_r($totales[$Nom_ant]);
			if($totales[$Nom_ant] == 'Total Aaa' || $totales[$Nom_ant] == 'Total Aa +'){
				$tot_calificaciones[0] += $valor_total;
				$tot_calificaciones[1] += $valor_total;
				$tot_calificaciones[2] += $valor_total;
				$tot_calificaciones[3] += $valor_total;
				$tot_calificaciones[4] += $valor_total; }
				
			if($totales[$Nom_ant] == 'Total Aa'){
				$tot_calificaciones[1] += $valor_total;
				$tot_calificaciones[2] += $valor_total;
				$tot_calificaciones[3] += $valor_total;
				$tot_calificaciones[4] += $valor_total; }
				
			if($totales[$Nom_ant] == 'Total Aa-'){
				$tot_calificaciones[2] += $valor_total;
				$tot_calificaciones[3] += $valor_total;
				$tot_calificaciones[4] += $valor_total; }
				
			if($totales[$Nom_ant] == 'Total A +'){
				$tot_calificaciones[3] += $valor_total;
				$tot_calificaciones[4] += $valor_total; }
				
			if($totales[$Nom_ant] == 'Total A'){
				$tot_calificaciones[4] += $valor_total; }
			//print_r($tot_calificaciones);*/
			
			/*if($fil == 1){
				echo "Valor Unitario=".$datos[14]."<br />";
				echo "Sumatoria =".$tot[$fil]."<br />";
			}*/
			
			
		/*
			*	Orden Valores dentro Array 
		*/
		
			if($datos[2] == 1){ // EXAMINA VINCULADO
				
				if($datos[1] == 1) $emi_vinc[$fil] += $datos[14]; // VINCULADOS
				if($datos[1] == 2) $depo_proh[$fil] += $datos[14]; // DEPOSITOS
				if($datos[5] == $id_ent) $emi_prop[$fil] += $datos[14]; //TITULO PROPIO
				if($datos[3] == 41) $admin_soc[$fil] += $datos[14]; //ADMINISTRADO POR LA SOCIEDAD
				if($datos[3] == 77 || $datos[3] == 82) $repos[$fil] += $datos[14]; //ADMINISTRADO POR LA SOCIEDAD
				
				// PENDIENTE $info_proh_admin_soc[$fil][] = "";
			}
		}
		fclose($fichero);
		//print_r($tact);
		/*echo 'TIPO MONEDA <BR />';
		for($i = 0; $i < count($tinv); ++$i){
			for($j = 0; $j < count($tinv[$i]); ++$j){
				if($tinv[$i][$j] == NULL) echo '* -';
				else echo $i.' + '.$j.' / '.$tinv[$i][$j].' - ';
			}
			echo '<br />';
		}*/
		
		$fondo[4] = $rnve;
		$fondo[5] = $tsisneg;
		$fondo[6] = $tmoneda;
		$fondo[7] = $tinv;
		
		$fondo[0] = $ttit;
		
		$fondo[19] = $tsec;
		$fondo[14] = $tact;
		$fondo[15] = $dias_vto;
		$fondo[20] = $cal;
		
		//$fondo[9] = $tot;*/
	}
	
	setlocale(LC_MONETARY, 'es_CO');
	$fmt = '%i';

	$cod_fon = 0;
	$id_tot = 0;
	$fil_calc = 0;
	$conteo_fon = 0;
	$valor_total = 0;
	$div = '<div class="container"> <div class="row"> <div class="col-md-12">';
	$html = '<div class="tab-content">';
	$cont = 1;
	$btns_cab = '<ul class="nav nav-tabs nav-tabs-ar nav-tabs-ar-white">';
	
	$ids_comp = array();
	$ids_tot = array();
	$ids_tit = array();
	$info_pdf = array();
	$fil_pdf = 0;
	$pos_prom_pon = 0;
	
	$k = 1;

/*
	*
	* RESULTADOS PROHIBICIONES
*/	

	$valor = 0;
	
	while($row_proh = mysql_fetch_array($sql_proh)){
		
		//if($cod_fon == 0) $cod_fon = $row_proh[13];
		
		if($cod_fon != $row_proh[13]){
			
			$cod_fon = $row_proh[13];
			$proh_pdf[$k] = $info_pdf_pr;
			
			/*print_r($info_pdf_pr);
			echo '<br>';
			echo $k;
			echo '<br>';*/
			
			$info_pdf_pr = array();
			
			$tot_fn = $tot[$k];
			$vinc_fn = $emi_vinc[$k];
			$tit_prop = $emi_prop[$k];
			$part_fic = $admin_soc[$k];
			$dep_pr = $depo_proh[$k];
			$repo = $repos[$k];
			
			$fil_pdf = 0;
			++ $k;
		}
		
		if($id_com_pr != $row_proh[18]){
		
			$id_com_pr = $row_proh[18];
			$info_pdf_pr[$fil_pdf][0] = '-';
			
			++$fil_pdf;
			
			$info_pdf_pr[$fil_pdf][0] = 'No';
			$info_pdf_pr[$fil_pdf][1] = utf8_encode($row_proh[26]);
			$info_pdf_pr[$fil_pdf][2] = 'Mín';
			$info_pdf_pr[$fil_pdf][3] = 'Máx';
			$info_pdf_pr[$fil_pdf][4] = 'Valor';
			$info_pdf_pr[$fil_pdf][5] = 'Participación';
			$info_pdf_pr[$fil_pdf][6] = 'Resultado';
			
			++$fil_pdf;
			$cont = 1;
		}
		
		if ($row_proh[36] == 1) $valor = $tit_prop;
		else if ($row_proh[36] == 2) $valor = $vinc_fn;
		else if($row_proh[36] == 3) $valor = $part_fic;
		else if($row_proh[36] == 11) $valor = $dep_pr;
		else if($row_proh[36] == 4 || $row_proh[36] == 5) $valor = $repo;
		else $valor = 0;
		
		$VPN_pr = money_format( $fmt, $valor );
		
		$pro_proh = ( $valor / $tot_fn ) *100;
		
		if( $pro_proh >= $row_proh[30] && $pro_proh <= $row_proh[29] ) $cal_pr = 'Cumple';
		else $cal_pr = 'No Cumple';
		
		$info_pdf_pr[$fil_pdf][0] = $cont;
		$info_pdf_pr[$fil_pdf][1] = utf8_encode($row_proh[37]);
		$info_pdf_pr[$fil_pdf][2] = $row_proh[30];
		$info_pdf_pr[$fil_pdf][3] = $row_proh[29];
		$info_pdf_pr[$fil_pdf][4] = $VPN_pr;
		$info_pdf_pr[$fil_pdf][5] = round($pro_proh, 2).' %';
		$info_pdf_pr[$fil_pdf][6] = $cal_pr;
		
		/*echo '<br>'.$valor.'<br>';
		echo $pro_proh.'<br>';
		print_r($info_pdf_pr);*/
		
		++$fil_pdf;
		++ $cont;
	}	
	
	/*print_r($info_pdf_pr);
	echo '<br>';
	echo $k;
	echo '<br>';*/
	
	$proh_pdf[$k] = $info_pdf_pr;
	$cod_fon = 0;
	
/*
	*
	* RESULTADOS POLITICAS
*/

	$k = 1;
	$id_tot_com = 0;
	$com = 0;
	$cont_cal = 0;
	
	$cont_grp_eco = 0;
	$fil_grp_eco = 0;
	
	while($row_sql = mysql_fetch_array($sql)){
	
		
		
		$id_ent = $row_sql[0];
		if($row_sql[13] != $cod_fon && $row_sql[13] != ""){
		
			
			
			if($fil_calc == 0) $clas = 'active';
			else $clas = '';
			
			$kb = $k-1;
			
			$info_pdf_prh = array_envia($proh_pdf[$k]);
			$info_pdf_emi_inv = array_envia($emisores_pdf_inv[$kb]);
			$info_pdf_emi_dep = array_envia($emisores_pdf_dep[$kb]);
			
			++$k;
			
			$id_tot = $row_sql[36];
			$No_cump = 0;
			
			/*
				-	CALCULO PROMEDIO PONDERADO
			*/

			++ $fil_calc;
			
			for($a = 1; $a <= count($dias_vto_tit[$fil_calc]); ++ $a){
				$prom_pond_tit = ($valor_titulo[$fil_calc][$a]/$tot[$fil_calc]) * $dias_vto_tit[$fil_calc][$a];
				$prom_pond_tot += $prom_pond_tit;
			}			
			
			$ult_prom_pond = $prom_pond_tot;
			
			/*
				-	GRUPOS ECONOMICOS 
			*/
			
			$table_grpeco_sql = array('jo33_FIC_UTLR_grupos_eco');
			$valC_grpeco_sql = array('active', 'trash');
			$val_grpeco_sql = array(1, 1);
			
			$sql_grpeco = $consult -> sql('S', $table_grpeco_sql, $val_grpeco_sql, $valC_grpeco_sql, $valU);
			
			$dat_grpeco = array();
			
			$c = 1;
			
			$nom_grpeco[0] =  'Sin Grupo';
			
			while($row_grpeco = mysql_fetch_array($sql_grpeco)){
				$nom_grpeco[$c] =  $row_grpeco['nombre']; 
				$id_grpeco[$c] = $row_grpeco['id'];
				
				$c ++;
			}
			
			$inf_grpeco_inv_ult_fic = $info_pdf_grpeco_inv;
			$inf_grpeco_dep_ult_fic = $info_pdf_grpeco_dep;
			$inf_grpeco_tot_ult_fic = $info_pdf_grpeco_tot;
			
			$info_pdf_grpeco_inv = array();
			$info_pdf_grpeco_dep = array();
			$info_pdf_grpeco_tot = array();
			$fil_grp_eco = 0;
			
			$table_grp_eco_inv = '
					<table class="table">
						<tr><td colspan="5" align="center"> <h4> Grupos Economicos por Inversiones </h4> </td></tr>
						<tr>
							<th>Nombre</th>
							<th>Máx</th>
							<th>Participación</th> 
							<th>Valor</th>
							<th>Calificación</th>
						</tr>';
			
			$info_pdf_grpeco_inv[$fil_grp_eco][0] = 'Nombre';
			$info_pdf_grpeco_inv[$fil_grp_eco][1] = 'Máx';
			$info_pdf_grpeco_inv[$fil_grp_eco][2] = 'Participación';
			$info_pdf_grpeco_inv[$fil_grp_eco][3] = 'Valor';
			$info_pdf_grpeco_inv[$fil_grp_eco][4] = 'Resultado';
			
			$table_grp_eco_dep = '
					<table class="table">
						<tr><td colspan="5" align="center"> <h4> Grupos Economicos por Depositos Bancarios </h4> </td></tr>
						<tr>
							<th>Nombre</th>
							<th>Máx</th>
							<th>Participación</th> 
							<th>Valor</th>
							<th>Calificación</th>
						</tr>';
			
			$info_pdf_grpeco_dep[$fil_grp_eco][0] = 'Nombre';
			$info_pdf_grpeco_dep[$fil_grp_eco][1] = 'Máx';
			$info_pdf_grpeco_dep[$fil_grp_eco][2] = 'Participación';
			$info_pdf_grpeco_dep[$fil_grp_eco][3] = 'Valor';
			$info_pdf_grpeco_dep[$fil_grp_eco][4] = 'Resultado';
			
			$table_grp_eco_tot = '
					<table class="table">
						<tr><td colspan="5" align="center"> <h4> Grupos Economicos Total </h4> </td></tr>
						<tr>
							<th>Nombre</th>
							<th>Máx</th>
							<th>Participación</th> 
							<th>Valor</th>
							<th>Calificación</th>
						</tr>';
			
			$info_pdf_grpeco_tot[$fil_grp_eco][0] = 'Nombre';
			$info_pdf_grpeco_tot[$fil_grp_eco][1] = 'Máx';
			$info_pdf_grpeco_tot[$fil_grp_eco][2] = 'Participación';
			$info_pdf_grpeco_tot[$fil_grp_eco][3] = 'Valor';
			$info_pdf_grpeco_tot[$fil_grp_eco][4] = 'Resultado';
			
			++ $fil_grp_eco;
			
			for($f=0; $f<count($nom_grpeco); ++$f){
				
				$table_grpecoXfon = array('jo33_FIC_UTLR_fondos_X_grupos_eco');
				$valC_grpecoXfon = array('id_fondos', 'id_grupos_eco');
				$val_grpecoXfon = array('"'.$row_sql[6].'"', '"'.$id_grpeco[$f].'"');				
				$sql_grpecoXfon = $consult -> sql('S', $table_grpecoXfon, $val_grpecoXfon, $valC_grpecoXfon, $valU);
				
				$row_grpecoXfon = mysql_fetch_array($sql_grpecoXfon);
				
			// CONSTRUCION DE LA TABLA HTML DE LOS GRUPOS ECONOMICOS POR INVERSIONES
				
				if($row_grpecoXfon['max_inv'] == ''){
					if($f == 0) $max_inv_grpec = 100;
					else $max_inv_grpec = 0;					
				
				}else $max_inv_grpec = $row_grpecoXfon['max_inv'];
				
				$parti_grp_eco = ($grp_eco_inv[$cont_grp_eco][$f] * 100)/$total_fn_sf;
				
				if($max_inv_grpec >= $parti_grp_eco){ $status = 'Cumple'; $class_grpec = 'class="success"'; }
				else{ $status = 'No Cumple'; $class_grpec = 'class="danger"'; }
				
				$table_grp_eco_inv .= '
					<tr '.$class_grpec.'>
						<td >'.utf8_encode($nom_grpeco[$f]).'</td>
						<td> '.$max_inv_grpec.' %</td>
						<td> '.round($parti_grp_eco, 2).' %</td> 
						<td>'.money_format($fmt, $grp_eco_inv[$cont_grp_eco][$f]).'</td>  
						<td> '.$status.' </td>
					</tr>';
			
				$info_pdf_grpeco_inv[$fil_grp_eco][0] = utf8_encode($nom_grpeco[$f]);
				$info_pdf_grpeco_inv[$fil_grp_eco][1] = $max_inv_grpec.' %';
				$info_pdf_grpeco_inv[$fil_grp_eco][2] = round($parti_grp_eco, 2).' %';
				$info_pdf_grpeco_inv[$fil_grp_eco][3] = money_format($fmt, $grp_eco_inv[$cont_grp_eco][$f]);
				$info_pdf_grpeco_inv[$fil_grp_eco][4] = $status;
			
			// CONSTRUCION DE LA TABLA HTML DE LOS GRUPOS ECONOMICOS POR DEPOSITOS
				
				if($row_grpecoXfon['max_dep'] == ''){ 
					if($f == 0) $max_dep_grpec = 100;
					else $max_dep_grpec = 0;
				
				}else $max_dep_grpec = $row_grpecoXfon['max_dep'];
				
				$parti_grp_eco = ($grp_eco_dep[$cont_grp_eco][$f] * 100)/$total_fn_sf;
				
				if($max_dep_grpec >= $parti_grp_eco){ $status = 'Cumple'; $class_grpec = 'class="success"'; }
				else{ $status = 'No Cumple'; $class_grpec = 'class="danger"'; }
				
				$table_grp_eco_dep .= '
					<tr '.$class_grpec.'>
						<td >'.utf8_encode($nom_grpeco[$f]).'</td>
						<td> '.$max_dep_grpec.' %</td>
						<td> '.round($parti_grp_eco, 2).' %</td> 
						<td>'.money_format($fmt, $grp_eco_dep[$cont_grp_eco][$f]).'</td>  
						<td> '.$status.' </td>
					</tr>';
					
				$info_pdf_grpeco_dep[$fil_grp_eco][0] = utf8_encode($nom_grpeco[$f]);
				$info_pdf_grpeco_dep[$fil_grp_eco][1] = $max_dep_grpec.' %';
				$info_pdf_grpeco_dep[$fil_grp_eco][2] = round($parti_grp_eco, 2).' %';
				$info_pdf_grpeco_dep[$fil_grp_eco][3] = money_format($fmt, $grp_eco_dep[$cont_grp_eco][$f]);
				$info_pdf_grpeco_dep[$fil_grp_eco][4] = $status;
				
			// CONSTRUCION DE LA TABLA HTML DE LOS GRUPOS ECONOMICOS TOTAL
				
				if($row_grpecoXfon['max_tot'] == ''){
					if($f == 0) $max_tot_grpec = 100;
					else $max_tot_grpec = 0;
				}else $max_tot_grpec = $row_grpecoXfon['max_tot'];
				
				$grp_eco_tot = $grp_eco_dep[$cont_grp_eco][$f] + $grp_eco_inv[$cont_grp_eco][$f];
				
				$parti_grp_eco = ($grp_eco_tot * 100)/$total_fn_sf;
				
				if($max_tot_grpec >= $parti_grp_eco){ $status = 'Cumple'; $class_grpec = 'class="success"'; }
				else{ $status = 'No Cumple'; $class_grpec = 'class="danger"'; }
				
				$table_grp_eco_tot .= '
					<tr '.$class_grpec.'>
						<td> '.utf8_encode($nom_grpeco[$f]).' </td>
						<td> '.$max_tot_grpec.' %</td>
						<td> '.round($parti_grp_eco, 2).' % </td>
						<td> '.money_format($fmt, $grp_eco_tot).' </td>
						<td> '.$status.' </td>
					</tr>';
				
				$info_pdf_grpeco_tot[$fil_grp_eco][0] = utf8_encode($nom_grpeco[$f]);
				$info_pdf_grpeco_tot[$fil_grp_eco][1] = $max_tot_grpec.' %';
				$info_pdf_grpeco_tot[$fil_grp_eco][2] = round($parti_grp_eco, 2).' %';
				$info_pdf_grpeco_tot[$fil_grp_eco][3] = money_format($fmt, $grp_eco_tot);
				$info_pdf_grpeco_tot[$fil_grp_eco][4] = $status;
				
				++ $fil_grp_eco;
				//print_r($info_pdf_grpeco_tot);
			}
			
			++ $cont_grp_eco;
			
			$table_grp_eco_inv .= '</table>';
			$table_grp_eco_dep .= '</table>';
			$table_grp_eco_tot .= '</table>';
			
			$table_grp_eco = $table_grp_eco_inv.$table_grp_eco_dep.$table_grp_eco_tot;			
			
			if($cod_fon != 0){				
				for($i = 0; $i < count($nom_tot_cal); ++$i){
					
					$table_cal .= '
						<tr '.$class_cal[$i].'>
							<td colspan="2">'.$nom_tot_cal[$i].'</td>
							<td>'.$min_tot_cal[$i].' %</td>
							<td>'.$max_tot_cal[$i].' %</td> 
							<td>'.$val_cal[$i].'</td> 
							<td>'.$prom_cal[$i].' %</td>  
							<td>'.$cal_cal[$i].'</td>
						</tr>';

					$info_pdf[$fil_pdf][0] = '-';
					$info_pdf[$fil_pdf][1] = $nom_tot_cal[$i];
					$info_pdf[$fil_pdf][2] = $min_tot_cal[$i].' %';
					$info_pdf[$fil_pdf][3] = $max_tot_cal[$i].' %';
					$info_pdf[$fil_pdf][4] = $val_cal[$i];
					$info_pdf[$fil_pdf][5] = $prom_cal[$i].' %';
					$info_pdf[$fil_pdf][6] = $cal_cal[$i];
						
					++ $fil_pdf;
						
						//
				}
				
				$info_pdf = array_envia($info_pdf);
				
				$info_pdf_grpeco_inv = array_envia($info_pdf_grpeco_inv);
				$info_pdf_grpeco_dep = array_envia($info_pdf_grpeco_dep);
				$info_pdf_grpeco_tot = array_envia($info_pdf_grpeco_tot);
				
				$form = '
				<form action="pdf.php" method="post" target="_blank" enctype="multipart/form-data">
					<input type="hidden" value="'.$info_pdf.'" name="info_pdf">
					<input type="hidden" value="'.$info_pdf_prh.'" name="info_pdf_pr">
					<input type="hidden" value="'.$info_pdf_emi_inv.'" name="info_pdf_emi_inv">
					<input type="hidden" value="'.$info_pdf_emi_dep.'" name="info_pdf_emi_dep">
					
					<input type="hidden" value="'.$info_pdf_grpeco_inv.'" name="info_pdf_grpeco_inv">
					<input type="hidden" value="'.$info_pdf_grpeco_dep.'" name="info_pdf_grpeco_dep">
					<input type="hidden" value="'.$info_pdf_grpeco_tot.'" name="info_pdf_grpeco_tot">
					
					<input type="hidden" value="'.$Nombre_fondo.' - '.$cod_fondo.'" name="cab_pdf">
					<input type="hidden" value="Valor Total del Fondo: '.$total_fondo.'" name="val_fon">
					<input type="hidden" value="Fecha del Fondo: '.$fecha_fonds.'" name="fecha_fonds">
					<input type="hidden" value="'.$id_ent.'" name="id_ent"><br>
					<input type="submit" value="Generar PDF" class="btn btn-primary btn-ar">
				</form>';
				
				
				$header_fon .= $form;
				$table = $header_fon.$table_prom.$table;
				$table .= '';
				$table .= $table_cal;
				$table = $table.'</table>'.$table_grp_eco;
				
				$table_cal = '';
				$cont_cal = 0;
				
				$html = $html.$table.'</div>';
				
				$header_fon = '<h1>Resultados para '.utf8_decode($row_sql[7]).' - '.$row_sql[13].'</h1>';
				$header_fon .= '<h4> Valor Total del Fondo: '.money_format($fmt, $tot[$fil_calc]).'</h4>';
				$header_fon .= '<h4> Fecha del Fondo: '.$fecha_fonds.' </h4>';
				$header_fon .= '<a class="btn btn-primary btn-ar" data-toggle="modal" data-target="#modal_'.$row_sql[13].'">Emisores</a>';
				$header_fon .= '<table class="table">';
				
				$Nombre_fondo = utf8_decode($row_sql[7]);
				$cod_fondo = $row_sql[13];
				$total_fondo = money_format($fmt, $tot[$fil_calc]);
				
				$total_fn_sf =  $tot[$fil_calc];
				
				$info_pdf = array();
				$fil_pdf = 0;
				$cont = 1;
				$pos_prom_pon = 0;
				
			}else{			
				
				$header_fon = '<h1> Resultados para '.utf8_decode($row_sql[7]).' - '.$row_sql[13].' </h1>';
				$header_fon .= '<h4> Valor Total del Fondo: '.money_format($fmt, $tot[$fil_calc]).' </h4>';
				$header_fon .= '<h4> Fecha del Fondo: '.$fecha_fonds.' </h4>';
				$header_fon .= '<a class="btn btn-primary btn-ar" data-toggle="modal" data-target="#modal_'.$row_sql[13].'"> Emisores </a>';
				$header_fon .= '<table class="table">';
				
				$Nombre_fondo = utf8_decode($row_sql[7]);
				$cod_fondo = $row_sql[13];
				$total_fondo = money_format($fmt, $tot[$fil_calc]);
				
				$total_fn_sf =  $tot[$fil_calc];
				
				$info_pdf = array();
				$fil_pdf = 0;
				$cont = 1;
			}
			
			$table_prom = '
				<table class="table">
					<tr><td colspan="4" align="center"> <h4> Promedio Ponderado </h4> </td></tr>
					<tr class="warning"> 
						<th>Nombre</th> 
						<th>Máx</th> 
						<th>Promedio General</th> 
						<th>Resultado </th> 
					</tr>';
					
			$info_pdf[$fil_pdf][0] = '*';
			$info_pdf[$fil_pdf][1] = 'Promedio Ponderado';
	
			++$fil_pdf;
			
			$info_pdf[$fil_pdf][0] = 'No';
			$info_pdf[$fil_pdf][1] = 'Nombre';
			$info_pdf[$fil_pdf][2] = '';
			$info_pdf[$fil_pdf][3] = 'Máx';
			$info_pdf[$fil_pdf][4] = '';
			$info_pdf[$fil_pdf][5] = 'Promedio del Fondo';
			$info_pdf[$fil_pdf][6] = 'Resultado';
			++ $fil_pdf;
				
				$max_promedio = $row_sql[8];
				
				if($prom_pond_tot > $max_promedio){
					$calif = 'No Cumple'; 
					$class_promp = 'class="danger"'; 
				}else { 
					$calif = 'Cumple'; 
					$class_promp = 'class="success"'; 
				}
					
				$table_prom .= '
					<tr '.$class_promp.'>
						<td> Días Promedio Ponderado del Portafolio </td>
						<td> '.$max_promedio.' </td>
						<td> '.round($prom_pond_tot, 2).' </td>
						<td> '.$calif.' </td>
					</tr>
				</table>';
				
				$info_pdf[$fil_pdf][0] = '1';
				$info_pdf[$fil_pdf][1] = 'Días Promedio Ponderado del Portafolio';
				$info_pdf[$fil_pdf][2] = '';
				$info_pdf[$fil_pdf][3] = $max_promedio;
				$info_pdf[$fil_pdf][4] = '';
				$info_pdf[$fil_pdf][5] = round($prom_pond_tot, 2);
				$info_pdf[$fil_pdf][6] = $calif;
				
				++ $fil_pdf;
				
				$info_pdf[$fil_pdf][0] = '-';
				$info_pdf[$fil_pdf][1] = '-';
				$info_pdf[$fil_pdf][2] = '-';
				$info_pdf[$fil_pdf][3] = '-';
				$info_pdf[$fil_pdf][4] = '-';
				$info_pdf[$fil_pdf][5] = '-';
				$info_pdf[$fil_pdf][6] = '-';
				
				++ $fil_pdf;
				
			$prom_pond_tot = 0;
			$cod_fon = $row_sql[13];
			
			$btns_cab = $btns_cab.'<li class="'.$clas.'"> <a href="#FIC_'.$row_sql[13].'" data-toggle="tab" >'.$row_sql[13].'</a> </li>';
			
			$table_act = array('jo33_FIC_UTLR_total_activos_fon');
			$valC_act = array('id_fondos');
			$val_act = array($row_sql[6]);
			
			$sql_act = $consult -> sql('S', $table_act, $val_act, $valC_act, $valU);
			$row_act = mysql_num_rows($sql_act);
			
			if($row_act == 0){
				$max_inver = 0;
				$min_inver = 0;
				$max_depo = 0;
				$min_depo = 0;
				$max_tota = 0;
				$min_tota = 0;
			}else {
				$row_act = mysql_fetch_array($sql_act);
				$max_inver = $row_act['max_inver'];
				$min_inver = $row_act['min_inver'];
				$max_depo = $row_act['max_depo'];
				$min_depo = $row_act['min_depo'];
				$max_tota = $row_act['max_tot'];
				$min_tota = $row_act['min_tot'];
			}
			
			$tot_ac = $depo[$fil_calc] + $invers[$fil_calc];
			
			$VPN_inver = money_format($fmt,  $invers[$fil_calc]); 
			$VPN_depo = money_format($fmt, $depo[$fil_calc]);
			$VPN_totac = money_format($fmt, $tot_ac);
			
			$pro_inver = ( $invers[$fil_calc] / $tot[$fil_calc] ) *100;
			$pro_depo = ( $depo[$fil_calc] / $tot[$fil_calc] ) *100;
			$pro_totac = ( $tot_ac / $tot[$fil_calc] ) *100;
			
		// INVERSIONES
			if($pro_inver <= $max_inver && $pro_inver >= $min_inver){
				$cum_inver = 'Cumple';
				$class_in = 'class="success"';
			}else{
				$cum_inver = 'No Cumple';
				$class_in = 'class="danger"';
			}
			
		// DEPOSITOS
			if($pro_depo <= $max_depo && $pro_depo >= $min_depo){
				$cum_depo = 'Cumple';
				$class_de = 'class="success"';
			}else{
				$cum_depo = 'No Cumple';
				$class_de = 'class="danger"';
			}
			
		// TOTAL
			if($pro_totac > 100){ $pro_totac = 100; }
			
			
			
			$html = $html.'<div class="tab-pane '.$clas.'" id="FIC_'.$row_sql[13].'">';
			
			$header_fon .= '<tr><td colspan="7" align="center"> <h4> Composición de los Activos de los FIC </h4> </td></tr>';
			$header_fon .= '<tr class="warning"> <th>No</th> <th>Nombre</th> <th>Mín</th> <th>Máx</th> <th>VPN</th> <th>Participación</th> <th>Resultado </th> </tr>';
			
			//echo $tot_ac.' ---- '.$max_smlv.'<br>';
			
			if( $tot_ac >= $max_smlv){			
				$cum_totac = 'Cumple'; 
				$class_to = 'class="success"';
			}else{
				$cum_totac = 'No Cumple';
				$class_to = 'class="danger"';
			}

			
			$header_fon .= '
				<tr '.$class_in.'>
					<td>1</td> 
					<td>Portafolio de Inversiones</td>
					<td>'.$min_inver.' %</td>
					<td>'.$max_inver.' %</td>
					<td>'.$VPN_inver.'</td>
					<td>'.round($pro_inver, 2).' %</td>
					<td>'.$cum_inver.'</td>
				</tr>
				<tr '.$class_de.'> 
					<td>2</td> 
					<td>Depósitos Bancarios</td> 
					<td>'.$min_depo.' %</td> 
					<td>'.$max_depo.' %</td> 
					<td>'.$VPN_depo.'</td> 
					<td>'.round($pro_depo, 2).' %</td> 
					<td>'.$cum_depo.'</td> 
				</tr>
				<tr '.$class_to.'> 
					<td colspan="2">Total Composición de los Activos de los FIC</td> 
					<td>2600 - SMLV</td> 
					<td> - </td> 
					<td>'.$VPN_totac.'</td> 
					<td>'.round($pro_totac, 2).' %</td> 
					<td>'.$cum_totac.'</td> 
				</tr>';
				
			$header_fon .= '</table>';
			
			$table = '<table class="table">';
			
			$info_pdf[$fil_pdf][0] = '*';
			$info_pdf[$fil_pdf][1] = 'Composición de los Activos de los FIC';
			
			++$fil_pdf;
			
			$info_pdf[$fil_pdf][0] = 'No';
			$info_pdf[$fil_pdf][1] = 'Nombre';
			$info_pdf[$fil_pdf][2] = 'Mín';
			$info_pdf[$fil_pdf][3] = 'Máx';
			$info_pdf[$fil_pdf][4] = 'VPN';
			$info_pdf[$fil_pdf][5] = 'Participación';
			$info_pdf[$fil_pdf][6] = 'Resultado ';
			
			++$fil_pdf;
			
			$info_pdf[$fil_pdf][0] = '1';
			$info_pdf[$fil_pdf][1] = 'Portafolio de Inversiones';
			$info_pdf[$fil_pdf][2] = $min_inver.' %';
			$info_pdf[$fil_pdf][3] = $max_inver.' %';
			$info_pdf[$fil_pdf][4] = $VPN_inver;
			$info_pdf[$fil_pdf][5] = round($pro_inver, 2).' %';
			$info_pdf[$fil_pdf][6] = $cum_inver;
			
			++$fil_pdf;
			
			$info_pdf[$fil_pdf][0] = '2';
			$info_pdf[$fil_pdf][1] = 'Depósitos Bancarios';
			$info_pdf[$fil_pdf][2] = $min_depo.' %';
			$info_pdf[$fil_pdf][3] = $max_depo.' %';
			$info_pdf[$fil_pdf][4] = $VPN_depo;
			$info_pdf[$fil_pdf][5] = round($pro_depo, 2).' %';
			$info_pdf[$fil_pdf][6] = $cum_depo;
			
			++$fil_pdf;
			
			$info_pdf[$fil_pdf][0] = '-';
			$info_pdf[$fil_pdf][1] = 'Total Composición de los Activos de los FIC';
			$info_pdf[$fil_pdf][2] = $min_tota.' %';
			$info_pdf[$fil_pdf][3] = $max_tota.' %';
			$info_pdf[$fil_pdf][4] = $VPN_totac;
			$info_pdf[$fil_pdf][5] = round($pro_totac, 2).' %';
			$info_pdf[$fil_pdf][6] = $cum_totac;
			
			++$fil_pdf;
		}		
		
		// Agrupación de las calificaciones 
		
		$cal_aaa = $cal[$fil_calc][1] + $cal[$fil_calc][2] + $cal[$fil_calc][3] + $cal[$fil_calc][4] + $cal[$fil_calc][5] + $cal[$fil_calc][6] + $cal[$fil_calc][7] + $cal[$fil_calc][8] + $cal[$fil_calc][9] + $cal[$fil_calc][10];
		$cal_aa_pos = $cal[$fil_calc][11] + $cal[$fil_calc][12] + $cal[$fil_calc][13] + $cal[$fil_calc][14] + $cal[$fil_calc][15] + $cal[$fil_calc][16] + $cal[$fil_calc][17]; 
		$cal_aa = $cal[$fil_calc][18] + $cal[$fil_calc][19] + $cal[$fil_calc][20] + $cal[$fil_calc][21] + $cal[$fil_calc][22];
		$cal_aa_neg = $cal[$fil_calc][23] + $cal[$fil_calc][24] + $cal[$fil_calc][25] + $cal[$fil_calc][26] + $cal[$fil_calc][27]; 
		$cal_a_pos = $cal[$fil_calc][28] + $cal[$fil_calc][29] + $cal[$fil_calc][30] + $cal[$fil_calc][31] + $cal[$fil_calc][32] + $cal[$fil_calc][33] + $cal[$fil_calc][34];
		$cal_a = $cal[$fil_calc][35] + $cal[$fil_calc][36] + $cal[$fil_calc][37] + $cal[$fil_calc][38];
		
		// Totalisación calificaciones
		
		$tot_calificaciones[0] = $cal_aaa + $cal_aa_pos;
		$tot_calificaciones[1] = $cal_aaa + $cal_aa_pos + $cal_aa;
		$tot_calificaciones[2] = $cal_aaa + $cal_aa_pos + $cal_aa + $cal_aa_neg;
		$tot_calificaciones[3] = $cal_aaa + $cal_aa_pos + $cal_aa + $cal_aa_neg + $cal_a_pos;
		$tot_calificaciones[4] = $cal_aaa + $cal_aa_pos + $cal_aa + $cal_aa_neg + $cal_a_pos + $cal_a;
		
		if($row_sql[41] == 'tot'){
			
			if($id_tot != $row_sql[36]){
				
				$id_tot = $row_sql[36];
				$pro_total = ($valor_total / $tot[$fil_calc]) * 100;
				
				if($pro_total >= $min_ant && $pro_total <= $max_ant) { $cal_tot = 'Cumple'; $class_to = 'class="success"'; }
				else { $cal_tot = 'No Cumple'; $class_to = 'class="danger"'; }
				
				$table = $table.'<tr '.$class_to.'><td colspan="2">'.utf8_encode($totales[$Nom_ant]).'</td><td>'.$min_ant.' %</td><td>'.$max_ant.' %</td> <td>'.money_format($fmt, $valor_total).'</td> <td>'.round($pro_total, 2).' %</td>  <td>'.$cal_tot.'</td></tr>';
				
				if(	$totales[$Nom_ant] == 'Total Bonos' ||
					$totales[$Nom_ant] == 'Total Nación' ||
					$totales[$Nom_ant] == 'Total Otros Renta Fija' ||
					$totales[$Nom_ant] == 'Total Acciones' ||
					$totales[$Nom_ant] == 'Total Participación en Fondos de Inversión Colectiva ' ||
					$totales[$Nom_ant] == 'Total Derechos Económicos' ||
					$totales[$Nom_ant] == 'Total Total Otros Renta Variable'){
					
					$tot_com += $valor_total;
				}
				
				$info_pdf[$fil_pdf][0] = '-';
				$info_pdf[$fil_pdf][1] = utf8_encode($totales[$Nom_ant]);
				$info_pdf[$fil_pdf][2] = $min_ant.' %';
				$info_pdf[$fil_pdf][3] = $max_ant.' %';
				$info_pdf[$fil_pdf][4] = money_format($fmt, $valor_total);
				$info_pdf[$fil_pdf][5] = round($pro_total, 2).' %';
				$info_pdf[$fil_pdf][6] = $cal_tot;
				
				++$fil_pdf;
				
				$valor_total = 0;
				$cont = 1;
			}
			
			$Nom_ant = $row_sql[44];
			$min_ant = $row_sql[40];
			$max_ant = $row_sql[39];		
		
			if($id_comp != $row_sql[23]){
			
				$id_comp = $row_sql[23];
				
				if($nom_tot_com != ''){
					$pro_com = ($tot_com / $tot[$fil_calc]) * 100;
					
					if($pro_com <= $max_tot_com && $pro_com >= $min_tot_com){ $cal_tot = 'Cumple'; $class_to = 'class="success"'; }
					else { $cal_tot = 'No Cumple'; $class_to = 'class="danger"'; }
					
					$table_com .= '<tr '.$class_to.'><td colspan="2">'.$nom_tot_com.'</td><td>'.$min_tot_com.' %</td><td>'.$max_tot_com.' %</td> <td>'.money_format($fmt, $tot_com).'</td> <td>'.round($pro_com, 2).' %</td>  <td>'.$cal_tot.'</td></tr>';
					
					$info_pdf[$fil_pdf][0] = '-';
					$info_pdf[$fil_pdf][1] = $nom_tot_com;
					$info_pdf[$fil_pdf][2] = $min_tot_com.' %';
					$info_pdf[$fil_pdf][3] = $max_tot_com.' %';
					$info_pdf[$fil_pdf][4] = money_format($fmt, $tot_com);
					$info_pdf[$fil_pdf][5] = round($pro_com, 2).' %';
					$info_pdf[$fil_pdf][6] = $cal_tot;
					
					++ $fil_pdf;
					
					$tot_com = 0;
					$pro_com = 0;
				}
				$table .= $table_com; 
				
				$table .= '<tr><td colspan="7" align="center"><h4>'.utf8_encode($composicion[$row_sql[23]]).'</h4></td></tr>';
				$table .= '<tr class="warning"> <th>No</th> <th>Nombre</th> <th>Mín</th> <th>Máx</th> <th>VPN</th> <th>Participación</th> <th>Resultado </th> </tr>';
				++ $conteo_fon;
				$i = 1;
				
				$table_com = '';
				$nom_tot_com = '';
				$com = 0;
				
				$info_pdf[$fil_pdf][0] = '*';
				$info_pdf[$fil_pdf][1] = utf8_encode($composicion[$row_sql[23]]);
				
				++$fil_pdf;
				
				$info_pdf[$fil_pdf][0] = 'No';
				$info_pdf[$fil_pdf][1] = 'Nombre';
				$info_pdf[$fil_pdf][2] = 'Mín';
				$info_pdf[$fil_pdf][3] = 'Máx';
				$info_pdf[$fil_pdf][4] = 'VPN';
				$info_pdf[$fil_pdf][5] = 'Participación';
				$info_pdf[$fil_pdf][6] = 'Resultado';
				
				++$fil_pdf;
			}
			
			if($id_comp <= 12 && $id_comp >= 9 || $id_comp == 18 || $id_comp == 17) $comp = $fondo[0];
			else $comp = $fondo[$id_comp];

			if($comp[$fil_calc][$row_sql[33]] == NULL) $comp[$fil_calc][$i] = 0;
			
			$pro_actual = ($comp[$fil_calc][$row_sql[33]] / $tot[$fil_calc]) * 100;
			$valor_actual = ($tot[$fil_calc] * $pro_actual) / 100;
			
			$valor_total = $valor_total + $valor_actual;

			if($pro_actual != 0){
				
				if($row_sql[26] != 0 || $row_sql[25] != 0){
					
					if($pro_actual <= $row_sql[25] && $pro_actual >= $row_sql[26]){
					
						$table = $table.'<tr class="success"> <td>'.$cont.'</td> <td>'.utf8_encode($row_sql[32]).' </td> <td>'.$row_sql[26].' %</td> <td>'.$row_sql[25].' %</td> <td>'.money_format($fmt, $valor_actual).'</td> <td>'.round($pro_actual, 2).' %</td> <td>Cumple</td> </tr>';

						$info_pdf[$fil_pdf][0] = $cont;
						$info_pdf[$fil_pdf][1] = utf8_encode($row_sql[32]);
						$info_pdf[$fil_pdf][2] = $row_sql[26].' %';
						$info_pdf[$fil_pdf][3] = $row_sql[25].' %';
						$info_pdf[$fil_pdf][4] = money_format($fmt, $valor_actual);
						$info_pdf[$fil_pdf][5] = round($pro_actual, 2).' %';
						$info_pdf[$fil_pdf][6] = 'Cumple';
						
						++ $fil_pdf;
						
					}else {
					
						$table = $table.'<tr class="danger"> <td>'.$cont.'</td> <td>'.utf8_encode($row_sql[32]).'</td> <td>'.$row_sql[26].' %</td> <td>'.$row_sql[25].' %</td> <td>'.money_format($fmt, $valor_actual).'</td> <td>'.round($pro_actual, 2).' %</td> <td>No - Cumple</td></tr>';
						
						$info_pdf[$fil_pdf][0] = $cont;
						$info_pdf[$fil_pdf][1] = utf8_encode($row_sql[32]);
						$info_pdf[$fil_pdf][2] = $row_sql[26].' %';
						$info_pdf[$fil_pdf][3] = $row_sql[25].' %';
						$info_pdf[$fil_pdf][4] = money_format($fmt, $valor_actual);
						$info_pdf[$fil_pdf][5] = round($pro_actual, 2).' %';
						$info_pdf[$fil_pdf][6] = 'No Cumple';
						
						++$fil_pdf;
					}
					$pro_actual = 0;
					
				}else if( $pro_actual == 0){
					
					$table = $table.'<tr class="success"> <td>'.$cont.'</td> <td>'.utf8_encode($row_sql[32]).' </td> <td>'.$row_sql[26].' %</td> <td>'.$row_sql[25].' %</td> <td>'.money_format($fmt, $valor_actual).'</td> <td>'.round($pro_actual, 2).' %</td> <td>Cumple</td> </tr>';
					
					$info_pdf[$fil_pdf][0] = $cont;
					$info_pdf[$fil_pdf][1] = utf8_encode($row_sql[32]);
					$info_pdf[$fil_pdf][2] = $row_sql[26].' %';
					$info_pdf[$fil_pdf][3] = $row_sql[25].' %';
					$info_pdf[$fil_pdf][4] = money_format($fmt, $valor_actual);
					$info_pdf[$fil_pdf][5] = round($pro_actual, 2).' %';
					$info_pdf[$fil_pdf][6] = 'Cumple';
					
					++$fil_pdf;
					
				}else{
				
					$table = $table.'
						<tr class="danger"> 
							<td> '.$cont.' </td> 
							<td> '.utf8_encode($row_sql[32]).' </td> 
							<td> '.$row_sql[26].' % </td> 
							<td> '.$row_sql[25].' % </td> 
							<td> '.money_format($fmt, $valor_actual).' </td> 
							<td> '.round($pro_actual, 2).' % </td> 
							<td> No Cumple </td>
						</tr>';
					
					$info_pdf[$fil_pdf][0] = $cont;
					$info_pdf[$fil_pdf][1] = utf8_encode($row_sql[32]);
					$info_pdf[$fil_pdf][2] = $row_sql[26].' %';
					$info_pdf[$fil_pdf][3] = $row_sql[25].' %';
					$info_pdf[$fil_pdf][4] = money_format($fmt, $valor_actual);
					$info_pdf[$fil_pdf][5] = round($pro_actual, 2).' %';
					$info_pdf[$fil_pdf][6] = 'No Cumple';
					
					++$fil_pdf;
				}
				
			
			
			}else if($pro_actual == 0 && $row_sql[26] == 0){
				
					$table = $table.'<tr class="success"> <td>'.$cont.'</td> <td>'.utf8_encode($row_sql[32]).'</td> <td>'.$row_sql[26].' %</td> <td>'.$row_sql[25].' %</td> <td>'.money_format($fmt, $valor_actual).'</td> <td>'.round($pro_actual, 2).' %</td> <td>Cumple</td> </tr>';
					
					$info_pdf[$fil_pdf][0] = $cont;
					$info_pdf[$fil_pdf][1] = utf8_encode($row_sql[32]);
					$info_pdf[$fil_pdf][2] = $row_sql[26].' %';
					$info_pdf[$fil_pdf][3] = $row_sql[25].' %';
					$info_pdf[$fil_pdf][4] = money_format($fmt, $valor_actual);
					$info_pdf[$fil_pdf][5] = round($pro_actual, 2).' %';
					$info_pdf[$fil_pdf][6] = 'Cumple';
					
					++$fil_pdf;
					
				}else {
				
					$table = $table.'<tr class="danger"> <td>'.$cont.'</td> <td>'.utf8_encode($row_sql[32]).'</td> <td>'.$row_sql[26].' %</td> <td>'.$row_sql[25].' %</td> <td>'.money_format($fmt, $valor_actual).'</td> <td>'.round($pro_actual, 2).' %</td> <td>No Cumple</td></tr>';
					
					$info_pdf[$fil_pdf][0] = $cont;
					$info_pdf[$fil_pdf][1] = utf8_encode($row_sql[32]);
					$info_pdf[$fil_pdf][2] = $row_sql[26].' %';
					$info_pdf[$fil_pdf][3] = $row_sql[25].' %';
					$info_pdf[$fil_pdf][4] = money_format($fmt, $valor_actual);
					$info_pdf[$fil_pdf][5] = round($pro_actual, 2).' %';
					$info_pdf[$fil_pdf][6] = 'No Cumple';
					
					++ $fil_pdf;
				}
				
			$valor_actual = 0;
			
			++ $cont;
			++ $i;
		}
		
		else if($row_sql[41] == 'com'){
			if( $id_tot_com != $row_sql[36] ){
			
				// FALTA CALCULAR EL VALOR Y EL PORCENTAJE
				if($com < 1){					
					$nom_tot_com = utf8_encode($totales[$row_sql[44]]);
					$min_tot_com = $row_sql[40];
					$max_tot_com = $row_sql[39];
					
					$id_tot_com = $row_sql[36];
					++ $com;
				}
				
			}else $id_tot_com = $row_sql[36];
		}
		
		else if($row_sql[41] == 'cal'){
			if( $id_tot_cal != $row_sql[36] && $cont_cal < 5){
				
				$pro_actual = ( $tot_calificaciones[$cont_cal] / $tot[$fil_calc] ) * 100;
				$valor_total_cal = $tot_calificaciones[$cont_cal];
				
				if($pro_actual >= $row_sql[40] && $pro_actual <= $row_sql[39]){ $cal_tot = 'Cumple'; $class_to = 'class="success"'; }
				else { $cal_tot = 'No Cumple'; $class_to = 'class="danger"'; }
				
				$nom_tot_cal[$cont_cal] = utf8_encode($totales[$row_sql[44]]);
				$min_tot_cal[$cont_cal] = $row_sql[40];
				$max_tot_cal[$cont_cal] = $row_sql[39];
				$val_cal[$cont_cal] = money_format($fmt, $valor_total_cal);
				$prom_cal[$cont_cal] = round($pro_actual, 2);
				$cal_cal[$cont_cal] = $cal_tot;
				$class_cal[$cont_cal] = $class_to;				
				$id_tot_cal = $row_sql[36];
				
				++ $cont_cal;
				
				$tot_calificaciones = array();
			}
		}
	}
	
	$btns_cab = $btns_cab.'</ul>';
	
	for($i = 0; $i < count($nom_tot_cal); ++$i){
					
		$table_cal .= '
			<tr '.$class_cal[$i].'>
				<td colspan="2">'.$nom_tot_cal[$i].'</td>
				<td>'.$min_tot_cal[$i].' %</td>
				<td>'.$max_tot_cal[$i].' %</td> 
				<td>'.$val_cal[$i].'</td> 
				<td>'.$prom_cal[$i].' %</td>  
				<td>'.$cal_cal[$i].'</td>
			</tr>';

			$info_pdf[$fil_pdf][0] = '-';
			$info_pdf[$fil_pdf][1] = $nom_tot_cal[$i];
			$info_pdf[$fil_pdf][2] = $min_tot_cal[$i].' %';
			$info_pdf[$fil_pdf][3] = $max_tot_cal[$i].' %';
			$info_pdf[$fil_pdf][4] = $val_cal[$i];
			$info_pdf[$fil_pdf][5] = $prom_cal[$i].' %';
			$info_pdf[$fil_pdf][6] = $cal_cal[$i];
				
			++ $fil_pdf;
	}
	
	$table_prom = '
	<table class="table">
		<tr><td colspan="4" align="center"> <h4> Promedio Ponderado </h4> </td></tr>
		<tr class="warning">
			<th>Nombre</th>
			<th>Máx</th>
			<th>Promedio General</th>
			<th>Resultado </th>
		</tr>';
		
	/*$info_pdf[$fil_pdf][0] = 'No';
	$info_pdf[$fil_pdf][1] = 'Nombre';
	$info_pdf[$fil_pdf][2] = '';
	$info_pdf[$fil_pdf][3] = ' Máx';
	$info_pdf[$fil_pdf][4] = '';
	$info_pdf[$fil_pdf][5] = 'Promedio del Fondo';
	$info_pdf[$fil_pdf][6] = 'Resultado';
	
	++ $fil_pdf;*/
	
	if($cod_fondo == 9001651) $max_promedio = 7300;
	else $max_promedio == 1825;
	
	if($ult_prom_pond > $max_promedio){
		$calif = 'No Cumple';
		$class_promp = 'class="danger"';
	}else {
		$calif = 'Cumple';
		$class_promp = 'class="success"';
	}

	$table_prom .= '
		<tr '.$class_promp.'>
			<td> Días Promedio Ponderado del Portafolio </td>
			<td> '.$max_promedio.' </td>
			<td> '.round($ult_prom_pond, 2).' </td>
			<td> '.$calif.' </td>
		</tr>
	</table>';
	
	/*$info_pdf[$fil_pdf][0] = '1';
	$info_pdf[$fil_pdf][1] = 'Días Promedio Ponderado del Portafolio';
	$info_pdf[$fil_pdf][2] = '';
	$info_pdf[$fil_pdf][3] = $max_promedio;
	$info_pdf[$fil_pdf][4] = '';
	$info_pdf[$fil_pdf][5] = round($ult_prom_pond, 2);
	$info_pdf[$fil_pdf][6] = $calif;
	++ $fil_pdf;*/
	
	/*$info_pdf[$fil_pdf][0] = '-';
	$info_pdf[$fil_pdf][1] = '-';
	$info_pdf[$fil_pdf][2] = '-';
	$info_pdf[$fil_pdf][3] = '-';
	$info_pdf[$fil_pdf][4] = '-';
	$info_pdf[$fil_pdf][5] = '-';
	$info_pdf[$fil_pdf][6] = '-';
	++ $fil_pdf;*/
	
	++ $kb;
	++ $fil_calc;
	
	$info_pdf = array_envia($info_pdf);
	$info_pdf_prh = array_envia($proh_pdf[$fil_calc]);
	$info_pdf_emi_inv = array_envia($emisores_pdf_inv[$kb]);
	$info_pdf_emi_dep = array_envia($emisores_pdf_dep[$kb]);
	
	$info_pdf_grpeco_inv = array_envia($inf_grpeco_inv_ult_fic);
	$info_pdf_grpeco_dep = array_envia($inf_grpeco_dep_ult_fic);
	$info_pdf_grpeco_tot = array_envia($inf_grpeco_tot_ult_fic);
	
	//print_r($inf_grpeco_inv_ult_fic);
	
	$form = '
		<form action="pdf.php" method="post" target="_blank" enctype="multipart/form-data">
			<input type="hidden" value="'.$info_pdf.'" name="info_pdf">
			<input type="hidden" value="'.$info_pdf_prh.'" name="info_pdf_pr">
			<input type="hidden" value="'.$info_pdf_emi_inv.'" name="info_pdf_emi_inv">
			<input type="hidden" value="'.$info_pdf_emi_dep.'" name="info_pdf_emi_dep">
				
			<input type="hidden" value="'.$info_pdf_grpeco_inv.'" name="info_pdf_grpeco_inv">
			<input type="hidden" value="'.$info_pdf_grpeco_dep.'" name="info_pdf_grpeco_dep">
			<input type="hidden" value="'.$info_pdf_grpeco_tot.'" name="info_pdf_grpeco_tot">
			
			<input type="hidden" value="'.$Nombre_fondo.' - '.$cod_fondo.'" name="cab_pdf">
			<input type="hidden" value="Valor Total del Fondo: '.$total_fondo.'" name="val_fon">
			<input type="hidden" value="Fecha del Fondo: '.$fecha_fonds.'" name="fecha_fonds">
			<input type="hidden" value="'.$id_ent.'" name="id_ent"><br>
			<input type="submit" value="Generar PDF" class="btn btn-primary btn-ar">
		</form>';
	
	$header_fon .= $form;
	
	$table = $header_fon.$table_prom.$table.$table_cal;
	
	$table = $table.'</table>';	
	
	$html = $div.$btns_cab.$html.$table.'</div></div></div></div></div>';
	
	$analisis = array();
	$analisis[0] = $html;
	$analisis[1] = $table;
	
	return $analisis;
	
}

function array_envia($array) {

    $tmp = serialize($array);
    $tmp = urlencode($tmp);

    return $tmp;
}


/*
	-*******************************************-
	-*******************************************-
	
	Funcion emisores
		- Crea nuevos emisores.
		- identifica si el emisor es nuevo 
		- solicita verificación 
		
	Recibe dos variables las cuales son:
		$ruta es la ruta del archivo cvs que se esta analisando.
		$id_ent es el id de la entidad ala cual pertenece el archivo.
*
*
*
*
*
*/


function emisores( $ruta, $id_ent, $tipo_procedimiento ){
	
	$nit = array();
	$name = array();
	$arr_fon = array();
	$nit_emisor = 0;
	$fil = 0;
	$cod_fon = 0;
	$num_fd = 0;
	$param = 0;
	$consult = new mysql();
	
	setlocale(LC_MONETARY, 'es_CO');
	$fmt = '%i';
	
	$emis = array();
	$cant_new_emis = 0;
	
	$table_emis = array('jo33_FIC_UTLR_emisores');
	$valC_emis = array('active', 'trash');
	$val_emis = array(1,1);
	$sql_emis = $consult -> sql('S', $table_emis, $val_emis, $valC_emis, $valU);
	
	$fil_nit = 0;
	
	while($row_emis = mysql_fetch_array($sql_emis)){ 
		$emis[$row_emis['nit']] = $row_emis['nombre']; 
		$grp_eco_emis[$row_emis['nit']] = $row_emis['id_grupo_eco'];
	}
	
	if (($fichero = fopen($ruta, "r")) !== FALSE) {
		while (($datos = fgetcsv($fichero, 1000, ";")) !== FALSE) {
			
			$exis = 0;
			if($datos[0] != $cod_fon){
				if($cod_fon != 0){
					
					$nit_X[$num_fd] = $nit;
					$name_X[$num_fd] = $name;
					$grp_eco_X[$num_fd] = $grp_eco;
					$vpn_emisor_inverX[$num_fd] = $vpn_emisor_inver;
					$vpn_emisor_depoX[$num_fd] = $vpn_emisor_depo;
					$tot_fnX[$num_fd] = $total;
					$nit = array();
					$name = array();
					$grp_eco = array();
					$vpn_emisor_inver = array();
					$vpn_emisor_depo = array();
					
					$num_fd ++;
					$fil = 0;
					$total = 0;
					$arr_fon[$num_fd] = $datos[0];
					
				}else{	$arr_fon[$num_fd] = $datos[0];	}
				
				$cod_fon = $datos[0];
				
			}
		
			if($datos[5] != $nit_emisor){
			
				if(count($nit) == 0){
					
					$name[$fil] = $emis[$datos[5]];
					$grp_eco[$fil] = $grp_eco_emis[$datos[5]];
					$nit[$fil] = $datos[5];
					
					if($datos[1] == 1) $vpn_emisor_inver[$fil] = $datos[14];
					elseif($datos[1] == 2) $vpn_emisor_depo[$fil] = $datos[14];
					
					++ $fil;
					
				}else{
					
					for($i=0; $i<=count($nit); ++$i){
						if($datos[5] == $nit[$i]){
							
							if($datos[1] == 1) $vpn_emisor_inver[$i] = $vpn_emisor_inver[$i] + $datos[14];
							elseif($datos[1] == 2) $vpn_emisor_depo[$i] = $vpn_emisor_depo[$i] + $datos[14];
							
							$exis ++;
						}
					}
					
					if($exis == 0){
						
						$name[$fil] = $emis[$datos[5]];
						$grp_eco[$fil] = $grp_eco_emis[$datos[5]];
						
						if($name[$fil] == ''){
						
							$new_emis[$datos[5]] = $datos[4];
							
							for($k=0; $k<=count($new_emis_nit[$num_fd]); ++$k){
								if($new_emis_nit[$num_fd][$k] == $datos[5]) {
									$ya ++;
								}
							}
							
							if($ya == 0){
								
								$new_emis_nit[$num_fd][$cant_new_emis] = $datos[5];
								$cant_new_emis ++;
								
								if($datos[1] == 1) $new_vpn_emisor_inver[$datos[5]] = $new_vpn_emisor_inver[$datos[5]] + $datos[14];
								if($datos[1] == 2) $new_vpn_emisor_depo[$datos[5]] = $new_vpn_emisor_depo[$datos[5]] + $datos[14];								
								
							}else $ya = 0;
						}else{
							
							$nit[$fil] = $datos[5];
							if($datos[1] == 1) $vpn_emisor_inver[$fil] = $vpn_emisor_inver[$fil] + $datos[14];
							if($datos[1] == 2) $vpn_emisor_depo[$fil] = $vpn_emisor_depo[$fil] + $datos[14]; 
							
							++ $fil;
						}
					}
				}
			
			}else{
			
				if($datos[1] == 1) $vpn_emisor_inver[$fil] = $vpn_emisor_inver[$fil] + $datos[14];
				if($datos[1] == 2) $vpn_emisor_depo[$fil] = $vpn_emisor_depo[$fil] + $datos[14];
			}
			
			$total = $total + $datos[14];			
		}
		
		$nit_X[$num_fd] = $nit;
		$name_X[$num_fd] = $name;
		$grp_eco_X[$num_fd] = $grp_eco;
		$vpn_emisor_inverX[$num_fd] = $vpn_emisor_inver;
		$vpn_emisor_depoX[$num_fd] = $vpn_emisor_depo;
		$tot_fnX[$num_fd] = $total;
			
		for($i=0; $i<=count($nit_X); ++$i){
			
		/*
			** EMISORES AUN NO REGISTRADOS QUE APLICAN PARA LAS INVERSIONES DEL FIC
		*/
			//print_r(count($new_emis_nit[$i]));
			//print_r($new_emis_nit);
			//echo'<br>';
			
			if(count($new_emis_nit[$i]) != 0){
			
				for($t=0; $t<=count($new_emis_nit[$i]); ++$t){
					
					if($new_vpn_emisor_inver[$new_emis_nit[$i][$t]] != ''){
						
						$table_cab_inver = '
						<div id="msj_inv"></div>
						<h1>Emisores Aún No Registrados que aplican a las Inversiones</h1>
						<table class="table">
							<tr> 
								<th>NIT</th> 
								<th>NOMBRE EMISOR</th>
								<!--th>MAXIMO DEL EMISOR</th-->
								<th>VALOR</th>
								<th>PARTICIPACIÓN</th>
								<th>GUARDAR</th>
							</tr>';
						
						$table_falt_inver = $table_falt_inver.'
						<tr> 
							<td><input id="nit_new_emi_'.$new_emis_nit[$i][$t].'" type="text" class="form-control" value="'.$new_emis_nit[$i][$t].'"></td>
							<td><input id="nom_new_emi_'.$new_emis_nit[$i][$t].'" type="text" class="form-control" value="'.$new_emis[$new_emis_nit[$i][$t]].'"></td>
							<!--td><input type="text" class="form-control" placeholder="0%"></td-->
							<td>'.money_format($fmt, $new_vpn_emisor_inver[$new_emis_nit[$i][$t]]).'</td>
							<td></td>
							<td><a href="javascript:new_emi('.$new_emis_nit[$i][$t].');" style="margin-left: 30px;" class="btn btn-info"><i class="fa fa-save"></i></a></td> 
						</tr>';
					
					}
					
					if($new_vpn_emisor_depo[$new_emis_nit[$i][$t]] != ''){
						
						$table_cab_depo = '
						<h1>Emisores Aún No Registrados que aplican a los Depositos</h1>
						<table class="table">
							<tr> 
								<th>NIT</th> 
								<th>NOMBRE EMISOR</th> 
								<!--th>MAXIMO DEL EMISOR</th--> 
								<th>VALOR</th> 
								<th>PARTICIPACIÓN</th> 
								<th>GUARDAR</th>
							</tr>';
						
						$table_falt_depo = $table_falt_depo.'
						<tr> 
							<td><input id="nit_new_emi_'.$new_emis_nit[$i][$t].'" type="text" class="form-control" value="'.$new_emis_nit[$i][$t].'"></td>
							<td><input id="nom_new_emi_'.$new_emis_nit[$i][$t].'" type="text" class="form-control" value="'.$new_emis[$new_emis_nit[$i][$t]].'"></td>
							<!--td><input id="max_new_emi_'.$new_emis_nit[$i][$t].'" type="text" class="form-control" value="0%"></td-->
							<td>'.money_format($fmt, $new_vpn_emisor_depo[$new_emis_nit[$i][$t]]).'</td>
							<td></td>
							<td><a href="javascript:new_emi('.$new_emis_nit[$i][$t].');" style="margin-left: 30px;" class="btn btn-info"><i class="fa fa-save"></i></a></td> 
						</tr>';
					}
				}
				
				$table_falt_inver = $table_cab_inver.$table_falt_inver.'</table>';
				$table_falt_depo = $table_cab_depo.$table_falt_depo.'</table>';
				
				$table_cab_depo = '';
				$table_cab_inver = '';
				
			}
			
			$faltantes = $table_falt_inver.$table_falt_depo;
			
			$table_falt_inver = '';
			$table_falt_depo = '';
			
		/*
			** FIN EMISORES AUN NO REGISTRADOS
		*/
		
			$nit_fn = $nit_X[$i];
			$name_fn = $name_X[$i];
			$grp_eco_fn = $grp_eco_X[$i];
			$inver_fn = $vpn_emisor_inverX[$i];
			$depo_fn = $vpn_emisor_depoX[$i];
			$tot_fn = $tot_fnX[$i];
			
			$html = $html.'
			<div id="modal_'.$arr_fon[$i].'" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">';
		
		//print_r($grp_eco_fn);
			
		/*
			** EMISORES POR INVERSIONES
		*/
		
			$cont_inver = 1;
			
			$pdf_emis_inver[0][0] = 'Nombre Emisor';
			$pdf_emis_inver[0][1] = 'Máx';
			$pdf_emis_inver[0][2] = 'Valor';
			$pdf_emis_inver[0][3] = 'Participación';
			$pdf_emis_inver[0][4] = 'Resultado';
			
			$contenido_pop_inv = "<div class='alert alert-danger' role='alert'>Indique el maximo para la selección de emisores. <br> <input id='inp_emi_$arr_fon[$i]' class='form-control' type='text' placeholder='0%'> <br> <a href='javascript:guarda_nemis($arr_fon[$i], 1)' class='btn btn-primary btn-ar'><i class='fa fa-floppy-o'></i>Guardar</a> <div id='div_msj_emi'></div> </div>";
			
			$script .= '<script> 
			$("#emis_inv'.$i.'").change(function () {
				if ($(this).is(":checked")) {
					//$("input[type=checkbox]").prop("checked", true); //todos los check
					$("#sel_todo_inv'.$i.' input[type=checkbox]").prop("checked", true); //solo los del objeto #diasHabilitados
				} else {
					//$("input[type=checkbox]").prop("checked", false);//todos los check
					$("#sel_todo_inv'.$i.' input[type=checkbox]").prop("checked", false);//solo los del objeto #diasHabilitados
				}
			});
			</script>
			
			<script>
				jQuery(function() {
					jQuery("#emis_inv'.$i.'").popover({
						html : true,
						title: "Maximo Para Emisores Por Inversiones",
						content: "'.$contenido_pop_inv.'",
						delay: { show: 500, hide: 100 }
					});
				});
			</script>';
		
		/*	EMISORES POR DEPOSITOS	*/
		
			$cont_dep = 1;
			
			$pdf_emis_depo[0][0] = 'Nombre Emisor';
			$pdf_emis_depo[0][1] = 'Máx';
			$pdf_emis_depo[0][2] = 'Valor';
			$pdf_emis_depo[0][3] = 'Participación';
			$pdf_emis_depo[0][4] = 'Resultado';
			
			$contenido_pop_dep = "<div class='alert alert-danger' role='alert'>Indique el maximo para la selección de emisores. <br> <input class='form-control' id='inp_emi_$arr_fon[$i]' type='text' placeholder='0%'> <br> <a href='javascript:guarda_nemis($arr_fon[$i],2)' class='btn btn-primary btn-ar'><i class='fa fa-floppy-o'></i>Guardar</a></div>";
			
			$script .= '<script>
			$("#emis_dep'.$i.'").change(function () {
				if ($(this).is(":checked")) {
					//$("input[type=checkbox]").prop("checked", true); //todos los check
					$("#sel_todo_dep'.$i.' input[type=checkbox]").prop("checked", true); //solo los del objeto #diasHabilitados
				} else {
					//$("input[type=checkbox]").prop("checked", false);//todos los check
					$("#sel_todo_dep'.$i.' input[type=checkbox]").prop("checked", false);//solo los del objeto #sel_todo_dep
				}
			});</script>
			
			<script>
				jQuery(function() {
					jQuery("#emis_dep'.$i.'").popover({
						html : true,
						title: "Maximo Para Emisores Por Depositos",
						content: "'.$contenido_pop_dep.'",
						delay: { show: 500, hide: 100 }
					});
				});
			</script>';
			
			for($j=0; $j<count($nit_fn); ++$j){
				
				$table_param = array('jo33_FIC_UTLR_fondos','jo33_FIC_UTLR_emisores_x_fondos','jo33_FIC_UTLR_emisores');
				$valC_param = array(
					'jo33_FIC_UTLR_fondos.codigo', 
					'jo33_FIC_UTLR_emisores.nit', 
					'jo33_FIC_UTLR_emisores_x_fondos.id_emisores', 
					'jo33_FIC_UTLR_emisores_x_fondos.id_fondos',
					'jo33_FIC_UTLR_fondos.active',
					'jo33_FIC_UTLR_fondos.trash');					
				$val_param = array(
					$arr_fon[$i], 
					$nit_fn[$j], 
					'jo33_FIC_UTLR_emisores.id', 
					'jo33_FIC_UTLR_fondos.id',
					1,1);
				
				$sql_param = $consult -> sql('S', $table_param, $val_param, $valC_param, $valU);
				$row_param = mysql_fetch_array($sql_param);
				
				$param = $row_param[10];
				$param = $param+0;
				
				if($inver_fn[$j] != ''){
					
					$pro_emis = ($inver_fn[$j] / $tot_fn) * 100;
					
					if($pro_emis > $param) {
						$cumplimi = 'No - Cumple'; $class_to = 'class="danger"'; 
						
						if($emi_cont <= 0){
							$script .= '<script>jQuery("#modal_'.$arr_fon[$i].'").modal("toggle")</script>';				
							$emi_cont ++;
						}
					
					}else if($pro_emis <= $param) { $cumplimi = 'Cumple'; $class_to = 'class="success"'; }
					
					$cont_table_inver = $cont_table_inver.'
						<tr '.$class_to.'>						
							<td><input type="checkbox" name="checkbox" class="form-control" id="check_inv'.$nit_fn[$j].'" value="'.$nit_fn[$j].'"></td>
							<td>'.utf8_encode($name_fn[$j]).'</td>
							<td><input id="val_inv_'.$nit_fn[$j].'_'.$arr_fon[$i].'" type="text" class="form-control" placeholder="'.$param.' %"></td>
							<td>'.money_format($fmt, $inver_fn[$j]).'</td>
							<td>'.round($pro_emis, 2).' %</td>
							<td>'.$cumplimi.'</td>
						</tr>';
						
					$pdf_emis_inver[$cont_inver][0] = utf8_encode($name_fn[$j]);
					$pdf_emis_inver[$cont_inver][1] = $param.' %';
					$pdf_emis_inver[$cont_inver][2] = money_format($fmt, $inver_fn[$j]);
					$pdf_emis_inver[$cont_inver][3] = round($pro_emis, 2);
					$pdf_emis_inver[$cont_inver][4] = $cumplimi;
					
					$nit_fn_inv[$cont_inver] = $nit_fn[$j];
					
					$grp_eco_inv[$grp_eco_fn[$j]] += $inver_fn[$j];
					
					$cont_inver ++;
					//echo 'entra___'.$grp_eco_fn[$j];
				}
				
				
				$param = $row_param[11];
				$param = $param+0;
				
				if($depo_fn[$j] != ''){
				
					$pro_emis = ($depo_fn[$j] / $tot_fn) * 100;
					
					if($pro_emis > $param){
						$cumplimi = 'No Cumple';
						$class_to = 'class="danger"';
						
						if($emi_cont <= 0){
							$script .= '<script>jQuery("#modal_'.$arr_fon[$i].'").modal("toggle")</script>';
							$emi_cont ++;
						}
						
					}else if($pro_emis <= $param){ $cumplimi = 'Cumple'; $class_to = 'class="success"'; }
					
					$cont_table_depo .='
						<tr '.$class_to.'>
							<td><input type="checkbox" name="checkbox" class="form-control" id="check_dep'.$nit_fn[$j].'" value="'.$nit_fn[$j].'"></td>
							<td>'.utf8_encode($name_fn[$j]).'</td>
							<td><input id="val_dep_'.$nit_fn[$j].'_'.$arr_fon[$i].'" type="text" class="form-control" placeholder="'.$param.'%"></td>
							<td>'.money_format($fmt,$depo_fn[$j]).'</td>
							<td>'.round($pro_emis, 2).'%</td>
							<td>'.$cumplimi.'</td>
						</tr>';
					
					$pdf_emis_depo[$cont_dep][0] = utf8_encode($name_fn[$j]);
					$pdf_emis_depo[$cont_dep][1] = $param.'%';
					$pdf_emis_depo[$cont_dep][2] = money_format($fmt, $depo_fn[$j]);
					$pdf_emis_depo[$cont_dep][3] = round($pro_emis, 2);
					$pdf_emis_depo[$cont_dep][4] = $cumplimi;
					
					$nit_fn_dep[$cont_dep] = $nit_fn[$j];
					
					$grp_eco_dep[$grp_eco_fn[$j]] += $depo_fn[$j];
					
					$cont_dep ++;
				}
			}
			
			//$grp_eco_inv[$grp_eco_fn[$j++]] += $inver_fn[$j++];
			
			//print_r($grp_eco_inv);
		// CABECERA DE LA TABLA DEPOSITOS
			
			$btn_gunoXuno_emis = "<a href='javascript:guarda_emi($arr_fon[$i], ".json_encode($nit_fn_dep).", 2)' class='btn btn-primary btn-ar'><i class='fa fa-floppy-o'></i> Guardar</a>";
			
			$cab_tab_depo = '
				<h1>Composición del Portafolio de Depositos Bancarios por Emisor</h1>
				<div id="sel_todo_dep'.$i.'">
				<form id="emis_dep'.$arr_fon[$i].'">
				<table class="table">
					<tr>
						<th>Selección <input data-toggle="popover" data-placement="left" type="checkbox" class="form-control" id="emis_dep'.$i.'"></th> 
						<th>Nombre Emisor</th> 
						<th>Maximo del Emisor <br> 
						'.$btn_gunoXuno_emis.'</th> 
						<th> Valor </th> 
						<th>Participación</th> 
						<th>Resultado</th>
					</tr>';
			
			$table_depo = $cab_tab_depo.$cont_table_depo.'</table> </form></div>';
			$cont_table_depo = '';
			$nit_fn_dep = array();
		
		// CABECERA DE LA TABLA INVERSION
		
			$btn_gunoXuno_emis = "<a href='javascript:guarda_emi($arr_fon[$i], ".json_encode($nit_fn_inv).", 1)' class='btn btn-primary btn-ar'><i class='fa fa-floppy-o'></i> Guardar</a>";
			
			$cab_tab_inver = '
			<h1>Composición del Portafolio de Inversiones por Emisor</h1>
				<div id="sel_todo_inv'.$i.'">
				<div id="msj_inv'.$arr_fon[$i].'"></div>
				<form id="emis_inv'.$arr_fon[$i].'">
				
				<table class="table">
					<tr>
						<th>Selección <input data-toggle="popover" data-placement="left" type="checkbox" class="form-control" id="emis_inv'.$i.'"></th>
						<th>Nombre Emisor</th>
						<th>Maximo del emisor <br>
						'.$btn_gunoXuno_emis.'</th>
						<th> Valor </th>
						<th>Participación</th>
						<th>Resultado</th>
					</tr>';
					
			$table_inver = $cab_tab_inver.$cont_table_inver.'</table> </form></div>';
			$cont_table_inver = '';
			$nit_fn_inv = array();
			
			$inp_nits = "<input id='emis_$arr_fon[$i]' type='hidden' value='".json_encode($nit_fn)."'>";
			$inp_pdf_depo = "<input id='pdf_dep_emi_$arr_fon[$i]' type='hidden' value='".array_envia($pdf_emis_depo)."'>";
			$inp_pdf_inver = "<input id='pdf_inver_emi_$arr_fon[$i]' type='hidden' value='".array_envia($pdf_emis_inver)."'>";
			
			$html = $html.$faltantes.$inp_nits.$inp_pdf_depo.$inp_pdf_inver.$table_inver.$table_depo.'</div></div></div> '.$script;
			$ib = $i + 1;
			
			$array_inver[$ib] = $pdf_emis_inver;
			$array_depo[$ib] = $pdf_emis_depo;
			
			$array_grp_eco[0][$ib] = $grp_eco_inv;
			$array_grp_eco[1][$ib] = $grp_eco_dep;
			
			$grp_eco_inv = array();
			$grp_eco_dep = array();
			
			$pdf_emis_inver = array();
			$pdf_emis_depo = array();
		
		}
	}
	
	if($tipo_procedimiento == '2I'){ return $array_inver; }
	else if($tipo_procedimiento == '2D'){ return $array_depo; }
	else if($tipo_procedimiento == '2G'){ return $array_grp_eco; }
	else return $html;	
}

function guardar_emisores($ruta){
	
	$consult = new mysql();
	setlocale(LC_MONETARY, 'es_CO');
	$fmt = '%i';
	$cont = 0;
	
	$table_inemis = array('jo33_FIC_UTLR_emisores');
	$valC_inemis = array('id', 'Nombre', 'nit', 'active', 'trash');
	$val_inemis = array('','');
	
	if (($fichero = fopen($ruta, "r")) !== FALSE) {
		while (($datos = fgetcsv($fichero, 1000, ";")) !== FALSE) {
			
			if($cont == 50){
				$sql_inemis = $consult -> sql('IP', $table_inemis, $val_inemis, $valC_inemis, $valU);
				$cont = 0;
			}
			
			$val_inemis[$cont][0] = '';
			$val_inemis[$cont][1] = $datos[0];
			$val_inemis[$cont][2] = $datos[1];
			$val_inemis[$cont][3] = 1;
			$val_inemis[$cont][4] = 1;
			
			++ $cont;
		}
	}
	
	return '<h1> Emisores creados con exito </h1>';
}
//echo $footer;
?>
