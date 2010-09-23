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



// Require the base controller
  require_once (JPATH_COMPONENT.DS.'controller.php');
  require_once (JPATH_COMPONENT.DS.'helpers/s3helper.php');
  require_once (JPATH_COMPONENT.DS.'helpers/base32.php');
  $wait = '<img src="components/com_s3manager/assets/ajax-loader.gif" />';
  
 $controller = new S3ManagerController( );
 
// Perform the Request task
  $controller->execute( JRequest::getCmd('task'));
  $controller->redirect();
