<?php

	function conectarse(){

		$link=mysqli_connect("localhost","aplicati_FIC","FIC2014");
		  
		if(mysqli_connect_errno()){
			print_r("Error conectando con la base de datos: %s\n", mysqli_connect_error());
		    exit();
		}
		if (!mysqli_select_db($link, "aplicati_FIC")){
			echo "Error seleccionando la base de datos.";
			exit();
		}  
		return $link;    
	}
	
 ?>
