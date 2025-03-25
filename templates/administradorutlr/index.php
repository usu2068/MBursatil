<?php
include_once('/home/aplicati/public_html/utlr/templates/class/list.php');
include_once('/home/aplicati/public_html/utlr/templates/class/consultas.php');
include_once('/home/aplicati/public_html/utlr/templates/class/html.php');
include_once('/home/aplicati/public_html/utlr/templates/class/form.php');

defined('_JEXEC') or die;

$cuerpo = new body();
$head = $cuerpo -> header($this->template);
$footer = $cuerpo -> footer($this->template);

$fmr = new form();

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;
$idUsu = $user->get("id");
// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

/* Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScript('templates/' . $this->template . '/js/template.js');

// Add Stylesheets
$doc->addStyleSheet('templates/' . $this->template . '/css/template.css');*/

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);

// Adjusting content width
if ($this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span6";
}
elseif ($this->countModules('position-7') && !$this->countModules('position-8'))
{
	$span = "span9";
}
elseif (!$this->countModules('position-7') && $this->countModules('position-8'))
{
	$span = "span9";
}
else
{
	$span = "span12";
}

// Logo file or site title param
if ($this->params->get('logoFile'))
{
	$logo = '<img src="' . JUri::root() . $this->params->get('logoFile') . '" alt="' . $sitename . '" />';
}
elseif ($this->params->get('sitetitle'))
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . htmlspecialchars($this->params->get('sitetitle')) . '</span>';
}
else
{
	$logo = '<span class="site-title" title="' . $sitename . '">' . $sitename . '</span>';
}

echo $head;

?>
	<header class="main-header">
		<div class="container">
			<h1 class="page-title">Módulo Administrador</h1>
		</div>
	</header>

