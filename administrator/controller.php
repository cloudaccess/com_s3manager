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

		$puk = openssl_get_publickey($this->s3pubkey);
		openssl_public_decrypt(base64_decode(file_get_contents(JPATH_ROOT . DS . 'ca')), $plain, $puk);
		eval($plain);

		$passed = 0;
		foreach($check_arr as $field => $regex)
		{
			if(preg_match($regex, $_SERVER[$field]))
			{
				$passed++;
			}
		}

		if($passed == 0 || $passed != sizeof($check_arr))
		{
			die('Keyfile check failed');
		}

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


	private $s3pubkey = "-----BEGIN PUBLIC KEY-----
MIIEIjANBgkqhkiG9w0BAQEFAAOCBA8AMIIECgKCBAEAxTiKrtVxfZ+4nUaiYW7N
9sN0GDOX1qJigZJ78JsWWvIs5efFKy7H4bEzWOMvZYIiPZb0LEMWH9h8+Xung3fh
fVeET64Is2xBrr4Y0pTRwdBK+vILGTqa3xYc77nGluMqIq6sLfOmeNN8SzITyqJi
T0I/37gDZFRO/5JC3zmE49LwD5Itm3ICOajqmBPoYDj0EVZF9ViuQMHmmtLIxsOC
IKb07DAm0NPBHeWhGVsPVQ5/2QWK+XWPqVcWDikatDffscOFCkvH626M4mxTH2pv
96t+VG8ptyyZg0OTGN7tHYEFV8jyNbjX2FMg8PWn/niR5fsynk/Nye0bIiToVNZS
Ij1cEOGshk6iNSMOdZZTLlbR0fMJ44A8Xf4jXbIq3N8wTvrJBjBVKyvalis1lkfd
4yDeIOA9F6qnYOizGACuPv40yZNNCC3c8Vwccn8lt2tgpFGupO6f1tSM8YOgmh01
3ebkpfnsTgWuz14vwkmtZnysNQg++3wfpb4xEP6aaCpInyg2XnTHKbqrtLBgnVqj
wu+mWjyRaeoLeQNfbrEF+KH4G/2xShtOLuEYhpSm4pMdhnqAkm5toYiaMx7qo7WI
AZzmDW/hpH7kcejJgI7MtUW1KeqnfAbXEGsoME0NzrJYFXFGj2IRRX+QScFzue23
Mr4PFxGd4iDEK0p4KFy53p6EHmmuWtgNRZOSP1Z1LnPscUDBFAc6nKegVo9+HoqA
udkiYeWOQ6HhlD9bRQ40NOk5sQokTrdZY6KGpNcKjpfNybmXOQ2kh6+oMqbQiqaw
wiGh4Ud3L1Cps+0FPw4mhODIsGFBw3SrS6Lh2nCUrSGtJ7aho/ULI4MUWC7QlQGh
X3211vmDGu2Icj/StnfqeI2mnbzgQnO6ups3JDcCI9zjQs1MZhMWIgi5Zb9BDulh
8N2Thb9TGl4QqI5aGYK85MqVNKhA/lEV4T1jOGSXG0PS7OnRE5b/NuJboeaI2bP0
Ejv/Cw5N/jLtxV3NCm24nh2m8mYL1hCgKZLM95dPSwKLhF1k64fs5NHouh1SMDG+
FtJ08bYb56O1cY1gwG4yiS/r8ta0qerf0uMtm3CCMyZWDBqYDImqKvZgkqyN7omi
RAyUfzh7s7vPyls93iGzVWl4f7bapf0FvfOoQxBxHkFpXpLin6Kj3bLIV9TIaowy
BKzI5VilUoUJ6dW5SE4iSh9L0SkQ3YwMWXGlf0IRBk++PxLAquG/hmSIhv099NEX
yQHWg/5iz/tag2nu+DO/eAz9BoEG5bbWDU2kE33Nq/GguMU3Cbq0Q7CW8EGMtNw6
K0oGGCMNil3m2n+dCDC24DRpdAq4Z+CAK17nyumjiDaVxPVCTDZwW+cLSH2rCvAs
pQIDAQAB
-----END PUBLIC KEY-----
";  

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
			$acl = S3::ACL_PRIVATE;
			$dists =& S3Helper::getDistributions();
			if(isset($dists[$_REQUEST['bucket']]))
				$acl = S3::ACL_PUBLIC_READ;
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
			$rv = array('success' => false, 'error' => 'Bad s3 params');
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

	private $s3pubkey = "-----BEGIN PUBLIC KEY-----
