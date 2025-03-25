<?php
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
date_default_timezone_set('America/Bogota');

$consult = new mysql();

$nits_emis = json_decode($_POST['ids_emis']);
$cod_fon = $_POST['id_fondo'];

$sel_inv = json_decode($_POST['sel_inv']);
$c_inv = $_POST['c_inv'];

$sel_dep = json_decode($_POST['sel_dep']);
$c_dep = $_POST['c_dep'];

print_r($sel_inv);

if($c_inv != 0 || $c_dep != 0){	
	
	
	
	for($i = 1; $i<=count($nits_emis); ++$i){
		if($sel_inv[$i] != '' || $sel_dep[$i] != ''){
			
			$table_emis = array('jo33_FIC_UTLR_emisores');
			$valC_emis = array('nit', 'active', 'trash');
			
			//if($nits_emis[$i] != null){
			print_r(nits_emis);
			$val_emis = array($nits_emis[$i], 1,1);
			$sql_emis = $consult -> sql('S', $table_emis, $val_emis, $valC_emis, $valU);
			$row_emis = mysql_fetch_array($sql_emis);
			
			$table_fond = array('jo33_FIC_UTLR_fondos');
			$valC_fond = array('codigo');
			$val_fond = array($cod_fon);
			$sql_fond = $consult -> sql('S', $table_fond, $val_fond, $valC_fond, $valU);
			$row_fond = mysql_fetch_array($sql_fond);
			
			$id_fon = $row_fond['id'];
			
			$table_emiXfon = array('jo33_FIC_UTLR_emisores_x_fondos');
			$valC_emiXfon = array('id_emisores','id_fondos');
			$val_emiXfon = array($row_emis['id'], $id_fon);
			$sql_emiXfon = $consult -> sql('S', $table_emiXfon, $val_emiXfon, $valC_emiXfon, $valU);
			$row_emiXfon = mysql_fetch_array($sql_emiXfon);
			
		// GUARDA VALORES DE LOS EMISORES POR INVERSION
			if($sel_inv[$i] != ''){
				
				if(count($row_emiXfon) <= 1){
				
					$valC_emiXfon = array('id_emisores','id_fondos','max_inv');
					$val_emiXfon = array($row_emis['id'], $id_fon, $sel_inv[$i]);
					$sql_emiXfon = $consult -> sql('I', $table_emiXfon, $val_emiXfon, $valC_emiXfon, $valU);
					
					//echo 'Guardado Con Insert';
					
				}else{
					$valC_emisXfon = array('max_inv');
					$val_emiXfon = array($sel_inv[$i]);
					$valU_emiXfon = array('id_emisores ='.$row_emis['id'],'id_fondos ='.$id_fon,);
					$sql_comp_emi = $consult -> sql('U', $table_emiXfon, $val_emiXfon, $valC_emisXfon, $valU_emiXfon);
					
					//echo 'Guardado Con Update';
					
				}
			}
			//}
		// GUARDA VALORES DE LOS EMISORES POR DEPOSITOS
			if($sel_dep[$i] != ''){
				
				if(count($row_emiXfon) <= 1){
				
					$valC_emisXfon = array('id_emisores','id_fondos','max_dep');
					$val_emiXfon = array($row_emis['id'], $id_fon, $sel_dep[$i]);
					
					$sql_emiXfon = $consult -> sql('I', $table_emiXfon, $val_emiXfon, $valC_emiXfon, $valU);
					
					//echo 'Guardado Con Insert';
				
				}else{
				
					$valC_emisXfon = array('max_dep');
					$val_emiXfon = array($sel_dep[$i]);
					$valU_emiXfon = array('id_emisores ='.$row_emis['id'],'id_fondos ='.$id_fon,);
					$sql_comp_emi = $consult -> sql('U', $table_emiXfon, $val_emiXfon, $valC_emisXfon, $valU_emiXfon);		

					//echo 'Guardado Con Update';
					
				}
			}
			
			echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong> Parametrizacion Exitosa! </strong><br> Refrescando pagina...  </div>';
			
		}
	}
}
?>