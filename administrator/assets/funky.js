/**
 * @package S3 Manager Component for Joomla! 1.5
 * @version $Id$
 * @author Cloudaccess.net
 * @copyright (C) 2009- Cloudacces.net
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
**/
/* Gets wait picture tag */
function getWaiter() {return '<img src="components/com_s3manager/assets/ajax-loader.gif" />';}
function getError() {return '<p style="color: red; font-size: 12pt;">Error!</p>';}

function updatePendingOperations()
{
	if(document.pendingOperations <= 0)
	{
		$('upload-pending').style.display = 'none';
	}
	else
	{
		var operation = 'operation';
		if(document.pendingOperations > 1)
		{
			operation = 'operations';
		}
		$('upload-pending').innerHTML = document.pendingOperations.toString() + ' pending ' + operation;
		$('upload-pending').style.display = 'block';
	}
}

function startOperation(id, message)
{
	if($(id))
	{
		endOperation(id);
	}
	document.pendingOperations++;
	$('upload-queue').innerHTML += '<p class="upload-queue-element" id="'+id+'">' + message + '&nbsp;' + getWaiter() + '</p>';
	updatePendingOperations();
}

function endOperation(id)
{
//	var slide = new Fx.Slide(id);
//	slide.addEvent('complete', function() {$('upload-queue').removeChild($(id))});
//	slide.slideOut();
	$('upload-queue').removeChild($(id))
	document.pendingOperations--;
	updatePendingOperations();
}

function failOperation(id, msg)
{
	alert(msg);
	el = $(id);
	el.innerHTML = msg;
	el.removeClass('upload-queue-element');
	el.addClass('upload-queue-element-error');
	updatePendingOperations();
}


function showBubbleBox(text, e)
{
	if(!e)
	{
		e = window.event;
	}
	var box = $('bubble-box');
	box.innerHTML = text + '<button id="bubble-box-close" onclick="$(\'bubble-box\').style.display=\'none\'; return false;" />X</button>';
	box.style.position = 'absolute';
//	box.style.left = (e.clientX + window.pageXOffset).toString() + 'px';
//	box.style.top = (e.clientY + window.pageYOffset).toString() + 'px';
	box.style.left = (window.mouseX).toString() + 'px';
	box.style.top = (window.mouseY).toString() + 'px';
	box.style.z_index = '123';
	box.style.display='block';
}

//////////////////////////////////////////////////////////////////

/* Shows list of buckets */
function showBuckets() {
	startOperation('show-buckets', 'Reading list of buckets');
	$('buckets').innerHTML = getWaiter();
	var req = new Ajax('index3.php?option=com_s3manager&view=buckets&format=raw',			{ method: 'GET',
onComplete: function(response) {$('buckets').innerHTML = response; endOperation('show-buckets');}
}).request();
}

function uploadComplete(file, response)
{
	if(response.success)
	{
		showBucket(response.bucket);
		var id = 'queue-' + file;
//		$('upload-queue').removeChild($(id));
		endOperation(id);
	}
	else
	{
		var id = 'queue-' + file;
		failOperation(id, 'Upload of ' + file + ' failed: ' + response.error);
	}
}

/* Shows content of a bucket */
function showBucket(bucket, sortby) {
	$('files').innerHTML = getWaiter();
	startOperation('read-bucket-' + bucket, 'Reading content of bucket <b>' + bucket + '</b>');
	$('files-in-bucket').innerHTML = 'Files in bucket ' + bucket + ':';
	var req = new Ajax('index3.php?option=com_s3manager&view=files&bucket=' + escape(bucket) + '&format=raw&sortby=' + sortby,			
	{ 
		method: 'GET',
		onComplete: function(response) {
			window.bucket = bucket;
			$('files').innerHTML = response; 
			SqueezeBox.initialize({});
			$$('a.modal').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBox.fromElement(el);
				});
			});
			window.ajaxupload.setData({
				option: 'com_s3manager', 
				task: 'upload',
				bucket: bucket
				});
			endOperation('read-bucket-' + bucket);
		},
		onFailure: function() {$('files').innerHTML = getError(); failOperation('read-bucket-' + bucket);}
	}).request();
}

function removeObject(bucket, filename, num)
{
	if(!confirm('Do you really want to delete file ' + filename + '?'))
	{
		return;
	}
	startOperation('queue-delete-'+filename, 'Deleting <b>' + filename + '</b> from bucket ' + bucket);
	var req = new Ajax('index3.php?option=com_s3manager&view=files&format=raw&task=deleteObject&bucket=' + escape(bucket) + '&object=' + escape(filename),
	{ 
		method: 'GET',
		onComplete: function(response) {
			if(response == '1')
			{
				var id = 'queue-delete-' + filename;
				endOperation(id);
				$('file-' + filename).style.display='none';
			}
			else
			{
				var id = 'queue-delete-' + filename;
				failOperation(id, 'Deleting file ' + filename + ' failed');
			}
	   	}
	}).request();
}

