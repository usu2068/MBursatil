<?php 

include('../../class/consultas.php');
require_once "/home/aplicati/public_html/utlr/templates/class/PasswordHash.php";// Aqui se carga la libreria si el codigo que ustedes usan esta en nuestro host solo dejela como esta de lo contrario descargue la libreria y enrute aca

$tipo = $_POST['tipo'];

function josHashPassword($pass)	{   // $pass es la contraseña que elige el usuario sin encriptar
	
	$phpass = new PasswordHash(10, false);
	$crypt = $phpass->HashPassword($pass, PASSWORD_DEFAULT); 
	$hash = $crypt; 
	return $hash;
}

if($tipo == 'E'){
	
	$id = $_POST['id'];
	$nombre = $_POST['nombre'];
	$nit = $_POST['nit'];
	$image = $_POST['image'];

	
	$consult = new mysql();
	$table = array('jo33_FIC_categories');
	$val = array($nombre, $nit, $image);
	$valC = array('title','alias','description');
	$valU = array('id = '.$id);
	
	$sql_u_ent = $consult -> sql('U',$table,$val,$valC, $valU);
	
	echo '	<div class="alert alert-success span 4" role="alert">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span></button>
				<p>Entidad Editada Exitosamente.</p>
			</div>'
	;
}elseif($tipo == 'U'){
	
	$id = $_POST['id_admin'];
	$ent = $_POST['entidad'];
	$nom = $_POST['nombre'];
	$ape = $_POST['apellido'];
	$ced = $_POST['cedula'];
	$ema = $_POST['email'];
	$usu = $_POST['usuario'];
	$pass =  josHashPassword($_POST['pass']);
	
	$consult = new mysql();
	$table = array('jo33_FIC_users', 'jo33_FIC_content');
	$val = array($nom."  ".$ape, $ema, $usu, $pass, $nom."  ".$ape, "<p>".$ced."</p>", $ent);
	$valC = array('jo33_FIC_users.name', 'jo33_FIC_users.email', 'jo33_FIC_users.username', 'jo33_FIC_users.password', 'jo33_FIC_content.title', 'jo33_FIC_content.introtext', 'jo33_FIC_content.catid');
	$valU = array('jo33_FIC_users.id = '.$id, 'jo33_FIC_content.alias = jo33_FIC_users.id');
	
	$sql_u_usu = $consult -> sql('U',$table,$val,$valC, $valU);
	
	echo '	<div class="alert alert-success span 4" role="alert">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span></button>
				<p>Usuario Editado Exitosamente.</p>
			</div>';
}

?>