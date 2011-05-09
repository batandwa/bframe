<?php
class Notification extends Base
{
	const NOTIF_NOTICE = 1;
	const NOTIF_ERROR = 2;
	const NOTIF_WARNING = 4;
	const NOTIF_SUCCESS = 8;
	const NOTIF_INFORMATION = 16;
	const NOTIF_DEBUG = 32;

	/**
	 * The notification index where notificaitons will be stored in $_SESSION.
	 */
	const SESSION_INDEX = "notixx";

	/**
	 * The errors generated by the application
	 *
	 * @var array The categoriese error list
	 */
	public $data = null;

	/**
	 * Returns the Singleton instance of the Notification class.
	 *
	 * @var Notification An instance of Notification.
	 */
	private static $instance;

	private function __construct()
	{
		$this->init_data();
	}
	
	public static function &instance()
	{
		if((!isset(self::$instance)))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	
	public function __clone()
	{
	    trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	
	public function __toString()
	{
		return $this->printNotifications();
	}
	
	/**
	 * Adds notification to be shown to user.
	 * @param $msg The message to be shown.
	 * @param $type The type of notification. The types are indicated on the Notification class as final members.
	 */
	public function addNotification($msg, $type=self::NOTIF_INFORMATION)
	{
		switch(true)
		{
			case(($type & self::NOTIF_NOTICE) != 0):
			{
				$elm = "notice";
				break;
			}
			case(($type & self::NOTIF_ERROR) != 0):
			{
				$elm = "error";
				break;
			}
			case(($type & self::NOTIF_WARNING) != 0):
			{
				$elm = "warning";
				break;
			}
			case(($type & self::NOTIF_SUCCESS) != 0):
			{
				$elm = "success";
				break;
			}
			case(($type & self::NOTIF_INFORMATION) != 0):
			{
				$elm = "information";
				break;
			}
			case(($type & self::NOTIF_DEBUG) != 0):
			{
				$elm = "debug";
				break;
			}
		}

		array_push($this->data[$elm], $msg);
		$this->save_global();
	}

	public function printNotifications()
	{
		$return = '<div class="notifications floating">';
		foreach($this->data as $catName => $category)
		{
			$return .= '<p class="'.$catName.' section">';
			foreach($category as $not)
			{
				$not = str_replace("\n", "<br />", $not);
				$return .=  $not."<br />";
			}
			$return .=  "</p>";
		}
		$return .=  "</div>";

		return $return;
	}

	public function retreiveNotifications()
	{
		$ntf = Request::get(self::SESSION_INDEX, "session", array(), "array");

		$this->init_data();
		
		if(!empty($ntf))
		{
			foreach($ntf as $cat_name => $cat_val)
			{
				$this->data[$cat_name] = $cat_val;
			}
		}
		else
		{
//			$this->init_data();
		}
		
		foreach($this->data as $cat_name => $cat_val)
		{
			if(is_null($cat_val))
			{
				$this->data[$cat_name] = array();
			}
		}
		
		$this->save_global();
	}
	
	private function init_data()
	{
		$this->data = array("notice" => array(), "error"=>array(), "warning"=>array(), "success"=>array(), "information"=>array(), "debug"=>array());
	}
	
	public function clear()
	{
		$this->init_data();
		$this->save_global();
	}
	
	private function save_global()
	{
		$_SESSION[self::SESSION_INDEX] = $this->data;
	}
}