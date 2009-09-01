<?php

require_once('S3.php');

class S3Helper
{
	static private $s3;

	static function getS3()
	{
		if(!self::$s3)
		{
			$params = JComponentHelper::getParams('com_s3manager');
			
			if($params)
			{
				$access = $params->get('accessKey');
				$secret = $params->get('secretKey');

				self::$s3 = new S3($access, $secret);
			}
			else
			{
				self::$s3 = NULL;
			}
		}

		return self::$s3;
	}

	/* Gets authenticated URL, will use cloudfront if available */
	static function getAuth2($bucket, $object, $valid_r)
	{
		$s3 = self::getS3();
/*		$dist =& self::getDistributions();
		if(isset($dist[$bucket]))
		{
			$link = 'http://' . $dist[$bucket]['domain'] . '/' . $object;
		}
		else */
		{
			$link = $s3->getAuthenticatedURL($bucket, $object, $valid_r);
		}

		return $link;
	}

	/* Cache-aware authenticated URL generator */
	function getAuthenticatedURL($bucket, $object, $valid = false)
	{
        $db = JFactory::getDBO();
		$q = 'SELECT * FROM #__s3_linkcache WHERE `bucket`='.$db->quote($bucket).' AND `object`='.$db->quote($object);
		$db->setQuery($q);
		if($db->query() && $row = $db->loadAssoc())
		{
			if($row['expires'] < time() + 120)
			{
				$s3 = self::getS3();
				if($valid === false)
				{
					$params = JComponentHelper::getParams('com_s3manager');
					$valid = $params->get('linkValidityPeriod');
				}
				$valid_r = $valid;
				$valid += time();
				$link = self::getAuth2($bucket, $object, $valid_r);
				$db->setQuery('UPDATE #__s3_linkcache SET `link`=' . $db->Quote($link) . ', `expires`=' . $db->Quote(time() + 3600) . 'WHERE `bucket`='.$db->Quote($bucket).' AND `object`=' . $db->Quote($object));
				$db->query();
				return $link;
			}
			else
			{
				return $row['link'];
			}
		}
		else
		{
			$s3 = self::getS3();
			if($valid === false)
			{
				$params = JComponentHelper::getParams('com_s3manager');
				$valid = $params->get('linkValidityPeriod');
			}
			$valid_r = $valid;
			$valid += time();
			$link = self::getAuth2($bucket, $object, $valid_r);
			$q = 'INSERT INTO #__s3_linkcache(`bucket`, `object`, `link`, `expires`) VALUES(' . $db->Quote($bucket) . ', ' .$db->Quote($object) . ', ' . $db->Quote($link) . ', ' . $db->Quote(time() + 3600) . ')';
			$db->setQuery($q);
			$db->query();
			return $link;
		}
	}

	function getDistributions()
	{
		$s3 = self::getS3();
		$distribs = $s3->listDistributions();
		$distributions = array();
		foreach($distribs as $d)
		{
			$origin = $d['origin'];
			$x = explode('.', $origin);
			$buck = $x[0];
			$distributions[$buck] = $d;
		}

		return $distributions;
	}
}
