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

function s3_size_cmp($a, $b)
{
	return ($a['size'] < $b['size']) ? 1 : -1;
}

function s3_size_rev_cmp($a, $b)
{
	return ($a['size'] > $b['size']) ? 1 : -1;
}

function s3_name_cmp($a, $b)
{
	return strcasecmp($a['name'], $b['name']);
}

/**
 * HTML View class for the s3manager component
 * @static
 * @package S3Manager
 */
class S3ManagerViewFiles extends JView
{
	function display($tpl = null)
	{
		global $mainframe, $option;

		$db =& JFactory::getDBO();
		$uri =& JFactory::getURI();
		$s3 = S3Helper::getS3();
		
		if($s3)
		{
			$files =& $s3->getBucket($_GET['bucket']);
			$links = array();
			foreach($files as $key => $file)
			{
				$links[$file['name']] = S3Helper::getAuthenticatedURL($_GET['bucket'], $file['name']);
			}
			if(isset($_REQUEST['sortby']))
			{
				$by = $_REQUEST['sortby'];
				if($by == 'size')
				{
					usort($files, 's3_size_cmp');
				}
				elseif($by == 'size-rev')
				{
					usort($files, 's3_size_rev_cmp');
				}
				elseif($by == 'name')
				{
					usort($files, 's3_name_cmp');
				}
			}
			$this->assignRef('files', $files);
			$this->assignRef('links', $links);
		}
		else
		{
			$this->assign('login_failed', 1);
			$this->assign('login_error', 'No login set');
		}

		parent::display($tpl);
	}
}
