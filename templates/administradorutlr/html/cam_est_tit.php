<?php
include_once('/home/aplicati/public_html/utlr/templates/class/consultas.php');
include_once('/home/aplicati/public_html/utlr/templates/class/list.php');

$consult = new mysql();
$lista = new listado();

$tip = $_POST['tip'];
$est = $_POST['est'];
$id_tit = $_POST['id_tit'];
$id_grptit = $_POST['id_grptit'];

if($tip == 0){
	$table = array('jo33_FIC_UTLR_titulos_X_titulos_pol_tip');
	$valU = array('id_titulos ='.$id_tit, 'id_titulos_pol_tip ='.$id_grptit);
	$valC_tit_asig = array('id_titulos_pol_tip');
	
}elseif($tip == 1){
	/**/
	$table = array('jo33_FIC_UTLR_titulos_has_jo33_FIC_UTLR_titulos_pol_fon');
	$valU = array('	id_titulos ='.$id_tit, 'id_titulos_pol_fon ='.$id_grptit);
	$valC_tit_asig = array('id_titulos_pol_fon');
	
}

if($est == 0)$est = 1;
elseif($est == 1)$est = 0;

$valC = array('active');
$val = array($est);
$sql = $consult -> sql('U', $table, $val, $valC, $valU);

$val_tit_asig = array($id_grptit);
$sql_tit_asig = $consult -> sql('S', $table, $val_tit_asig, $valC_tit_asig, $valU_tit_asig);
$contenido_asig_tit = $lista -> paginacion($sql_tit_asig, $tip);

echo $contenido_asig_tit;
?>