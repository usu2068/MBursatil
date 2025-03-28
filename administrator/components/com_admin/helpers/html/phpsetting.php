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
 * Clse utilitaria para trabajar con configuraciones de PHP en Joomla.
 * Utility class working with phpsetting
 * 
 * Esta clase proporciona metodos para convertir valores de configuracion de PHP en mensajes de texto que pueden ser utilizados en la interfaz de Joomla.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 * @since       1.6
 */
abstract class JHtmlPhpSetting
{
	/**
	 * Genera un mensaje de estado booleano (encendido/apagado).
	 * Method to generate a boolean message for a value
	 *
	 * Indica si la opcion esta activada (true) o desactivada (false)
	 * @param   boolean  $val  is the value set?
	 *
	 * Devuelve el texto 'JON' si esta activada o 'JOFF' si esta desactivada
	 * @return  string html code
	 */
	public static function boolean($val)
	{
		if ($val)
		{
			//encendido en el contexto de JOOMLA
			return JText::_('JON');
		}
		else
		{
			//Apagado en el contexto de JOOMLA
			return JText::_('JOFF');
		}
	}

	/**
	 * Genera un mensaje de esetado booleano (si/no).
	 * Method to generate a boolean message for a value
	 *
	 * indica si la opcion esta activada (true) o desactivada (false).
	 * @param   boolean  $val  is the value set?
	 *
	 * Devuelve el texto 'JYES' si esta activado y 'JNO' si esta desactivado
	 * @return  string html code
	 */
	public static function set($val)
	{
		if ($val)
		{
			return JText::_('JYES');
		}
		else
		{
			return JText::_('JNO');
		}
	}

	/**
	 * Convierte un valor de configuracion de PHP en un mensaje de texto.
	 * Method to generate a string message for a value
	 *
	 * valor de una configuracion de PHP (puede ser una cadena vacia o un valor definido)
	 * @param   string  $val  a php ini value
	 *
	 * devuelve el valor si esta definido; en caso contrario, devuelve 'JNONE' (ninguno).
	 * @return  string html code
	 */
	public static function string($val)
	{
		if (empty($val))
		{
			return JText::_('JNONE'); //ninguno en el contexto de JOOMLA
		}
		else
		{
			//devuelve el valor tal como esta si no esta vacio
			return $val;
		}
	}

	/**
	 * convierte un valor de configuracion de PHP en un numero entero
	 * **Nota** Este metodo esta en deshuso desde Joomla 4.0 y se recomienda usar 'intval()' o casting '(int)'.
	 * Method to generate an integer from a value
	 *
	 * valor de una configuracion de PHP
	 * @param   string  $val  a php ini value
	 *
	 * devuelve el valor convertido a un numero entero.
	 * @return  string html code
	 *
	 * @deprecated  4.0  Use intval() or casting instead.
	 */
	public static function integer($val)
	{
		//registra un mensaje de advertencia en el sistema de logs de Joomla indicando que este metodo este obsoleto.
		JLog::add(
			'JHtmlPhpSetting::integer() is deprecated. Use intval() or casting instead.',
			JLog::WARNING,
			'deprecated'
		);

		return (int) $val; //convierte el valor en un numero entero y lo devuelve
	}
}
