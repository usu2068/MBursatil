<?php
	include_once('/home/aplicati/public_html/utlr/templates/class/consultas.php');
	
	$consult = new mysql();
	
	$ids_tit = json_decode($_POST['ids_tit']);
	$max = json_decode($_POST['maxs']);
	$min = json_decode($_POST['mins']);
	
	$tip = $_POST['tip'];
	
	if($tip == 0){
		$table_tit = array('jo33_FIC_UTLR_titulos_pro_tip');
	}elseif($tip == 1){
		$table_tit = array('jo33_FIC_UTLR_titulos_pro_fon');
	}
	
	for($i = 0; $i < count($ids_tit); ++$i){
		
		if($ids_tit[$i] != 0){
			
			$valC_tit = array('max', 'min');
			$val_tit = array($max[$i], $min[$i]);
			$valU_tit = array('id ='.$ids_tit[$i]);
			$sql_tit = $consult -> sql('U', $table_tit, $val_tit, $valC_tit, $valU_tit);
		}
	}
	
	echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Felicidades!</strong> La parametrización finalizo con exito.</div>';
	
?> 