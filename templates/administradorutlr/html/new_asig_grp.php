<?php
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
date_default_timezone_set('America/Bogota');

$tip_grp = $_POST['tip_grp'];
$tip = $_POST['tip'];

$conslt = new mysql();

if($tip == 0){// creación y asignación de grupo
	
	$nom = $_POST['nom'];
	$max = $_POST['max'];
	$min = $_POST['min'];
	$id_tit = $_POST['id_tit'];
	
	$table_grp = array('jo33_FIC_UTLR_grupos_pol_tip');
	//$table_grp_fon = array('jo33_FIC_UTLR_grupos_pol_tip');
	$valC_grp = array('nombre', 'max', 'min', 'tipo');
	$val_grp = array($nom, $max, $min, $tip_grp);
	$sql_grp = $conslt -> sql('I', $table_grp, $val_grp, $valC_grp, $valU);
	
	$id_grp = $conslt -> sql_ultimo('jo33_FIC_UTLR_grupos_pol_tip');

}elseif($tip == 1){
	
	$id_tit = $_POST['id_tit'];
	$id_grp = $_POST['grp'];
	
}

// Busqueda de un titulo ya relacionado en el grupo
$table_rela = array('jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip');
$valC_rela = array('id_grupos_pol_tip');
$val_rela = array($id_grp);
$sql_rela = $conslt -> sql('S', $table_rela, $val_rela, $valC_rela, $valU);

$row_num = mysqli_num_rows($sql_rela);

if($row_num != 0){

	$row_tit = mysqli_fetch_array($sql_rela);

	// Comparacion si pertenece a la misma compocición del nuevo titulo
	$table_rela = array('jo33_FIC_UTLR_titulos_pol_tip');
	$valC_rela = array('id');
	$val_rela = array($row_tit[0]);
	$sql_rela = $conslt -> sql('S', $table_rela, $val_rela, $valC_rela, $valU);
	$row_tit = mysqli_fetch_array($sql_rela);

	$table_rela = array('jo33_FIC_UTLR_titulos_pol_tip');
	$valC_rela = array('id');
	$val_rela = array($id_tit);
	$sql_rela = $conslt -> sql('S', $table_rela, $val_rela, $valC_rela, $valU);
	$row_new_tit = mysqli_fetch_array($sql_rela);

	if($row_tit[7] === $row_new_tit[7]){ //comparacion de compociciones
		$table_rela = array('jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip');
		$valC_rela = array('id_titulos_pol_tip', 'id_grupos_pol_tip');
		$val_rela = array($id_tit, $id_grp);
		$sql_rela = $conslt -> sql('I', $table_rela, $val_rela, $valC_rela, $valU);
	}else{
		$js = "<script>
					jQuery.ajax({
						type: 'POST',
						dataType :  'html'
						success: function( data ){
							jQuery(id_btn).popover('hide');
						}
					});
				</script>";
		echo '<div class="alert alert-info" role="alert"><strong>Atención</strong> no es posible asignar este titulo al grupo ya que el titulo no pertenece a a la compocicion a la cual esta asignada el grupo.</div>'.$js;
		
	}
}else{
	$table_rela = array('jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip');
	$valC_rela = array('id_titulos_pol_tip', 'id_grupos_pol_tip');
	$val_rela = array($id_tit, $id_grp);
	$sql_rela = $conslt -> sql('I', $table_rela, $val_rela, $valC_rela, $valU);
}
?>