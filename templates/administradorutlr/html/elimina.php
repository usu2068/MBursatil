<?php
	include('../../class/consultas.php');
	
	$id = $_POST['id'];
	$tipo = $_POST['tipo'];
	$consult = new mysql();
	
	if($tipo == 'E'){
	
		$table = array('jo33_FIC_categories');
		$val = array('0');
		$valC = array('published');
		$valU = array('id = '.$id);
		
		$sql_elim_ent = $consult -> sql('U', $table, $val, $valC, $valU);
	
	}elseif($tipo = 'U'){
		
		$table = array('jo33_FIC_users');
		$val = array('1');
		$valC = array('block');
		$valU = array('id = '.$id);
		
		$sql_elim_ent = $consult -> sql('U', $table, $val, $valC, $valU);
		
	}
?>