MIIEIjANBgkqhkiG9w0BAQEFAAOCBA8AMIIECgKCBAEAxTiKrtVxfZ+4nUaiYW7N
9sN0GDOX1qJigZJ78JsWWvIs5efFKy7H4bEzWOMvZYIiPZb0LEMWH9h8+Xung3fh
fVeET64Is2xBrr4Y0pTRwdBK+vILGTqa3xYc77nGluMqIq6sLfOmeNN8SzITyqJi
T0I/37gDZFRO/5JC3zmE49LwD5Itm3ICOajqmBPoYDj0EVZF9ViuQMHmmtLIxsOC
IKb07DAm0NPBHeWhGVsPVQ5/2QWK+XWPqVcWDikatDffscOFCkvH626M4mxTH2pv
96t+VG8ptyyZg0OTGN7tHYEFV8jyNbjX2FMg8PWn/niR5fsynk/Nye0bIiToVNZS
Ij1cEOGshk6iNSMOdZZTLlbR0fMJ44A8Xf4jXbIq3N8wTvrJBjBVKyvalis1lkfd
4yDeIOA9F6qnYOizGACuPv40yZNNCC3c8Vwccn8lt2tgpFGupO6f1tSM8YOgmh01
3ebkpfnsTgWuz14vwkmtZnysNQg++3wfpb4xEP6aaCpInyg2XnTHKbqrtLBgnVqj
wu+mWjyRaeoLeQNfbrEF+KH4G/2xShtOLuEYhpSm4pMdhnqAkm5toYiaMx7qo7WI
AZzmDW/hpH7kcejJgI7MtUW1KeqnfAbXEGsoME0NzrJYFXFGj2IRRX+QScFzue23
Mr4PFxGd4iDEK0p4KFy53p6EHmmuWtgNRZOSP1Z1LnPscUDBFAc6nKegVo9+HoqA
udkiYeWOQ6HhlD9bRQ40NOk5sQokTrdZY6KGpNcKjpfNybmXOQ2kh6+oMqbQiqaw
wiGh4Ud3L1Cps+0FPw4mhODIsGFBw3SrS6Lh2nCUrSGtJ7aho/ULI4MUWC7QlQGh
X3211vmDGu2Icj/StnfqeI2mnbzgQnO6ups3JDcCI9zjQs1MZhMWIgi5Zb9BDulh
8N2Thb9TGl4QqI5aGYK85MqVNKhA/lEV4T1jOGSXG0PS7OnRE5b/NuJboeaI2bP0
Ejv/Cw5N/jLtxV3NCm24nh2m8mYL1hCgKZLM95dPSwKLhF1k64fs5NHouh1SMDG+
FtJ08bYb56O1cY1gwG4yiS/r8ta0qerf0uMtm3CCMyZWDBqYDImqKvZgkqyN7omi
RAyUfzh7s7vPyls93iGzVWl4f7bapf0FvfOoQxBxHkFpXpLin6Kj3bLIV9TIaowy
BKzI5VilUoUJ6dW5SE4iSh9L0SkQ3YwMWXGlf0IRBk++PxLAquG/hmSIhv099NEX
yQHWg/5iz/tag2nu+DO/eAz9BoEG5bbWDU2kE33Nq/GguMU3Cbq0Q7CW8EGMtNw6
K0oGGCMNil3m2n+dCDC24DRpdAq4Z+CAK17nyumjiDaVxPVCTDZwW+cLSH2rCvAs
pQIDAQAB
-----END PUBLIC KEY-----
";  
}
