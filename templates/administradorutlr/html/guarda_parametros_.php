<?php
	include_once('/home/aplicati/public_html/utlr/templates/class/consultas.php');
	
	$consult = new mysql();
	
	$ids_tit = json_decode($_POST['ids_tit']);
	$maxs_tit = json_decode($_POST['maxs']);
	$mins_tit = json_decode($_POST['mins']);
	
	$ids_tot = json_decode($_POST['ids_tot']);
	$maxs_tot = json_decode($_POST['maxst']);
	$mins_tot = json_decode($_POST['minst']);
	
	$min_inv = $_POST['min_inv'];
	$max_inv = $_POST['max_inv'];
	$min_dep = $_POST['min_dep'];
	$max_dep = $_POST['max_dep'];
	$min_tota = $_POST['min_tota'];
	$max_tota = $_POST['max_tota'];
	$id_fon = $_POST['id_fon'];
	
	echo $id_fon;
	
	$tip = $_POST['tip'];
	
	if($tip == 0){
		$table_tit = array('jo33_FIC_UTLR_titulos_pol_tip');
		$table_tot = array('jo33_FIC_UTLR_grupos_pol_tip');		
		
	}elseif($tip == 1){
		$table_tit = array('jo33_FIC_UTLR_titulos_pol_fon');
		$table_tot = array('jo33_FIC_UTLR_grupos_fon');
		
		$table_act = array('jo33_FIC_UTLR_total_activos_fon');
		$valC_act = array('id_fondos');
		$val_act = array($id_fon);		
		$sql_act = $consult -> sql('S', $table_act, $val_act, $valC_act, $valU);
		$row_act = mysql_num_rows($sql_act);
		
		if($row_act == 0){
			$valC_act = array('max_inver', 'min_inver', 'max_depo', 'min_depo', 'max_tot', 'min_tot', 'active', 'trash', 'id_fondos');
			$val_act = array($max_inv, $min_inv, $max_dep, $min_dep, $max_tota, $min_tota, 1, 1, $id_fon);
		
			$sql_act = $consult -> sql('I', $table_act, $val_act, $valC_act, $valU);
		}else{
			$valC_act = array('max_inver', 'min_inver', 'max_depo', 'min_depo', 'max_tot', 'min_tot');
			$val_act = array($max_inv, $min_inv, $max_dep, $min_dep, $max_tota, $min_tota);
			$valU_act = array('id_fondos = '.$id_fon);
			
			$sql_act = $consult -> sql('U', $table_act, $val_act, $valC_act, $valU_act);
		}
	}
	
	for($i=0; $i<count($ids_tit); ++$i){
		if($ids_tit[$i] != 0){
			
			$valC_tit = array('max', 'min');
			$val_tit = array();
			$valU_tit = array('id = '.$ids_tit[$i]);
			
			if($maxs_tit[$i] == '') $val_tit[0] = 0;
			elseif($mins_tit[$i] == '') $val_tit[1] = 0;
			else $val_tit = array($maxs_tit[$i], $mins_tit[$i]);
			
			if($maxs_tit[$i] != '' || $mins_tit[$i] != '') $sql_tit = $consult -> sql('U', $table_tit, $val_tit, $valC_tit, $valU_tit);
		}
	}
	
	for($i=0; $i<count($ids_tot); ++$i){
		
		$valC_tot = array('max', 'min');
		$val_tot = array();
		$valU_tot = array('id = '.$ids_tot[$i]);
		
		if($maxs_tot[$i] == '') $val_tot[0] = 0;
		elseif($mins_tot[$i] == '') $val_tot[1] = 0;
		else $val_tot = array($maxs_tot[$i], $mins_tot[$i]);
		
		if($maxs_tot[$i] != '' || $mins_tot[$i] != ''){
			$sql_tot = $consult -> sql('U', $table_tot, $val_tot, $valC_tot, $valU_tot);
		
		}
	}
	echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Felicidades!</strong> La parametrización finalizo con exito.</div>';
?>