<?php 

	include_once('consultas.php');
	include_once('form.php');

	class listado{
		
		function tablas($table_lis, $id_cat_pri, $parent, $tip){
			
			$conslt = new mysql();
			
			$table = array($table_lis);
			$val = array($id_cat_pri, '1');
			$valC = array($parent, 'published');
			
			$columnas = $conslt->num_col($table_lis, 'aplicati_FIC');
			$sql_list = $conslt->sql('S', $table, $val, $valC, $valU);
			
			$table= '<table class="table">';
			
			if($tip == 'E'){
			
				$table = $table.'<tr><th>Nombre</th><th>Nit</th><th>Editar</th><th>Eliminar</th></tr>';
				
				for($i=0; $i<=4; ++$i){			
					while($row_list = mysqli_fetch_array($sql_list)){
						$table = $table.'<tr><td>'.$row_list[8].'</td><td>'.$row_list[9].'</td><td><a class="btn btn-info" data-toggle="modal" data-target="#modal_edit_'.$row_list[0].'"><i class="fa fa-pencil"></i></a></td><td><a class="btn btn-danger" data-toggle="modal" data-target="#modal_elim_'.$row_list[0].'"><i class="fa fa-trash-o"></i></a></td></tr>';
						$modal_edit = $modal_edit.'<div class="modal fade" id="modal_edit_'.$row_list[0].'" tabindex="-1" role="dialog" aria-labelledby="modal_edit" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">'.$this->frm_edit('E', $row_list[0]).'</div></div></div>';
						$js_elim = "javascript:eliminar('".$row_list[0]."', 'E')";
						
						$modal_elim = $modal_elim.'	<div class="modal fade" id="modal_elim_'.$row_list[0].'" tabindex="-1" role="dialog" aria-labelledby="modal_elim" aria-hidden="true">
														<div class="modal-dialog">
														
															<div class="modal-content alert alert-danger in"> 
																<div class="modal-header">
																	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																	<strong>Eliminar '.$row_list[8].'</strong>
																</div>
																<div class="modal-body">
																	<p>Esta seguro que desea eliminar esta entidad.</p>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
																	<a href="'.$js_elim.'" class="btn btn-danger"><i class="fa fa-times"></i>Eliminar</a>
																</div>
															</div>
															
														</div>
													</div>';
					}
				}
				
			}else if($tip == 'U'){
				
				while($row_list = mysqli_fetch_array($sql_list)){
				
					$table_sql = array('jo33_FIC_content','jo33_FIC_users');
					$val = array($row_list[0],'jo33_FIC_users.id','0');
					$valC = array('jo33_FIC_content.catid','jo33_FIC_content.alias','jo33_FIC_users.block');
					
					$sql_usuXent = $conslt -> sql('S', $table_sql, $val, $valC, $valU);
					$row_usuXent = mysqli_num_rows($sql_usuXent);
					
					if($row_usuXent == 0)
						echo'<div class="alert alert-info" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Atención!</strong> No existe ningun usuario registrado para la entidad <strong>'.$row_list[8].'</strong></div>';
					else{
						
						$table = $table.'<h3>Usuarios de la entidad '.$row_list[8].'</h3><tr><th>Nombre</th><th>Cedula</th><th>Usuario</th><th>Editar</th><th>Eliminar</th></tr>';
						
						while($row_usuXent = mysqli_fetch_array($sql_usuXent)){
							
							$table = $table.'<tr><td>'.$row_usuXent[2].'</td><td>'.$row_usuXent[4].'</td><td>'.$row_usuXent[32].'</td><td><a class="btn btn-info" data-toggle="modal" data-target="#modal_edit_usu'.$row_usuXent[3].'"><i class="fa fa-pencil"></i></a></td><td><a class="btn btn-danger" data-toggle="modal" data-target="#modal_elim_usu'.$row_usuXent[3].'"><i class="fa fa-trash-o"></i></a></td></tr>';
							
							$modal_edit = $modal_edit.'<div class="modal fade" id="modal_edit_usu'.$row_usuXent[3].'" tabindex="-1" role="dialog" aria-labelledby="modal_edit" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">'.$this->frm_edit('U', $row_usuXent[3]).'</div></div></div>';
							
							$js_elim_usu = "javascript:eliminar('".$row_usuXent[3]."', 'U')";
							$modal_elim = $modal_elim.'	<div class="modal fade" id="modal_elim_usu'.$row_usuXent[3].'" tabindex="-1" role="dialog" aria-labelledby="modal_elim" aria-hidden="true">
															<div class="modal-dialog">
															
																<div class="modal-content alert alert-danger in"> 
																	<div class="modal-header">
																		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
																		<strong>Eliminar !!</strong>
																	</div>
																	<div class="modal-body">
																		<p>Esta seguro que desea eliminar el registro del funcionario <strong>'.$row_usuXent[2].'</strong>.</p>
																	</div>
																	<div class="modal-footer">
																		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
																		<a href="'.$js_elim_usu.'" class="btn btn-danger"><i class="fa fa-times"></i>Eliminar</a>
																	</div>
																</div>
																
															</div>
														</div>';
						}
					}
				}
			}
			
			$table = $table.'</table>'.$modal_elim.$modal_edit;
			return $table;
		}
		
		function frm_edit($tip, $id){
			
			$conslt = new mysql();
			
			if($tip == 'E'){
				
				$val = array($id);
				$valC = array('id');
				$table = array('jo33_FIC_categories');
				
				$sql_ent = $conslt -> sql('S', $table, $val, $valC, $valU);
				$row_ent = mysqli_fetch_array($sql_ent);
				
				$frm = '<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="myModalLabel">Edición </h4>
						</div>
						
						<div class="modal-body">
							<div class="panel panel-primary">
								<div class="panel-heading">Edición de Entidad</div>
								<div style="margin: 10px;" id = "mjs_edi"></div>
								<div class="panel-body">
								
									<form role="form" method="post" action="">
									  <div class="form-group">
										<label for="nombre_edi_'.$row_ent[0].'">Nombre Entidad</label>
										<input type="text" class="form-control" id="nombre_edi_'.$row_ent[0].'" value="'.$row_ent[8].'">
									  </div>
									  <div class="form-group">
										<label for="nit_edi_'.$row_ent[0].'">Nit</label>
										<input type="text" class="form-control" id="nit_edi_'.$row_ent[0].'" value="'.$row_ent[9].'">
									  </div>
									  <div class="form-group">
										<label for="logo_edi_'.$row_ent[0].'">Logo</label>
										<input type="text" class="form-control" id="logo_edi_'.$row_ent[0].'" placeholder="Logo">
									  </div>
									</form>
									
								</div>
							</div> 
							
						</div>
		
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<a href="javascript:edita('.$row_ent[0].')" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Guardar Cambios</a>
						</div>';
						
			}elseif($tip == 'U'){
				
				$table = array('jo33_FIC_content','jo33_FIC_users');
				$val = array($id,'jo33_FIC_users.id');
				$valC = array('jo33_FIC_users.id','jo33_FIC_content.alias');
				
				$sql_usu = $conslt -> sql('S', $table, $val, $valC, $valU);
				$row_usu = mysqli_fetch_array($sql_usu);
				$nom_usu = explode('  ',$row_usu[2]);
				
				$tits = array('Entidad','Cédula','Nombre','Apellido','Email','Usuario','Pass','Confirmación Pass');
				$cont = array('', $row_usu[4], $nom_usu[0], $nom_usu[1], $row_usu[33],$row_usu[32],'','');
				$input = array('select', 'text', 'text', 'text', 'email', 'text', 'password', 'password');
				$ids = array('nom_ent_edi', 'ced_usu_edi', 'nom_usu_edi', 'ape_usu_edi', 'ema_usu_edi', 'user_edi', 'pass_usu_edi', 'cpass_usu_edi');
				$js = 'g_usu';
				
				$frm_edi_usu = new form();
				$frm = $frm_edi_usu -> fmr_usuario($id, $tits, $cont, $input, $ids, $js,'value'); 
			
			}
			
			return $frm;
		}
		
		function tipo_fondo($tab_sql, $tip, $id_entJ){
				
				$conslt = new mysql();
				
				$ids_grp_eco = array();
				$pos_grp = 0;
				
				if($tip == 0){
				
					$table_tip = array($tab_sql);
					$valC = array('active','trash');
					$val = array('1','1');
					$sql_tip = $conslt -> sql('S', $table_tip, $val, $valC, $valU);
					$table= '<table class="table"><tr><th>Nombre</th><th>Prohibiciones</th><th>Políticas</th><th>Eliminar</th></tr>';
					
					$modal_ed_pr = 'modal_edit_tipF_proh';
					$modal_ed_po = 'modal_edit_tipF_pol_';
					$modal_el = 'modal_elim_tipF_';
					
				}else if($tip == 1){
					
					$table_tip = array($tab_sql, 'jo33_FIC_categories', 'jo33_FIC_UTLR_entidad');
					$valC = array('jo33_FIC_categories.parent_id', 'jo33_FIC_UTLR_entidad.id_joomla', $tab_sql.'.id_entidad', $tab_sql.'.active', $tab_sql.'.trash');
					$val = array($id_entJ, 'jo33_FIC_categories.id', 'jo33_FIC_UTLR_entidad.id', '1', '1');
					$sql_tip = $conslt -> sql('S', $table_tip, $val, $valC, $valU);
					
					$table = '<table class="table">';
					$modal_ed_pr = 'modal_edit_fon_proh';
					$modal_ed_po = 'modal_edit_fon_pol_';
					$modal_el = 'modal_elim_fon_';
					
				}
				//echo $tab_sql."<br />".$tip."<br />".$modal_ed_pr."<br />".$modal_ed_po."<br />".$modal_el;
				while($row_tip = mysqli_fetch_array($sql_tip)){
				
					if($tip == 1 && $id_ent != $row_tip[6]){
					
						/*
							- Organización por Entidad
							* Se obtienen datos de la entidad
							* Se imprime html con el resultado
							* Modal de la Carga de archivos
						*/
					
						$table_ent = array('jo33_FIC_UTLR_fondos', 'jo33_FIC_UTLR_entidad','jo33_FIC_categories');
						$valC_ent = array('jo33_FIC_UTLR_fondos.id', 'jo33_FIC_UTLR_entidad.id','jo33_FIC_UTLR_entidad.id_joomla');
						$val_ent = array($row_tip[0], 'jo33_FIC_UTLR_fondos.id_entidad', 'jo33_FIC_categories.id');
						$sql_ent = $conslt -> sql('S', $table_ent, $val_ent, $valC_ent, $valU);
						$row_ent = mysqli_fetch_array($sql_ent);
						
						$table = $table.'<th colsan=3><h4>Fondos de '.$row_ent[22].'</h4></th><th><a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal_carg_'.$row_ent[6].'"><i class="fa fa-cloud-upload"></i> Carga Archivos</a></th>';
						$table= $table.'<tr><th>Nombre</th><th>Prohibiciones</th><th>Políticas</th><th>Eliminar</th></tr>';
						$id_ent = $row_ent[6];
						
						$modals = $modals.'
						<div class="modal fade" id="modal_carg_'.$row_ent[6].'" tabindex="-1" role="dialog" aria-labelledby="modal_carg_'.$row_ent[6].'" aria-hidden="true">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<h4 class="modal-title" id="myModalLabel">Cargar Archivos para '.$row_ent[22].'</h4>
									</div>
									<div class="modal-body">
										<form action="../templates/class/upLoad.php" method="post" target="_blank" enctype="multipart/form-data">
											<input type="hidden" name="ent" value="'.$row_ent[6].'"/>
											<input type="hidden" name="ent_n" value="'.$row_ent[22].'"/>
											<h5>Carga Archivo (.csv)</h5>
											<input class="form-control" name="arch_dep" type="file" /><br />
											<input class="form-control" name="fech_dep" id="fech_dep" type="text" value="'.date('Y-m-d').'" /><br />
											<input type="submit" target="_blank"class="btn btn-info" value="Subir" />
										</form>
									</div>
								</div>
							</div>
						</div>';

						$table_act = array('jo33_FIC_UTLR_total_activos_fon');
						$valC_act = array('id_fondos');
						$val_act = array($row_tip[0]);
						
						$sql_act = $conslt -> sql('S', $table_act, $val_act, $valC_act, $valU);
						$row_act = mysqli_num_rows($sql_act);
						
						if($row_act == 0){
							$max_inver = 0;
							$min_inver = 0;
							$max_depo = 0;
							$min_depo = 0;
							$max_tota = 0;
							$min_tota = 0;
						}else {
							$row_act = mysqli_fetch_array($sql_act);
							$max_inver = $row_act['max_inver'];
							$min_inver = $row_act['min_inver'];
							$max_depo = $row_act['max_depo'];
							$min_depo = $row_act['min_depo'];
							$max_tota = $row_act['max_tot'];
							$min_tota = $row_act['min_tot'];
						}					
					}
				/*PRAMETRIZACIÓN ACTIVOS*/
					$activ = '	
							<table class="table">
								<tr style="background-color: #B20000; text-align: center;">
									<td><h4 style="color: #ffffff;">Composición de los Activos de FIC</h4></td>
								</tr>
							</table>
							<table class="table">
								<tr style="background-color: #666666;">
									<th style="color: #ffffff;">Nombre</th>
									<th style="color: #ffffff;">Min</th>
									<th style="color: #ffffff;">Max</th>
								</tr>
								<tr id="tr_inv'.$row_tip[0].'">
									<td>PORTAFOLIO DE INVERSIONES</td>
									<td><input id="min_inv'.$row_tip[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$min_inver.' %"></td>
									<td><input id="max_inv'.$row_tip[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_inver.' %"></td>
								</tr>
								<tr id="tr_dep'.$row_tip[0].'">
									<td>DEPÓSITOS BANCARIOS</td>
									<td><input id="min_dep'.$row_tip[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$min_depo.' %"></td>
									<td><input id="max_dep'.$row_tip[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_depo.' %"></td>
								</tr>
								<tr style="background-color: #B1B1B1;">
									<td>TOTAL ACTIVOS</td>
									<td><input id="min_tota'.$row_tip[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$min_tota.' %"></td>
									<td><input id="max_tota'.$row_tip[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_tota.' %"></td>
								</tr>
							</table>
							<table class="table">
								<tr style="background-color: #B1B1B1;">
									<td>Maximo Promedio Ponderado</td>
									<td><input id="max_prompo'.$row_tip[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$row_tip[2].'"></td>
								</tr>
							</table>
						';
					/*FIN ACTIVOS*/
					
					/* PARAMETRIZACIÓN GRUPOS ECONOMICOS */
					
						$grp_eco = '	
							<table class="table">
								<tr style="background-color: #B20000; text-align: center;">
									<td><h4 style="color: #ffffff;">Composición de los Gupos Economicos</h4></td>
								</tr>
							</table>
							<table class="table">
							
								<tr style="background-color: #666666;">
									<th style="color: #ffffff;">Nombre</th>
									<th style="color: #ffffff;">Max Inversion</th>
									<th style="color: #ffffff;">Max Depositos</th>
									<th style="color: #ffffff;">Max Total</th>
								</tr>';
							
							$table_grp_eco = array('jo33_FIC_UTLR_grupos_eco');
							$valC_grp_eco = array('active', 'trash');
							$val_grp_eco = array(1, 1);
							
							$sql_grp_eco = $conslt->sql('S',$table_grp_eco, $val_grp_eco, $valC_grp_eco, $valU);
							
							while($row_grp_eco = mysqli_fetch_array($sql_grp_eco)){
								
								$table_grp_ecoXfn = array('jo33_FIC_UTLR_fondos_X_grupos_eco');
								$valC_grp_ecoXfn = array('id_fondos', 'id_grupos_eco');
								$val_grp_ecoXfn = array($row_tip[0], $row_grp_eco['id']);
								
								$sql_grp_ecoXfn = $conslt->sql('S', $table_grp_ecoXfn, $val_grp_ecoXfn, $valC_grp_ecoXfn, $valU);
								$row_grp_ecoXfn = mysqli_fetch_array($sql_grp_ecoXfn);
								
								if(count($row_grp_ecoXfn) <= 1){
									$max_inv_grp = 0;
									$max_dep_grp = 0;
									$max_tot_grp = 0;
								}else {
									$max_inv_grp = $row_grp_ecoXfn['max_inv'];
									$max_dep_grp = $row_grp_ecoXfn['max_dep'];
									$max_tot_grp = $row_grp_ecoXfn['max_tot'];
								}
								
								$grp_eco .= '								
								<tr>
									<td>'.utf8_encode($row_grp_eco['nombre']).'</td>
									<td><input id="max_inv_grp'.$row_tip[0].$row_grp_eco[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_inv_grp.' %"></td>
									<td><input id="max_dep_grp'.$row_tip[0].$row_grp_eco[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_dep_grp.' %"></td>
									<td><input id="max_tot_grp'.$row_tip[0].$row_grp_eco[0].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_tot_grp.' %"></td>
								</tr>';
								
								$ids_grp_eco[$pos_grp] = $row_grp_eco[0];
								$pos_grp ++;
								
							}
						
						$grp_eco .= '</table>';
					
					/*FIN GRUPOS ECONOMICOS*/
					
					
					$table = $table.'<tr><td>'.utf8_decode($row_tip[1]).'</td> <td><a class="btn btn-info" data-toggle="modal" data-target="#'.$modal_ed_pr.$row_tip[0].'"><i class="fa fa-cogs"></i></a></td> <td><a class="btn btn-info" data-toggle="modal" data-target="#'.$modal_ed_po.$row_tip[0].'"><i class="fa fa-cogs"></i></a></td><td><a class="btn btn-danger" data-toggle="modal" data-target="#'.$modal_el.$row_tip[0].'"><i class="fa fa-trash-o"></i></a></td></tr>';
					
					$modals = $modals.'
							<div class="modal fade" id="'.$modal_ed_pr.$row_tip[0].'" tabindex="-1" role="dialog" aria-labelledby="modal_prohibiciones" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
											<h4 class="modal-title" id="myModalLabel">Prohibiciones de '.utf8_decode($row_tip[1]).'</h4>
										</div>
										<div class="modal-body">
											'.$this -> prohibiciones($tip ,$row_tip[0]).'
										</div>
									</div>
								</div>
							</div>
							
							<div class="modal fade" id="'.$modal_ed_po.$row_tip[0].'" tabindex="-1" role="dialog" aria-labelledby="modal_prohibiciones" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
											<h4 class="modal-title" id="myModalLabel">Politicas de '.utf8_decode($row_tip[1]).'</h4>
										</div>
										<div class="modal-body">
											'.$activ.'
											'.$grp_eco.'
											'.$this -> politicas($tip ,$row_tip[0], $ids_grp_eco).'
										</div>
									</div>
								</div>
							</div>
							
							<div class="modal fade" id="'.$modal_el.$row_tip[0].'" tabindex="-1" role="dialog" aria-labelledby="modal_elim_fon" aria-hidden="true">
								<div class="modal-dialog modal-lg">
									<div class="modal-content alert alert-danger">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
											<h4 class="modal-title" id="myModalLabel">Eliminar '.utf8_decode($row_tip[1]).'</h4>
										</div>
										<div class="modal-body">
											<strong>Atención!</strong> Está seguro de eliminar el tipo de fondo '.utf8_decode($row_tip[1]).'
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
											<a href="javascript:elim_tip('.$row_tip[0].')" class="btn btn-danger"><i class="fa fa-times"></i>Eliminar</a>
										</div>
									</div>
									
								</div>
							</div>';
							
				}
				
				$table = $table.'</table>'.$modals;
				
				return $table;
		}	
		
		function prohibiciones($tip, $id){
			
			$conslt = new mysql();
			$id_comp = 0;
			$ids_tit = array();
			$cont = 0;
			
			/*
				*	DECLARAMOS LA CONSULTA DE ACUERDO AL TIPO
			*/
				
			// SQL POR TIPO DE FONDO
			if($tip == 0){
				
				$table_pr_fn = array(
					'jo33_FIC_UTLR_prohibiciones_tip', 
					'jo33_FIC_UTLR_compocicion_pro_tip', 
					'jo33_FIC_UTLR_titulos_pro_tip',
					'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip',
					'jo33_FIC_UTLR_nom_compocicion_pro',
					'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip',
					'jo33_FIC_UTLR_nom_titulo_pro'
				);
				
				$valC_pr_fn = array(
					'jo33_FIC_UTLR_prohibiciones_tip.id_tipos_cartera',
					'jo33_FIC_UTLR_compocicion_pro_tip.id_prohibiciones_tip',
					'jo33_FIC_UTLR_titulos_pro_tip.id_compocicion_pro_tip',
					'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip.id_compocicion_pro_tip',
					'jo33_FIC_UTLR_nom_compocicion_pro.id',
					'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip.id_titulos_pro_tip',
					'jo33_FIC_UTLR_nom_titulo_pro.id'
				);
				$val_pr_fn = array(
					$id,
					'jo33_FIC_UTLR_prohibiciones_tip.id',
					'jo33_FIC_UTLR_compocicion_pro_tip.id',
					'jo33_FIC_UTLR_compocicion_pro_tip.id',
					'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip.id_nom_compocicion_pro',
					'jo33_FIC_UTLR_titulos_pro_tip.id',
					'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip.id_nom_titulo_pro'
				);
				
				$sql_pr_fn = $conslt -> sql('S', $table_pr_fn, $val_pr_fn, $valC_pr_fn, $valU);
				
			// SQL POR FONDO
			}else if($tip == 1){
				
				$table_pr_fn = array(
					'jo33_FIC_UTLR_prohibiciones_fon', 
					'jo33_FIC_UTLR_compocicion_pro_fon', 
					'jo33_FIC_UTLR_titulos_pro_fon',
					'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon',
					'jo33_FIC_UTLR_nom_compocicion_pro',
					'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon',
					'jo33_FIC_UTLR_nom_titulo_pro'
				);
				
				$valC_pr_fn = array(
					'jo33_FIC_UTLR_prohibiciones_fon.id_fondos',
					'jo33_FIC_UTLR_compocicion_pro_fon.id_prohibiciones_fon',
					'jo33_FIC_UTLR_titulos_pro_fon.id_compocicion_pro_fon',
					'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon.id_compocicion_pro_fon',
					'jo33_FIC_UTLR_nom_compocicion_pro.id',
					'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon.id_titulos_pro_fon',
					'jo33_FIC_UTLR_nom_titulo_pro.id'
				);
				
				$val_pr_fn = array(
					$id,
					'jo33_FIC_UTLR_prohibiciones_fon.id',
					'jo33_FIC_UTLR_compocicion_pro_fon.id',
					'jo33_FIC_UTLR_compocicion_pro_fon.id',
					'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon.id_nom_compocicion_pro',
					'jo33_FIC_UTLR_titulos_pro_fon.id',
					'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon.id_nom_titulo_pro'
				);
				
				$sql_pr_fn = $conslt -> sql('S', $table_pr_fn, $val_pr_fn, $valC_pr_fn, $valU);
				
			}
			
			$table = '<table class="table" style="width: 100%;">';
			$table .= '<div id="gua_pr"></div>';
			$table .= '<tr><td><a href="jascript:guard_para('.$tip.', '.json_encode($ids).');" class="btn btn-warning" data-toggle="popover" data-placement="right"><i class="fa fa-floppy-o"></i> Guardar parametros</a></td></tr>';
			
			$pos_id_comp = 14;
			$pos_nom_comp = 18;
			$pos_id_tit = 9;
			$pos_nom_tit = 23;
			$pos_min_pr = 11;
			$pos_max_pr = 10;
			
			while($row_pr_fn = mysqli_fetch_array($sql_pr_fn)){
			
				if($id_comp != $row_pr_fn[$pos_id_comp] || $id_comp == 0){
					
					$table .= '<tr><td colspan="3"> <h4>'.utf8_decode($row_pr_fn[$pos_nom_comp]).'</h4> </td><tr>';
					$table .= '
						<tr>
							<th>Nombre</th>
							<th>Min</th>
							<th>Max</th>
						</tr>';
					$id_comp = $row_pr_fn[$pos_id_comp];
				}
				
				$table .= '
					<tr>
						<td>'.utf8_decode($row_pr_fn[$pos_nom_tit]).'</td>
						<td>
							<input id="min_pr_'.$tip.$row_pr_fn[$pos_id_tit].'"class="form-control" type="text" placeholder="'.$row_pr_fn[$pos_min_pr].' %">
						</td>
						<td>
							<input id="max_pr_'.$tip.$row_pr_fn[$pos_id_tit].'" class="form-control" type="text" placeholder="'.$row_pr_fn[$pos_max_pr].' %">
						</td>
					</tr>';
				
				$ids_tit[$cont] = $row_pr_fn[$pos_id_tit];
				$cont++;
				
			}
			
			
			$js_guarda = "href='javascript:guard_para_proh(".$tip.", ".json_encode($ids_tit).");'";
			
			$table = $table.'<tr><td><a '.$js_guarda.' class="btn btn-warning" data-toggle="popover" data-placement="right"><i class="fa fa-floppy-o"></i> Guardar parametros</a></td></tr>';
			$table = $table.'<tr><td><div id="gua_pr_II"></div></td></tr>';
			
			$table .= '</table>';
			
			return $table;
			/*
			$nom_comp = array();
			$nom_tit = array();
			
			$table_nom_comp = array('jo33_FIC_UTLR_nom_compocicion_pro');
			$valC_nom_comp = array('active');
			$val_nom_comp = array('1');
			$sql_nom_comp = $conslt -> sql('S', $table_nom_comp, $val_nom_comp, $valC_nom_comp, $valU);
			
			$table_nom_tit = array('jo33_FIC_UTLR_nom_titulo_pro');
			$valC_nom_tit = array('active');
			$val_nom_tit = array('1');
			$sql_nom_tit = $conslt -> sql('S', $table_nom_tit, $val_nom_tit, $valC_nom_tit, $valU);
			
			$opc_comp = '<option value="0">-- Seleccione Nombre de la compocición --</option>';
			$opc_tit = '<option value="0">-- Sleccione Nombre --</option>';
			
			$ids_tit = array();
			$cont_tp = 0;
			
			while($row_nom_comp = mysqli_fetch_array($sql_nom_comp)){
			
				$nom_comp [$row_nom_comp['id']] = $row_nom_comp['nombre'];
				$opc_comp = $opc_comp.'<option value="'.$row_nom_comp['id'].'">'.utf8_encode($row_nom_comp['nombre']).'</option>';
			}
			
			while($row_nom_tit = mysqli_fetch_array($sql_nom_tit)){
			
				$nom_tit [$row_nom_tit['id']] = $row_nom_tit['nombre'];
				$opc_tit = $opc_tit.'<option value="'.$row_nom_tit['id'].'">'.utf8_encode($row_nom_tit['nombre']).'</option>';
			}
			
			if($tip == 0){
			
			//- declaramos consulta para las prohibiciones por tipo de fondo
				
				$table_pr = array('jo33_FIC_UTLR_prohibiciones_tip');
				$val_pr = array($id);
				$valC_pr = array('id_tipos_cartera');
				$sql_pr = $conslt -> sql('S', $table_pr, $val_pr, $valC_pr, $valU);
				
			//- declaramos consulta para las compociciones por tipo de fondo
				
				$table_pr_com = array('jo33_FIC_UTLR_compocicion_pro_tip', 'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip');
				$valC_pr_com = array('jo33_FIC_UTLR_compocicion_pro_tip.id_prohibiciones_tip', 'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_tip.id_compocicion_pro_tip');
				
			//- declaramos consulta para ls titulos por tipo de fondo
				
				$table_pr_tit = array('jo33_FIC_UTLR_titulos_pro_tip', 'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip');
				$valC_pr_tit = array('jo33_FIC_UTLR_titulos_pro_tip.id_compocicion_pro_tip', 'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_tip.id_titulos_pro_tip');
				$vals_pr_tit = 'jo33_FIC_UTLR_titulos_pro_tip';
				
				$id_popver_new_com = 'pop_pro_t';
				$id_popver_new_tit = 'pop_tit_t';
				$vals = 'jo33_FIC_UTLR_compocicion_pro_tip';
			
			}elseif($tip == 1){
				
			//- declaramos consulta para las prohibiciones por fondo
				
				$table_pr = array('jo33_FIC_UTLR_prohibiciones_fon');
				$val_pr = array($id);
				$valC_pr = array('id_fondos');
				$sql_pr = $conslt -> sql('S', $table_pr, $val_pr, $valC_pr, $valU);
				
			//- declaramos consulta para las compociciones por fondo
				
				$table_pr_com = array('jo33_FIC_UTLR_compocicion_pro_fon','jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon');
				$valC_pr_com = array('jo33_FIC_UTLR_compocicion_pro_fon.id_prohibiciones_fon', 'jo33_FIC_UTLR_nom_compocicion_pro_X_compocicion_pro_fon.id_compocicion_pro_fon');
				
			//- declaramos consulta para ls titulos por tipo de fondo
				
				$table_pr_tit = array('jo33_FIC_UTLR_titulos_pro_fon', 'jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon');
				$valC_pr_tit = array('jo33_FIC_UTLR_titulos_pro_fon.id_compocicion_pro_fon','jo33_FIC_UTLR_nom_titulo_pro_X_titulos_pro_fon.id_titulos_pro_fon');
				$vals_pr_tit = 'jo33_FIC_UTLR_titulos_pro_fon';
				
				$id_popver_new_com = 'pop_pro_f';
				$id_popver_new_tit = 'pop_tit_f';
				
				$style_btn = 'visibility: hidden';
				$vals = 'jo33_FIC_UTLR_compocicion_pro_fon';
			}
			
			
			$table = '<table class="table" style="width: 100%;">';
			$table = $table.'<div id="gua_pr"></div>';
			$table = $table.'<tr><td><a href="jascript:guard_para('.$tip.', '.json_encode($ids).');" class="btn btn-warning" data-toggle="popover" data-placement="right"><i class="fa fa-floppy-o"></i> Guardar parametros</a></td></tr>';
			
			//consulta la existencia de prohibiciones para el tipo de fondo
				
			$row_num = mysqli_num_rows($sql_pr);
				
			if($row_num != 0){
				
			//consulta la existencia de compociciones para las prohibiciones
			
				$row_pr = mysqli_fetch_array($sql_pr);	
				
				$val_pr_com = array($row_pr[0], $vals.'.id');
				$sql_pr_com = $conslt -> sql('S', $table_pr_com, $val_pr_com, $valC_pr_com, $valU);
				
				$row_num = mysqli_num_rows($sql_pr_com);
				
				if($row_num != 0){
					
				//consulta la existencia de titulos para las compociciones
				
					$select = '<select class="form-control" id="sel_comp"><option value="0"> -- Nivel --</option>';
					
					while($row_pr_com = mysqli_fetch_array($sql_pr_com)){
					
						$val_pr_tit = array($row_pr_com[0], $vals_pr_tit.'.id');
						$sql_pr_tit = $conslt -> sql('S', $table_pr_tit, $val_pr_tit, $valC_pr_tit, $valU);
						//print_r($nom_tit);
						$select = $select.'<option value="'.$row_pr_com[0].'">'.utf8_decode($row_pr_com[1]).'</option>';
						$table = $table.'<tr><th><h4>'.utf8_encode($nom_comp[$row_pr_com[5]]).'</h4></th></tr>';
					
					/* 
						*	PopVer creación Titulos
					/
					
						$id_popver_new_tit = $id_popver_new_tit.$row_pr_com['id'];
						$js_env = "javascript:new_tit_pro(".$row_pr_com['id'].")";
						$title = 'Creación de Títulos:';
						$contenido = '<div id="msj_newtit_c'.$row_pr_com[0].'"></div><select type="text" class="form-control" id="new_tit_nom'.$row_pr_com[0].'">'.$opc_tit.'</select><br /><input type="text" class="form-control" id="new_tit_max" placeholder="Maximo"></input><br /><input type="text" class="form-control" id="new_tit_min" placeholder="Minimo"></input><br /><a href="'.$js_env.'" class="btn btn-info"><i class="fa fa-plus-square-o"></i> Crear</a>';
						
						$row_num = mysqli_num_rows($sql_pr_tit);
						
						if($row_num != 0){
							
							$table = $table.'<tr><td><table class="table">';
							$table = $table.'<tr><th>Nombre</th><th>Min</th><th>Max</th></tr>';
							
							while($row_pr_tit = mysqli_fetch_array($sql_pr_tit)){
								
								$ids_tit[$cont_tp] = $row_pr_tit['id'];
								
								$table = $table.'<tr id="tr_pr_'.$row_pr_tit['id'].'"><td>'.utf8_encode($nom_tit[$row_pr_tit[6]]).'</td><td width="100px"><input id="min_pr_'.$tip.$row_pr_tit['id'].'"class="form-control" style="width: 80px;" type="text" placeholder="'.$row_pr_tit['min'].' %"></td><td width="100px"><input id="max_pr_'.$tip.$row_pr_tit['id'].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$row_pr_tit['max'].' %"></td></tr>';
								
								++$cont_tp;
							}
							
							$table=$table.'<tr><td><a href="#" id="'.$id_popver_new_tit.'" class="btn btn-info" style="'.$style_btn.'" data-toggle="popover" data-placement="right"><i class="fa fa-plus-square-o"></i> Nuevo Título</a></td></tr></table></td></tr>';
							
							$script = $script.$this -> pop_ver($contenido, $id_popver_new_tit, $title);
							
						//Cuando no exite ningún titulo en la compocición
						
						}else{
							
							$table=$table.'<tr><td><div class="alert alert-warning" role="alert"><p>La compocición aún no contiene titulos.<p><a href="#" id="'.$id_popver_new_tit.'" class="btn btn-info" style="'.$style_btn.'" data-toggle="popover" data-placement="right"><i class="fa fa-plus-square-o"></i> Nuevo Título</a></div></td></tr>';
							
							$script = $script.$this -> pop_ver($contenido, $id_popver_new_tit, $title);
							
						}
						
						$title_com = 'Creación de Compociciones:';
						$parent_com = 1;
						$js_env_com = "javascript:new_pro_pol(".$row_pr_com[4].",".$parent_com.",1,2,".$tip.")";
						$select = $select.'</select>';
						$id_popver_new_com = $id_popver_new_com.$row_pr_com['id'];
						
					}
					
					$table = $table.'<tr><td><a href="#" id="'.$id_popver_new_com.'" class="btn btn-info" style="'.$style_btn.'" data-toggle="popover" data-placement="right"><i class="fa fa-plus-square-o"></i> Nueva Compocición</a></td></tr>';
					
					$table = $table.'</tr></td></table>';
					
					$contenido_com = '<div id="msj_newpro"></div>  <select type="text" class="form-control" id="new_pro_txt">'.$opc_comp.'</select>   <br /><a href="'.$js_env_com.'" class="btn btn-info"><i class="fa fa-plus-square-o"></i> Crear</a>';
					
					$script = $script.$this -> pop_ver($contenido_com, $id_popver_new_com, $title_com);
					
				}
			//Cuando no existe prohibiciones
			}else {

					$title = 'Creación de Compociciones:';
					$parent = 0;
					$js_env = "javascript:new_pro_pol(".$id.",".$parent.",1,1,".$tip.")";
					$id_popver_new_com = $id_popver_new_com.$row_pr_com[0];
					
					$table = $table.'<tr><td><div class="alert alert-warning" role="alert"><p>Las prohibiciones aún no contiene compociciones.</p>
					<a href="#" id="'.$id_popver_new_com.'" style="'.$style_btn.'" class="btn btn-info" data-toggle="popover" data-placement="right"><i class="fa fa-plus-square-o" ></i> Nueva Compocición</a></div></td></tr>';
					
					$contenido = '<div id="msj_newpro"></div><select type="text" class="form-control" id="new_pro_txt">'.$opc_comp.'</select><br /><a href="'.$js_env.'" class="btn btn-info"><i class="fa fa-plus-square-o"></i> Crear</a>';
					
					$script = $script.$this -> pop_ver($contenido, $id_popver_new_com, $title);
					
				}
				
			$js_guarda = "href='javascript:guard_para_proh(".$tip.", ".json_encode($ids_tit).");'";
			
			$table = $table.'<tr><td><a '.$js_guarda.' class="btn btn-warning" data-toggle="popover" data-placement="right"><i class="fa fa-floppy-o"></i> Guardar parametros</a></td></tr>';
			$table = $table.'<tr><td><div id="gua_pr_II"></div></td></tr>';
			$table = $table.'</table>'.$script;
			
			return $table;*/
		}
		
		function politicas($tip, $id, $ids_grp_eco){
			
			$conslt = new mysql();
			
			/*
				* CUANDO LAS POLITICAS SON DE TIPO DE FONDO
			*/
			if($tip == 0){
				
				/*
				
					SELECT * 
					FROM 
						jo33_FIC_UTLR_tipos_cartera, 
						jo33_FIC_UTLR_politicas_tip, 
						jo33_FIC_UTLR_compocicion_pol_tip, 
						jo33_FIC_UTLR_nom_compocicion, 
						jo33_FIC_UTLR_titulos_pol_tip, 
						jo33_FIC_UTLR_nom_tiulo, 
						jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip, 
						jo33_FIC_UTLR_grupos_pol_tip, 
						jo33_FIC_UTLR_nom_tot 
					WHERE 
						jo33_FIC_UTLR_tipos_cartera.id = 5 AND 
						jo33_FIC_UTLR_politicas_tip.tipos_cartera_id = jo33_FIC_UTLR_tipos_cartera.id AND 
						jo33_FIC_UTLR_compocicion_pol_tip.id_politicas_tip = jo33_FIC_UTLR_politicas_tip.id AND 
						jo33_FIC_UTLR_nom_compocicion.id = jo33_FIC_UTLR_compocicion_pol_tip.id_nom_compocicion AND 
						jo33_FIC_UTLR_titulos_pol_tip.id_compocicion_pol_tip = jo33_FIC_UTLR_compocicion_pol_tip.id AND 
						jo33_FIC_UTLR_nom_tiulo.id = jo33_FIC_UTLR_titulos_pol_tip.id_nom_tiulo AND 
						jo33_FIC_UTLR_tipos_cartera.active = 1 AND 
						jo33_FIC_UTLR_tipos_cartera.trash = 1 AND 
						jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip.id_titulos_pol_tip = jo33_FIC_UTLR_titulos_pol_tip.id AND 
						jo33_FIC_UTLR_grupos_pol_tip.id = jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip.id_grupos_pol_tip AND 
						jo33_FIC_UTLR_grupos_pol_tip.tipo = "tot" AND 
						jo33_FIC_UTLR_nom_tot.id = jo33_FIC_UTLR_grupos_pol_tip.id_nom_tot

				*/
				
				// SELECIÓN DE LOS TIPOS DE FONDO
				$table_pol = array(
					'jo33_FIC_UTLR_tipos_cartera', 
					'jo33_FIC_UTLR_politicas_tip', 
					'jo33_FIC_UTLR_compocicion_pol_tip',
					'jo33_FIC_UTLR_nom_compocicion',
					'jo33_FIC_UTLR_titulos_pol_tip', 
					'jo33_FIC_UTLR_nom_tiulo',
					'jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip',
					'jo33_FIC_UTLR_grupos_pol_tip',
					'jo33_FIC_UTLR_nom_tot'				
				);
				
				$valC_pol = array(
					'jo33_FIC_UTLR_tipos_cartera.id',
					'jo33_FIC_UTLR_politicas_tip.tipos_cartera_id',
					'jo33_FIC_UTLR_compocicion_pol_tip.id_politicas_tip',
					'jo33_FIC_UTLR_nom_compocicion.id',
					'jo33_FIC_UTLR_titulos_pol_tip.id_compocicion_pol_tip',
					'jo33_FIC_UTLR_nom_tiulo.id',
					'jo33_FIC_UTLR_tipos_cartera.active',
					'jo33_FIC_UTLR_tipos_cartera.trash',
					'jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip.id_titulos_pol_tip',
					'jo33_FIC_UTLR_grupos_pol_tip.id',
					'jo33_FIC_UTLR_nom_tot.id'
				);
				
				$val_pol = array(
					$id,
					'jo33_FIC_UTLR_tipos_cartera.id',
					'jo33_FIC_UTLR_politicas_tip.id',
					'jo33_FIC_UTLR_compocicion_pol_tip.id_nom_compocicion',
					'jo33_FIC_UTLR_compocicion_pol_tip.id',
					'jo33_FIC_UTLR_titulos_pol_tip.id_nom_tiulo',
					1, 1,
					'jo33_FIC_UTLR_titulos_pol_tip.id',
					'jo33_FIC_UTLR_titulos_pol_tip_X_grupos_pol_tip.id_grupos_pol_tip',
					'jo33_FIC_UTLR_grupos_pol_tip.id_nom_tot'
				);
				
				$sql_pol = $conslt -> sql('S', $table_pol, $val_pol, $valC_pol);
				
				$table_parent = 'jo33_FIC_UTLR_compocicion_pol_tip';
				
			// POSICIONES EN LA CONSULTA DE LAS COMPOSICIONES
				$pos_id_comp = 8;
				$pos_nom_comp = 15;
				$pos_parent = 9;
				
			// POSICIONES EN LA CONSULTA DE LOS TITULOS
				$pos_id_tit = 18;
				$pos_nom_tit = 26;
				$pos_min_tit = 20;
				$pos_max_tit = 19;
				$pos_tip_tot = 35;
				
			// POSICIONES EN LA CONSULTA DE LOS TOTALES
				$pos_id_tot = 32;
				$pos_nom_tot = 40;
				$pos_min_tot = 34;
				$pos_max_tot = 33;
				
				
			/*
				* CUANDO LAS POLITICAS SON DE LOS FONDOS DE LAS ENTIDADES
			*/
			}elseif($tip == 1){
				
				$table_pol = array(
					'jo33_FIC_UTLR_fondos',
					'jo33_FIC_UTLR_politicas_fon',
					'jo33_FIC_UTLR_compocicion_pol_fon',
					'jo33_FIC_UTLR_nom_compocicion',
					'jo33_FIC_UTLR_titulos_pol_fon',
					'jo33_FIC_UTLR_nom_tiulo',
					'jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon',
					'jo33_FIC_UTLR_grupos_fon',
					'jo33_FIC_UTLR_nom_tot');
				
				$valC_pol = array(
					'jo33_FIC_UTLR_fondos.id',
					'jo33_FIC_UTLR_politicas_fon.id_fondos',
					'jo33_FIC_UTLR_compocicion_pol_fon.id_politicas_fon',
					'jo33_FIC_UTLR_nom_compocicion.id',
					'jo33_FIC_UTLR_titulos_pol_fon.id_compocicion_pol_fon',
					'jo33_FIC_UTLR_nom_tiulo.id',
					'jo33_FIC_UTLR_fondos.active',
					'jo33_FIC_UTLR_fondos.trash',
					'jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon.id_titulos_pol_fon',
					'jo33_FIC_UTLR_grupos_fon.id',
					'jo33_FIC_UTLR_nom_tot.id');
					
				$val_pol = array(
					$id,
					'jo33_FIC_UTLR_fondos.id',
					'jo33_FIC_UTLR_politicas_fon.id',
					'jo33_FIC_UTLR_compocicion_pol_fon.id_nom_compocicion',
					'jo33_FIC_UTLR_compocicion_pol_fon.id',
					'jo33_FIC_UTLR_titulos_pol_fon.id_nom_tiulo',
					1, 1,
					'jo33_FIC_UTLR_titulos_pol_fon.id',
					'jo33_FIC_UTLR_titulos_pol_fon_X_grupos_fon.id_grupos_fon',
					'jo33_FIC_UTLR_grupos_fon.id_nom_tot');
					
				$sql_pol = $conslt -> sql('S', $table_pol, $val_pol, $valC_pol);
				//$sql_pol_II = $conslt -> sql('S', $table_pol, $val_pol, $valC_pol);
				
				$table_parent = 'jo33_FIC_UTLR_compocicion_pol_fon';
				/*$table_parent_nom = 'jo33_FIC_UTLR_nom_compocicion';*/
				
			// POSICIONES EN LA CONSULTA DE LAS COMPOSICIONES
				$pos_id_comp = 12;
				$pos_nom_comp = 19;
				$pos_parent = 13;
				
			// POSICIONES EN LA CONSULTA DE LOS TOTALES
				$pos_id_tit = 22;
				$pos_nom_tit = 30;
				$pos_min_tit = 24;
				$pos_max_tit = 23;
				$pos_tip_tot = 39;
				
			// POSICIONES EN LA CONSULTA DE LOS TOTALES
				$pos_id_tot = 36;
				$pos_nom_tot = 44;
				$pos_min_tot = 38;
				$pos_max_tot = 37;
			}
			
			$html = '<table class="table">';
			$comp = 0;
			$tot = 0;
			$pos_tit = 0;
			$pos_tot = 0;
			$parent = 0;
			$idst = array();
			$idto = array();
			$num_cal = 0;
			$com = 0;
			//$row_next = mysqli_fetch_array($sql_pol_II);
			
			while($row_pol = mysqli_fetch_array($sql_pol)){
			
			
				/* IF filtra totales de las secciones */
				
				if($row_pol[$pos_tip_tot] == 'tot'){
				
					if($tot != $row_pol[$pos_id_tot] && $tot != 0){
					
					// HTML TOTALES
						$list = $list.'
						<tr style="background-color: #B1B1B1; color: #ffffff">
							<th style="width: 500px;" >'.utf8_encode($nom_tot).'</th>
							<th><input id="mint_'.$tot.'" class="form-control" style="width: 80px;" type="text" placeholder="'.$min_tot.' %"></th>
							<th><input id="maxt_'.$tot.'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_tot.' %"></th>
							<!--th>Relacionar</th-->
						</tr>';
						
						$idto[$pos_tot] = $tot;
						$tot = $row_pol[$pos_id_tot];
						$nom_tot = $row_pol[$pos_nom_tot];
						$min_tot = $row_pol[$pos_min_tot];
						$max_tot = $row_pol[$pos_max_tot];
						++ $pos_tot;
					}
					
					//$row_next = mysqli_fetch_array($sql_pol_II);
					
					if($row_pol[$pos_parent] != $parent && $parent != 0){
						
						$table_sql_parent = array($table_parent, 'jo33_FIC_UTLR_nom_compocicion');
						$valC_parent = array($table_parent.'.id', $table_parent.'.id_nom_compocicion');
						$val_parent = array($parent, 'jo33_FIC_UTLR_nom_compocicion.id');
						$sql_parent = $conslt -> sql('S', $table_sql_parent, $val_parent, $valC_parent, $valU);
						$row_parent = mysqli_fetch_array($sql_parent);
						
						$parent = $row_pol[$pos_parent];
						
						$list .= '<tr style="background-color: #FFFFFF; color: #ffffff"> <td><br></td><td></td><td></td> </tr>';
						
						$html .= '
						<tr style="background-color: #FFFFFF; color: #ffffff"> 
							<td colspan = "3"><h2> '.utf8_encode($row_parent[7]).' </h2></td></tr>'.$list;
						
						$list = '';
						
					}else $parent = $row_pol[$pos_parent];
					
					// HTML COMPOSICIONES
					if($comp != $row_pol[$pos_id_comp]){
					
						$list = $list.$comp_tot.'
						<tr style="background-color: #B20000; text-align: center;">
							<th colspan="3"> 
								<h4 style="color: #ffffff">'.utf8_encode($row_pol[$pos_nom_comp]).'</h4> 
							</th>
						</tr>
						<tr style="background-color: #666666; color: #ffffff">
							<th style="width: 500px;" >Nombre</th>
							<th>Min</th>
							<th>Max</th>
							<!--th>Relacionar</th-->
						</tr>';
						$comp_tot = '';
						$com = 0;
						// VALOR INICIAL DE LOS TOTALES
						if($comp == 0){
							$tot = $row_pol[$pos_id_tot];
							$nom_tot = $row_pol[$pos_nom_tot];
							$min_tot = $row_pol[$pos_min_tot];
							$max_tot = $row_pol[$pos_max_tot];
						}
						$comp = $row_pol[$pos_id_comp];
					}
					
					//HTML TITULOS
					$list = $list.'
					<tr>
						<td>'.utf8_encode($row_pol[$pos_nom_tit]).'</td>
						<td><input id="min_'.$row_pol[$pos_id_tit].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$row_pol[$pos_min_tit].' %"></td>
						<td><input id="max_'.$row_pol[$pos_id_tit].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$row_pol[$pos_max_tit].' %"></td>
					</tr>';
					
					$idst[$pos_tit] = $row_pol[$pos_id_tit];
					++ $pos_tit;
				
				}else if($row_pol[$pos_tip_tot] == 'com'){
					//$tot = $row_pol[$pos_id_tot];
					$nom_comp_tot = $row_pol[$pos_nom_tot];
					$min_comp_tot = $row_pol[$pos_min_tot];
					$max_comp_tot = $row_pol[$pos_max_tot];
					
					
					if($com < 1){
						$comp_tot .= '
							<tr style="background-color: #B1B1B1; color: #ffffff">
								<th style="width: 500px;" > '.utf8_encode($nom_comp_tot).' </th>
								<th> <input id="mint_'.$row_pol[$pos_id_tot].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$min_comp_tot.' %"> </th>
								<th> <input id="maxt_'.$row_pol[$pos_id_tot].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_comp_tot.' %"> </th>
							</tr>';
						$idto[$pos_tot] = $row_pol[$pos_id_tot];
						++ $pos_tot;
					}
					++ $com;
					
				}else if($num_cal < 5){
					$nom_comp_tot = $row_pol[$pos_nom_tot];
					$min_comp_tot = $row_pol[$pos_min_tot];
					$max_comp_tot = $row_pol[$pos_max_tot];
					
					$cal_tot .= '
					<tr style="background-color: #B1B1B1; color: #ffffff">
						<th style="width: 500px;" >'.utf8_encode($nom_comp_tot).'</th>
						<th><input id="mint_'.$row_pol[$pos_id_tot].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$min_comp_tot.' %"></th>
						<th><input id="maxt_'.$row_pol[$pos_id_tot].'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_comp_tot.' %"></th>
					</tr>';
					
					$idto[$pos_tot] = $row_pol[$pos_id_tot];
					++ $pos_tot;
					
					$num_cal ++;
				}
			}
			
			$html .= $list;
			
			$html = $html.'
			<tr style="background-color: #B1B1B1; color: #ffffff">
				<th style="width: 500px;" >'.utf8_encode($nom_tot).'</th>
				<th><input id="mint_'.$tot.'" class="form-control" style="width: 80px;" type="text" placeholder="'.$min_tot.' %"></th>
						<th><input id="maxt_'.$tot.'" class="form-control" style="width: 80px;" type="text" placeholder="'.$max_tot.' %"></th>
				<!--th>Relacionar</th-->
			</tr>'.$cal_tot;
			
			$idto[$pos_tot] = $tot;
			
			$js_guarda = "href='javascript:guard_para_pol($tip, ".json_encode($idst).", ".json_encode($idto).", $id, ".json_encode($ids_grp_eco).");'";
			
			$html = $html.'<tr><td><a '.$js_guarda.' class="btn btn-warning" data-toggle="popover" data-placement="right"><i class="fa fa-floppy-o"></i> Guardar parametros</a></td></tr>';			
			$html = $html.'</table>';
			
			return $html;
		}
		
		function paginacion($sql_tit_asig, $tip){
			
			$conslt = new mysql();
			
			if(mysqli_num_rows($sql_tit_asig) <= 12){
			
				while($row_tit_asig = mysqli_fetch_array($sql_tit_asig)){
				
					if($row_tit_asig[2] != 1){ $st_text = 'text-danger'; $icon = 'fa-times'; }
					else { $st_text = 'text-success'; $icon = 'fa-check'; }
					
					$table_tit_351 = array('jo33_FIC_UTLR_titulos');
					$valC_tit_351 = array('id');
					$val_tit_351 = array($row_tit_asig[0]);
					$sql_tit_351 = $conslt -> sql('S', $table_tit_351, $val_tit_351, $valC_tit_351, $valU);
					$row_tit_351 = mysqli_fetch_array($sql_tit_351);
					
					$contenido_asig_tit .= '<a href="javascript:cam_est_tit('.$row_tit_351[0].', '.$row_tit_asig[1].', '.$row_tit_asig[2].', '.$tip.')" class="btn btn-link '.$st_text.'">'.$row_tit_351[1].' <i class="fa '.$icon.'"></i></a>';
				}
			//Paginacion titulos asignados
			}else{
				$num = 1;
				$cont = 1;
				$contenido_asig_tit = '<div class="tab-content"><div class="tab-pane active" id="pag'.$num.'">';
				$pag = '<ul class="pagination pagination-sm"><li class="active"><a href="#pag'.$num.'" data-toggle="tab">'.$num.'</a></li>';
				
				while($row_tit_asig = mysqli_fetch_array($sql_tit_asig)){
					
					$table_tit_351 = array('jo33_FIC_UTLR_titulos');
					$valC_tit_351 = array('id');
					$val_tit_351 = array($row_tit_asig[0]);
					$sql_tit_351 = $conslt -> sql('S', $table_tit_351, $val_tit_351, $valC_tit_351, $valU);
					$row_tit_351 = mysqli_fetch_array($sql_tit_351);
					
					if($row_tit_asig[2] != 1){ $st_text = 'text-danger'; $icon = 'fa-times'; }
					else { $st_text = 'text-success'; $icon = 'fa-check'; }
					
					$contenido_asig_tit = $contenido_asig_tit.'<a href="javascript:cam_est_tit('.$row_tit_351['id'].', '.$row_tit_asig[1].', '.$row_tit_asig[2].', '.$tip.')" class="'.$st_text.' btn btn-link">'.$row_tit_351[1].' <i class="fa '.$icon.'"></i></a>';
					$cont ++;
					
					if($cont > 12){
						$cont = 0;
						$num ++;
						$contenido_asig_tit = $contenido_asig_tit.'</div><div class="tab-pane" id="pag'.$num.'">';
						$pag = $pag.'<li><a href="#pag'.$num.'" data-toggle="tab">'.$num.'</a></li>';
					}
				}
				$contenido_asig_tit = $contenido_asig_tit.'</div></div>';
				$contenido_asig_tit = $pag.'</ul>';
			}
			
			//$contenido_asig_tit = $contenido_asig_tit;
			return $contenido_asig_tit;
		}
		
		function pop_ver($contenido, $popver, $title){

			$script = "<script>
				jQuery(function() {
					jQuery('#".$popver."').popover({
						html : true,
						title: '".$title."',
						content: '".$contenido."',
						delay: { show: 500, hide: 100 }
					});
				});
			</script>";
			
			return $script;
		}
		
	}
	
?> 