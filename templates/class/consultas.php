<?php

include('conectarse.php');
	
class mysql{

	public function sql( $tip, $table, $val, $valC, $valU){ 
		
		$link = conectarse();
		mysqli_select_db($link, "aplicati_FIC");
		
		/*
			$tip = es el tipo de consulta que se realizara; 
			$table = son las tablas o la tabla en donde queremos trabajar;
			$val = son los valores usuario;
			$valC = son los valores sql;
		*/
		
		if($tip == 'S'){
		
			$sql = "SELECT * FROM ";
			$max = sizeof($table);
			$cont = 1;
			
			for($i = 0; $i < $max; $i++){
				
				if($max == 1 || $cont  >= $max) $sql = $sql.$table[$i];
				else if($cont < $max) $sql = $sql.$table[$i].", ";
				
				++$cont;
			}
			
			$sql = $sql.' WHERE ';
			$max = sizeof($val);
			$cont = 1;
			
			for($k = 0; $k < $max; ++$k){
			
				if($max == 1 || $cont == $max) $sql = $sql.$valC[$k]." = ".$val[$k]."";
				else if($cont < $max) $sql = $sql.$valC[$k]." = ".$val[$k]." AND ";
				
				++$cont;
			}
			
			if($valU != "") $sql = $sql." ORDER BY ".$valU[0];
			//echo $sql."<br />";
			
		}elseif($tip == 'I'){
		
			$sql = "INSERT INTO ";
			$max_tab = sizeof($table);
			
			for($k = 0; $k < $max_tab; ++$k){
				if($max_tab == 1 || $cont == $max_tab) $sql = $sql.$table[$k]."(";
				else $sql = $sql.$table[$k].",";
			}
			
			$max = sizeof($val);
			$value = ")VALUES(";
			$cont = 1;
			
			for($i = 0; $i < $max; ++$i){
				
				if($max == 1 || $cont == $max){
					$sql = $sql."`".$valC[$i]."`";
					$value = $value."'".$val[$i]."')";
				}elseif($cont < $max) {
					$sql = $sql."`".$valC[$i]."`,";
					$value = $value."'".$val[$i]."',";
				}
				
				++$cont;
			}
			
			$sql = $sql.$value;
			//echo $sql."<br />";
		}elseif($tip == 'IP'){
		
			$sql = "INSERT INTO ";
			$max_tab = sizeof($table);
			
			for($k = 0; $k < $max_tab; ++$k){
				if($max_tab == 1 || $cont == $max_tab)$sql = $sql.$table[$k]."(";
				else $sql = $sql.$table[$k].",";
			}
			
			$max = sizeof($valC);
			$value = ")VALUES(";
			$cont = 1;
			
			for($i = 0; $i < $max; ++$i){
				
				if($max == 1 || $cont == $max){
					$sql = $sql."`".$valC[$i]."`";
				}elseif($cont < $max) {
					$sql = $sql."`".$valC[$i]."`,";
				}
				
				++$cont;
			}
			
			
			$col = 1;
			$fil = 0;
			foreach ($val as $v1) {
			
				if($fil != 0) $value = $value."),(";
				$max_value = sizeof($v1);
				++$fil;
				foreach ($v1 as $v2) {
					if($col < $max_value){ $value = $value."'".$v2."',"; }
					else{ $value = $value."'".$v2."'"; $col = 0;}
					++ $col;
				}
			}
			
			$value = $value.");";
			$sql = $sql.$value;
			//echo $sql."<br />";
		}elseif($tip == 'U'){
			
			$sql = "UPDATE ";
			$max = sizeof($table);
			$cont = 1;
			
			for($i = 0; $i < $max; $i++){
				if($max == 1 || $cont == $max) $sql = $sql.$table[$i];
				else $sql = $sql.$table[$i].",";
				++$cont;
			}
			
			$sql = $sql.' SET ';
			$max = sizeof($val);
			$cont = 1;
			
			for($k = 0; $k < $max; ++$k){
				if($max == 1 || $cont == $max) $sql = $sql.$valC[$k]." = '".$val[$k]."'";
				else $sql = $sql.$valC[$k]." = '".$val[$k]."', ";
				++$cont;
			}
			
			$sql = $sql.' WHERE ';
			$max = sizeof($valU);
			$cont = 1;
			
			for($l = 0; $l < $max; ++$l){
				if($max == 1 || $cont == $max) $sql = $sql.$valU[$l];
				else $sql = $sql.$valU[$l]." AND ";
				++$cont;
			}
			//echo $sql.'<br />';
		}
		
		$result = mysqli_query($link, $sql);
		return $result;
	}
	
	public function sql_ultimo($table){
		
		$link = conectarse();
		mysqli_select_db($link, "aplicati_FIC");
		
		$sql = "SELECT MAX(id) AS id FROM ".$table;
		$result = mysqli_query($link, $sql);
		$row = mysqli_fetch_array($result);
		return $row[0];
	}
	
	public function num_col($table, $db){
		
		$link = conectarse();
		mysqli_select_db($link, "aplicati_FIC");
		
		$sql = "SELECT count(*) FROM information_schema.`COLUMNS` C 
				WHERE table_name = '".$table."' AND 
				TABLE_SCHEMA = '".$db."'";
		$result = mysqli_query($link, $sql)or die('Error:'.mysql_error());
		$row = mysqli_fetch_array($result);
		
		return $row[0];
	}
}
?>