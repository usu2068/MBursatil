<?php
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
date_default_timezone_set('America/Bogota');

$consult = new mysql();

$table = array('jo33_FIC_UTLR_tipos_cartera');
$val = array('0', '0');
$valC = array('active', 'trash');
$valU = array('id ='.$_POST['id_tip']);

$sql_elim = $consult -> sql('U', $table, $val, $valC, $valU);
?>