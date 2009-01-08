<?php
/**
 * Asset Packer CakePHP Component
 * Copyright (c) 2008 Matt Curry
 * www.PseudoCoder.com
 * http://www.pseudocoder.com/archives/2007/08/08/automatic-asset-packer-cakephp-helper
 *
 * @author      	mattc <matt@pseudocoder.com>
 * @version     	1.2
 * @license     	MIT
 * @version         $Revision:$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 *
 */
/**
 * Modifica mradosta para ser compatible con las necesidades de pragtico.
 *
 * Cuando esta en debug level 0, retorna una packed version de todos los scripts (incluso los inline).
 * En otro debug level, retorna los script tal cual se fueron agregando desde las vistas o los layouts.
 *
 * @package     pragtico
 * @subpackage  app.views.helpers
 */
class AssetHelper extends Helper {

	var $helpers = array('Html', 'Javascript');
	var $checkTS = true;
	var $md5FileName = true;
	var $cachePath = 'packed/';

	function scripts_for_layout() {
		$view =& ClassRegistry::getObject('view');

		/**
		* Si no hay scripts, salgo porque no hay nada que hacer.
		*/
		if (empty($view->__scripts) && empty($view->__myScripts)) {
			return;
		}
		
		/**
		* It's important to sort because of dependencies before rendering.
		*/
		foreach (array("view", "ready", "links") as $location) {
			if (empty($view->__myScripts[$location])) {
				$view->__myScripts[$location] = array();
			} else {
				$tmp = $view->__myScripts[$location];
				ksort($tmp);
				$view->__myScripts[$location] = $tmp;
			}
		}

		/**
		* Cuando este en debug level 0 y no este debugeando este helper se ejecuta, sino retorna
		* los scripts como los fui agregando.
		*/
		
		if (Configure::read('debug') > 0) {
			$scripts_for_layout = "\n\n";
			$scripts_for_layout .= join("\n\t", $view->__scripts);
			$scripts_for_layout .= $this->Javascript->link($view->__myScripts['links']);
			$scripts_for_layout .= $this->Javascript->codeBlock("/**\n * Agrego el codigo en la funcion Ready de JQuery\n */\njQuery(document).ready(function($) {\n\n\t" . implode("\n\n\t", $view->__myScripts['ready']) . "\n\n});");
			$scripts_for_layout .= $this->Javascript->codeBlock(implode("/**SCRIPT*/\n\n", $view->__myScripts['view']));
		}
		else {
			/**
			* Armo los Css
			*/
			foreach ($view->__scripts as $i=>$script) {
				if (preg_match('/css\/(.*).css/', $script, $match)) {
					$temp = array();
					$temp['script'] = $match[1];
					$temp['name'] = basename($match[1]);
					$css[] = $temp;
				}
				else {
					trigger_error("Aun quedan script sin usar el metodo addScript del formulario");
				}
			}
			
			/**
			* Armo los Js
			*/
			foreach ($view->__myScripts['links'] as $i=>$script) {
				$temp = array();
				$temp['script'] = $script;
				$temp['name'] = $script;
				$js[] = $temp;
			}
			
			$linkeados = "";
			if (!empty($js)) {
				$linkeados .= $this->Javascript->link($this->cachePath . $this->process("js", $js));
			}
			if (!empty($css)) {
				$linkeados .= $this->Html->css($this->cachePath . $this->process("css", $css));
			}
			$scripts_for_layout = "";
			$scripts_for_layout .= $this->Javascript->codeBlock("jQuery(document).ready(function($) {" . implode("\n", $view->__myScripts['ready']) . "});");
			$scripts_for_layout .= $this->Javascript->codeBlock(implode("\n\n", $view->__myScripts['view']));
			App::import("Vendor", "jsmin", true, array(APP . "vendors" . DS . "jsmin"), "jsmin-1.1.0.php");
			$scripts_for_layout = $linkeados . trim(JSMin::minify($scripts_for_layout));
		}
		return $scripts_for_layout;
  	}


	function process($type, $data) {
		switch ($type) {
			case 'js':
				$path = JS;
				break;
			case 'css':
				$path = CSS;
				break;
    	}
    	
    	$folder = new Folder;

    	/**
    	* Me aseguro que existe la carpeta de cache.
    	*/
   		if (!$folder->create($path . $this->cachePath, "777")) {
   			Configure::write('debug', 1);
      		trigger_error("No es posible crear el directorio '" . $path . $this->cachePath . "'. Por favor creelo manualmente con permisos 777");
    	}

		/**
		* Verifico si no lo tengo ya creado al archivo en cache.
		*/
		$names = Set::extract($data, '{n}.name');
		$folder->cd($path . $this->cachePath);
		$fileName = $folder->find($this->__generateFileName($names) . '_([0-9]{10}).' . $type);
		if (!empty($fileName)) {
			/**
			* Tomo el primer archivo, porque en realidad debe ser solo 1 ya
			* que hice un merge de los que hayan sido en solo uno.
			*/
			$fileName = $fileName[0];
		}

		/**
		* Me aseguro de que todos los archivos que formaron el packet script,
		* son mas viejos que la packed version.
		*/
    	if ($this->checkTS && $fileName) {
      		$packed_ts = filemtime($path . $this->cachePath . $fileName);

			$latest_ts = 0;
			$scripts = Set::extract($data, "{n}.script");
			foreach ($scripts as $script) {
				$latest_ts = max($latest_ts, filemtime($path . $script . '.' . $type));
			}

			/**
			* Si un archivo origen es mas nuevo, entonces debo crear nuevamente el packed script.
			*/
			if ($latest_ts > $packed_ts) {
				unlink($path . $this->cachePath . $fileName);
				$fileName = null;
			}
    	}

		/**
		* Si no existe, lo creo.
		*/
		if (empty($fileName)) {
    		$ts = time();

			/**
			* Uno los scripts.
			*/
			$scriptBuffer = "";
			$scripts = Set::extract($data, '{n}.script');
			foreach ($scripts as $script) {
				$buffer = file_get_contents($path . $script . '.' . $type);

				switch ($type) {
					case "js":
						App::import("Vendor", "jsmin", true, array(APP . "vendors" . DS . "jsmin"), "jsmin-1.1.0.php");
						$buffer = trim(JSMin::minify($buffer));
						break;
					case "css":
						App::import("Vendor", "csstidy", true, array(APP . "vendors" . DS . "csstidy"), "class.csstidy.php");
						$tidy = new csstidy();
						$tidy->load_template("highest_compression");
						$tidy->parse($buffer);
						$buffer = $tidy->print->plain();
						/**
						* Me aseguro que las rutas no cambien, si le agrego un nivel, me vuelvo ese nivel con las urls.
						*/
						if (!empty($this->cachePath)) {
							$buffer = str_replace("url(", "url(../", $buffer);
						}
						break;
				}
				$scriptBuffer .= "\n/* $script.$type */\n" . $buffer;
			}

			/**
			* Escribo el archivo (packed).
			*/
			$fileName = $this->__generateFileName($names) . '_' . $ts . '.' . $type;
			$file = new File($path . $this->cachePath . $fileName);
			if (!$file->write(trim($scriptBuffer))) {
				Configure::write('debug', 1);
				trigger_error("No fue posible crear el archivo " . $path . $this->cachePath . $fileName . ". Verifique que la ruta exista y tenga los permisos correctos.");
			}
    	}
    	return $fileName;
  	}

  	function __generateFileName($names) {
    	$fileName = str_replace(".", "-", implode("_", $names));
    	if ($this->md5FileName) {
      		$fileName = md5($fileName);
    	}
    	return $fileName;
  	}
}
?>