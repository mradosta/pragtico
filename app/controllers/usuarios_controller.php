<?php
/**
 * Este archivo contiene toda la logica de negocio asociada al manejo de usuarios.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada al manejo de usuarios.
 *
 * @package	    pragtico
 * @subpackage	app.controllers
 */

class UsuariosController extends AppController { 

/**
 * roles.
 * Muestra via desglose los roles asociados a este Usuario.
 */
	function roles($id) {
		$this->Usuario->contain(array("RolesUsuario", "Rol"));
		$this->data = $this->Usuario->read(null, $id);
	}


/**
 * grupos.
 * Muestra via desglose grupos secundarios a los que pertenece este usuario.
 */
	function grupos($id) {
		/**
		* Como un usuario tiene un grupo primario y puede tener grupos secundarios, y la clase del model para ambos
		* casos es Grupo, el framework hace un merge del array de resultados, lo cual no es correcto, por lo que
		* para este caso (mostrar los grupos secundarios del usuario) deseteo los elementos del grupo primario.
		*/
		$this->Usuario->contain(array("Grupo"));
		$usuario = $this->Usuario->read(null, $id);
		foreach($usuario['Grupo'] as $k=>$v) {
			if(!is_numeric($k)) {
				unset($usuario['Grupo'][$k]);
			}
			
		}
		$this->data = $usuario;
	}

	
    function login() {
        if(!empty($this->data)) {
			if($usuario = $this->Usuario->verificarLogin(array("nombre"=>$this->data['Usuario']['loginNombre'], "clave"=>$this->data['Usuario']['loginClave']))) {

				/**
				* Guardo en la session el usuario.
				*/
				$this->Session->write("__Usuario", $usuario);

				/**
				* Busco los menus.
				*/
				$this->Session->write('__MenuItems', $this->Usuario->traerMenus($usuario));
				$this->redirect('../relaciones/index', null, true);
			}
			else {
				$this->Session->setFlash("Usuario o contraseña incorrectos.", "error");
				$this->redirect("login", null, true);
			}
        }
        else {
        	$this->layout = "login";
        }
    }
    
     
    function logout() { 
        $this->Session->destroy("Usuario");
        $this->Session->setFlash("Ha salido exitosamente de la aplicacion.", "ok");
        $this->redirect("login", null, true);
    } 


	function cambiar_grupo() {
		if(!empty($this->data)) {
			if($this->data['Form']['accion'] == "grabar") {
				$usuario = $this->Session->read("__Usuario");
				//d($usuario);
			}
		}
		$usuario = $this->Session->read("__Usuario");
		foreach($usuario['Grupo'] as $grupo) {
			if($grupo['tipo'] == "De Grupos") {
				$grupos[$grupo['id']] = $grupo['nombre'];
			}
		}
		if(empty($grupos)) {
			$this->Session->setFlash('Usted no tiene otro grupo para realizar el cambio.', 'error');
		}
		else {
			$this->set("grupos", $grupos);
			$this->set("usuario", $this->Session->read('__Usuario'));			
		}
	}

/**
 * Permite realizar el cambio de clave de un usuario.
 */
    function cambiar_clave() {
    	if(!empty($this->data)) {
    		if(!empty($this->data['Form']['accion']) && $this->data['Form']['accion'] === "grabar" && $this->Usuario->validates()) {
    			unset($this->data['Form']);
    			if($this->Usuario->save($this->data)) {
    				$this->Session->setFlash("La clave se cambio correctamente.", "ok");
					$this->History->goBack();
    			}
    		}
    	}
		$this->set("usuario", $this->Session->read('__Usuario'));
	}

} 

?>