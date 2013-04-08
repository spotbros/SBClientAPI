<?php
require_once(__DIR__.'/SBApp.php');
require_once(__DIR__.'/includes/predis/predis.php');
/**
 * SBPersistentApp
 *
 * @author Spotbros <support@spotbros.com>
 *
 */
abstract class SBPersistentApp extends SBApp
{
	// the redis client object
	private $_rClient;
	/**
	 * Constructor
	 * @param string $key_	the app key
	 * @param string $SBCode_	the app sbcode
	 * @param string $ip_	the IP of the host where redis server is running
	 * @param string $port_	the port on which redis server is running
	 */
	public function __construct($key_, $SBCode_, $ip_="127.0.0.1", $port_="6379")
	{
		$this->_rClient=new Predis_Client(array('host'=>$ip_,'port'=>$port_,'connection_timeout'=>0.1));
		parent::__construct($key_,$SBCode_);
	}
	/**
	 * Verifies whether the connection to redis is OK
	 * @return bool	true if connection succeeds, false otherwise
	 */
	private function isRDBConnectionOk()
	{
		try
		{
			if(!$this->_rClient->isConnected())
			{
				$this->_rClient->connect();
				if(!$this->_rClient->isConnected())
				{
					return false;
				}
			}
			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}
	/**
	 * Sets the value of a key
	 * @param string $key_	the key to be set
	 * @param string $value_	the value of the key
	 * @param int $ttl_	the ttl for that key (when it will expire)
	 * @return true|false true if the key was set or false if there was any error with the key/connection
	 */
	protected function setOrFalse($key_,$value_,$ttl_=0)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				$redisKey = $this->_appSBCode."_".$key_;
				$this->_rClient->set($redisKey,$value_);
				if(is_numeric($ttl_) && ($ttl_>0))
				{
					$this->_rClient->expire($redisKey, $ttl_);
				}
				return true;
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Gets the value of a key
	 * @param string $key_	the key
	 * @return mixed|false	the value of the key or false if there is any error or the key does not exist
	 */
	protected function getOrFalse($key_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->get($this->_appSBCode."_".$key_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Deletes a key
	 * @param string $key_	the key to be deleted
	 * @return bool true if the key was deleted, false otherwise
	 */
	protected function delOrFalse($key_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->del($this->_appSBCode."_".$key_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	
	/* lists */
	/**
	 * Store value at the end of the list stored at some key
	 * @param string $key_	the key where the list is stored at
	 * @param mixed $value_	the value to be stored at the end of the list
	 * @return int|false	the lenght of the list after the insertion or false if any error occurs
	 */
	protected function rpushOrFalse($key_,$value_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->rpush($this->_appSBCode."_".$key_,$value_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Removes and returns the first element of the list stored at key
	 * @param string $key_	the key where the list is stored at
	 * @return mixed|false	the value of the first element of the list or false if any error occurs
	 */
	protected function lpopOrFalse($key_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->lpop($this->_appSBCode."_".$key_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Returns all the elements of the list specified at some key
	 * @param string $key_	the key where the list is stored at
	 * @return integer|false	the elements of the list or false if any error occurs
	 */
	protected function lrangeOrFalse($key_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->lrange($this->_appSBCode."_".$key_,0,-1);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Returns the length of the list stored at some key
	 * @param string $key_	the key where the list is stored at
	 * @return integer|false	the length of the list or false if any error occurs
	 */
	protected function llenOrFalse($key_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->llen($this->_appSBCode."_".$key_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Remove the first $nOcurrences_ of $value_ from the list stored at $key_
	 * @param unknown_type $key_	the key where the list is stored at
	 * @param unknown_type $value_	the value to be removed
	 * @param unknown_type $nOcurrences_	the first 'n' ocurrences to be removed from the list
	 * @return integer|false the number of removed elements or false if any error occurs
	 */
	protected function lremOrFalse($key_,$value_,$nOcurrences_=0)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->lrem($this->_appSBCode."_".$key_,$nOcurrences_,$value_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	
	/* unordered sets */
	/**
	 * Adds a member to a set stored at $key_
	 * @param string $key_	the key where the set is stored
	 * @param string $value_	the value to be added to the set
	 * @return integer|false	the number of members added to the set (only one in this case) or false if any error occurs
	 */
	protected function saddOrFalse($key_,$value_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->sadd($this->_appSBCode."_".$key_,$value_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Removes a member $value_ from a set stored at $key_
	 * @param string $key_ the key where the set is stored at
	 * @param mixed $value_	the value of the new member to be added
	 * @return integer|false the number of members removed from the set (only one in this case) or false if any error occurs
	 */
	protected function sremOrFalse($key_,$value_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->srem($this->_appSBCode."_".$key_,$value_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Checks if an element with value $value_ is member of a set stored at key $key_
	 * @param unknown_type $key_	the key where the set is stored at
	 * @param unknown_type $value_	the value of the member to be checked
	 * @return integer|false 0 if the member is not in the set or the key does not exist, 1 if it is in the set, false if any error occurs
	 */
	protected function sismemberOrFalse($key_,$value_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->sismember($this->_appSBCode."_".$key_,$value_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Removes and returns a random element from the set stored at key $key_
	 * @param string $key_	the key where the set is stored at
	 * @return mixed|false the removed element or false if any error occurs
	 */
	protected function spopOrFalse($key_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->spop($this->_appSBCode."_".$key_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Returns all the members of the set stored at $key_
	 * @param string $key_ the key where the set is stored at
	 * @return array|false the members of the set or false if any error occurs
	 */
	protected function smembersOrFalse($key_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->smembers($this->_appSBCode."_".$key_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	/**
	 * Returns the number of elements in the set stored at $key_
	 * @param string $key_	the key where the set is stored at
	 * @return integer|bool	the number of elements or false if any error occurs
	 */
	protected function scardOrFalse($key_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->scard($this->_appSBCode."_".$key_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	
	/* hashes */
	protected function hsetOrFalse($key_,$field_,$value_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->hset($this->_appSBCode."_".$key_,$field_,$value_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	protected function hgetOrFalse($key_,$field_)
	{
		if($this->isRDBConnectionOk() && (strlen($this->_appSBCode)>0) && is_string($key_) && (strlen($key_)>0))
		{
			try
			{
				return $this->_rClient->hget($this->_appSBCode."_".$key_,$field_);
			}
			catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
}
?>