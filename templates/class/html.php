<?php 
class body{
	
	public function header($template, $r_font){
		$head = '
			<!DOCTYPE html>
			<html lang="en">

			<head>
				<meta charset="UTF-8">
				<meta name=viewport content="width=device-width, initial-scale=1">
				<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

				<title>UTLR</title>

				<link rel="shortcut icon" href="../img/favicon.png" />

				<meta name=description content="">

				<!-- CSS -->
				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/preload.css" rel="stylesheet" media="screen">
				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/bootstrap.css" rel="stylesheet" media="screen">
				<link href="'.$r_font.'templates/css_gen/template/font-awesome.min.css" rel="stylesheet" media="screen">
				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/animate.min.css" rel="stylesheet" media="screen">
				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/slidebars.css" rel="stylesheet" media="screen">
				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/lightbox.css" rel="stylesheet" media="screen">
				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/jquery.bxslider.css" rel="stylesheet" />
				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/syntaxhighlighter/shCore.css" rel="stylesheet"  media="screen">

				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/style-red.css" rel="stylesheet" media="screen" title="default">
				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/width-full.css" rel="stylesheet" media="screen" title="default">

				<link href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/buttons.css" rel="stylesheet" media="screen">
				
				<link rel="stylesheet" type="text/css" media="screen" href="http://www.aplicativojuridico.com/utlr/templates/css_gen/template/bootstrap-datetimepicker.min.css">
				
				 <!-- Scripts -->
	
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/jquery-1.11.1.min.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/jquery.cookie.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/bootstrap.min.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/wow.min.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/slidebars.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/jquery.bxslider.min.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/holder.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/buttons.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/styleswitcher.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/jquery.mixitup.min.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/circles.min.js"></script>
				
				<!-- Syntaxhighlighter -->
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/syntaxhighlighter/shCore.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/syntaxhighlighter/shBrushXml.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/syntaxhighlighter/shBrushJScript.js"></script>

				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/app.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/index.js"></script>
				<script src="http://www.aplicativojuridico.com/utlr/templates/'.$template.'/js/template.js"></script>
				
				<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
				<!--[if lt IE 9]>
					<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/html5shiv.min.js"></script>
					<script src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/respond.min.js"></script>
				<![endif]-->
				
				<script type="text/javascript" src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/bootstrap-datetimepicker.min.js"></script>
				<script type="text/javascript" src="http://www.aplicativojuridico.com/utlr/templates/js_gen/template/bootstrap-datetimepicker.pt-ES.js"></script>
				
				
			</head>
			<!-- Preloader -->
			<div id="preloader">
				<div id="status">&nbsp;</div>
			</div>

			<body>    
				<div id="theme-options" class="hidden-xs">
				
				<!-- div escondido Login -->
					<div id="body-options">
					   <jdoc:include type="modules" name="login" style="none" />
					</div>
					<div id="icon-options">
						<i class="fa fa-user fa-2x fa-flip-horizontal"></i>
					</div>
				
				</div>
			<div id="sb-site">
				<div class="boxed">	
				
			<!-- CABECERA -->	
					<header id="header-full-top" class="header-full header-full-dark hidden-xs">
						<div class="container">
						
						<!-- NOMBRE -->
							<div class="header-full-title">
								<h1 class="animated fadeInRight"><a style="font-size: 30px;" href="index.html">Custody Portfolio Control</a></h1>
								<p class="animated fadeInRight"></p>
							</div>
							
						<!-- BUSCADOR -->
							<nav class="top-nav">
								<div class="dropdown animated fadeInDown animation-delay-13">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search"></i></a>
									<div class="dropdown-menu dropdown-menu-right dropdown-search-box animated fadeInUp">
										<form role="form">         
											<div class="input-group">
												<input type="text" class="form-control" placeholder="Burcar...">
												<span class="input-group-btn">
													<button class="btn btn-ar btn-primary" type="button">Go!</button>
												</span>
											</div>
										</form>    
									</div>
								</div> 
							</nav>
							
						</div> 
					</header> 

			<!-- MENU -->		
					<nav class="navbar navbar-static-top navbar-default navbar-header-full navbar-dark" role="navigation" id="header">
						<div class="container">
							
							<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
								<jdoc:include type="modules" name="position-7" style="none" />
							</div><!-- Menu -->
							
						</div><!-- container -->
					</nav>';
		return $head;
	
	}
	
	public function footer($template){
		
		$foot = '
		<!-- PIE DE PAGINA -->
		
        <aside id="footer-widgets">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="footer-widget-title">Paginas Amigas</h3>
                        <ul class="list-unstyled three_cols">
                            <li><a href="http://www.maestrobursatil.com">Maestro Bursátil Personal</a></li>
							<li><a href="http://www.ustarizabogados.com/UTLRPRO">Maestro Bursátil Corporativo</a></li>
                            <li><a href="http://www.ustarizabogados.com">Ustariz & Abogados</a></li>
                            <li><a href="http://www.aplicativojuridico.com/dcf">Defensoria del Consumidor Financiero</a></li>
                        </ul>
                        
                    </div>
                    
                    <div class="col-md-4">
                        <div class="footer-widget">
                            <h3 class="footer-widget-title">Contactenos</h3>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-3 col-xs-6">
									<p>Direccion: Cra 11A No 96 - 51 Oficina 203</p>
									<p>Telefono: 6108161/4</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
        </aside> <!-- footer-widgets -->

        <footer id="footer">
            <p>&copy; 2014 <a href="http://www.ustarizabogados.com">Ustariz & Abogados</a>, inc. All rights reserved.</p>
        </footer>
        
    </div> <!-- boxed -->
    </div> <!-- sb-site -->

    <div class="sb-slidebar sb-right">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search...">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
            </span>
        </div><!-- /input-group -->

        <h2 class="slidebar-header no-margin-bottom">Navigation</h2>
        <ul class="slidebar-menu">
            <li><a href="index.html">Home</a></li>
            <li><a href="portfolio_topbar.html">Portfolio</a></li>
            <li><a href="page_about3.html">About us</a></li>
            <li><a href="blog.html">Blog</a></li>
            <li><a href="page_contact.html">Contact</a></li>
        </ul>

        <h2 class="slidebar-header">Social Media</h2>
        <div class="slidebar-social-icons">
            <a href="#" class="social-icon-ar rss"><i class="fa fa-rss"></i></a>
            <a href="#" class="social-icon-ar facebook"><i class="fa fa-facebook"></i></a>
            <a href="#" class="social-icon-ar twitter"><i class="fa fa-twitter"></i></a>
            <a href="#" class="social-icon-ar pinterest"><i class="fa fa-pinterest"></i></a>
            <a href="#" class="social-icon-ar instagram"><i class="fa fa-instagram"></i></a>
            <a href="#" class="social-icon-ar wordpress"><i class="fa fa-wordpress"></i></a>
            <a href="#" class="social-icon-ar linkedin"><i class="fa fa-linkedin"></i></a>
            <a href="#" class="social-icon-ar flickr"><i class="fa fa-flickr"></i></a>
            <a href="#" class="social-icon-ar vine"><i class="fa fa-vine"></i></a>
            <a href="#" class="social-icon-ar dribbble"><i class="fa fa-dribbble"></i></a>
        </div>
    </div> <!-- sb-slidebar sb-right -->

    <div id="back-top">
        <a href="#header"><i class="fa fa-chevron-up"></i></a>
    </div>

</body>
</html>';
	
	return $foot;
	
	}

}


?>