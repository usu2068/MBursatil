<?php
/**
 * define el modelo de help, que proporciona informacion de ayuda dentro del backend del sistema 
 * Maneja el sistema de ayuda en el backend, permitiendo:
 * 	Buscar información en la documentación
 * 	Cargar la pagina de ayuda correspondiente
 * 	Obtener la tabla de contenidos
 * 	Verificar la version mas reciente de joomla
 * 
 * el paquete joomla.administrator, esta definido para que el administrador del sitio pueda usar los elementos de joomla sobre la cual esta montada la pagina
 * 
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

 /**evita que el archivo sea ejecutado directamente fuera de joomla - proteccion contra acceso
  * JEXEC es una constante que solo esta definida cuando joonla esta en ejecucion
  */
defined('_JEXEC') or die;

/**
 * Admin Component Help Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 * @since       1.6
 */
class AdminModelHelp extends JModelLegacy
{
	/**
	 * The search string
	 *
	 * @var    string
	 *
	 * @since  1.6
	 */
	protected $help_search = null; //busqueda de ayuda

	/**
	 * The page to be viewed
	 *
	 * @var    string
	 *
	 * @since  1.6
	 */
	protected $page = null; //pagina actual de ayuda

	/**
	 * The iso language tag
	 *
	 * @var    string
	 *
	 * @since  1.6
	 */
	protected $lang_tag = null; //codigo de idioma ISO

	/**
	 * Table of contents
	 *
	 * @var    array
	 *
	 * @since  1.6
	 */
	protected $toc = null; //tabla de contenido (TOC)

	/**
	 * URL for the latest version check
	 *
	 * @var    string
	 *
	 * @since  1.6
	 */
	protected $latest_version_check = null; //URL para verificar la version mas reciente

	/**
	 * Method to get the help search string
	 *
	 * @return  string  Help search string
	 *
	 * @since  1.6
	 */
	public function &getHelpSearch() //metodo, obtiene la cadena de busqueda ingresada por el usuario
	{
		if (is_null($this->help_search))
		{
			//si help_search es null, recupera el valor de helpsearch desde la solicitud http.
			$this->help_search = JFactory::getApplication()->input->getString('helpsearch');
		}

		return $this->help_search; //devuelve la cadena de busqueda
	}

	/**
	 * Method to get the page
	 *
	 * @return  string  The page
	 *
	 * @since  1.6
	 */
	public function &getPage() //metodo, obtiene la ayuda que se debe visualizar
	{
		if (is_null($this->page))
		{
			//si page es null, recupera la pagina desde la solicitud http o  usa JHELP_START_HERE por defecto
			$page = JFactory::getApplication()->input->get('page', 'JHELP_START_HERE');
			$this->page = JHelp::createUrl($page); //genera la URL correspondiente
		}

		return $this->page; //devuelve la URL de la pagina de ayuda
	}

	/**
	 * Method to get the lang tag
	 *
	 * @return  string  lang iso tag
	 *
	 * @since  1.6
	 */
	public function getLangTag() //metodo, obtiene el codigo de idioma ISO que se usara en la ayuda
	{
			//si lang_tag es null, obtiene el idioma del sistema $lang->getTag()
		if (is_null($this->lang_tag))
		{
			$lang = JFactory::getLanguage();
			$this->lang_tag = $lang->getTag();

			//si no existe documentacion en ese idioma, usa en-GB (inglés) por defecto
			if (!is_dir(JPATH_BASE . '/help/' . $this->lang_tag))
			{
				// Use english as fallback
				$this->lang_tag = 'en-GB'; 
			}
		}
		//devuelve el codigo del idioma
		return $this->lang_tag;
	}

	/**
	 * Method to get the toc
	 *
	 * @return  array  Table of contents
	 */
	public function &getToc() //metodo, obtiene la tabla de contenidos (TOC) de la ayuda
	{
		//si toc es null, obtiene el codigo de idioma y la busqueda
		if (is_null($this->toc))
		{
			// Get vars
			$lang_tag = $this->getLangTag();
			$help_search = $this->getHelpSearch();

			// New style - Check for a TOC JSON file
			//si existe un archivo toc.json, lo carga y lo decodifica.
			if (file_exists(JPATH_BASE . '/help/' . $lang_tag . '/toc.json'))
			{
				$data = json_decode(file_get_contents(JPATH_BASE . '/help/' . $lang_tag . '/toc.json'));

				// Loop through the data array
				//traduce y almacena los titulos de la ayuda
				foreach ($data as $key => $value)
				{
					$this->toc[$key] = JText::_('COM_ADMIN_HELP_' . $value);
				}
			}
			else
			{
				// Get Help files
				//si no hay archivos JSON, busca archivos .xml o .html
				jimport('joomla.filesystem.folder');
				$files = JFolder::files(JPATH_BASE . '/help/' . $lang_tag, '\.xml$|\.html$');
				$this->toc = array();

				foreach ($files as $file)
				{
					$buffer = file_get_contents(JPATH_BASE . '/help/' . $lang_tag . '/' . $file);

					//extrae el titulo del archivo
					if (preg_match('#<title>(.*?)</title>#', $buffer, $m))
					{
						$title = trim($m[1]);

						if ($title)
						{
							// Translate the page title
							//traduce el titulo de la pagina
							$title = JText::_($title);

							// Strip the extension
							$file = preg_replace('#\.xml$|\.html$#', '', $file);

							if ($help_search) //filtra los archivos segun la busqueda
							{
								if (JString::strpos(JString::strtolower(strip_tags($buffer)), JString::strtolower($help_search)) !== false)
								{
									// Add an item in the Table of Contents
									$this->toc[$file] = $title;
								}
							}
							else
							{
								// Add an item in the Table of Contents
								$this->toc[$file] = $title;
							}
						}
					}
				}
			}

			// Sort the Table of Contents
			//ordena los resultados alfabeticamente
			asort($this->toc);
		}

		return $this->toc; //devuelve la tabla de contenidos
	}

	/**
	 * Method to get the latest version check
	 *
	 * @return  string  Latest Version Check URL
	 */
	public function &getLatestVersionCheck() //metodo, obtiene la URL para verificar la version mas reciente de joomla
	{
		//si latest_version_check es null, construye la URL con JHelp::createUrl
		if (!$this->latest_version_check)
		{
			$override = 'http://help.joomla.org/proxy/index.php?option=com_help&keyref=Help{major}{minor}:Joomla_Version_{major}_{minor}_{maintenance}';
			$this->latest_version_check = JHelp::createUrl('JVERSION', false, $override);
		}

		return $this->latest_version_check; //devuelve la URL de veriicación
	}
}
