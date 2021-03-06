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


?>
<?php JHTML::_('behavior.tooltip'); ?>
<?php
  // Set toolbar items for the page
  JToolBarHelper::title('Amazon Simple Storage Service', 's3manager_title');
//  JToolBarHelper::save();
//  JToolBarHelper::cancel();
  JToolBarHelper::preferences('com_s3manager', '400');
  $document =& JFactory::getDocument();
  $document->addStyleSheet(JURI::base(true) . '/components/com_s3manager/assets/icon.css', 'text/css', null, array());
  $document->addScript(JURI::base(true) . '/components/com_s3manager/assets/funky.js');
  $document->addScript(JURI::base(true) . '/components/com_s3manager/assets/ajaxupload.js');
  $document->addScriptDeclaration("window.addEvent('domready', function() {window.ajaxupload = new AjaxUpload('upload-div-id', {action: 'index2.php', data: {option: 'com_s3manager', task: 'upload'}, name: 'file', responseType: 'json', onComplete: uploadComplete, onSubmit: uploadSubmitted});});");

  ?>
<form action="index.php" method="post" name="adminForm">
<input type="hidden" name="option" value="com_s3manager" />
<input type="hidden" name="task" value="" />

<?php if($this->login_failed) {
	?>
<h2 style="color:red">S3 login failed</h2>
<p>Please go to Parameters and set proper S3 credentials.</p>
<p>Error details: <pre><?=htmlspecialchars($this->login_error) ?></pre></p>
<?php
} else {
	?>
<table id="s3-table">
<tr>
<td valign="top" width="30%">
<h3>Buckets:&nbsp;<a href="" onclick="showBuckets(); return false;" title="refresh" />&#9099;</a></h3><?php /* &#9099; &#10211; &#1023; */ ?>
<div id="buckets">&nbsp;</div>
<b>New bucket name:</b><br />
<input type="text" size="13" name="new-bucket" id="new-bucket" /><br />
<button onclick="createBucket($('new-bucket').value); return false;">Create bucket</button>
</td><td valign="top">
<h3 id="files-in-bucket">Files:</h3>
<p>Sort by: 
<a href="" onclick="showBucket(window.bucket, 'name'); return false;">Name</a> |
<a href="" onclick="showBucket(window.bucket, 'size'); return false;">Size (desc)</a> |
<a href="" onclick="showBucket(window.bucket, 'size-rev'); return false;">Size (asc)</a>
</p>
<div id="files">Please select a bucket...</div>
</td></tr><td>
</td><td>
<div width="100%">
	<div class="upload">
		<button id="upload-div-id">Upload to bucket</button>
	</div>
	<h3 id="upload-pending" style="display:none">Pending uploads</h3>
	<div id="upload-queue">
	</div>
</div>
</td></tr></table>
<div id="bubble-box" style="display: none; background: yellow; padding: 1em;">&nbsp;</div>
<?php
} // login_failed
?>
</form>
<p class="copyright">Amazon Simple Storage Service integration &copy; by <a href="http://cloudaccess.net">CloudAccess.net</a></p>
