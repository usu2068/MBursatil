<?php
/**
 * el paquete joomla.administrator, esta definido para que el administrador del sitio pueda usar los elementos de joomla sobre la cual esta montada la pagina
 * 
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This file contains post-installation message handling for the checking minimum PHP version support
 * Este archivo contiene la gestion de mensajes posteriores a la instalación para verificar la compatibilidad con la version minima de PHP requerida.
 */

 //evita el acceso directo al archivo
defined('_JEXEC') or die;

/**
 * Checks if the PHP version is less than 5.3.10.
 * comprueba si la version de PHP es inferiror a 5.3.10
 *
 * @return  integer //retorna 1 si la version de PPHP es menor que 5.3.10, de lo contrario, retorna 0
 *
 * @since   3.2
 */
function admin_postinstall_phpversion_condition()
{
	return version_compare(PHP_VERSION, '5.3.10', 'lt'); //compara la version de PHP con la minima requerida
}

//Nota: se alerta a los administradores si su servidor no cumple con los requisitos minimos de PHP.