<?php
/**
 * Behavior que contiene el manejo de permisos a nivel registros (row level).
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models.behaviors
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 225 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-01-18 19:12:43 -0200 (dom, 18 ene 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * Especifico todos los metodos que me garantizan que de manera automagica cada registro que es recuperado o
 * guardado, siempre contendra el usuario y el grupo correcto, como asi tambien los permisos.
 *
 * Me baso en la idea expuesta por:
 * http://www.xaprb.com/blog/2006/08/16/how-to-build-role-based-access-control-in-sql/
 *
 * @package     pragtico
 * @subpackage  app.models.behaviors
 */
class CrumbableBehavior extends ModelBehavior {

/**
 * Una vez que haya realizado una busqueda, a cada registro le agrego dos nuevos campos que
 * con una bandera booleana me indican si puedo escribir y/o borrar.
 *
 * @param object $model Model que usa este behavior.
 * @param array $results Los resultados que retorno alguna query.
 * @param boolean $primary Indica si este resultado viene de una query principal o de una query que
 *						   es generada por otra (recursive > 1)
 * @return array array $results Los resultados con los campos de permisos ya agregados a cada registro.
 * @access public
 */	
	function afterFind(&$model, $results, $primary = false) {
		
		if ($primary === true && isset($results[0][$model->name])) {
			$breadCrumb = null;
			if (empty($model->breadCrumb)) {
				$breadCrumb['fields'][] = $model->name . "." . $model->primaryKey;
			} else {
				if (is_string($model->breadCrumb)) {
					$breadCrumb['fields'][] = $model->breadCrumb;
				} else {
					$breadCrumb = $model->breadCrumb;
				}
			}
			
			foreach ($results as $k => $result) {
				$texts = null;
				foreach ($breadCrumb['fields'] as $contents) {
					$c = explode('.', $contents);
					if (count($c) === 3) {
						$text = $result[$c[0]][$c[1]][$c[2]];
					}
					elseif (count($c) === 2) {
						$text = $result[$c[0]][$c[1]];
					} elseif (count($c) === 1) {
						$text = $result[$c[0]];
					}
					$texts[] = $text;
				}
				if (!empty($breadCrumb['format'])) {
					$results[$k][$model->name]['bread_crumb_text'] = vsprintf($breadCrumb['format'], $texts);
				} else {
					$results[$k][$model->name]['bread_crumb_text'] = implode(' ', $texts);
				}
			}
		}
		return $results;
	}

	
}
?>