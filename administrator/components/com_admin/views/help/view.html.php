<?php
/**
 * el paquete joomla.administrator, esta definido para que el administrador del sitio pueda usar los elementos de joomla sobre la cual esta montada la pagina
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

 //evita el acceso directo al archivo y la ejecucion fuera de un entorno que no sea joomla
defined('_JEXEC') or die;

/**
 * HTML View class for the Admin component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 * @since       1.6
 */
class AdminViewHelp extends JViewLegacy
{
	/**
	 * @var string the search string
	 */
	protected $help_search = null; // cadena de busqueda para filtrar resultados en la ayuda

	/**
	 * @var string the page to be viewed
	 */
	protected $page = null; // URL o contenido de la pagina de ayuda que se debe mostrar

	/**
	 * @var string the iso language tag
	 */
	protected $lang_tag = null; // Etiqueta el idioma (ISO) utilizada en la documentacion

	/**
	 * @var array Table of contents
	 */
	protected $toc = null; // tabla de contenido con los temas disponibles en la ayuda

	/**
	 * @var string url for the latest version check
	 */
	protected $latest_version_check = 'http://www.joomla.org/download.html'; // URL donde se puede verificar la ultima version de joomla

	/**
	 * @var string url for the start here link.
	 */
	protected $start_here = null; // URL de la pagina inicial de ayuda

	/**
	 * Display the view
	 */
	public function display($tpl = null) //renderiza la vista
	{
		$this->help_search			= $this->get('HelpSearch'); //obtiene la cadena de busqueda
		$this->page					= $this->get('Page'); // obtiene la URL de la pagina de ayuda
		$this->toc					= $this->get('Toc'); //obtiene la tabla de contenido
		$this->lang_tag				= $this->get('LangTag'); //obtiene el codigo de idioma
		$this->latest_version_check	= $this->get('LatestVersionCheck'); //obtiene la URL de verificacion de version

		$this->addToolbar(); //para agregar herramientas de navegacion
		parent::display($tpl); //para procesar la plantilla y renderizar la vista
	}

	/**
	 * Setup the Toolbar
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_ADMIN_HELP'), 'support help_header');
		//Agrega un título a la barra de herramientas en Joomla.
		//JText::_('COM_ADMIN_HELP') permite la traducción del texto "Ayuda del Administrador".
		//'support help_header' establece el icono de la barra de herramientas.
	}
}
