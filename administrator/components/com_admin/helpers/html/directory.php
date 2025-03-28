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
 * Utility class working with directory
 * Clase utilitaria para trabajar con directorios en joomla.
 *
 * Esta clase proporciona metodos para generar mensaje sobre la accesibilidad de los directorios (si son escribible o no) y
 * mostrar mensajes personalizados sobre directorios 
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 * @since       1.6
 */

 //JHtmlDirectory - clase abstracta utilizada para generar mensajes sobre directorios en joomla
abstract class JHtmlDirectory
{
	/**
	 * Genera un mensaje indicando si un directorio es escribible o no.
	 * Method to generate a (un)writable message for directory
	 *
	 * indica si el directorio es escribible (true) o no (false)
	 * @param   boolean  $writable  is the directory writable? 
	 *
	 * (codigo HTML con un mensaje visual que indica la accesibilidad del directorio).
	 * @return  string	html code 
	 */
	public static function writable($writable)
	{
		if ($writable)
		{
			//si el direcitorio es escribible, muestra un mensaje con una etiqueta de éxito.
			return '<span class="badge badge-success">' . JText::_('COM_ADMIN_WRITABLE') . '</span>';
		}
		else
		{
			//si el directorio no es escribible, muestra un mensaje con una etiqueta de advertencia 
			return '<span class="badge badge-important">' . JText::_('COM_ADMIN_UNWRITABLE') . '</span>';
		}
	}

	/**
	 * Genera un mensaje personalizado para un directorio especifico
	 * Method to generate a message for a directory
	 *
	 * (ruta o nombre del directorio del que se generara el mensaje)
	 * @param   string   $dir      the directory 
	 * 
	 * (mensaje que se mostrara sobre el estado del directorio)
	 * @param   boolean  $message  the message 
	 * 
	 *  (indica si el nombre del directorio debe ser visible en el mensaje (true) o no (false))
	 * @param   boolean  $visible  is the $dir visible?
	 *
	 * @return  string	html code (codigo html con el mensaje generado).
	 */
	public static function message($dir, $message, $visible = true)
	{
		//si se permite mostrar el nombre del directorio, se almacena en la variable de salida 
		if ($visible)
		{
			$output = $dir;
		}
		else
		{
			$output = '';
		}
		//si no hay un mensaje definido, simplemente retorna el nombre del directorio (o una cadena vacia si no es visible).
		if (empty($message))
		{
			return $output;
		}
		else
		{
			//retorna el nombre del directorio (si es visible) seguido del mensaje en negrita
			return $output . ' <strong>' . JText::_($message) . '</strong>';
		}
	}
}
