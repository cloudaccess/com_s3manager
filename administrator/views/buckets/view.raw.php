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
class S3ManagerViewBuckets extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$s3 = S3Helper::getS3();
		
		if($s3)
		{
			$buckets = $s3->listBuckets(0);
			$this->assignRef('buckets', $buckets);

			$locations = array();
			foreach($buckets as $buck)
			{
				$locations[$buck] = $s3->getBucketLocation($buck);
			}
#			$distributions = S3Helper::getDistributions();
			$this->assignRef('locations', $locations);
#			$this->assignRef('distributions', $distributions);
		}
		else
		{
			$this->assign('login_failed', 1);
			$this->assign('login_error', 'No login set');
		}

		parent::display($tpl);
	}
}
