<?php
/**
 * @package S3 Manager Content Plugin for Joomla! 1.5
 * @version $Id$
 * @author Cloudaccess.net
 * @copyright (C) 2009- Cloudacces.net
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_s3manager' . DS . 'helpers' . DS . 's3helper.php');


$mainframe->registerEvent('onPrepareContent', 'plgContentS3');

function s3_handle_url($arr)
{
	return $arr[1] . S3Helper::getAuthenticatedURL($arr[4], $arr[6]);
}

function plgContentS3(&$row, &$params, $page=0)
{
	if (is_object($row)) 
	{
#		$regex = '#(["\']).*index\\.php\\?option=com_s3manager\\&(amp;)?task=redirect\\&(amp;)?bucket=(.+)&(amp;)?object=([^"\']+)#';
		$regex = '#(["\']).*?administrator/index\\.php\\?option=com_s3manager&(amp;)?task=redir&(amp;)?bucket=(.+?)&(amp;)?object=([^"\']+)#';
		$row->text = preg_replace_callback($regex, 's3_handle_url', $row->text);
#		preg_match($regex, $row->text, $matches);
#		$row->text = var_export($matches, 1);
		return true;
	}
}

?>
