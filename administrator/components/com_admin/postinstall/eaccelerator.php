<?php
/**
 * el paquete joomla.administrator, esta definido para que el administrador del sitio pueda usar los elementos de joomla sobre la cual esta montada la pagina
 * 
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This file contains post-installation message handling for eAccelerator compatibility.
 * Este archivo maneja los mensajes posteriores a la instalación relacionados con la compatibilidad de eAccelerator.
 */

defined('_JEXEC') or die;

/**
 * Verifica si el método de almacenamiento en caché eAccelerator está habilitado.
 * 
 * Checks if the eAccelerator caching method is enabled. This check should be
 * done through the 3.x series as the issue impacts migrated sites which will
 * most often come from the previous LTS release (2.5). Remove for version 4 or
 * when eAccelerator support is added.
 * 
 * Esta comprobación se debe realizar a lo largo de la serie 3.x, ya que el problema afecta
 * a los sitios migrados desde la versión LTS anterior (2.5). Se debe eliminar en la versión 4
 * o cuando se agregue soporte para eAccelerator.
 *
 * This check returns true when the eAccelerator caching method is user, meaning
 * that the message concerning it should be displayed.
 * 
 * Devuelve verdadero si el método de almacenamiento en caché es eAccelerator, lo que significa
 * que se debe mostrar un mensaje al respecto.
 *
 * @return  integer Retorna 1 si el método de caché es eAccelerator, de lo contrario, 0.
 *
 * @since   3.2
 */
function admin_postinstall_eaccelerator_condition()
{
	$app = JFactory::getApplication();
	$cacheHandler = $app->get('cacheHandler', '');

	return (ucfirst($cacheHandler) == 'Eaccelerator');
}

/**
 * Disables the unsupported eAccelerator caching method, replacing it with the
 * "file" caching method.
 * 
 * Deshabilita el método de almacenamiento en caché eAccelerator no compatible y lo reemplaza
 * con el método de almacenamiento en caché "file".
 *
 * @return  void
 *
 * @since   3.2
 */
function admin_postinstall_eaccelerator_action()
{
	// Obtiene la configuración actual
	$prev = new JConfig;
	$prev = JArrayHelper::fromObject($prev);

	// Establece el nuevo método de caché como "file"
	$data = array('cacheHandler' => 'file');

	// Fusiona la configuración anterior con la nueva configuración
	$data = array_merge($prev, $data);

	// Carga la configuración en un objeto JRegistry
	$config = new JRegistry('config');
	$config->loadArray($data);

	// Importa las clases necesarias para la manipulación de archivos
	jimport('joomla.filesystem.path');
	jimport('joomla.filesystem.file');

	// Set the configuration file path.
	// Define la ruta del archivo de configuración
	$file = JPATH_CONFIGURATION . '/configuration.php';

	// Get the new FTP credentials.
	// Obtiene las credenciales FTP para posibles modificaciones del archivo
	$ftp = JClientHelper::getCredentials('ftp', true);

	// Attempt to make the file writeable if using FTP.
	// Intenta hacer que el archivo sea escribible si no se usa FTP
	if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0644'))
	{
		JError::raiseNotice('SOME_ERROR_CODE', JText::_('COM_CONFIG_ERROR_CONFIGURATION_PHP_NOTWRITABLE'));
	}

	// Attempt to write the configuration file as a PHP class named JConfig.
	// Intenta escribir el nuevo archivo de configuración con la clase JConfig
	$configuration = $config->toString('PHP', array('class' => 'JConfig', 'closingtag' => false));

	if (!JFile::write($file, $configuration))
	{
		JFactory::getApplication()->enqueueMessage(JText::_('COM_CONFIG_ERROR_WRITE_FAILED'), 'error');

		return;
	}

	// Attempt to make the file unwriteable if using FTP.
	// Intenta hacer que el archivo sea de solo lectura si no se usa FTP
	if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0444'))
	{
		JError::raiseNotice('SOME_ERROR_CODE', JText::_('COM_CONFIG_ERROR_CONFIGURATION_PHP_NOTUNWRITABLE'));
	}
}
