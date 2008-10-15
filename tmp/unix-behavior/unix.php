<?php

	/**
	 * Unix Behavior
	 *
	 * Unix style permissions for rows and tables. Based largely on the work of Xaprb at:
	 * http://www.xaprb.com/blog/2006/08/16/how-to-build-role-based-access-control-in-sql/
	 * 
	 * @author          joebeeson
	 */

	class UnixyBehavior extends ModelBehavior {
		
		/**
		 * Holds our UnixActions model which is responsible for tying
		 * actions to models/rows.
		 *
		 * @var object(model)
		 */
		var $ActionsModel;
		
		/**
		 * Boolean to control whether we're active or not. Reset after each
		 * after[Delete|Save|Find] back to true. Can be overridden remotely.
		 *
		 * @var boolean
		 */
		var $Active = true;
		
		/**
		 * Default array of column names. These can be overridden by changing
		 * them in the $actsAs array in the model.
		 *
		 * @var unknown_type
		 */
		var $ColumnDefaults = array(
			'ID'			=> 'id',
			'Permissions' 	=> 'permissions',
			'Owner'			=> 'owner',
			'Group'			=> 'group',
			'Status'		=> 'status'
		);
		
		/**
		 * All groups in an array.
		 * [GroupName] => Integer
		 * 
		 * @var array
		 */
		var $Groups;
		
		/**
		 * Holds our UnixGroups model which is responsible for storing
		 * all of our available groups. 
		 *
		 * @var object(model)
		 */
		var $GroupsModel;
		
		/**
		 * Holds our UnixImplemented model which is responsible for telling
		 * us what actions can be performed on objects, in which table, at
		 * which status.
		 *
		 * @var object(model)
		 */
		var $ImplementedModel;
		
		/**
		 * Where should the system be active?
		 * 1 = Admin sections only
		 * 2 = Non-admin sections only
		 * 3 = Everywhere
		 *
		 * @var integer
		 */
		var $Locale = 1;
		
		/**
		 * Internal placeholder for our passed model.
		 *
		 * @var object(model)
		 */
		var $Model;
		
		/**
		 * Internal use boolean to determine if we should be operating.
		 *
		 * @var boolean
		 */
		var $Operational = false;
		
		/**
		 * Internal usage for determining permission names
		 * and their associated bit values.
		 *
		 * @var array
		 */
		var $Permissions = array(
			'OwnerRead'		=>	256,
			'OwnerWrite'	=> 	128,
			'OwnerDelete'	=>	64,
			'GroupRead'		=>	32,
			'GroupWrite'	=>	16,
			'GroupDelete'	=>	8,
			'OtherRead'		=>	4,
			'OtherWrite'	=>	2,
			'OtherDelete'	=>	1
		);
		
		/**
		 * Holds our UnixPrivileges model which is responsible for storing
		 * specific table privileges that overrides the row-level ones.
		 *
		 * @var object(model)
		 */
		var $PrivilegesModel;
		
		/**
		 * Sum of all the user's group values.
		 *
		 * @var int
		 */
		var $UserGroupsBit;
		
		/**
		 * The user's ID
		 *
		 * @var char(36)
		 */
		var $UserID;
		
		/**
		 * Behavior setup. Ran once per model.
		 *
		 * @param object $Model
		 */
		function setup(&$Model, $Settings = array()) {
			// Since our settings may override our locale default, we need to do this now.
			$this->ApplySettings($Settings);
			if ($this->CheckLocale()) {
				$this->Operational 		= true;
				$this->ActionsModel 	= ClassRegistry::init('UnixAction',    'model');
				$this->GroupsModel  	= ClassRegistry::init('UnixGroup',     'model');
				$this->PrivilegesModel	= ClassRegistry::init('UnixPrivilege', 'model');
				$this->ImplementedModel	= ClassRegistry::init('UnixImplementedAction','model');
				$this->Model			= $Model;
				$this->UserSetup();
			}
		}
		
		function ApplySettings($Array) {
			// Allow the locale to be overridden from the default, if need be.
			if (isset($Array['Locale']) and !empty($Array['Locale'])) {
				if (is_numeric($Array['Locale']) and $Array['Locale'] > 0 and $Array['Locale'] < 4) {
					$this->Locale = $Array['Locale'];
				}
			}
			// Does the model provide different column names for the system?
			if (isset($Array['Columns']) and is_array($Array['Columns'])) {
				foreach ($Array['Columns'] as $Internal=>$External) {
					// Check that the name is accepted internally and set it if so
					if (isset($this->ColumnDefaults[$Internal])) {
						$this->ColumnDefaults[$Internal] = $External;
					}
				}
			}
		}
		
		/**
		 * Determine where we are and return true or false depending
		 * on the predefined location we should be active on.
		 *
		 * @return boolean
		 */
		function CheckLocale() {
			switch ($this->Locale) {
				case '1':
					// Admin areas only.
					if (Set::extract(explode('/', $_SERVER['REQUEST_URI']), '1') == Configure::read('Routing.admin')) {
						return true;
					}
				break;
				case '2':
					// Non-admin areas only.
					if (Set::extract(explode('/', $_SERVER['REQUEST_URI']), '1') != Configure::read('Routing.admin')) {
						return true;
					}
				break;
				case '3':
					// All locations. Always return true.
					return true;
				break;
			}
			// Catch-all, just in case none of the above is hit.
			return false;			
		}
		
		function __setError() { /* Sometimes SessionComponent needs this. I know it's bad... */ }
		
		/**
		 * Determine the user's information and set it for later use.
		 *
		 */
		function UserSetup() {
			
			// You may need to change the below to fit your application.
			$this->UserID = Set::extract(SessionComponent::__returnSessionVars(), 'Auth.User.id');
			
			if (!empty($this->UserID)) {
				// There's probably a much better way to do this...
				$TempUserGroupArray = $this->GroupsModel->query(
					'SELECT UnixGroup.* FROM users 
						JOIN unix_groups_users ON users.id=unix_groups_users.user_id 
						JOIN unix_groups as UnixGroup ON unix_groups_users.unix_group_id=UnixGroup.id 
					WHERE users.id=\''.$this->UserID.'\'');
				$this->UserGroupsBit  = array_sum(Set::extract($TempUserGroupArray, '{n}.UnixGroup.value'));
			} else { 
				// We need these set, even if they're not actually logged in.
				$this->UserGroupsBit = '0'; 
				$this->UserID = 'Not Logged In'; 
			}
			$this->GroupsModel->restrict();
			$this->Groups = $this->GroupsModel->findAll();
			$this->Groups = array_combine(Set::extract($this->Groups, '{n}.UnixGroup.name'), Set::extract($this->Groups, '{n}.UnixGroup.value'));
		}
		
		/**
		 * Ensure that the user attempting to delete an object has permissions
		 * to do so. If not, return false and stop the query.
		 *
		 * @param object(model) $Model
		 * @param array $Query
		 * @return boolean
		 */
		function beforeDelete(&$Model, $Query) {
			if (!in_array('delete', $this->RetrieveObjectPrivileges($Model->id))) { 
				return false; 
			} else { return true; }
		}
		
		/**
		 * Method for filtering find queries depending on if it is a SELECT ALL
		 * or if they're viewing a specific one. Fails if noone is logged in.
		 *
		 * @param object $Model
		 * @param array $Query
		 * @return false if blocked, nothing if allowed
		 */
		function beforeFind(&$Model, &$Query) {
			/*	First check if we're operational, which is set earlier in ::setup(). Then
				see if we can get user information, if not we stop the query; they're not
				logged in. Finally check if we're looking at a specific record or a group	*/
			if ($this->Operational and $this->Active) {
				if (UnixyBehavior::ModifyQueryForFind($Model->name, $Query)) {
					$NewQuery = "
					select distinct obj.".$this->ColumnDefaults['ID']."
					from ".$Model->useTable." as obj
					   inner join ".$this->ImplementedModel->useTable." as ia
					      on ia.table = '".$Model->useTable."'
					         and ia.action = 'read'
					         and ((ia.status = 0) or (ia.status & obj.".$this->ColumnDefaults['Status']." <> 0))
					   inner join ".$this->ActionsModel->useTable." as ac
					      on ac.title = 'read'
					   left outer join ".$this->PrivilegesModel->useTable." as pr
					      on pr.related_table = '".$Model->useTable."'
					         and pr.action = 'read'
					         and (
					            (pr.type = 'object' and pr.related_id = obj.".$this->ColumnDefaults['ID'].")
					            or pr.type = 'global'
					            or (pr.role = 'self' and '".$this->UserID."' = obj.".$this->ColumnDefaults['ID']." and '".$Model->useTable."' = 'user'))
					where
					   ac.apply_object
					   and (
					      (".$this->UserGroupsBit." & ".$this->Groups['Root']." <> 0)
					      or (ac.title = 'read' and (
					         (obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OtherRead']." <> 0)
					         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OwnerRead']." <> 0)
					            and obj.".$this->ColumnDefaults['Owner']." = '".$this->UserID."')
					         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['GroupRead']." <> 0)
					            and (".$this->UserGroupsBit." & obj.".$this->ColumnDefaults['Group']." <> 0))))
					      or (ac.title = 'write' and (
					         (obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OtherWrite']." <> 0)
					         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OwnerWrite']." <> 0)
					            and obj.".$this->ColumnDefaults['Owner']." = '".$this->UserID."')
					         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['GroupWrite']." <> 0)
					            and (".$this->UserGroupsBit." & obj.".$this->ColumnDefaults['Group']." <> 0))))
					      or (ac.title = 'delete' and (
					         (obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OtherDelete']." <> 0)
					         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OwnerDelete']." <> 0)
					            and obj.".$this->ColumnDefaults['Owner']." = '".$this->UserID."')
					         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['GroupDelete']." <> 0)
					            and (".$this->UserGroupsBit." & obj.".$this->ColumnDefaults['Group']." <> 0))))
					      or (pr.role = 'user' and pr.who = '".$this->UserID."')
					      or (pr.role = 'owner' and obj.".$this->ColumnDefaults['Owner']." = '".$this->UserID."')
					      or (pr.role = 'owner_group' and (obj.".$this->ColumnDefaults['Group']." & ".$this->UserGroupsBit." <> 0))
					      or (pr.role = 'group' and (pr.who & ".$this->UserGroupsBit." <> 0)))
					      or pr.role = 'self'
					";
					
					/*	Since we were passed the query by reference, we can just make some
						changes to it here and it'll be used. No need to return anything.	*/
					$Query['conditions'][] = '`'.$Model->name.'`.`'.$this->ColumnDefaults['ID'].'` IN ('.$NewQuery.')';
				} else {
					/*	So we're trying to get a specific record. Check if they have read access
						for that specific row of data. If not, halt the query 				*/
					$Permissions = $this->RetrieveObjectPrivileges($Query['conditions'][$Model->name.'.'.$this->ColumnDefaults['ID']]);
					if (!in_array('read', $Permissions)) {
						return false;	// We don't have read access. Stop the query.
					}
				}
			}
			/*	End bracket for $this->Operational check. Pass nothing and let the model
				do its own thing. 															*/
		}
		
		/**
		 * Does the user have the 'create' permission for this model?
		 * If so then we need to do a little bit of addition to set them as
		 * the owner of the new row. We'll also add their current group to 
		 * the row. Both are set if the data doesn't already have those.
		 * 
		 * @param object $Model
		 * @return boolean
		 */
		function beforeSave(&$Model) {
			if ($this->Operational and $this->Active) { 
				// We're updating an object. Make sure we have permissions to do that.
				if (isset($Model->id) and !empty($Model->id)) {
					if (in_array('write', $this->RetrieveObjectPrivileges($Model->id))) {
						return true;
					}
				} else {
					// We're creating a new object. Do we have create permissions on the table?
					if (in_array('create', $this->RetrieveTablePrivileges())) {
						// If the owner or group is already set, then don't bother. Otherwise set.
						if (!isset($Model->data[$Model->name][$this->ColumnDefaults['Owner']])) { $Model->data[$Model->name]['owner'] = $this->UserID; }
						if (!isset($Model->data[$Model->name][$this->ColumnDefaults['Group']])) { $Model->data[$Model->name]['group'] = $this->UserGroups; }
						return true;
					}
				}
				return false;
			}
		}
		
		/**
		 * Add some psuedo-columns to the outgoing data that could prove to be useful by
		 * the system. These include a relation field, to determine how a person can access
		 * that data; permissions array to say what they can do to that data; groups array
		 * saying what groups that data belongs to.
		 *
		 * @param object $Model
		 * @param array $Results
		 */
		function afterFind(&$Model, &$Results) {
			if ($this->Operational && $this->Active) {
				foreach ($Results as $Key=>$Result) {
					
					$ObjectGroups 		= $this->DetermineGroups($Result[$Model->name]['group']);
					$ObjectPermissions 	= $this->DeterminePermissions($Result[$Model->name]['permissions']);
					
					// The relation names must match the names up top, at least the first parts
					if ($Result[$Model->name]['owner'] == $this->UserID) { $Relation = 'Owner'; }
					elseif (array_intersect_key($ObjectGroups, $this->Groups)) { $Relation = 'Group'; }
					else { $Relation = 'Other'; }
					
					// Create an array of permissions. Example: array('Owner' => array('Read' => true, etc, etc)
					foreach ($ObjectPermissions as $Obj=>$Value) {
						foreach (array('Owner', 'Group', 'Other') as $Connection) {
							if (substr($Obj, 0, strlen($Connection)) == $Connection) {
								$PermissionArray[$Connection][str_replace($Connection, '', $Obj)] = $Value;
							}
						}
					}

					$Results[$Key][$Model->name]['UserRelation'] 	 = $Relation;
					$Results[$Key][$Model->name]['PermissionsArray'] = $PermissionArray;
					$Results[$Key][$Model->name]['ObjectGroups'] 	 = $ObjectGroups;
				}
				// Since we're editing the results by reference, we don't need to pass anything.
			}
			// Reset our active status.
			$this->Active = true;
		}
		
		function afterSave(&$Model) {
			// Reset our active status.
			$this->Active = true;
		}
		
		function afterDelete(&$Model) {
			// Reset our active status.
			$this->Active = true;
		}
		
		/**
		 * Helper method for getting an array of privileges allowed to the user.
		 * 
		 * If no information is passed for $Model, $UserID, or $UserGroups, the
		 * system assumes it's the current information set through ::UserSetup()
		 *
		 * @param string $ObjectID
		 * @param object/null $Model
		 * @param string/null $UserID
		 * @param int/null $UserGroups
		 * @return array
		 */
		function RetrieveObjectPrivileges($ObjectID = null, &$Model = null, $UserID = null, $UserGroups = null) {
			/*	Determine which, if any, of our required variables is empty. If
				they are, then assume they can be found elsewhere. $ObjectID cannot
				be empty but if it is, we return an empty array						*/
			switch (1==1) {
				case empty($ObjectID):
					return array();
				case empty($Model):
					$Model = $this->Model;
				case empty($UserID):
					$UserID = $this->UserID;
				case is_null($UserGroups):
					$UserGroups = $this->UserGroupsBit;
			}

			$query = "
			select distinct ac.title
			from
			   unix_actions as ac
			   inner join ".$Model->useTable." as obj on obj.".$this->ColumnDefaults['ID']." = '$ObjectID'
			   inner join unix_implemented_actions as ia
			      on ia.action = ac.title
			         and ia.table = '".$Model->useTable."'
			         and ((ia.status = 0) or (ia.status & obj.".$this->ColumnDefaults['Status']." <> 0))
			   left outer join unix_privileges as pr
			      on pr.related_table = '".$Model->useTable."'
			         and pr.action = ac.title
			         and (
			            (pr.type = 'object' and pr.related_id = '$ObjectID')
			            or pr.type = 'global'
			            or (pr.role = 'self' and '$UserID' = '$ObjectID' and '".$Model->useTable."' = 'users'))
			where
			   ac.apply_object
			   and (
			      ($UserGroups & ".$this->Groups['Root']." <> 0)
			      or (ac.title = 'read' and (
			         (obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OtherRead']." <> 0)
			         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OwnerRead']." <> 0)
			            and obj.".$this->ColumnDefaults['Owner']." = '$UserID')
			         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['GroupRead']." <> 0)
			            and ($UserGroups & obj.".$this->ColumnDefaults['Group']." <> 0))))
			      or (ac.title = 'write' and (
			         (obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OtherWrite']." <> 0)
			         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OwnerWrite']." <> 0)
			            and obj.".$this->ColumnDefaults['Owner']." = '$UserID')
			         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['GroupWrite']." <> 0)
			            and ($UserGroups & obj.".$this->ColumnDefaults['Group']." <> 0))))
			      or (ac.title = 'delete' and (
			         (obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OtherDelete']." <> 0)
			         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['OwnerDelete']." <> 0)
			            and obj.".$this->ColumnDefaults['Owner']." = '$UserID')
			         or ((obj.".$this->ColumnDefaults['Permissions']." & ".$this->Permissions['GroupDelete']." <> 0)
			            and ($UserGroups & obj.".$this->ColumnDefaults['Group']." <> 0))))
			      or (pr.role = 'user' and pr.who = '$UserID')
			      or (pr.role = 'owner' and obj.".$this->ColumnDefaults['Owner']." = '$UserID')
			      or (pr.role = 'owner_group' and (obj.".$this->ColumnDefaults['Group']." & $UserGroups <> 0))
			      or (pr.role = 'group' and (pr.who & $UserGroups <> 0)))
			      or pr.role = 'self';
			";
			$Return = array();
			$Results = $Model->query($query);
			if (isset($Results) and is_array($Results)) {
				$Return = Set::extract($Results, '{n}.ac.title');
			}
			return $Return;
		} 
		
		/**
		 * Return an array of the actions allowed to a user, or group on the model.
		 *
		 * @param object $Model
		 * @param char(36) $UserID
		 * @param int $UserGroups
		 * @return array
		 */
		function RetrieveTablePrivileges(&$Model = null, $UserID = null, $UserGroups = null, $TableOverride = null) {
			/*	Determine which, if any, of our required variables is empty. If
				they are, then assume they can be found elsewhere. 				*/
			switch (1==1) {
				case empty($Model):
					$Model = $this->Model;
				case empty($UserID):
					$UserID = $this->UserID;
				case is_null($UserGroups):
					$UserGroups = $this->UserGroupsBit;
				case is_null($TableOverride):
					$TableOverride = $Model->useTable;
			}
			$query = "
			select ac.title
			from
			    unix_actions as ac
			    left outer join unix_privileges as pr
			        on pr.related_table = '".$TableOverride."'
			            and pr.action = ac.title
			            and pr.type = 'table'
			where
			    (ac.apply_object = 0) and (
			        ($UserGroups & ".$this->Groups['Root']." <> 0)
			        or (pr.role = 'user' and pr.who = '$UserID')
			        or (pr.role = 'group' and (pr.who & $UserGroups <> 0)))
			";
			$Return = array();
			$Results = $Model->query($query);
			foreach ($Results as $Array) {
				$Return[] = $Array['ac']['title'];
			}
			return $Return;
		}
		
		/**
		 * Based on the permission integer pased to us, we build an array of permissions
		 * and true/false for access/denied. This will return an entire permission list
		 * back with boolean for their respective status.
		 *
		 * @param int $PermissionInt
		 * @return array
		 */
		function DeterminePermissions($PermissionInt) {
			foreach ($this->Permissions as $Key=>$Int) {
				if (($PermissionInt & $Int) != 0) { $PermissionArray[$Key] = true; }
				else { $PermissionArray[$Key] = false; }
			}
			return $PermissionArray;
		}
		
		/**
		 * Based on an int value of a person/row's group(s), we spit back an array
		 * with the groups they're in. Note that this is different than the DeterminePermissions()
		 * because it will only return the groups that they are *in*.
		 *
		 * @param int $GroupInt
		 * @return array
		 */
		function DetermineGroups($GroupInt) {
			$GroupArray = array();
			if (isset($this->Groups) and !empty($this->Groups)) {
				foreach ($this->Groups as $Key=>$Int) {
					if (($GroupInt & $Int) != 0) { $GroupArray[$Key] = true; }
				}
				return $GroupArray;
			}
		}
		
		/**
		 * Static function to determine whether the passed query conditions
		 * are for finding a single row, or for multiple rows. 
		 * 
		 * Static because it can be, and it's faster that way.
		 *
		 * @param object(model) $ModelName
		 * @param array $Query
		 * @return boolean
		 */
		static function ModifyQueryForFind($ModelName, $Query) {
			if (is_array($Query['conditions'])) {
				foreach ($Query['conditions'] as $Column=>$Condition) {
					if ($Column == ($ModelName.'.'.$this->ColumnDefaults['ID'])) { return false; }
				}
			}
			return true;
		}
		
	} // Class end