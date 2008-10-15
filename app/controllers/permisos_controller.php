<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los permisos de los registros.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a los permisos de los registros.
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class PermisosController extends AppController {

	var $uses = array("Usuario");

	function asignar() {


		$modelsList = Configure::listObjects("model", APP . "models");
		asort($modelsList);
		$modelsList[] = "Todos";
		$this->set("models", $modelsList);

		$this->Usuario->Grupo->contain();
		$grupos = $this->Util->combine($this->Usuario->Grupo->find("all"), "{n}.Grupo.id", "{n}.Grupo.nombre");
		$this->set("grupos", $grupos);

		$this->Usuario->contain();
		$usuarios = $this->Util->combine($this->Usuario->find("all"), "{n}.Usuario.id", "{n}.Usuario.nombre_completo");
		$this->set("usuarios", $usuarios);

		if(!empty($this->data['Permisos']['model_id'])) {

			$total = 0;
			$mensaje = array();
			if(!empty($this->data['Permisos']['dl']) && $this->data['Permisos']['dl'] == 1) {
				$total += 256;
				$mensaje[] = "Permitir lectura al dueño";
			}
			else {
				$mensaje[] = "Denegar lectura al dueño";
			}
			if(!empty($this->data['Permisos']['gl']) && $this->data['Permisos']['gl'] == 1) {
				$total += 32;
				$mensaje[] = "Permitir lectura al grupo";
			}
			else {
				$mensaje[] = "Denegar lectura al grupo";
			}
			if(!empty($this->data['Permisos']['ol']) && $this->data['Permisos']['ol'] == 1) {
				$total += 4;
				$mensaje[] = "Permitir lectura a los otros";
			}
			else {
				$mensaje[] = "Denegar lectura a los otros";
			}
			if(!empty($this->data['Permisos']['de']) && $this->data['Permisos']['de'] == 1) {
				$total += 128;
				$mensaje[] = "Permitir escritura al dueño";
			}
			else {
				$mensaje[] = "Denegar escritura al dueño";
			}
			if(!empty($this->data['Permisos']['ge']) && $this->data['Permisos']['ge'] == 1) {
				$total += 16;
				$mensaje[] = "Permitir escritura al grupo";
			}
			else {
				$mensaje[] = "Denegar escritura al grupo";
			}
			if(!empty($this->data['Permisos']['oe']) && $this->data['Permisos']['oe'] == 1) {
				$total += 2;
				$mensaje[] = "Permitir escritura a los otros";
			}
			else {
				$mensaje[] = "Denegar escritura a los otros";
			}
			if(!empty($this->data['Permisos']['dd']) && $this->data['Permisos']['dd'] == 1) {
				$total += 64;
				$mensaje[] = "Permitir eliminar al dueño";
			}
			else {
				$mensaje[] = "Denegar eliminar al dueño";
			}
			if(!empty($this->data['Permisos']['gd']) && $this->data['Permisos']['gd'] == 1) {
				$total += 8;
				$mensaje[] = "Permitir eliminar al grupo";
			}
			else {
				$mensaje[] = "Denegar eliminar al grupo";
			}
			if(!empty($this->data['Permisos']['od']) && $this->data['Permisos']['od'] == 1) {
				$total += 1;
				$mensaje[] = "Permitir eliminar a los otros";
			}
			else {
				$mensaje[] = "Denegar eliminar a los otros";
			}
			$update['permissions'] = $total;	

			if(!empty($this->data['Permisos']['usuario_id'])) {
				$update['user_id'] = $this->data['Permisos']['usuario_id'];
			}
			
			if(!empty($this->data['Permisos']['grupo_id'])) {
				$update['group_id'] = $this->data['Permisos']['grupo_id'];
			}

			$model = $modelsList[$this->data['Permisos']['model_id']];
			if($model == "Todos") {
				foreach($modelsList as $v) {
					App::import('Model', $v);
					$modelParaUpdate = new $v();
					//$modelParaUpdate->updateAll($update);
				}
			}
			else {
				App::import('Model', $model);
				$modelParaUpdate = new $model();
				if($this->data['Formulario']['accion'] == "confirmado") {
					//d($update);
					if($modelParaUpdate->updateAll($update)) {
						$this->Session->setFlash("Los cambios a los registros se realizaron correctamente.", "ok");
						$this->redirect("asignar");
					}
				}
				//$modelParaUpdate->updateAll($update);
			}
		}

		if($this->data['Formulario']['accion'] == "falta_confirmacion") {
			$this->set("accion", "falta_confirmacion");
			$this->set("mensaje", $mensaje);
			$this->set("model", $model);
			if(!empty($usuarios[$this->data['Permisos']['usuario_id']])) {
				$this->set("usuario", $usuarios[$this->data['Permisos']['usuario_id']]);
			}
			if(!empty($grupos[$this->data['Permisos']['grupo_id']])) {
				$this->set("grupo", $grupos[$this->data['Permisos']['grupo_id']]);
			}
		}
		
		//d();
	}
	

	function __getCakeModels()
	{
		$controllers = array();
		//$modelsList = listClasses(APP . "models");
		$modelsList = Configure::listObjects("model", APP . "models");
		d($modelsList);
		foreach($controllerList AS $controller => $file)
		{
			list($name) = explode('.',$file);
			$controllerName = Inflector::camelize(str_replace('_controller','',$name));
			$controllers[] = $controllerName;
		}
		if(!empty($controllers))
		{
			return $controllers;
		}
		else
		{
			return false;
		}
	}


	function _getCakeControllers($directorio)
	{
		$controllers = array();
		$controllerList = listClasses($directorio . "controllers");
		foreach($controllerList AS $controller => $file)
		{
			list($name) = explode('.',$file);
			$controllerName = Inflector::camelize(str_replace('_controller','',$name));
			$controllers[] = $controllerName;
		}
		if(!empty($controllers))
		{
			return $controllers;
		}
		else
		{
			return false;
		}
	}

    function _getCakeControllerMethods($directorio, $controllerName)
    {
            $file = $directorio . "controllers" . DS . Inflector::underscore($controllerName)."_controller.php";
            if (file_exists($file))
            {
                require_once($file);
                $parentClassMethods = get_class_methods('AppController');
                $subClassMethods = get_class_methods($controllerName.'Controller');
                $classMethods = array_diff($subClassMethods, $parentClassMethods);
                $subClassVars = false;
                $subClassVars = get_class_vars($controllerName.'Controller');
                if(in_array('scaffold', array_keys($subClassVars)))
                {
                        $scaffold_file = CAKE."libs".DS."controller".DS."scaffold.php";			
                        require_once($scaffold_file);			
                        $scaffoldClassMethods = get_class_methods("Scaffold");			
                        $scaffoldMethods = array_diff($scaffoldClassMethods, $parentClassMethods);
                        foreach($scaffoldMethods AS $sMethod)
                        {
                                $classMethods[] = $sMethod;
                        }
                }
                
                return $classMethods;
            }
            else
            {
                return false;
            }
    }
	
}
?>