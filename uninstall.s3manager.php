<?php 

/**
 * Joomla! 1.5 component salesforce
 * Code generated by : Danny's Joomla! 1.5 MVC Component Code Generator
 * http://www.joomlafreak.be
 * date generated:  
 * @version 0.8
 * @author Danny Buytaert 
 * @package com_salesforce
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
