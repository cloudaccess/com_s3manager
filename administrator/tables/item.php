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


// Include library dependencies
  jimport('joomla.filter.input');
/**
  * Salesforce Item Table class
  * @package Salesforce
  */
  class TableItem extends JTable {
  /**
  * Primary Key
  *
  * @var int
  */
  var $id = null;
 /**
  * @var int
  */
  var $catid = null;
 /**
  * @var string
  */
  var $title = null;
  
  /**
  * @var string
  */
  var $alias = null;
 /**
  * @var string
  */
  var $key = null;
   /**
  * @var string
  */
  var $ value = null;
  
 /**
  * @var datetime
  */
  var $created = null;
 /**
  * @var int
  */
  var $created_by = null;
 /**
  * @var string
  */
  var $created_by_alias = null;
 /**
  * @var datetime
  */
  var $modified = null;

 /**
  * @var int
  */
  var $modified_by = null;
 /**
  * @var boolean
  */
  var $checked_out = 0;
 /**
  * @var time
  */
  var $checked_out_time = 0;
 /**
  * @var int
  */
  var $published = null;
   /**
  * @var int
  */
  var $ordering = null;
 /**
  * @var int
  */
  var $hits = null;

 /**
  * Constructor
  *
  * @param object Database connector object
  */
  function __construct(& $db) {
  parent :: __construct('#__salesforce', 'id', $db);
  }
 /**
  * Overloaded bind function
  *
  * @acces public
  * @param array $hash named array
  * @return null|string null is operation was satisfactory, otherwise returns an error
  * @see JTable:bind
  */
  function bind($array, $ignore = '') {
  if (key_exists('params', $array) && is_array($array['params'])) {
  $registry = new JRegistry();
  $registry->loadArray($array['params']);
  $array['params'] = $registry->toString();
  }
 return parent :: bind($array, $ignore);
  }
 /**
  * Overloaded check method to ensure data integrity
  *
  * @access public
  * @return boolean True on success
  */
  function check() {
  /** check for valid name */
  if (trim($this->title) == '') {
  $this->_error = JText :: _('Please provide a valid title');
  return false;
  }
 /** check for existing name */
  $query = 'SELECT id FROM #__salesforce WHERE title = ' . $this->_db->Quote($this->title) . ' AND catid = ' . (int) $this->catid;
  $this->_db->setQuery($query);
 $xid = intval($this->_db->loadResult());
  if ($xid && $xid != intval($this->id)) {
  $this->_error = JText :: sprintf('WARNNAMETRYAGAIN', JText :: _('Item'));
  return false;
  }
  if(empty($this->alias)) {
  $this->alias = $this->title;
  }
  $this->alias = JFilterOutput::stringURLSafe($this->alias);
  if(trim(str_replace('-','',$this->alias)) == '') {
  $datenow =& JFactory::getDate();
  $this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
  }
 jimport('joomla.filter.output');
 return true;
  }
  }
