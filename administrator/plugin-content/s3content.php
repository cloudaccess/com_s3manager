<?php
defined( '_JEXEC' ) or die( 'Restricted access' );


$mainframe->registerEvent('onPrepareContent', 'plgContentS3');


function plgContentS3(&$row, &$params, $page=0)
{
	if (is_object($row)) 
	{
	}
}

?>
