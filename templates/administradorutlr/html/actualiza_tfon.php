<?php 
include_once('/home/aplicati/public_html/utlr/templates/class/list.php');

$id_ent = $_POST['id_ent'];
$tip = $_POST['tip']; 

if($tip == 0) $t_sql = 'jo33_FIC_UTLR_tipos_cartera';
else $t_sql = 'jo33_FIC_UTLR_fondos';

$list_fon = new listado();
$tip_fon = $list_fon -> tipo_fondo($t_sql, $tip, $id_ent);

echo $tip_fon;
?>