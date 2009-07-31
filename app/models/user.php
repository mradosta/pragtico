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
 
function get($path) {
  $_user =& User::getInstance();
 
  $path = str_replace('.', '/', $path);
  if (strpos($path, 'Usuario') !== 0) {
    $path = sprintf('Usuario/%s', $path);
  }
 
  if (strpos($path, '/') !== 0) {
    $path = sprintf('/%s', $path);
  }
 
  $value = Set::extract($path, $_user);
 
  if (!$value) {
    return false;
  }
 
  return $value[0];
}
 }
?>