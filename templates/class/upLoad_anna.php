<?php
//$upload_351="true";
$upload_depo="true";
$id_ent = $_POST['ent'];
$file_name = 'formato_anna';

$uploadedfile_size=$_FILES['arch_351'][size];
$fecha_fonds = $_POST['fech_dep'];


if ($_FILES[arch_dep][size]>25000000){
	$msg=$msg."Uno de los archivos es mayor que 25Mg, debes reduzcirlo antes de subirlo";
	//$upload_351="false";
	$upload_depo="false";
}

/*if (!($_FILES[arch_351][type] =="text/plain")){
	$msg=$msg." El archivo de 351 tiene que ser txt. Otros archivos No son permitidos";
	$upload_351="false";
}*/

/*if (!($_FILES[arch_dep][type] =="application/vnd.ms-excel")){
	$msg=$msg." El archivo de los depositos tiene que ser csv. Otros archivos No son permitidos";
	$upload_depo="false";
}*/

$add_depo="../../planos/anna/".$file_name.".csv";

if($upload_depo=="true"){
	if(move_uploaded_file ($_FILES[arch_dep][tmp_name], $add_depo)){
		echo "Archivo cargado con exito";
	}else{echo "Error al subir el archivo";}
}else{echo $msg;}
?>