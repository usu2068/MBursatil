<?php
include_once('/home/aplicati/public_html/utlr/templates/class/conectarse.php');
include_once('/home/aplicati/public_html/utlr/templates/class/html.php');

$link = conectarse();
mysql_select_db("aplicati_FIC",$link);

defined('_JEXEC') or die;

$cuerpo = new body();
$head = $cuerpo -> header($this->template);
$footer = $cuerpo -> footer($this->template);

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

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
        <section class="carousel-section">
            <div id="carousel-example-generic" class="carousel carousel-razon slide" data-ride="carousel" data-interval="5000">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner">
				
                    <div class="item active">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 col-sm-7">
                                    <div class="carousel-caption">
                                        <div class="carousel-text">
                                           <h1 class="animated fadeInDownBig animation-delay-7 carousel-title">Custody Portfolio Control</h1>
                                           <h2 class="animated fadeInDownBig animation-delay-5  crousel-subtitle">Herramienta especializada para medir los límites de inversión de los Fondos Inversión Colectiva.</h2>
                                           <ul class="list-unstyled carousel-list">
                                               <li class="animated bounceInLeft animation-delay-11"><i class="fa fa-check"></i>Prametrización conforme a los reglamentos.</li>
                                               <li class="animated bounceInLeft animation-delay-13"><i class="fa fa-check"></i>Monitoreo permanente.</li>
                                               <li class="animated bounceInLeft animation-delay-15"><i class="fa fa-check"></i>Reporte de resultados.</li>
                                           </ul>
                                           <!--p class="animated fadeInUpBig animation-delay-17">Lorem ipsum dolor sit amet consectetur adipisicing elit. In rerum maxime quis tenetur dolor <span>recusandae a nulla</span> qui enim dolorem.</p-->
                                       </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-5 hidden-xs carousel-img-wrap">
                                    <div class="carousel-img">
                                        <img src="/utlr/images/home.png" style="width: 312px;" class="img-responsive animated bounceInUp animation-delay-3" alt="Image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
                    <!--div class="item">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 col-sm-8">
                                    <div class="carousel-caption">
                                        <div class="carousel-text">
                                           <h1 class="animated fadeInDownBig animation-delay-7 carousel-title">Customization extremes</h1>
                                           <h2 class="animated fadeInDownBig animation-delay-5  crousel-subtitle">Configure your own template in few easy steps</h2>
                                           <ul class="list-unstyled carousel-list">
                                               <li class="animated bounceInLeft animation-delay-11"><i class="fa fa-check"></i>25 default colors</li>
                                               <li class="animated bounceInLeft animation-delay-13"><i class="fa fa-check"></i>Variables less for all colors</li>
                                               <li class="animated bounceInLeft animation-delay-15"><i class="fa fa-check"></i>Full width and boxed mode</li>
                                           </ul>
                                           <p class="animated fadeInUpBig animation-delay-17">Lorem ipsum dolor sit amet consectetur adipisicing elit. In rerum maxime quis tenetur dolor <span>recusandae a nulla</span> qui enim dolorem.</p>
                                       </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-4 hidden-xs carousel-img-wrap">
                                    <div class="carousel-img">
                                        <img src="../img/demo/pre2.png" class="img-responsive animated bounceInUp animation-delay-3" alt="Image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					
                    <div class="item">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-6 col-md-7 col-sm-9">
                                    <div class="carousel-caption">
                                        <div class="carousel-text">
                                           <h1 class="animated fadeInDownBig animation-delay-7 carousel-title">Templates for almost everything</h1>
                                           <h2 class="animated fadeInDownBig animation-delay-5  crousel-subtitle">Artificial Reason include over 80 HTML templates</h2>
                                           <ul class="list-unstyled carousel-list">
                                               <li class="animated bounceInLeft animation-delay-11"><i class="fa fa-check"></i>84 HTML Templates</li>
                                               <li class="animated bounceInLeft animation-delay-13"><i class="fa fa-check"></i>More than 50 components</li>
                                               <li class="animated bounceInLeft animation-delay-15"><i class="fa fa-check"></i>Extra CSS classes</li>
                                           </ul>
                                           <p class="animated fadeInUpBig animation-delay-17">Lorem ipsum dolor sit amet consectetur adipisicing elit. In rerum maxime quis tenetur dolor <span>recusandae a nulla</span> qui enim dolorem.</p>
                                       </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-5 col-sm-3 hidden-xs carousel-img-wrap">
                                    <div class="carousel-img">
                                        <img src="../img/demo/pre3.png" class="img-responsive animated bounceInUp animation-delay-3" alt="Image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div-->
					
                </div>

                <!-- Controls -->
                <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
            </div>
        </section> <!-- carousel -->

        <section class="section-lines">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="home-devices">
							<?php 
								$sql_dec = "SELECT * 
										FROM
											`jo33_FIC_content`,
											`jo33_FIC_categories`											 
										WHERE 
											`jo33_FIC_categories`.`id` = 10 AND
											`jo33_FIC_content`.`catid` = `jo33_FIC_categories`.`id`
										ORDER BY `jo33_FIC_content`.`ordering` ASC";
								$result_dec = mysqli_query($link, $sql_dec);
								$row_dec = mysqli_fetch_array($result_dec);
							?>
                            <h3><?php echo(utf8_encode($row_dec[38])); ?></h3>
                            <?php 
								echo $row_dec[41];
								$result_dec = mysqli_query($link, $sql_dec);
							?>
                           
							<ul class="icon-devices">
							<?php while($row_dec = mysqli_fetch_array($result_dec)){ ?>
                                <li class="<?php echo $class; ?>"><a href="#<?php echo $row_dec[3] ?>" data-toggle="tab"><i class="fa fa-circle" style=" font-size: 14px; "></i></a></li>
							<?php 
								$class = " ";								
								}
								
								$class = "active";	
								$result_dec = mysqli_query($link, $sql_dec);
							?>
                            </ul>

                        </div>
                    </div>
					
                    <div class="col-md-8">
                        <div class="tab-content">
						<?php while($row_dec = mysqli_fetch_array($result_dec)){ ?>
                            <div class="tab-pane <?php echo $class ?>" id="<?php echo $row_dec[3] ?>">
								<h2><?php echo(utf8_encode($row_dec[2]))?></h2>
                                <?php 
									echo (utf8_encode($row_dec[4]));
									$class = " ";
								?>
                            </div>
						
						<?php } ?>
                        </div>
                    </div>
					
				</div>
            </div>
        </section>
		
		<section class="margin-bottom">
			<div class="container">
                <div class="row">
				
				   <h2 class="section-title">Nuestras Publicaciones</h2>
				   <div class="bxslider-controls">
						<span id="bx-prev4"></span>
						<span id="bx-next4"></span>
					</div>
					
					<ul class="bxslider" id="latest-works">
						<?php 
							$sql_pub = "SELECT * 
										FROM `jo33_FIC_content` 
										WHERE 
											`catid` = 9 
										ORDER BY `jo33_FIC_content`.`ordering` ASC";
							$result_pub = mysqli_query($link, $sql_pub);
							
							while($row_pub = mysqli_fetch_array($result_pub)){ ?>
						
								<li>
									<div class="img-caption-ar">
										<?php echo $row_pub[4] ?>
										<div class="caption-ar">
											<div class="caption-content">
												<a href="#modal_pub_<?php echo $row_pub[0]?>" class="animated fadeInDown" data-toggle="modal"><i class="fa fa-search"></i>Más Info.</a>
												<h4 class="caption-title"><?php echo (utf8_encode($row_pub[2])); ?></h4>
											</div>
										</div>
									</div>
								</li>
						
							<?php 
							}
							?>
					</ul>
					<?php 
					$result_pub = mysqli_query($link, $sql_pub);
						
						while($row_pub = mysqli_fetch_array($result_pub)){
					?>	

					<!-- Modal -->
					<div class="modal fade bs-example-modal-lg" id="modal_pub_<?php echo $row_pub[0]?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
							<h4 class="modal-title" id="myModalLabel"><?php echo (utf8_encode($row_pub[2])); ?></h4>
						  </div>
						  <div class="modal-body">
							<?php echo (utf8_encode($row_pub[5])); ?>
						  </div>
						</div>
					  </div>
					</div>
					
					<?php } ?>

				</div>
			</div>			
	   </section>		
<?php 
	echo $footer;
?>	   