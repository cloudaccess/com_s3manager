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


jimport( 'joomla.application.component.view');
/**
 * HTML View class for the s3manager component
 * @static
 * @package S3Manager
 */
class S3ManagerViewS3Manager extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$params = JComponentHelper::getParams('com_s3manager');
		
		if(sizeof($params))
		{
		}
		else
		{
			$this->assign('login_failed', 1);
			$this->assign('login_error', 'No login set');
		}

		parent::display($tpl);
	}
}
