<?php
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
date_default_timezone_set('America/Bogota');

$consult = new mysql();

$tipo = $_POST['tipo'];
$fondo = $_POST['fondo'];
$max_sel = $_POST['max_selec'];
$nits_emis = json_decode($_POST['nits_emis']);

$table_fn = array('jo33_FIC_UTLR_fondos');
$valC_fn = array('codigo', 'active', 'trash');
$val_fn = array($fondo, 1, 1);

$sql_fn = $consult -> sql('S', $table_fn, $val_fn, $valC_fn, $valU);
$row_fn = mysqli_fetch_array($sql_fn);

$cont = 0;

/*
	* OBTENEMOS LOS EMISORES LLAMANDOLOS DESDE LA BASE DE DATOS
*/

$table_emis = array('jo33_FIC_UTLR_emisores');
$valC_emis = array('active', 'trash');
$val_emis = array(1,1);
$sql_emis = $consult -> sql('S', $table_emis, $val_emis, $valC_emis, $valU);

while($row_emis = mysqli_fetch_array($sql_emis)){ $emis[$row_emis['nit']] = $row_emis['id']; }

/*
	* INSERTAMOS LOS PORCENTAJES DE CADA EMISOR
*/

$table_emisXfn = array('jo33_FIC_UTLR_emisores_x_fondos');
$valC_emisXfn = array('id_emisores','id_fondos','max_inv','max_dep');

if($tipo == 1) { $max_inv = $max_sel; $max_dep = 0; }
if($tipo == 2) { $max_dep = $max_sel; $max_inv = 0; }

for($i=0; $i<count($nits_emis); ++$i){
	
	$id_emicomp = $emis[$nits_emis[$i]];
	
	$valC_comp_emi = array('id_emisores','id_fondos');
	$val_comp_emi = array($id_emicomp, $row_fn['id']);
	//echo $nits_emis[$i].'<br>';
	
	if($nits_emis[$i] != '') {
	
		$sql_comp_emi = $consult -> sql('S', $table_emisXfn, $val_comp_emi, $valC_comp_emi, $valU);
		$row_comp_emi = mysqli_fetch_array($sql_comp_emi);
	}
	
	/*$val_emisXfn[$cont][1] = $id_emicomp;
	$val_emisXfn[$cont][2] = $row_fn['id'];
	$val_emisXfn[$cont][3] = $max_inv;
	$val_emisXfn[$cont][4] = $max_dep;*/
	//echo count($row_comp_emi);
	if(count($row_comp_emi) == 1){
		$val_emisXfn[$cont][1] = $id_emicomp;
		$val_emisXfn[$cont][2] = $row_fn['id'];
		$val_emisXfn[$cont][3] = $max_inv;
		$val_emisXfn[$cont][4] = $max_dep;
		
		++ $cont;
	}else{
	
		if($tipo == 1) $valC_comp_emi = array('max_inv');
		if($tipo == 2) $valC_comp_emi = array('max_dep');
		$val_comp_emi = array($max_sel);
		$valU_comp_emi = array('id_emisores ='.$id_emicomp , 'id_fondos ='.$row_fn["id"]);
		//echo $nits_emis[$i].'<br>';
		if($nits_emis[$i] != '') 
			$sql_comp_emi = $consult -> sql('U', $table_emisXfn, $val_comp_emi, $valC_comp_emi, $valU_comp_emi);
	}
	
	if($cont == 50 ){ 
		$sql_emisXfn = $consult -> sql('IP', $table_emisXfn, $val_emisXfn, $valC_emisXfn, $valU); 
		$cont = 0;
	}
}


if($cont > 1){ $sql_emisXfn = $consult -> sql('IP', $table_emisXfn, $val_emisXfn, $valC_emisXfn, $valU);}
else if($cont != 0){
	$val_emisXfnII = array($val_emisXfn[0][1], $val_emisXfn[0][2], $val_emisXfn[0][3], $val_emisXfn[0][4]);
	//print_r($val_emisXfn);
	//echo 'hola mundo!!';
	$sql_emisXfn = $consult -> sql('I', $table_emisXfn, $val_emisXfnII, $valC_emisXfn, $valU); 
}

?>