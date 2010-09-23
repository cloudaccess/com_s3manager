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
jimport('joomla.application.router');
header("Cache-Control: no-store, no-cache, must-revalidate");


?>
<?php
foreach($this->buckets as $bucket)
{
	$ebucket = addcslashes($bucket, "\\\'\"&\n\r<>");
	echo '<div id="bucket-' . $bucket . '">';
	/* Delete */
	echo '<a href="#" onclick="deleteBucket(\''.$ebucket.'\'); return false;"><img src="components/com_media/images/remove.png" /></a>';
	if(false){
	/* CloudFront icon */
	if(isset($this->distributions[$bucket]))
	{
		echo '<a href="" onclick="deleteDistribution(\''.$bucket.'\'); return false;" title="Disable CloudFront, status: '.$this->distributions[$bucket]['status'].', enabled: '.$this->distributions[$bucket]['enabled'].'" /><img src="components/com_s3manager/assets/cloudfront-on.png" alt="Enabled" /></a>';
	}
	else
	{
		echo '<a href="" onclick="createDistribution(\''.$bucket.'\'); return false;" title="Enable CloudFront" /><img src="components/com_s3manager/assets/cloudfront-off.png" alt="Disabled" /></a>';
	}
	}
	/* Bucket icon */
	echo '<img src="components/com_s3manager/assets/bucket.png" />';
	/* Bucket name and link */
	echo '<a href="#" onclick="showBucket(\''.$ebucket.'\'); return false;">' . JHTML::tooltip($this->locations[$bucket], 'Location', '', $bucket) . '</a></div>';
}
?>
