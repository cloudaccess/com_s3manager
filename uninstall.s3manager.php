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
	$db->setQuery('DELETE FROM #__plugins WHERE `element`=\'salesforce\' AND `folder`=\'user\'');
	$db->query();
  ?>
<div class="header">Salesforce is sucessfully removed </div>
<p>
  Bye.
</p>
<?php
  }
?>
