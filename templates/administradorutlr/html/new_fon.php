<?php 
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
date_default_timezone_set('America/Bogota');

$consult = new mysql();
$tip = $_POST['tip'];
$id_grp_tfn = 0;

if($tip == 0){

	$table = array('jo33_FIC_UTLR_tipos_cartera');
	$val = array('', utf8_encode($_POST['nom_fon']));
	$valC = array('id', 'nombre');

	$sql_fon = $consult -> sql('I', $table, $val, $valC, $valU);
	
}else if($tip == 1){

	$id_tip = $_POST['sel_tip'];
	$id_ent = $_POST['sel_ent'];
	$nom_fon = utf8_encode($_POST['nom_fon']);
	$cod_fond = $_POST['cod_fond'];
	$prom = 0;
	$comp_pap_fnXtip = Array();
	
/*
	-- Consulta para determinar la existencia de registros en la tabla entidad 
*/	
	$table_en = array('jo33_FIC_UTLR_entidad');
	$valC_en = array('id_joomla', 'active', 'trash');
	$val_en = array($id_ent, '1', '1');
	
	$sql_en = $consult -> sql('S', $table_en, $val_en, $valC_en, $valU);
	$row_num = mysqli_num_rows($sql_en);
	
	if($row_num == 0){// Cuando no existe ningun registro
		$sql_en = $consult -> sql('I', $table_en, $val_en, $valC_en, $valU);
		$id_ent = $consult -> sql_ultimo('jo33_FIC_UTLR_entidad');
		
	}else{// Cuando ya hay registros existentes
		$row_ent = mysqli_fetch_array($sql_en);
		$id_ent = $row_ent[0];
	}
	
	/*
		-- CONSULTA PARA EXTRAER LA INFORMACIÓN DEL TIPO DE FONDO
	*/
	$table_inf_tip_tit = array(
		'jo33_FIC_UTLR_tipos_cartera', 
		'jo33_FIC_UTLR_politicas_tip', 
		'jo33_FIC_UTLR_compocicion_pol_tip', 
		'jo33_FIC_UTLR_titulos_pol_tip', 
		'jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip', 
		'jo33_FIC_UTLR_grupos_pol_tip');
	
	$valC_inf_tip_tit = array(
		'jo33_FIC_UTLR_tipos_cartera.id', 
		'jo33_FIC_UTLR_politicas_tip.tipos_cartera_id',
		'jo33_FIC_UTLR_compocicion_pol_tip.id_politicas_tip', 
		'jo33_FIC_UTLR_titulos_pol_tip.id_compocicion_pol_tip', 
		'jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip.id_titulos_pol_tip', 
		'jo33_FIC_UTLR_grupos_pol_tip.id',
		'jo33_FIC_UTLR_titulos_pol_tip.active',
		'jo33_FIC_UTLR_titulos_pol_tip.trash');
	
	$val_inf_tip_tit = array(
		$id_tip, 
		'jo33_FIC_UTLR_tipos_cartera.id',
		'jo33_FIC_UTLR_politicas_tip.id',
		'jo33_FIC_UTLR_compocicion_pol_tip.id', 
		'jo33_FIC_UTLR_titulos_pol_tip.id', 
		'jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip.id_grupos_pol_tip',
		1, 1);
	
	$sql_inf_tip_tit = $consult -> sql('S', $table_inf_tip_tit, $val_inf_tip_tit, $valC_inf_tip_tit, $valU);
	
	/*
		-- CONSULTA LAS COMPOCICIONES PADRES
	*/	
	$table_inf_tip_comp = array(
		'jo33_FIC_UTLR_tipos_cartera', 
		'jo33_FIC_UTLR_politicas_tip',
		'jo33_FIC_UTLR_compocicion_pol_tip');
		
	$valC_inf_tip_comp = array(
		'jo33_FIC_UTLR_compocicion_pol_tip.parent', 
		'jo33_FIC_UTLR_tipos_cartera.id', 
		'jo33_FIC_UTLR_politicas_tip.tipos_cartera_id',
		'jo33_FIC_UTLR_compocicion_pol_tip.id_politicas_tip',
		'jo33_FIC_UTLR_compocicion_pol_tip.active', 
		'jo33_FIC_UTLR_compocicion_pol_tip.trash');
		
	$val_inf_tip_comp = array(0, $id_tip, 'jo33_FIC_UTLR_tipos_cartera.id', 'jo33_FIC_UTLR_politicas_tip.id',1 ,1);
	
	$sql_inf_tip_comp = $consult -> sql('S', $table_inf_tip_comp, $val_inf_tip_comp, $valC_inf_tip_comp, $valU);
	
	$id_pol_actual = 0;
	$id_comp_actual = 0;
	$id_grup_actual = 0;
	
	$val_titfn = array();
	$val_gruXtit = array();
	$cont = 0;
	$id_ult_tit = $consult -> sql_ultimo('jo33_FIC_UTLR_titulos_pol_fon');
	
	/*Creacion de un nuevo fondo*/	

	$table_fn = array('jo33_FIC_UTLR_fondos');
	$valC_fn = array('nombre', 'promedio_pon', 'id_tipos_cartera', 'id_entidad','codigo');
	$val_fn = array($nom_fon, $prom, $id_tip, $id_ent, $cod_fond);
	
	$sql_fn = $consult -> sql('I', $table_fn, $val_fn, $valC_fn, $valU);
	$id_fn = $consult -> sql_ultimo('jo33_FIC_UTLR_fondos');
	
	while($row_inf_tip_tit = mysqli_fetch_array($sql_inf_tip_tit)){

	/*traslado de la info de tipo de fondo a fondo de la entidad*/
		
		// POLITICAS PARA FONDOS -- SOLO SE HARA UNA VEZ DURANTE EL CICLO
		if($id_pol_actual != $row_inf_tip_tit[4]){ // -- Si el id de politicas es diferente procede a crear una nueva politica
		
			$table_polfn = array('jo33_FIC_UTLR_politicas_fon');
			$valC_polfn = array('id', 'active', 'trash', 'id_fondos');
			$val_polfn = array('', 1, 1, $id_fn);
			
			$sql_polfn = $consult -> sql('I', $table_polfn, $val_polfn, $valC_polfn, $valU);
			$id_pol_actual = $row_inf_tip_tit[4];
			$id_pol_actualfn = $consult -> sql_ultimo('jo33_FIC_UTLR_politicas_fon');
			
			/*
				-- INSERTA LAS COMPOCICIONES PADRES A NUEVO FONDO
			*/
			while($row_inf_tip_comp = mysqli_fetch_array($sql_inf_tip_comp)){
				
				$table_comp_papfn = array('jo33_FIC_UTLR_compocicion_pol_fon');
				$valC_comp_papfn = array('id', 'parent', 'active', 'trash', 'id_politicas_fon', 'id_nom_compocicion');
				$val_comp_papfn = array('', 0, 1, 1, $id_pol_actualfn, $row_inf_tip_comp[13]);
				
				$sql_comp_papfn = $consult -> sql('I', $table_comp_papfn, $val_comp_papfn, $valC_comp_papfn, $valU);
				$id_comp_papfn = $consult -> sql_ultimo('jo33_FIC_UTLR_compocicion_pol_fon');
				
				$comp_pap_fnXtip[$row_inf_tip_comp[8]] = $id_comp_papfn;
			}
			/*echo '<br/>';
			print_r($comp_pap_fnXtip);
			echo '<br/>';
			print_r($row_inf_tip_tit[9]);
			echo '<br/>';*/
		}
		
	// COMPICICIONES
		if($id_comp_actual != $row_inf_tip_tit[8]){
			
			$table_compfn = array('jo33_FIC_UTLR_compocicion_pol_fon');
			$valC_compfn = array('id', 'parent', 'active', 'trash', 'id_politicas_fon', 'id_nom_compocicion');
			$val_compfn = array('', $comp_pap_fnXtip[$row_inf_tip_tit[9]], 1, 1, $id_pol_actualfn, $row_inf_tip_tit[13]);
			
			$sql_compfn = $consult -> sql('I', $table_compfn, $val_compfn, $valC_compfn, $valU);
			
			$id_comp_actual = $row_inf_tip_tit[8];
			$id_comp_fn_actual = $consult -> sql_ultimo('jo33_FIC_UTLR_compocicion_pol_fon');
			//$cont ++;
		}
		
	// TITULOS
		$val_titfn[$cont][0] = '';
		$val_titfn[$cont][1] = 0;
		$val_titfn[$cont][2] = 0;
		$val_titfn[$cont][3] = 1;
		$val_titfn[$cont][4] = 1;
		$val_titfn[$cont][5] = $id_comp_fn_actual;
		$val_titfn[$cont][6] = $row_inf_tip_tit[20];
		
	//GRUPOS
		
		if($id_grup_actual != $row_inf_tip_tit[22]){
		
			$table_grupfn = array('jo33_FIC_UTLR_grupos_fon');
			$valC_grupfn = array('id', 'max', 'min', 'tipo', 'active', 'trash', 'id_nom_tot');
			$val_grupfn = array('', 0, 0, $row_inf_tip_tit[26], 1, 1, $row_inf_tip_tit[29]);
			
			$sql_grupfn = $consult -> sql('I', $table_grupfn, $val_grupfn, $valC_grupfn, $valU);
			
			if($row_inf_tip_tit[26] == 'tot'){
				$id_grup_actual = $row_inf_tip_tit[22];
				$id_grup_actualfn_tot = $consult -> sql_ultimo('jo33_FIC_UTLR_grupos_fon');
			}else $id_grup_actualfn_com = $consult -> sql_ultimo('jo33_FIC_UTLR_grupos_fon');
		}
		
		if($row_inf_tip_tit[26] == 'tot') $id_grup_actualfn = $id_grup_actualfn_tot;
		else $id_grup_actualfn = $id_grup_actualfn_com;
	
	//GRUPOS X TITULOS
		$id_ult_tit ++;
		$val_gruXtit[$cont][0] = $id_grup_actualfn;
		$val_gruXtit[$cont][1] = $id_ult_tit;
		$cont ++;
		
		if($cont == 50){
			
		// TITULOS
			$table_titfn = array('jo33_FIC_UTLR_titulos_pol_fon');
			$valC_titfn = array('id', 'max', 'min', 'active', 'trash', 'id_compocicion_pol_fon', 'id_nom_tiulo');
			
		// TITULOS X GRUPOS
			$table_gruXtit = array('jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon');
			$valC_gruXtit = array('id_grupos_fon', 'id_titulos_pol_fon');
		
			//if($row_inf_tip_tit[26] != 'cal'){ 
			
			$sql_compfn = $consult -> sql('IP', $table_titfn, $val_titfn, $valC_titfn, $valU);
			$sql_gruXtit = $consult -> sql('IP', $table_gruXtit, $val_gruXtit, $valC_gruXtit, $valU);
			
			$cont = 0;
			$val_gruXtit = array();
			$val_titfn = array();
			//}
			
		}
	}
	
	print_r($val_gruXtit);
	
	/*Array ( 
		[0] => Array ( [0] => 7268 [1] => 29331 ) 
		[1] => Array ( [0] => 7281 [1] => 29332 ) 
		[2] => Array ( [0] => 7282 [1] => 29333 ) 
		[3] => Array ( [0] => 7283 [1] => 29334 ) 
		[4] => Array ( [0] => 7284 [1] => 29335 ) 
		[5] => Array ( [0] => 7285 [1] => 29336 ) 
		[6] => Array ( [0] => 7286 [1] => 29337 ) 
		[7] => Array ( [0] => 7284 [1] => 29338 ) 
		[8] => Array ( [0] => 7287 [1] => 29339 ) 
		[9] => Array ( [0] => 7288 [1] => 29340 ) 
		[10] => Array ( [0] => 7284 [1] => 29341 ) 
		[11] => Array ( [0] => 7289 [1] => 29342 ) 
		[12] => Array ( [0] => 7290 [1] => 29343 ) 
		[13] => Array ( [0] => 7284 [1] => 29344 ) 
		[14] => Array ( [0] => 7291 [1] => 29345 ) 
		[15] => Array ( [0] => 7292 [1] => 29346 ) 
		[16] => Array ( [0] => 7284 [1] => 29347 ) 
		[17] => Array ( [0] => 7293 [1] => 29348 ) 
		[18] => Array ( [0] => 7294 [1] => 29349 ) 
		[19] => Array ( [0] => 7284 [1] => 29350 ) 
		[20] => Array ( [0] => 7295 [1] => 29351 ) 
		[21] => Array ( [0] => 7296 [1] => 29352 ) 
		[22] => Array ( [0] => 7284 [1] => 29353 ) 
		[23] => Array ( [0] => 7297 [1] => 29354 ) 
		[24] => Array ( [0] => 7298 [1] => 29355 ) 
		[25] => Array ( [0] => 7299 [1] => 29356 ) 
		[26] => Array ( [0] => 7300 [1] => 29357 ) 
		[27] => Array ( [0] => 7299 [1] => 29358 ) 
		[28] => Array ( [0] => 7301 [1] => 29359 ) 
		[29] => Array ( [0] => 7299 [1] => 29360 ) 
		[30] => Array ( [0] => 7302 [1] => 29361 ) 
		[31] => Array ( [0] => 7299 [1] => 29362 ) 
		[32] => Array ( [0] => 7303 [1] => 29363 ) 
		[33] => Array ( [0] => 7304 [1] => 29364 ) 
		[34] => Array ( [0] => 7304 [1] => 29365 ) 
		[35] => Array ( [0] => 7304 [1] => 29366 ) 
		[36] => Array ( [0] => 7304 [1] => 29367 ) 
		[37] => Array ( [0] => 7304 [1] => 29368 ) 
		[38] => Array ( [0] => 7304 [1] => 29369 ) 
		[39] => Array ( [0] => 7305 [1] => 29370 ) 
		[40] => Array ( [0] => 7305 [1] => 29371 ) 
		[41] => Array ( [0] => 7305 [1] => 29372 ) 
		[42] => Array ( [0] => 7305 [1] => 29373 ) 
		[43] => Array ( [0] => 7305 [1] => 29374 ) 
		[44] => Array ( [0] => 7306 [1] => 29375 ) 
		[45] => Array ( [0] => 7306 [1] => 29376 ) 
		[46] => Array ( [0] => 7306 [1] => 29377 ) 
	) */
	
	if($cont > 1){
	
	// TITULOS
		$table_titfn = array('jo33_FIC_UTLR_titulos_pol_fon');
		$valC_titfn = array('id', 'max', 'min', 'active', 'trash', 'id_compocicion_pol_fon', 'id_nom_tiulo');
		
	// TITULOS X GRUPOS
		$table_gruXtit = array('jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon');
		$valC_gruXtit = array('id_grupos_fon', 'id_titulos_pol_fon');
		
		$sql_compfn = $consult -> sql('IP', $table_titfn, $val_titfn, $valC_titfn, $valU);	
		$sql_gruXtit = $consult -> sql('IP', $table_gruXtit, $val_gruXtit, $valC_gruXtit, $valU);
		
	}
	
	echo 'Fondo creado exitosamente';

//----/// Prohibiciones

	/*$table_po_fn = array('jo33_FIC_UTLR_prohibiciones_fon');
	$valC_po_fn = array('id_fondos');
	$val_po_fn = array($id_fn);
	
	$sql_po_fn = $consult -> sql('I', $table_po_fn, $val_po_fn, $valC_po_fn, $valU);
	$id_po_fn = $consult -> sql_ultimo('jo33_FIC_UTLR_prohibiciones_fon');*/
	
  /*
	- Compocicion de las Prohibiciones
	* Obtenemos datos a para predeterminar las compociciones del fondo
	* Guardamos datos en la compocicion del fondo
  */
  
	$table_co_tfn = array(
		'jo33_FIC_UTLR_prohibiciones_tip', 
		'jo33_FIC_UTLR_compocicion_pro_tip', 
		'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip',
		'jo33_FIC_UTLR_titulos_pro_tip',
		'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip',
		'jo33_FIC_UTLR_nom_compocicion_pro',
		'jo33_FIC_UTLR_nom_titulo_pro'
	);
	
	$valC_co_tfn = array(
		'jo33_FIC_UTLR_prohibiciones_tip.id_tipos_cartera', 
		'jo33_FIC_UTLR_compocicion_pro_tip.id_prohibiciones_tip',
		'jo33_FIC_UTLR_titulos_pro_tip.id_compocicion_pro_tip',
		'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip.id_compocicion_pro_tip',
		'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip.id_titulos_pro_tip',
		'jo33_FIC_UTLR_nom_compocicion_pro.id',
		'jo33_FIC_UTLR_nom_titulo_pro.id'
	);
	
	$val_co_tfn = array(
		$id_tip, 
		'jo33_FIC_UTLR_prohibiciones_tip.id',
		'jo33_FIC_UTLR_compocicion_pro_tip.id',
		'jo33_FIC_UTLR_compocicion_pro_tip.id',
		'jo33_FIC_UTLR_titulos_pro_tip.id',
		'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip.id_nom_compocicion_pro',
		'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip.id_nom_titulo_pro'
	);
	
	$sql_co_tfn = $consult -> sql('S', $table_co_tfn, $val_co_tfn, $valC_co_tfn, $valU);
	
	$id_comp = 0;
	$cont_ins = 0;
	
	/*
		PARAMETROS PARA GUARDAR EN LA BASE PARA EL NUEVO FONDO 
	*/
	
	// PROHIBICIONES
		$table_pr_fn = array('jo33_FIC_UTLR_prohibiciones_fon');
		$valC_pr_fn = array('id_fondos');
		$val_pr_fn = array($id_fn);
	
		$sql_pr_fn = $consult -> sql('I', $table_pr_fn, $val_pr_fn, $valC_pr_fn, $valU);
		$id_pr_fn = $consult -> sql_ultimo('jo33_FIC_UTLR_prohibiciones_fon');
	
	// COMPOCICIONES
		$table_com_prfn = array('jo33_FIC_UTLR_compocicion_pro_fon');
		$valC_com_prfn = array('id', 'parent', 'active', 'trash', 'id_prohibiciones_fon');
		
	// TITULOS
		$table_tit_prfn = array('jo33_FIC_UTLR_titulos_pro_fon');
		$valC_tit_prfn = array('id', 'max', 'min', 'active', 'trash', 'id_compocicion_pro_fon');
	
	// NOMBRES DE COMPOCICIONES
		$table_nomXcom_prfn = array('jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon');
		$valC_nomXcom_prfn = array('id_nom_compocicion_pro', 'id_compocicion_pro_fon');
	
	// NOMBRES DE TITULOS
		$table_nomXtit_prfn = array('jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon');
		$valC_nomXtit_prfn = array('id_nom_titulo_pro', 'id_titulos_pro_fon');
	
	while($row_co_tfn = mysqli_fetch_array($sql_co_tfn)){
	
		
		if($id_comp != $row_co_tfn[10] || $id_comp == 0){
		
			$id_comp = $row_co_tfn[10];
		// INSERT COMPOCICIÓN
			$val_com_prfn = array('', 0, 1, 1, $id_pr_fn);
			$sql_com_prfn = $consult -> sql('I', $table_com_prfn, $val_com_prfn, $valC_com_prfn, $valU);
			
			$id_com_prfn = $consult -> sql_ultimo('jo33_FIC_UTLR_compocicion_pro_fon');
			
		// INSERT RELACIÓN COMPOCICIÓN x NOMBRE 	
			$val_nomXcom_prfn = array($row_co_tfn[9], $id_com_prfn);
			$sql_nomXcom_prfn = $consult -> sql('I', $table_nomXcom_prfn, $val_nomXcom_prfn, $valC_nomXcom_prfn, $valU);
			
		}		
		
	// INSERT TITULO
		$val_tit_prfn = array('', 0, 0, 1, 1, $id_com_prfn);
		$sql_tit_prfn = $consult -> sql('I', $table_tit_prfn, $val_tit_prfn, $valC_tit_prfn, $valU);
		
		$id_tit_prfn = $consult -> sql_ultimo('jo33_FIC_UTLR_titulos_pro_fon');
		
	//INSERT TITULO x NOMBRE
		$val_nomXtit_prfn = array($row_co_tfn[17], $id_tit_prfn);
		$sql_nomXtit_prfn = $consult -> sql('I', $table_nomXtit_prfn, $val_nomXtit_prfn, $valC_nomXtit_prfn, $valU);
	
	}
}
?>