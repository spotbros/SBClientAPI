<?php
class CurlMngr
{
  private static $_CurlMngrInstance;
  private $_MCHandler;
  private $_cHandlers;
  private function __construct()
  {
  	$this->_MCHandler = curl_multi_init();  
  	$this->_cHandlers=array();
  }
  public function __destruct()
  {}
  public static function getInstance() 
  { 
   	if (!self::$_CurlMngrInstance) 
    {self::$_CurlMngrInstance = new CurlMngr();} 
    return self::$_CurlMngrInstance; 
  }
  /**
   * Queries url using cURL
   * @param unknown_type $url_				the url to query
   * @param array $params_						the url parameters as ("param0" => "value0", "param1" => "value1"...,"paramN" => "valueN")
   * @param unknown_type $timeoutMS_	the maximum number of seconds to allow cURL functions to execute
   * @param unknown_type $userAgent_	the contents of the "User Agent" header for http requests
   */
  public function queryStringThisUrlOrFalse($url_, Array $params_=null, $timeoutMS_=1000,$userAgent_="")
  {
  	$timeoutMS_=(is_numeric($timeoutMS_)&&$timeoutMS_>1000)?$timeoutMS_:1000;
  	$handlerId=md5(round((microtime(1)*1000),0).rand(0,1000000));
  	if($params_!=null)
  	{
  		$url_ = $url_."?".http_build_query($params_);
  	}
  	if(($this->_cHandlers[$handlerId]=curl_init($url_))!=false)
  	{
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_RETURNTRANSFER, true);  
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_HEADER, false); //not include header
  		if($userAgent_!="")
  		{
  		  curl_setopt($this->_cHandlers[$handlerId], CURLOPT_USERAGENT,$userAgent_);
  		}
  		//curl_setopt($this->_cHandlers[$handlerId], CURLOPT_CONNECTTIMEOUT_MS, 75); //100ms max timeout connecting
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_TIMEOUT_MS, $timeoutMS_); //full execution process
  		if(curl_multi_add_handle($this->_MCHandler,$this->_cHandlers[$handlerId])==0)
  		{
  			curl_multi_exec($this->_MCHandler, $active);
    		return $handlerId;
  		}
  	}
		unset($this->_cHandlers[$handlerId]);
		return false;
  }
  /**
   * Performs a POST HTTP request using cURL
   * @param unknown_type $url_				the url to query the POST request
   * @param unknown_type $timeoutMS_	the maximum number of seconds to allow cURL functions to execute
   * @param string $json_							json encoded string, which will be the body of the POST request				
   */
  public function postJSONToThisURLOrFalse($url_,$timeoutMS_=1000, $json_)
  {
  	$timeoutMS_=(is_numeric($timeoutMS_)&&$timeoutMS_>1000)?$timeoutMS_:1000;
  	$handlerId=md5(round((microtime(1)*1000),0).rand(0,1000000));
  	if(($this->_cHandlers[$handlerId]=curl_init($url_))!=false)
  	{
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_RETURNTRANSFER, true);  
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_HEADER, false); //not include header
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_CUSTOMREQUEST, "POST");
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_POSTFIELDS, $json_);  
  		//curl_setopt($this->_cHandlers[$handlerId], CURLOPT_CONNECTTIMEOUT_MS, 75); //100ms max timeout connecting
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_TIMEOUT_MS, $timeoutMS_); //full execution process
  		if(curl_multi_add_handle($this->_MCHandler,$this->_cHandlers[$handlerId])==0)
  		{
  			curl_setopt($this->_cHandlers[$handlerId], CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json_))); 
  			curl_multi_exec($this->_MCHandler, $active);
    		return $handlerId;
  		}
  	}
		unset($this->_cHandlers[$handlerId]);
		return false;
  }
  /**
   * Gets cURL responses when they are ready
   * @param unknown_type $timeoutMS_
   */
  public function getResponsesWhenReadyOrFalse($timeoutMS_=1000)
  {
  	$startTime=microtime(true);
  	do
  	{  
  		curl_multi_exec($this->_MCHandler, $active);
  		if(microtime(true) < ($startTime+$timeoutMS_))
  		{usleep(1000); /*1 ms*/}
  		else
  		{return false;}
  		
  	}
  	while($active > 0);
  	$replies=array();
  	foreach($this->_cHandlers as $handlerId => $handler)
  	{
  		$replies[$handlerId] = curl_multi_getcontent($handler); 
  	}
  	return $replies;
  }
}
?>