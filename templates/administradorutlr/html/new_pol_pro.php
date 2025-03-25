<?php
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
date_default_timezone_set('America/Bogota');

$tipo_guard = $_POST['tipo_g'];
$conslt = new mysql();

if($tipo_guard == 1){
	
	$pc = $_POST['pc'];
	
	if($pc == 1){
	
		$id_pro = $_POST['id_tip'];
		$nom_com = utf8_encode($_POST['nombre_com']);
		$parent = $_POST['parent'];
		$pap = $_POST['id_pap'];
		
		$table = array('jo33_FIC_UTLR_prohibiciones_tip');
		$val = array('', $pap);
		$valC = array('id', 'id_tipos_cartera');
		
		$sql_proh = $conslt -> sql('I', $table, $val, $valC, $valU);
		
		$id_proh = $conslt -> sql_ultimo('jo33_FIC_UTLR_prohibiciones_tip');
		
	}elseif($pc == 2){
		
		$nom_com = $_POST['nombre_com'];
		$parent = $_POST['parent'];
		$id_proh = $_POST['id_pap'];
		
	}
	
	$table = array('jo33_FIC_UTLR_compocicion_pro_tip');
	$val = array($parent, $id_proh);
	$valC = array('parent', 'id_prohibiciones_tip');
	
	$sql_com_pro = $conslt -> sql('I', $table, $val, $valC, $valU);
	
	$id_comp = $conslt -> sql_ultimo('jo33_FIC_UTLR_compocicion_pro_tip');
	
	$table = array('jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip');
	$valC = array('id_nom_compocicion_pro','id_compocicion_pro_tip');
	$val = array($nom_com, $id_comp);
	
	$sql_nom_comp = $conslt -> sql('I', $table, $val, $valC, $valU);
	
}else if($tipo_guard == 0){ 

	$pc = $_POST['pc'];
	
	if($pc == 1){
	
		$id_pro = $_POST['id_tip'];
		$nom_com = utf8_encode($_POST['nombre_com']);
		$parent = $_POST['parent'];
		$pap = $_POST['id_pap'];
		
		$table = array('jo33_FIC_UTLR_politicas_tip');
		$val = array('', $pap);
		$valC = array('id', 'tipos_cartera_id');
		
		$sql_pol = $conslt -> sql('I', $table, $val, $valC, $valU);
		
		$id_pol = $conslt -> sql_ultimo('jo33_FIC_UTLR_politicas_tip');
		
	}elseif($pc == 2){
		
		$nom_com = utf8_encode($_POST['nombre_com']);
		$parent = $_POST['parent'];
		$id_pol = $_POST['id_pap'];
		
	}
	
	$table = array('jo33_FIC_UTLR_compocicion_pol_tip');
	$val = array($nom_com, $parent, $id_pol);
	$valC = array('nombre', 'parent', 'politicas_tip_id');
	
	$sql_com_pol = $conslt -> sql('I', $table, $val, $valC, $valU);
}

?>