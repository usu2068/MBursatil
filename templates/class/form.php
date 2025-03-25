<?php 
include_once('consultas.php');

class form{

	function fmr_entidad($id, $class){
		
		$js="javascript:".$class."('".$id."');";
		
		$form='		
			<div class="panel panel-primary">
				<div class="panel-heading">Creación de Entidad</div>
				<div style="margin: 10px;" id = "mjs_ent"></div>
				<div class="panel-body">
					<form role="form" method="post" action="">
					  <div class="form-group">
						<label for="nombre_ent">Nombre Entidad</label>
						<input type="text" class="form-control" id="nombre_ent" placeholder="Nombre Completo">
					  </div>
					  <div class="form-group">
						<label for="nit_ent">Nit</label>
						<input type="text" class="form-control" id="nit_ent" placeholder="Nit">
					  </div>
					  <div class="form-group">
						<label for="logo_ent">Logo</label>
						<input type="text" class="form-control" id="logo_ent" placeholder="Logo">
					  </div>
					  <a href="'.$js.'" class="btn btn-ar btn-primary">Crear</a>
					</form>
				</div>
			</div>'
		;
		
		return $form;
	}
	
	function fmr_usuario($id,$tits,$cont, $input,$ids,$js,$tip){
		
		$ar_js = json_encode($ids);		
		$cons = new mysql();
		$sty = 'col-md-6';
		
		if($tip != 'value'){
			$txt_btn = 'Crear';
			$div_ms = 'mjs_usu_new';
			$table = array('jo33_FIC_categories','jo33_FIC_content');
			$val = array($id,'jo33_FIC_content.catid');
			$valC = array('jo33_FIC_content.alias','jo33_FIC_categories.parent_id');
		}else{	
			$txt_btn = 'Editar';
			$div_ms = 'mjs_usu_edi_'.$id;
			$table = array('jo33_FIC_categories','jo33_FIC_content');
			$val = array($id,'jo33_FIC_content.catid','jo33_FIC_categories.parent_id');
			$valC = array('jo33_FIC_content.alias','jo33_FIC_categories.id','jo33_FIC_categories.parent_id');
			
			$sql_prins = $cons -> sql('S',$table,$val,$valC,$valU);
			$row_prins = mysqli_fetch_array($sql_prins);
			
			$table = array('jo33_FIC_categories');
			$val = array($row_prins[2]);
			$valC = array('parent_id');
		}
		
		$id = '"'.$id.'"';
		$txt_btn = '"'.$txt_btn.'"';
		
		$js="<a href='javascript:".$js."(".$id.",".$ar_js.", ".$txt_btn.");' class='btn btn-ar btn-primary'>".$txt_btn."</a>";
		
		$sql_prins = $cons -> sql('S',$table,$val,$valC,$valU);
		
		$form='	<div class="panel panel-primary"><div class="panel-heading">Creación de Usuario</div><div style="margin: 10px;" id = "'.$div_ms.'"></div><div class="panel-body"><form role="form" method="post" action="">';
		
		for($i = 0; $i<count($tits); ++$i){
			$texto = $cont[$i];
			
			for($k=0;$k<3;++$k) 
				$p = $p.$texto[$k];
			
			if($p == '<p>'){
				$cont[$i] = '';
				$char = strlen($texto);
				$char = $char - 4;
				for($j=3;$j<$char;++$j)
						$cont[$i]=$cont[$i].$texto[$j];
			}else $p = '';

			if($input[$i] != 'select')
				$form = $form.'<div class="form-group '.$sty.'"><label for="'.$ids[$i].'">'.$tits[$i].'</label><input type="'.$input[$i].'" class="form-control" id="'.$ids[$i].'" '.$tip.'="'.$cont[$i].'"></div>';
			else {
				$form = $form.'	<div class="form-group '.$sty.'"><label for="'.$ids[$i].'">'.$tits[$i].'</label><select class="form-control" id="'.$ids[$i].'"><option value="0">Seleccione una Entidad</option>';
				
				while($row_prins = mysqli_fetch_array($sql_prins)){
					$form = $form.'<option value='.$row_prins[0].'>'.$row_prins[8].'</option>';
				}
				
				$form = $form.'</select></div>';
			}
		}
		
		$form = $form.$js.'</form></div></div>';
		return $form;
	}
	
}
?>