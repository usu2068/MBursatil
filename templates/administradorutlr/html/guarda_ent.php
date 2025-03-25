<?php 
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
date_default_timezone_set('America/Bogota');

$id_admin = $_POST['id_admin'];
$nombre = $_POST['nombre'];
$nit = $_POST['nit'];
$conslt = new mysql();

$table = array("jo33_FIC_content", "jo33_FIC_categories");
$val = array($id_admin, 'jo33_FIC_content.catid');
$valC = array('jo33_FIC_content.alias','jo33_FIC_categories.id');

//obtenemos la informacion del la entidad del usuario
$sql_ent_g = $conslt -> sql('S', $table, $val, $valC, $valU);
$row_ent_g = mysqli_fetch_array($sql_ent_g);

/*-HALLAMOS LA CATEGORIA PRINCIPAL*/
	$table = array("jo33_FIC_assets");
	$val = array("'com_content.category.".$row_ent_g[30]."'");
	$valC = array('name');

	$sql_asse_g = $conslt -> sql('S', $table, $val, $valC, $valU);
	$row_asse_g = mysqli_fetch_array($sql_asse_g);
	
	$lft_n = $row_asse_g[3];
	$rgt_n = $row_asse_g[3] + 1;

	$lft_n_hers = $row_asse_g[3] + 3;
	$rgt_n_hers = $row_asse_g[3] + 4;
	
/*-HALLAMOS LAS CATEGORIAS HERMANAS DE LA PRINCIPAL*/
	$table = array("jo33_FIC_assets");
	$val = array($row_asse_g[1]);
	$valC = array('parent_id');

	$sql_asse_hers = $conslt -> sql('S', $table, $val, $valC, $valU);

/*-RECORRE LAS HERMANAS Y ASIGNA EL CONSECUTIVO QUE LE CORRESPONDE*/
	while($row_asse_hers = mysqli_fetch_array($sql_asse_hers)){
	
		if($row_asse_hers[0] != $row_asse_g[0]){
		
			$table = array("jo33_FIC_assets");
			$val = array($lft_n_hers, $rgt_n_hers);
			$valC = array('lft','rgt');
			$valU = array('parent_id = '.$row_asse_hers[1], 'id = '.$row_asse_hers[0]);
			
			$sql_her_g = $conslt -> sql('U', $table, $val, $valC, $valU);
			
			$lft_n_hers = $lft_n_hers + 2;
			$rgt_n_hers = $rgt_n_hers + 2;
		
		}
	}

$ult_cat = $conslt -> sql_ultimo('jo33_FIC_categories');
$ult_cat = $ult_cat;

$rules_ent_g = '{"core.create":{"6":1,"3":1},"core.delete":{"6":1},"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1},"core.edit.own":{"6":1,"3":1}}';

$level = $row_ent_g[35] + 1;

/*- AQUI CREAMOS UN HIJO DE LA CATEGORIA PRINCIPAL */

$table = array('jo33_FIC_assets');
$val = array($row_asse_g[0], $lft_n, $rgt_n, $level, "com_content.category.".$ult_cat, $nombre, $rules_ent_g);
$valC = array('parent_id', 'lft', 'rgt', 'level', 'name', 'title', 'rules');

$sql_ent_pa = $conslt->sql('I', $table, $val, $valC, $valU);
++$rgt_n;
/*- ASIGNAMOS CONSECUTIVO CORRESPONDIENTE A LA CATEGORIA PRINCIPAL */

$table = array("jo33_FIC_assets");
$val = array($rgt_n);
$valC = array('rgt');
$valU = array('parent_id = '.$row_asse_g[1], 'id = '.$row_asse_g[0]);

$sql_pri_g = $conslt -> sql('U', $table, $val, $valC, $valU);

/*-AQUI EMPIEZA LA CREACIÓN DE LA CATEGORIA HIJA*/

$ult_ass = $conslt -> sql_ultimo('jo33_FIC_assets');
$asset_id = $ult_ass;

$lft_cat_hij = $row_ent_g[34];
$rgt_cat_hij = $lft_cat_hij + 1;
$rgt_cat_pri = $lft_cat_hij + 2;
$path = $row_ent_g[36].'/'.$nit;

$table = array('jo33_FIC_categories');
$val = array('', $asset_id, $row_ent_g[7], $lft_cat_hij, $rgt_cat_hij, $level, $path, 'com_content', $nombre, $nit, $image, "''", '1', '0', '0000-00-00 00:00:00', '1', '{"category_layout":"","image":""}', "''", "''", '{"author":"","robots":""}', $id_admin, date('Y-m-d H:i:s'), '0', '0000-00-00 00:00:00', '0', '*', '1');
$valC = array('id', 'asset_id', 'parent_id', 'lft', 'rgt', 'level', 'path', 'extension', 'title', 'alias', 'note', 'description', 'published', 'checked_out', 'checked_out_time', 'access', 'params', 'metadesc', 'metakey', 'metadata', 'created_user_id', 'created_time', 'modified_user_id', 'modified_time', 'hits', 'language', 'version');

$sql_ent_pa = $conslt->sql('I', $table, $val, $valC, $valU);

/*- ACTUALIZAMOS EL CONTADOR DE LA CATEGORIA PRINCIPAL */

$table = array("jo33_FIC_categories");
$val = array($rgt_cat_pri);
$valC = array('rgt');
$valU = array('id = '.$row_ent_g[7]);

$sql_pri_cat = $conslt -> sql('U', $table, $val, $valC, $valU);

echo '	<div class="alert alert-success fade in" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span></button>
			<p><strong>Atención!</strong> Entidad generada Exitosamente.</p>
		</div>'
	;
?>