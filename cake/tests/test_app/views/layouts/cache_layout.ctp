<?php
/* SVN FILE: $Id: cache_layout.ctp 7588 2008-09-09 18:51:28Z phpnut $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *			1785 E. Sahara Avenue, Suite 490-204
 *			Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.view.templates.layouts
 * @since			CakePHP(tm) v 0.10.0.1076
 * @version			$Revision: 7588 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-09-09 15:51:28 -0300 (Tue, 09 Sep 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<p>This is regular text</p>
<cake:nocache>
	<?php echo microtime(); ?>
</cake:nocache>

<?php echo $content_for_layout; ?>

<?php echo $superman; ?>

<cake:nocache>
	<?php echo $variable; ?>
</cake:nocache>
<p>Additional regular text.</p>


