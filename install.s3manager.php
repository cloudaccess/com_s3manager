<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.folder'); 
jimport('joomla.filesystem.file'); 


function com_install()
  {
  ?>
<p>
S3 Media Manager for Joomla!
</p>
<p>
By <img src="http://cloudaccess.net/images/stories/logo_small_png.png" alt="CloudAccess.net" />
</p>
<?php
	//get necessary tools
	$jfolder=new JFolder;
	$jfile=new JFile;
	$db=&JFactory::getDBO();
	$errors = 0;

	echo "<h2>Auto-installing plugin...</h2>";
	echo "<h3>Copying files</h3>";
	echo "<ul>";

	//content plugin
	$files = $jfolder->files(JPATH_ROOT.'/administrator/components/com_s3manager/plugin-system');
	
	foreach($files as $file)
	{
		$result = $jfile->copy(JPATH_ROOT.'/administrator/components/com_s3manager/plugin-system/'.$file, JPATH_ROOT.'/plugins/system/'.$file);
		$tresult = $result ? 'OK' : 'Error';
		echo "<li>$file: $tresult</li>";
		if(!$result)
			$errors++;
	}
	echo "</ul>";

	echo "<h3>Update database</h3>";
	$sql="
	INSERT INTO `#__plugins` 
	(`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`) 
	VALUES
	('System - S3 Media Manager', 's3system', 'system', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');";
	$db->setQuery($sql);
	if(!$db->query())
	{
		$errors++;
	}
	echo "<p>Updating plugin database: ".$db->stdErr() . "</p>";

	$sql="CREATE TABLE IF NOT EXISTS `#__s3_linkcache` (
	`bucket` varchar(255) NOT NULL,
	`object` varchar(255) NOT NULL,
	`expires` int(11) NOT NULL,
	`link` text NOT NULL,
	PRIMARY KEY  (`bucket`,`object`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
	$db->setQuery($sql);
	if(!$db->query())
	{
		$errors++;
	}
	echo "<p>Creating cache table: ".$db->stdErr() . "</p>";


	if($errors == 0)
	{
		echo "<h2>Success.</h2>";
	}
	else
	{
		echo "<h2>Failure</h2><p>$errors errors were encountered during installation. Please solve problems causing these errors and install again.</p>";
	}
  }
?>
