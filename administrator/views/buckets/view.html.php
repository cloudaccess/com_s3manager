<?php 

/**
 * Joomla! 1.5 component s3manager
 * Code generated by : Danny's Joomla! 1.5 MVC Component Code Generator
 * http://www.joomlafreak.be
 * date generated:  
 * @version 0.8
 * @author Danny Buytaert 
 * @package com_s3manager
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


jimport( 'joomla.application.component.view');
/**
 * HTML View class for the s3manager component
 * @static
 * @package S3Manager
 */
class S3ManagerViewBuckets extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$params = JComponentHelper::getParams('com_s3manager');
		
		if(sizeof($params))
		{
			$access = $params->get('accessKey');
			$secret = $params->get('secretKey');

			$s3 = new S3($access, $secret);
			$this->assign('buckets', $s3->listBuckets());
		}
		else
		{
			$this->assign('login_failed', 1);
			$this->assign('login_error', 'No login set');
		}

		parent::display($tpl);
	}
}