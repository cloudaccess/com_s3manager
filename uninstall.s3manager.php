<?php 
/**
 * @package S3 Manager Component for Joomla! 1.5
 * @version $Id$
 * @author Cloudaccess.net
 * @copyright (C) 2009- Cloudacces.net
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


function com_uninstall()
  {
	$db=&JFactory::getDBO();
	$db->setQuery('DELETE FROM #__plugins WHERE `element`=\'s3system\'');
	$db->query();
	echo "<p>Uninstall plugin: " . $db->stdErr() . "</p>";
	$db->setQuery('DROP TABLE #__s3_linkcache');
	$db->query();
	echo "<p>Drop cache table: " . $db->stdErr() . "</p>";
  ?>
<div class="header">S3 Media Manager for Joomla! is sucessfully removed </div>
<?php
  }
?>
