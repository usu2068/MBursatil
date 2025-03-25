<?php
	include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
	date_default_timezone_set('America/Bogota');

	$consulta = new mysql();

	$nit_new_emi = $_POST['nit_emi'];
	$nom_new_emi = $_POST['nom_emi'];

	$table_emi = array('jo33_FIC_UTLR_emisores');
	$valC_emi_sel = array('nit');
	$val_emi_sel = array($nit_new_emi);
	
	$sql_emi_sel = $consulta -> sql('S', $table_emi, $val_emi_sel, $valC_emi_sel, $valU);
	
	$num_fil = 0;
	$num_fil += mysqli_num_rows($sql_emi_sel);

	if($num_fil <= 0){

		$valC_emi = array('id', 'nombre', 'nit', 'active', 'trash');
		$val_emi = array('', $nom_new_emi, $nit_new_emi, 1, 1);
		$sql_emi = $consulta -> sql('I', $table_emi, $val_emi, $valC_emi, $valU);
		
		echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong> Parametrizacion Exitosa! </strong><br> Refrescando pagina...  </div>';
		
	}else{ echo'<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong> Este Emisor ya Existe, Debe Revisar el Documento .CSV</strong><br> Refrescando pagina...  </div>'; }
?>