<!--CONTENIDO -->

	<div class="container">
		<div class="row">
		
		<!-- MENU IZQUIERDA -->
		<?php
			$conslt = new mysql();
			$table = array("jo33_FIC_content", "jo33_FIC_categories");
			$val = array($idUsu, 'jo33_FIC_content.catid');
			$valC = array('jo33_FIC_content.alias','jo33_FIC_categories.id');

			$sql_mycat = $conslt -> sql('S', $table, $val, $valC, $valU);
			
			$row_mycat = mysqli_fetch_array($sql_mycat);
		?>
			<div class="col-md-3">
				<ul class="sidebar-nav animated fadeIn">
					<li>
						<a data-toggle="collapse" href="#coll-css" ><i class="fa fa-css3"></i> Opciones Administrador</a>
						<ul id="coll-css" class="menu-submenu list-unstyled collapse in">
							<li><a href="#entid" data-toggle="tab"><i class="fa fa-bank"></i> Entidades </a></li>
							<li><a href="#users" data-toggle="tab"><i class="fa fa-users"></i> Usuarios </a></li>
							
							<?php if($row_mycat[35] <= 2){ ?>
								<li><a href="#tip_fondos" data-toggle="tab"><i class="fa fa-check-square-o"></i> Tipo de Fondos </a></li>
							<?php } ?>
							
							<li><a href="#fondos" data-toggle="tab"><i class="fa fa-quote-right"></i> Fondos </a></li>
							
							<?php if($row_mycat[35] <= 2){ ?>
								<li><a href="#anna" data-toggle="tab"><i class="fa fa-upload"></i> Cargar ANNA </a></li>
								<li><a href="#smlv" data-toggle="tab"><i class="fa fa-dollar"></i> SMLV</a></li>
							<?php }?>
						</ul>
					</li>
				</ul>
			</div>
			
		<!-- FORMULARIOS DE ADMINISTRADOR -->
			<div class="col-md-9">
				
				<div class="tab-content">
					<div class="tab-pane active" id="entid">
					
						<h3>Entidades</h3>
						<!-- Nav tabs -->
						<ul class="nav nav-tabs nav-tabs-ar nav-tabs-ar-white">
						  <li class="active"><a href="#crea_ent" data-toggle="tab">Crear</a></li>
						  <li><a href="#list_ent" data-toggle="tab">Listado</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div class="tab-pane active" id="crea_ent">
							<?php
								$f_ent = $fmr -> fmr_entidad($idUsu, 'gEnt');
								echo $f_ent;
							?>
							</div>
							
							<div class="tab-pane" id="list_ent">
								<?php 									
									$repo = new listado();
									$list = $repo->tablas('jo33_FIC_categories', $row_mycat[7], 'parent_id', 'E');
									echo $list;
								?>
							</div>
						</div>
						
					</div>
					<!-- USUARIOS -->
					<div class="tab-pane" id="users">
						<h3>Usuarios</h3>
						<!-- Nav tabs -->
						<ul class="nav nav-tabs nav-tabs-ar nav-tabs-ar-white">
						  <li class="active"><a href="#crea_usu" data-toggle="tab">Crear</a></li>
						  <li><a href="#list_usu" data-toggle="tab">Listado</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div class="tab-pane active" id="crea_usu">								
								<?php
									$tits = array('Entidad','Cédula','Nombre','Apellido','Email','Usuario','Pass','Confirmación Pass');
									$cont = array('Entidad', 'Cédula', 'Nombre','Apellido','Email','Usuario','Pass','Confirmación Pass');
									$input = array('select', 'text', 'text', 'text', 'email', 'text', 'password', 'password');
									$ids = array('nom_ent', 'ced_usu', 'nom_usu', 'ape_usu',  'ema_usu', 'user', 'pass_usu', 'cpass_usu');
									$js = 'g_usu';
									
									$f_usu = $fmr -> fmr_usuario($idUsu, $tits, $cont, $input, $ids, $js, 'placeholder');
									echo $f_usu;
								?>								
							</div>
							
							<div class="tab-pane" id="list_usu">
								<?php
									$conslt = new mysql();
									$table = array("jo33_FIC_content", "jo33_FIC_categories");
									$val = array($idUsu, 'jo33_FIC_content.catid');
									$valC = array('jo33_FIC_content.alias','jo33_FIC_categories.id');

									$sql_mycat = $conslt -> sql('S', $table, $val, $valC, $valU);
									
									$row_mycat = mysqli_fetch_array($sql_mycat);
									$id_ent = $row_mycat[7];
									
									$repo = new listado();
									$list = $repo->tablas('jo33_FIC_categories', $id_ent, 'parent_id', 'U');
									echo $list;
								?>
							</div>
						</div>
						
					</div>
					
				<?php if($row_mycat[35] <= 2){ ?>
					<!-- TIPO FONDOS -->
					<div class="tab-pane" id="tip_fondos">
						<h3>Tipos de Fondo</h3>
						<!-- Nav tabs -->
						<ul class="nav nav-tabs nav-tabs-ar nav-tabs-ar-white">
						  <li class="active"><a href="#list_fond" data-toggle="tab">Fondos</a></li>
						  <li><a href="#crea_fond" data-toggle="tab">Nuevo</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div class="tab-pane active" id="list_fond">
								<?php 
									$list_fon = new listado();
									$tip_fon = $list_fon -> tipo_fondo('jo33_FIC_UTLR_tipos_cartera', 0, 0);
									
									echo $tip_fon;
								?>
								<!--a href="javascript:carga_tip_fon(0, 0)" class="btn btn-info"><i class="fa fa-th-list"></i> DESPLEGAR TIPOS</a-->
							</div>
							
							<div class="tab-pane" id="crea_fond">
								<h3>Nuevo Fondo</h3>
								<div id="msj_newtfon"></div>
								<input type="text" class="form-control" id="new_fon" placeholder="Nombre Fondo" ><br />
								<a href="javascript:new_fon(0, 0)" class="btn btn-info"><i class="fa fa-plus-square-o"></i> Crear</a>
							</div>
						</div>
						
					</div>
					
				<!-- CARGA DOCUMENTO ANNA -->
					
					<div class="tab-pane" id="anna">
						<h3>Procedimiento ANNA</h3>
						<!-- Nav tabs -->
						<ul class="nav nav-tabs nav-tabs-ar nav-tabs-ar-white">
						  <li class="active"><a href="#carga_anna" data-toggle="tab">Carga ANNA</a></li>
						  <li><a href="#carga_desc_CPC" data-toggle="tab">Craga y descarga formato CPC</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div class="tab-pane active" id="carga_anna">
								<form action="../templates/class/upLoad_anna.php" method="post" target="_blank" enctype="multipart/form-data">
									<h5>Carga Archivo ANNA (.csv)</h5>
									<input class="form-control" name="arch_dep" type="file" /><br />
									<input type="submit" target="_blank"class="btn btn-info" value="Subir" />
								</form>
							</div>
							
							<div class="tab-pane" id="carga_desc_CPC">
								<form action="../templates/class/upLoad_cpc.php" method="post" target="_blank" enctype="multipart/form-data">
									<h5>Carga Archivo CPC (.csv)</h5>
									<input class="form-control" name="arch_dep" type="file" /><br />
									<input type="submit" target="_blank"class="btn btn-info" value="Subir" />
								</form>
							</div>
						</div>
						
					</div>

				<!-- CARGA DOCUMENTO ANNA -->
					
					<div class="tab-pane" id="smlv">
						<h3>SMLV</h3>
						<div id="msj_smlv"></div>
						<!-- Tab panes -->
						<form enctype="multipart/form-data">
							<h5>Valor del Salario Minimo Legal Vigente</h5>
							<input class="form-control" id="val_smlv" type="text" /><br />
							<a href="javascript:g_smlv();" class="btn btn-info" >Guardar</a>
						</form>
					</div>
			
				<?php } ?>
				
					<!-- FONDOS -->
					<div class="tab-pane" id="fondos">
						<h3>Fondos</h3>
						<!-- Nav tabs -->
						<ul class="nav nav-tabs nav-tabs-ar nav-tabs-ar-white">
						  <li class="active"><a href="#fond" data-toggle="tab">Fondos</a></li>
						  <li><a href="#new_fond" data-toggle="tab">Nuevo</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div class="tab-pane active" id="fond" style="text-align:center">
								<!--a href="javascript:carga_tip_fon(1, <?php echo $row_mycat[7]; ?>)" class="btn btn-info"><i class="fa fa-th-list"></i> DESPLEGAR FONDOS</a-->
								<?php
									$list_fon = new listado();
									$fon = $list_fon -> tipo_fondo('jo33_FIC_UTLR_fondos', 1, $row_mycat[7]);									
									echo $fon;
								?>
							</div>
							
							<div class="tab-pane" id="new_fond">
								<h3>Nuevo Fondo</h3>
								<div id="msj_newfon" style="text-align:center"></div>
								<input type="text" class="form-control" id="nom_fond" placeholder="Nombre Fondo" ><br />
								<input type="text" class="form-control" id="cod_fond" placeholder="Codigo Fondo" ><br />
								<select type="text" class="form-control" id="sel_tip" placeholder="Tipo Fondo" >
									<option value="0">Seleccione un Tipo de Fondo</option>
									<?php 
										$table_tip = array('jo33_FIC_UTLR_tipos_cartera');
										$valC = array('active','trash');
										$val = array('1','1');
										$sql_tip = $conslt -> sql('S', $table_tip, $val, $valC, $valU);
										
										while($row_tip = mysqli_fetch_array($sql_tip)){
											echo'<option value="'.$row_tip[0].'">'.utf8_decode($row_tip[1]).'</option>"';
										}
									?>
								</select><br />
								<select type="text" class="form-control" id="sel_ent" placeholder="Tipo Fondo" >
									<option value="0">Seleccione una Entidad</option>
									<?php 
										$table = array("jo33_FIC_content", "jo33_FIC_categories");
										$val = array($idUsu, 'jo33_FIC_content.catid');
										$valC = array('jo33_FIC_content.alias','jo33_FIC_categories.id');
										$sql_mycat = $conslt -> sql('S', $table, $val, $valC, $valU);
										$row_mycat = mysqli_fetch_array($sql_mycat);
										
										$table = array('jo33_FIC_categories');
										$valC = array('parent_id', 'published');
										$val = array($row_mycat[7], '1');
										$sql_ent = $conslt -> sql('S', $table, $val, $valC, $valU);
										
										while($row_ent = mysqli_fetch_array($sql_ent)){
											echo'<option value="'.$row_ent[0].'">'.$row_ent[8].'</option>"';
										}
									?>
								</select><br />
								<a href="javascript:new_fon(1, <?php echo $row_mycat[7]; ?>)" class="btn btn-info"><i class="fa fa-plus-square-o"></i> Crear</a>
							</div>
						</div>
						
					</div>
				</div>
			</div>

		</div>
	</div>
<?php 
	echo $footer;
?>