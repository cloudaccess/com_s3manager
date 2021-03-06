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

/**
  * Return human readable sizes
  *
  * @author      Aidan Lister <aidan@php.net>
  * @version     1.3.0
  * @link        http://aidanlister.com/repos/v/function.size_readable.php
  * @param       int     $size        size in bytes
  * @param       string  $max         maximum unit
  * @param       string  $system      'si' for SI, 'bi' for binary prefixes
  * @param       string  $retstring   return string format
  */
function size_readable($size, $max = null, $system = 'si', $retstring = '%01.2f %s')
{
	// Pick units
	$systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
	$systems['si']['size']   = 1000;
	$systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
	$systems['bi']['size']   = 1024;
	$sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];

	// Max unit to display
	$depth = count($sys['prefix']) - 1;
	if ($max && false !== $d = array_search($max, $sys['prefix'])) {
		$depth = $d;
	}

	// Loop
	$i = 0;
	while ($size >= $sys['size'] && $i < $depth) {
		$size /= $sys['size'];
		$i++;
	}

	return sprintf($retstring, $size, $sys['prefix'][$i]);
}

?>
<?php

$num = 0;
if(!sizeof($this->files))
{
	?>
No files in bucket
	<?php
}
foreach($this->files as $file)
{
	echo '<div class="file-div" style="float: left;" id="file-'.$file['name'].'">';
	echo '<input type="hidden" id="file-size-' . $file['name'] . '" name="file-size-' . $file['name'] . '" value="' . $file['size'] . '" />';
	$tip = 'Size: ' . $file['size'];
	$thumb = '';
	$preview = '';
	$x = explode('.', $file['name']);
	$extension = strtolower($x[sizeof($x) - 1]);
	if(in_array($extension, array('png', 'jpg', 'jpeg', 'gif', 'svg', 'mng')))
	{
		$thumb = $this->links[$file['name']];
		$preview = 'modal';
	}                          
	elseif(file_exists(JPATH_ADMINISTRATOR . '/components/com_s3manager/assets/mime/' . $extension . '.png'))
	{
		$thumb = 'components/com_s3manager/assets/mime/' . $extension . '.png';
	}
	else
	{
		$thumb = 'components/com_s3manager/assets/unknown.png';
	}

	echo '<div align="center">';
	/* Thumbnail link */
	echo '<a  href="' . $this->links[$file['name']] . '"  rel="{handler: \'iframe\', size: {x: 1000, y: 700}}" class="' . $preview . '">';
	/* Thumbnail */
	echo '<img src="' . $thumb . '" height="64" /></a><br />';
	/* Todo : C-style escape */
	/* Delete link */
	echo '<a href="" onclick="removeObject(\''.$_GET['bucket'].'\', \'' . $file['name'] . '\', '.$num.'); return false;" title="Remove object"><img src="components/com_media/images/remove.png" alt="Remove" /></a>';
	/* Checkbox */
	if(false) echo '<input type="checkbox" name="obj-' . base32_encode($file['name']) . '" />';
	
	/* URL */
	echo '<a href="" onclick="showURLBox(\''.JURI::root(true).'\', \''.$_GET['bucket'].'\', \''.$file['name'].'\'); return false;"><img src="components/com_s3manager/assets/icon_hand.gif" title="Get link URL" /></a>';
	/* Size */
	echo '&nbsp;<span style="color: gray">' . size_readable($file['size']) . '</span>';
	echo '</div>';
	/* Name */
	echo '<div align="center">';
	echo JHTML::tooltip($tip, 'Properties', '', $file['name']);
	echo '</div>';
	echo '</div>';

	$num++;
}
?>
</table>
