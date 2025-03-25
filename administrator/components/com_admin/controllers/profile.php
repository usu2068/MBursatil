<?php
/**
 * Controlador de perfil de usuario para la administración de Joomla
 * 
 * Este controlador gestiona las acciones relacionadas con la edición y
 * guardado del perfil del usuario administrador.
 * 
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Clase del controlador para la gestión del perfil de usuario
 * User profile controller class.
 * 
 * Permite verificar permisos de edición, guardar cambios en el perfil y cancelar la edición.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 * @since       1.6
 */
class AdminControllerProfile extends JControllerForm
{
	/**
	 * Method to check if you can edit a record. (Metodo para verificar si el usuario tiene permisos para editar su perfil)
	 *
	 * Extended classes can override this if necessary. (Las clases extendidas pueden anular esto si es necesario)
	 *
	 * @param   array   $data  An array of input data. (datos del perfil del usuario)
	 * @param   string  $key   The name of the key for the primary key. (clave primaria del registro (por defecto 'id'))
	 *
	 * @return  boolean  Verdadero si el usuario tiene permisos para editar, falso en caso contrario.
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Solo permite la edicion so el ID del usuario coincide con el ID de sesion actual.
		return isset($data['id']) && $data['id'] == JFactory::getUser()->id;
	}

	/**
	 * Guarda los cambios en el perfil del usuario y redirige segun la acción realizada
	 * Overrides parent save method to check the submitted passwords match.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable. (calve primaria del a variable en la URL)
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions). (nombre de la variable en la URL (puede ser diferente de la clave primaria)).
	 *
	 * @return  boolean  True if successful, false otherwise. (TRUE si la operación fue exitosa, falso en caso contrario).
	 *
	 * @since   3.2
	 */
	public function save($key = null, $urlVar = null)
	{
		//Redirige a la vista de edicion del perfil del usuario actual
		$this->setRedirect(JRoute::_('index.php?option=com_admin&view=profile&layout=edit&id=' . JFactory::getUser()->id, false));

		// Llama al metodo guardado del controlador padre
		$return = parent::save();

			// si la tarea no es 'apply', redirige a la pagina principal
		if ($this->getTask() != 'apply')
		{
			// Redirect to the main page. (Redirige a la pagina principal)
			$this->setRedirect(JRoute::_('index.php', false));
		}

		return $return;
	}

	/**
	 * Cancela la edición del perfil y redirige a la pagina principal
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable. (clave primaria de la variable en la URL)
	 *
	 * @return  Boolean  True if access level checks pass, false otherwise. (True si la operación fue exitosa, False en caso contrario)
	 *
	 * @since   1.6
	 */
	public function cancel($key = null) //define el metodo cancel en la clase AdminControllerProfile
	{
		//llama al metodo de cancelacion del controlador padre
		$return = parent::cancel($key);

		// Redirect to the main page. (redirige a la pagina principal del panel de administración)
		$this->setRedirect(JRoute::_('index.php', false));

		return $return;
	}
}
