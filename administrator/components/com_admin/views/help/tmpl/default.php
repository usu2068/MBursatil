<?php
/**
 * Este código es parte del sistema de administración de Joomla. Se encarga de mostrar una interfaz en la que los administradores pueden 
 * buscar ayuda dentro del panel de administración de Joomla.
 * 
 * el paquete joomla.administrator, esta definido para que el administrador del sitio pueda usar los elementos de joomla sobre la cual esta montada la pagina
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

 //evita el acceso directo al archivo PHP, garantizando sque solo se ejecute dentro del entorno de joomla
defined('_JEXEC') or die;

//carga la funcionalidad de "tooltips" (informacion emergente) usando bootstrap
JHtml::_('bootstrap.tooltip');
?>

<!--Se crea un formulario HTML que apunta a la vista de ayuda de joomla (com_admin&view=help)
	JRoute::_ genera una URL amigable según la configuración de Joomla.
	Se usa method="post" para enviar datos.
-->
<form action="<?php echo JRoute::_('index.php?option=com_admin&amp;view=help'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">

<!--Representa la barra lateral de la interfaz de ayuda, contiene:
		Un campo de búsqueda (helpsearch).
		Botón de búsqueda con icono de lupa (icon-search).
		Botón de limpieza para restablecer la búsqueda (icon-remove).
-->
		<div id="sidebar" class="span3">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search input-append">
					<label for="helpsearch" class="element-invisible"><?php echo JText::_('COM_ADMIN_SEARCH'); ?></label>

		<!--Representa la barra lateral de la interfaz de ayuda, contiene:
			Permite a los administradores buscar dentro del sistema de ayuda.
			JText::_('JSEARCH_FILTER') define un texto traducible para el placeholder.
			hasTooltip añade un mensaje emergente (tooltip).
		-->
					<input type="text" name="helpsearch" id="helpsearch" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->help_search); ?>" class="input-small hasTooltip" title="<?php echo JHtml::tooltipText('COM_ADMIN_SEARCH'); ?>" />
					<!-- ejecuta la busqueda cuando el usuario hace clic-->
					<button type="submit" class="btn hasTooltip" title="<?php JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i></button>
						<!-- limpia el campo de busqueda y envia el formulario de nuevo-->
					<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="f=document.adminForm;f.helpsearch.value='';f.submit()">
						<i class="icon-remove"></i></button>
				</div>
			</div>
			<div class="clearfix"></div>
			<!-- Genera un menú con enlaces a diferentes secciones de ayuda.
					JHtml::_('link', URL, TEXTO, array('target' => 'helpFrame')) crea enlaces que se abren dentro de un iframe. 
					"Comenzar aquí" (JHELP_START_HERE).
					Verificación de la última versión de Joomla ($this->latest_version_check).
					Licencia GPL (http://www.gnu.org/licenses/gpl-2.0.html).
					Glosario (JHELP_GLOSSARY).
			-->
			<div class="sidebar-nav">
				<ul class="nav nav-list">
					<li><?php echo JHtml::_('link', JHelp::createUrl('JHELP_START_HERE'), JText::_('COM_ADMIN_START_HERE'), array('target' => 'helpFrame')) ?></li>
					<li><?php echo JHtml::_('link', $this->latest_version_check, JText::_('COM_ADMIN_LATEST_VERSION_CHECK'), array('target' => 'helpFrame')) ?></li>
					<li><?php echo JHtml::_('link', 'http://www.gnu.org/licenses/gpl-2.0.html', JText::_('COM_ADMIN_LICENSE'), array('target' => 'helpFrame')) ?></li>
					<li><?php echo JHtml::_('link', JHelp::createUrl('JHELP_GLOSSARY'), JText::_('COM_ADMIN_GLOSSARY'), array('target' => 'helpFrame')) ?></li>
					<hr class="hr-condensed" />

					<!-- Muestra un índice de temas de ayuda organizados alfabéticamente.
						$this->toc es un array con claves ($k) y valores ($v) que representan los temas disponibles.
					-->
					<li class="nav-header"><?php echo JText::_('COM_ADMIN_ALPHABETICAL_INDEX'); ?></li>
					<?php foreach ($this->toc as $k => $v): ?>
						<li>
							<?php $url = JHelp::createUrl('JHELP_' . strtoupper($k)); ?>
							<?php echo JHtml::_('link', $url, $v, array('target' => 'helpFrame')); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>

		<!-- Muestra el contenido de la ayuda dentro de un iframe. La URL de la ayuda ($this->page) se carga dinámicamente.-->
		<div class="span9">
			<iframe name="helpFrame" height="2100px" src="<?php echo $this->page; ?>" class="helpFrame table table-bordered"></iframe>
		</div>
	</div>
	<!-- Envía el parámetro option=com_admin en cada solicitud del formulario.-->
	<input class="textarea" type="hidden" name="option" value="com_admin" />
</form>
