<?php
/**
 * el paquete joomla.administrator, esta definido para que el administrador del sitio pueda usar los elementos de joomla sobre la cual esta montada la pagina
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Clase utilitaria para trabajar con configuraciones del sistema en Joomla
 * Utility class working with system
 * 
 * Esta clase proporciona un metodo para manejar valores de configuracion del servidor y generar mensajes adecuados para la interfaz de usuario
 *
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 * @since       1.6
 */
abstract class JHtmlSystem
{
	/**
	 * Genera un mensaje de texto basado en un valor del sistema
	 * Method to generate a string message for a value
	 * 
	 * Este metodo se usa para mostrar valores de configuracion del sevidor.
	 * Si el valor esta vacio, devuelve un mensaje predeterminado indicando que la informacion no esta disponible
	 *
	 * Valor de una configuracion del servidor o PHP
	 * @param   string  $val  a php ini value
	 *
	 * Devuelve el valor si esta definido, de lo contrario devuelve 'COM_ADMIN_NA' (no disponible).
	 * @return  string html code
	 */
	public static function server($val)
	{
		if (empty($val))
		{
			//"no disponible" en el contexto de joomla
			return JText::_('COM_ADMIN_NA');
		}
		else
		{
			//devuelve el valor tal como esta si no esta vacio
			return $val;
		}
	}
}
