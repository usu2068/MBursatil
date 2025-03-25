<?php
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
date_default_timezone_set('America/Bogota');

$consult = new mysql();

$id_comp = $_POST['id_comp'];
//$cod = $_POST['cod'];
$nombre_tit = $_POST['nom'];
$tipo = $_POST['tipo'];
$min= $_POST['min'];
$max= $_POST['max'];

if($tipo == 1){
	
	$table = array('jo33_FIC_UTLR_titulos_pro_tip');		
	$val = array('', $max, $min, $id_comp);
	$valC = array('id', 'max', 'min', 'id_compocicion_pro_tip');
	$sql_tit = $consult -> sql('I', $table, $val, $valC, $valU);
		
	$id_tit = $consult -> sql_ultimo('jo33_FIC_UTLR_titulos_pro_tip');
	
	$table = array('jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip');
	$valC = array('id_nom_titulo_pro', 'id_titulos_pro_tip');
	$val = array($nombre_tit, $id_tit);
	$sql_nom = $consult -> sql('I', $table, $val, $valC, $valU);
	
	echo '<div class="alert alert-warning" role="alert"><p>Breve nene.<p></div>';
	
}else if($tipo == 0){
	
	$table = array('jo33_FIC_UTLR_titulos_pol_tip');
	
	$nombre_tit_S = "'".$nombre_tit."'";
	
	$val = array($nombre_tit_S, 1, 1);
	$valC = array('nombre','active', 'trash');
	$sql = $consult -> sql('S', $table, $val, $valC, $valU);
	
	$num_reg = mysql_num_rows($sql);
	
	if($num_reg == 0){
		
		$val = array($nombre_tit, $max, $min, $id_comp);
		$valC = array('nombre', 'max', 'min', 'compocicion_pol_tip_id');
		$sql = $consult -> sql('I', $table, $val, $valC, $valU);
		
	}else
		echo '<div class="alert alert-warning" role="alert"><p>Ya existe un título registrado con este nombre, verifique e intente nuevamente.<p></div>';
}
?>