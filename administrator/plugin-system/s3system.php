<?php
/**
 * @package S3 Manager System Plugin for Joomla! 1.5
 * @version $Id$
 * @author Cloudaccess.net
 * @copyright (C) 2009- Cloudacces.net
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_s3manager' . DS . 'helpers' . DS . 's3helper.php');


$mainframe->registerEvent('onAfterRender', 'plgSystemS3');

function s3_handle_url($arr)
{
	return $arr[1] . str_replace('&', '&amp;', S3Helper::getAuthenticatedURL($arr[4], $arr[6]));
}

function plgSystemS3()
{
	$app =& JFactory::getApplication();

	if($app->getName() != 'site') {
		return true;
	}

	$regex = '#(["\'])administrator/index\\.php\\?option=com_s3manager&(amp;)?task=redir&(amp;)?bucket=(.+?)&(amp;)?object=([^"\']+)#';

	$buffer = JResponse::getBody();
    $buffer = preg_replace_callback($regex, 's3_handle_url', $buffer);
	JResponse::setBody($buffer);

	return true;
}

?>