function createBucket(name)
{
	if(name == "")
	{
		alert("Can't create bucket with empty name");
		return;
	}
	
	startOperation('create-bucket-'+ name, 'Creating bucket <b>' + name+'</b>');
	var req = new Ajax('index3.php?option=com_s3manager&view=buckets&format=raw&task=createBucket&bucket=' + escape(name),
	{ 
		method: 'GET',
		onComplete: function(response) {
			if(response == '1')
			{
				var id = 'create-bucket-' + name;
				endOperation(id);
				showBuckets();
			}
			else
			{
				var id = 'create-bucket-' + name;
				failOperation(id, 'Creating bucket ' + name + ' failed. Possibly the name is already taken.');
			}
	   	}
	}).request();
}


function deleteBucket(name)
{
	if(!confirm('Do you really want to delete bucket ' + name + '?'))
	{
		return;
	}
	startOperation('delete-bucket-'+ name, 'Deleting bucket <b>' + name+'</b>');
	var req = new Ajax('index3.php?option=com_s3manager&view=buckets&format=raw&task=deleteBucket&bucket=' + escape(name),
	{ 
		method: 'GET',
		onComplete: function(response) {
			if(response == '1')
			{
				var id = 'delete-bucket-' + name;
				endOperation(id);
				var slide = new Fx.Slide('bucket-'+name);
				slide.slideOut();
			}
			else
			{
				var id = 'delete-bucket-' + name;
				failOperation(id, 'Deleting bucket ' + name + ' failed. Is the bucket empty?');
			}
	   	}
	}).request();
}

function createDistribution(bucket)
{
	if(!confirm('Do you really want to create CloudFront distribution of ' + bucket + '?'))
	{
		return;
	}
	startOperation('dist-create-'+bucket, 'Creating CloudFront distribution based on bucket <b>' + bucket + '</b>');
	var req = new Ajax('index3.php?option=com_s3manager&view=buckets&format=raw&task=createDistribution&bucket=' + escape(bucket),
	{ 
		method: 'GET',
		onComplete: function(response) {
			if(response == '1')
			{
				var id = 'dist-create-' + bucket;
				endOperation(id);
				showBuckets();
			}
			else
			{
				var id = 'dist-create-' + bucket;
				failOperation(id, 'Creating distribution of ' + bucket + ' failed');
			}
	   	}
	}).request();
}

function deleteDistribution(bucket)
{
	if(!confirm('Do you really want to create CloudFront distribution of ' + bucket + '?\nThis will not delete your files.'))
	{
		return;
	}
	startOperation('dist-delete-'+bucket, 'Deleting CloudFront distribution based on bucket <b>' + bucket + '</b>');
	var req = new Ajax('index3.php?option=com_s3manager&view=buckets&format=raw&task=deleteDistribution&bucket=' + escape(bucket),
	{ 
		method: 'GET',
		onComplete: function(response) {
			if(response == '1')
			{
				var id = 'dist-delete-' + bucket;
				endOperation(id);
				showBuckets();
			}
			else
			{
				var id = 'dist-delete-' + bucket;
				failOperation(id, 'Deleting distribution of ' + bucket + ' failed');
			}
	   	}
	}).request();
}


function uploadSubmitted(file, extension)
{
	$('upload-queue').innerHTML += '<p class="upload-queue-element" id="queue-'+file+'">Uploading <b>' + file + '</b>&nbsp;' + getWaiter() + '</p>';
	document.pendingOperations++;
	updatePendingOperations();
}

function showURLBox(root, bucket, obj, e)
{
	showBubbleBox('Please use this URL when inserting link to media in articles:<br><input id="bubble-box-url" type="text" size="50" value="' + root + 'administrator/index.php?option=com_s3manager&amp;task=redir&amp;bucket=' + escape(bucket) + '&amp;object=' + escape(obj) + '" />');
	$('bubble-box-url').addEventListener('focus', function(e){
				this.select();
				if(window.clipboardData)
				{
					clipboardData.setData('Text', this.value);
				}
			}, false);
}

/* Store mouse position */
window.addEvent('mousemove', function(e) {	var posx = 0;
		var posy = 0;
		if (!e) var e = window.event;
		if (e.pageX || e.pageY) 	{
		posx = e.pageX;
		posy = e.pageY;
		}
		else if (e.clientX || e.clientY) 	{
		posx = e.clientX + document.body.scrollLeft
		+ document.documentElement.scrollLeft;
		posy = e.clientY + document.body.scrollTop
		+ document.documentElement.scrollTop;
		}
		window.mouseX = posx;
		window.mouseY = posy;
		});

/* Preload the gay ajax loader */
if(document.images)
{
	pic = new Image(32, 32);
	pic.src = 'components/com_s3manager/assets/ajax-loader.gif';
}

/* Show buckets on start */
window.addEvent('domready', function() {document.pendingOperations = 0;showBuckets();});
window.addEvent('domready', function() {$('files').innerHTML='<h1>Please select a bucket on the left</h1>'; });
