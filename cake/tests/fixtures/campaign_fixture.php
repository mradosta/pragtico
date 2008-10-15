<?php 
/* SVN FILE: $Id: campaign_fixture.php 7588 2008-09-09 18:51:28Z phpnut $ */
/**
 * Short description for campaign_fixture.php
 * 
 * Long description for campaign_fixture.php
 * 
 * PHP versions 4 and 5
 * 
 * CakePHP(tm) : Rapid Development Framework <http://www.cakephp.org/>
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @filesource
 * @copyright            CakePHP(tm) : Rapid Development Framework <http://www.cakephp.org/>
 * @link                 http://www.cakephp.org
 * @package              cake
 * @subpackage           cake.tests.fixtures
 * @since                1.2
 * @version              $Revision: 7588 $
 * @modifiedBy           $LastChangedBy: phpnut $
 * @lastModified         $Date: 2008-09-09 15:51:28 -0300 (Tue, 09 Sep 2008) $
 * @license              http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * CampaignFixture class
 * 
 * @package              cake
 * @subpackage           cake.tests.fixtures
 */
class CampaignFixture extends CakeTestFixture {
/**
 * name property
 * 
 * @var string 'Campaign'
 * @access public
 */
	var $name = 'Campaign';    
/**
 * fields property
 * 
 * @var array
 * @access public
 */
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'length' => 255, 'null' => false),
	); 
/**
 * records property
 * 
 * @var array
 * @access public
 */
	var $records = array(
		array('name' => 'Hurtigruten'),
		array('name' => 'Colorline'),
		array('name' => 'Queen of Scandinavia')
	);
}

?>