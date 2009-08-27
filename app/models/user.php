<?php
/*
* Static methods that can be used to retrieve the logged in user
* from anywhere
*
* Copyright (c) 2008 Matt Curry
* www.PseudoCoder.com
* http://github.com/mcurry/cakephp/tree/master/snippets/static_user
* http://www.pseudocoder.com/archives/2008/10/06/accessing-user-sessions-from-models-or-anywhere-in-cakephp-revealed/
*
* @author Matt Curry <matt@pseudocoder.com>
* @license MIT
*
*/
 
//in AppController::beforeFilter:
//App::import('Model', 'User');
//User::store($this->Auth->user());

 class User extends AppModel {
    var $useTable = false;
    
function &getInstance($user=null) {
    
    
  static $instance = array();

  if ($user) {
    $instance[0] =& $user;
  }
 
  if (!$instance) {
    trigger_error(__("User not set.", true), E_USER_WARNING);
    return false;
  }
 
  return $instance[0];
}
 
public function store($user) {
  if (empty($user)) {
    return false;
  }
 
  User::getInstance($user);
  return true;
}
 
/**
 * Get data related to current logged in user.
 *
 * @param string $path An absolute XPath 2.0 path
 * @param string $options Currently only supports 'flatten' which reduce the array to a single value if there is only one numeric value after Set:extract filter.
 * @return mixed An array of matched items or single value
 * @access public
 * @static
 */ 
    function get($path, $options = array()) {
 
        $options = array_merge(array('flatten' => true), $options);

        $data = Set::extract($path, User::getInstance());
        if ($options['flatten'] === true && count($data) === 1 && !empty($data[0])) {
            return $data[0];
        } else {
            return $data;
        }
    }

/**
 * Gets current logged in user's groups.
 *
 * @param integer $filter Filter groups based on bitwise math.
 * @param mixed $filter If integer, filter groups based on bitwise math.
 *                      If 'selected', filters based on currently selected group.
 *                      If 'all', return all user's groups.    
 * @return array GroupId => GroupName, empty array if the user has no groups.
 * @access public
 */
    function getUserGroups($filter = 'selected') {

        if ($filter === 'selected') {
            /** If more than one group is selected, return array of groups, else just selected one */
            $filter = User::get('/Usuario/preferencias/grupos_seleccionados');
        } elseif ($filter === 'all') {
            return Set::combine(User::get('/Grupo'), '{n}.Grupo.id', '{n}.Grupo.nombre');
        } elseif (!is_numeric($filter)) {
            trigger_error(__('Invalid filter option.', true), E_USER_WARNING);
        }
        
        foreach (User::get('/Grupo') as $group) {
            if ($group['Grupo']['id'] & $filter) {
                $filteredGroups[] = $group;
            }
        }
        if (!empty($filteredGroups)) {
            return Set::combine($filteredGroups, '{n}.Grupo.id', '{n}.Grupo.nombre');
        } else {
            return array();
        }
    }
    
 }
?>