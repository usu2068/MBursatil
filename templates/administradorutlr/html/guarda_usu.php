<?php 
include('/home/aplicati/public_html/utlr/templates/class/consultas.php');
require_once "/home/aplicati/public_html/utlr/templates/class/PasswordHash.php";// Aqui se carga la libreria si el codigo que ustedes usan esta en nuestro host solo dejela como esta de lo contrario descargue la libreria y enrute aca
date_default_timezone_set('America/Bogota');

function josHashPassword($pass)	{   // $pass es la contraseña que elige el usuario sin encriptar
	
	$phpass = new PasswordHash(10, false);
	$crypt = $phpass->HashPassword($pass, PASSWORD_DEFAULT); 
	$hash = $crypt; 
	return $hash;
}

$consult = new mysql();
$id_admin= $_POST['id_admin'];
$ent = $_POST['entidad'];
$nom = $_POST['nombre'];
$ape = $_POST['apellido'];
$ced = $_POST['cedula'];
$ema = $_POST['email'];
$usu = $_POST['usuario'];
$pass = josHashPassword($_POST['pass']);

$table = array('jo33_FIC_users');
$val = array("'".$usu."'");
$valC = array('username');

$sql_usu_exi = $consult -> sql('S', $table, $val, $valC, $valU);
$row_usu_exi = mysqli_num_rows($sql_usu_exi);

$table = array('jo33_FIC_users');
$val = array("'".$ema."'");
$valC = array('email');

$sql_ema_exi = $consult -> sql('S', $table, $val, $valC, $valU);
$row_ema_exi = mysqli_num_rows($sql_ema_exi);

$table = array('jo33_FIC_content');
$val = array("'<p>".$ced."</p>'");
$valC = array('alias');

$sql_ced_exi = $consult -> sql('S', $table, $val, $valC, $valU);
$row_ced_exi = mysqli_num_rows($sql_ced_exi);

if($row_usu_exi != 0) echo'<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Ya existe el usuario '.$usu.' por favor digite uno diferente e intente de nuevo.</div>';
else if($row_ced_exi != 0) echo'<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Ya existe un registro con el número de cedula '.$ced.' por favor digite uno diferente e intente de nuevo.</div>';
else if($row_ema_exi != 0) echo'<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> Ya existe un registro con el correo electronico '.$ema.' por favor digite uno diferente e intente de nuevo.</div>';

else{

	$params = '{"admin_style":"","admin_language":"","language":"","editor":"","helpsite":"","timezone":""}';

	$table = array('jo33_FIC_users');
	$val = array('', $nom." ".$ape, $usu, $ema, $pass, '0', '0', date('Y-m-d H:i:s'), '0000-00-00 00:00:00', '', $params, '0000-00-00 00:00:00', '0', '', '', '0');
	$valC = array('id', 'name', 'username', 'email', 'password', 'block', 'sendEmail', 'registerDate', 'lastvisitDate', 'activation', 'params', 'lastResetTime', 'resetCount', 'otpKey', 'otep', 'requireReset');

	$sql_usu = $consult -> sql('I', $table, $val, $valC, $valU);

	$table = array('jo33_FIC_categories','jo33_FIC_assets');
	$val = array($ent, 'jo33_FIC_categories.asset_id');
	$valC = array('jo33_FIC_categories.id','jo33_FIC_assets.id');

	$sql_ent_usu = $consult -> sql('S',$table,$val,$valC,$valU);
	$row_ent_usu = mysqli_fetch_array($sql_ent_usu);

	/*-ASIGNACION DE PERMISOS AL USUARIO*/
	if($row_ent_usu[5] > 3)	$niv_usu = 2;
	else $niv_usu = 3;

	$utl_usu = $consult -> sql_ultimo('jo33_FIC_users');
	$table = array('jo33_FIC_user_usergroup_map');
	$val = array($utl_usu, $niv_usu);
	$valC = array('user_id', 'group_id');

	$sql_usu_niv = $consult -> sql('I', $table, $val, $valC, $valU);
	/*-*/

	$lft_art = $row_ent_usu[30];
	$rtg_art = $lft_art + 1;
	$utl_art = $consult -> sql_ultimo('jo33_FIC_content');
	$utl_art = $utl_art + 1;
	$rules_art = '{"core.delete":{"6":1},"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1}}';
	$level_art = $row_ent_usu[31] + 1;

	$table = array('jo33_FIC_assets');
	$val = array('', $row_ent_usu[27], $lft_art, $rtg_art, $level_art, 'com_content.article.'.$utl_art, $nom." ".$ape, $rules_art);
	$valC = array('id', 'parent_id', 'lft', 'rgt', 'level', 'name', 'title', 'rules');

	$sql_ass_usu = $consult -> sql('I', $table, $val, $valC, $valU);
	echo $row_ent_usu[31]."<br />";
	$rtg_art = $rtg_art + 1;
	echo $rtg_art."<br />";
	$val = array($rtg_art);
	$valC = array('rgt');
	$valU = array('id = '.$ent);

	$sql_ass_ent = $consult -> sql('U', $table, $val, $valC, $valU);

	$val = array($row_ent_usu[28]);
	$valC = array('parent_id');

	$sql_ass_her = $consult -> sql('S',$table,$val,$valC,$valU);

	$lft_her = $rtg_art + 1;
	$rtg_her = $rtg_art + 2;
		
	while($row_ass_her = mysqli_fetch_array($sql_ass_her)){
		if($row_ass_her[0] != $row_ent_usu[27] && $row_ass_her[3] >= $rtg_art){
			
			$val = array($lft_her, $rtg_her);
			$valC = array('lft','rgt');
			$valU = array('id = '.$row_ass_her[0]);
			
			$sql_ass_hh = $consult -> sql('U', $table, $val, $valC, $valU);
			
			++$lft_her;
			++$rtg_her;
		}
	}

	$utl_ass = $consult -> sql_ultimo('jo33_FIC_assets');
	$id_usu_reg = $consult -> sql_ultimo('jo33_FIC_users');
	$fec_null = '0000-00-00 00:00:00';
	$images = '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}';
	$urls = '{"urla":false,"urlatext":"","targeta":"","urlb":false,"urlbtext":"","targetb":"","urlc":false,"urlctext":"","targetc":""}';
	$atribs = '{"show_title":"","link_titles":"","show_tags":"","show_intro":"","info_block_position":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}';
	$metadata = '{"robots":"","author":"","rights":"","xreference":""}';

	$table = array('jo33_FIC_content');
	$val = array('', $utl_ass, $nom."  ".$ape, $id_usu_reg, "<p>".$ced."</p>", '', '1', $ent, date('Y-m-d H:i:s'), $id_admin, '', $fec_null, '0', '0', $fec_null, date('Y-m-d H:i:s'), $fec_null, $images, $urls, $atribs, '1', '0', '', '', '1', '0', $metadata, '0', '*', '');
	$valC = array('id', 'asset_id', 'title', 'alias', 'introtext', 'fulltext', 'state', 'catid', 'created', 'created_by', 'created_by_alias', 'modified', 'modified_by', 'checked_out', 'checked_out_time', 'publish_up', 'publish_down', 'images', 'urls', 'attribs', 'version', 'ordering', 'metakey', 'metadesc', 'access', 'hits', 'metadata', 'featured', 'language', 'xreference');

	$sql_artXusu = $consult -> sql('I', $table, $val, $valC, $valU);
	
	echo'<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Felicidades!</strong> El usuario se creo satisfactoriamente.</div>';
}
?>