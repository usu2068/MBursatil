<?php
/**
 * el paquete joomla.administrator, esta definido para que el administrador del sitio pueda usar los elementos de joomla sobre la cual esta montada la pagina
 * 
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

 //evita el acceso directo al archivo
defined('_JEXEC') or die;

//Importa el modelo de usuario de joomla
require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';

/**
 * User model.
 * Gestiona la informacion y validaciones para el perfil de usuario en el administrador
 *
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 * @since       1.6
 */
class AdminModelProfile extends UsersModelUser
{
	/**
	 * Method to get the record form.
	 * Obtiene el formulario de usuario
	 *
	 * @param   array    $data      An optional array of data for the form to interogate. (datos opcionales para el formulario)
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not. (indica si se deben cargar los datos en caso de fallo)
	 *
	 * @return  JForm    A JForm object on success, false on failure (objeto JForm en caso de exito, false en caso de fallo)
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		//Carga el formulario desde XML
		$form = $this->loadForm('com_admin.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Check for username compliance and parameter set
		//Validación del nombre de usuario
		$usernameCompliant = true;

		if ($this->loadFormData()->username)
		{
			$username = $this->loadFormData()->username;
			$isUsernameCompliant = !(preg_match('#[<>"\'%;()&\\\\]|\\.\\./#', $username) || strlen(utf8_decode($username)) < 2
				|| trim($username) != $username);
		}

		$this->setState('user.username.compliant', $isUsernameCompliant);

		//si el nombre de usuario no puede cambiarse, se deshabilita el campo en el formulario
		if (!JComponentHelper::getParams('com_users')->get('change_login_name') && $isUsernameCompliant)
		{
			$form->setFieldAttribute('username', 'required', 'false');
			$form->setFieldAttribute('username', 'readonly', 'true');
			$form->setFieldAttribute('username', 'description', 'COM_ADMIN_USER_FIELD_NOCHANGE_USERNAME_DESC');
		}

		// If the user needs to change their password, mark the password fields as required
		//si el usuario necesita cambiar su contraseña, se marcan los campos como obligatorios
		if (JFactory::getUser()->requireReset)
		{
			$form->setFieldAttribute('password', 'required', 'true');
			$form->setFieldAttribute('password2', 'required', 'true');
		}

		return $form;
	}

	/**
	 * Obtiene los datos que deben inyectarse en el formulario
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		//recupera datos almacenados temporalmente en la sesion
		$data = JFactory::getApplication()->getUserState('com_users.edit.user.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		// Load the users plugins.
		//importa los plugins de usuario y permite modificaciones
		JPluginHelper::importPlugin('user');

		$this->preprocessData('com_admin.profile', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 * Obtiene los datos del usuario autenticado
	 *
	 * @param   integer  $pk  The id of the primary key. ID del usuario (opcional)
	 *
	 * @return  mixed  Object on success, false on failure. Objeto con los datos del usuario o false si falla
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$user = JFactory::getUser();

		return parent::getItem($user->get('id'));
	}

	/**
	 * Method to save the form data.
	 * Guarda los datos del usuario
	 *
	 * @param   array  $data  The form data. Datos del formulario
	 *
	 * @return  boolean  True on success. True si se guardo correctamente, false si ocurrio un error
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$user = JFactory::getUser();

		//elimina campos que no deben ser modificados directamente
		unset($data['id']); 
		unset($data['groups']);
		unset($data['sendEmail']);
		unset($data['block']);

		// Unset the username if it should not be overwritten
		$username = $data['username'];

		//verifica si se permite cambiar el nombre de usuario
		$isUsernameCompliant = $this->getState('user.username.compliant');

		if (!JComponentHelper::getParams('com_users')->get('change_login_name') && $isUsernameCompliant)
		{
			unset($data['username']);
		}

		// Bind the data.
		//vincula los datos al objeto usuario
		if (!$user->bind($data))
		{
			$this->setError($user->getError());

			return false;
		}

		$user->groups = null;

		// Store the data.
		//guarda los datos en la base de datos
		if (!$user->save())
		{
			$this->setError($user->getError());

			return false;
		}

		$this->setState('user.id', $user->id);

		return true;
	}
}
