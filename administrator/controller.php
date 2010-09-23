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


jimport( 'joomla.application.component.controller' );
/**
 * S3Manager Controller
 *
 * @package S3Manager
 */
class S3ManagerController extends JController
{
	function __construct()
	{
		parent::__construct();

	}

	function display( )
	{
		parent::display();
	}
	function save()
	{
		$link = 'index.php?option=com_s3manager';
		$db = JFactory::getDBO();
		$this->setRedirect( $link, $this->msg);

	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_s3manager' );
	}


	function deleteObject()
	{
		$s3 = S3Helper::getS3();
		
		if($s3)
		{
			$rv = $s3->deleteObject($_GET['bucket'], $_GET['object']);
			$db = JFactory::getDBO();
			$db->setQuery('DELETE FROM #__s3_linkcache WHERE `bucket`='.$db->Quote($_REQUEST['bucket']).' AND `object`=' . $db->Quote($_REQUEST['object']));
			$db->query();
			die($rv ? '1' : '0');
		}
		die('0');
	}

	function upload()
	{
		$s3 = S3Helper::getS3();
		$rv = array();
		if($s3)
		{                              
			$acl = com_s3manager_S3::ACL_PRIVATE;
			$dists =& S3Helper::getDistributions();
			if(isset($dists[$_REQUEST['bucket']]))
				$acl = com_s3manager_S3::ACL_PUBLIC_READ;
			$file = $_FILES['file']['tmp_name'];
			$s3rv = $s3->putObject($s3->inputFile($file), $_REQUEST['bucket'], basename($_FILES['file']['name']), $acl, array(), array( "Cache-Control" => "max-age=315360"));
			if($s3rv)
			{
				$rv = array('success' => true);
			}
			else
			{
				$rv = array('success' => false, 'error' => 'upload to s3 failed');
			}
		}
		else
		{
			$rv = array('success' => false, 'error' => 'Bad s3 access keys. Go to parameters and change settings.');
		}
		$rv['bucket'] = $_REQUEST['bucket'];

		die(json_encode($rv));
	}

	function createBucket()
	{
        $s3 = S3Helper::getS3();
		if($s3->putBucket($_REQUEST['bucket']))
		{
			die('1');
		}
		die('0');
	}

	function deleteBucket()
	{
        $s3 = S3Helper::getS3();
		if($s3->deleteBucket($_REQUEST['bucket']))
		{
			die('1');
		}
		die('0');
	}

	function createDistribution()
	{
        $s3 = S3Helper::getS3();
		
		/* Make files public */
		foreach($s3->getBucket($_REQUEST['bucket']) as $obj)
		{
			$acl = $s3->getAccessControlPolicy($_REQUEST['bucket'], $obj);
			$acl[] = array('type' => 'Group', 'uri' => 'http://acs.amazonaws.com/groups/global/AllUsers', 'permission' => 'READ');

			$s3->setAccessControlPolicy($_REQUEST['bucket'], $obj, $acl);
		}

#		$s3->setAccessControlPolicy($_REQUEST['bucket'], '', $acl);

		/* Delete cache */
		$db = JFactory::getDBO();
		$db->setQuery('DELETE FROM #__s3_linkcache WHERE `bucket`='.$db->Quote($_REQUEST['bucket']));
		$db->query();

		/* Create the distro */
		if($s3->createDistribution($_REQUEST['bucket']))
		{
			die('1');
		}
		die('0');
	}

	function deleteDistribution()
	{
        $s3 = S3Helper::getS3();
		
		/* Make files public */
		$acl = array(array());
		foreach($s3->getBucket($_REQUEST['bucket']) as $obj)
		{
			$s3->setAccessControlPolicy($_REQUEST['bucket'], $obj, $acl);
		}


		/* Delete cache */
		$db = JFactory::getDBO();
		$db->setQuery('DELETE FROM #__s3_linkcache WHERE `bucket`='.$db->Quote($_REQUEST['bucket']));
		$db->query();

		/* Create the distro */
		$dists =& S3Helper::getDistributions();
		$d = $s3->getDistribution($dists[$_REQUEST['bucket']]['id']);
		$d['enabled'] = false;
#		if(!$s3->updateDistribution($d))
		{
#			die('could not update');
		}
		$d = $s3->getDistribution($dists[$_REQUEST['bucket']]['id']);
		var_dump($d);
		if($s3->deleteDistribution($d))
		{
			die('1');
		}
		die('0');
	}

	function redir()
	{
		$bucket = $_REQUEST['bucket'];
		/* Todo: bucket acl? */
		$object = $_REQUEST['object'];
		$url = S3Helper::getAuthenticatedURL($bucket, $object);
		header('Location: ' . $url);
		die();
	}

}
