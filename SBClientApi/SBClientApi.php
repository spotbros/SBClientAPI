<?php
require_once('./SBClientApi/php-classes/class_CurlMngr.php');
require_once('./SBClientApi/includes/SBTypes.php');
require_once('./SBClientApi/php-classes/class_SBMessage.php');
require_once('./SBClientApi/php-classes/class_SBAttachments.php');
class SBClientApi
{
  public  $_curlMngr;
  private $_appKey;
  private $_appSBCode;
  private $_SBMessage;
  public  $_SBAttachments;
  
  public function __construct($appSBCode_,$appKey_)
  {
    $this->_appKey=$appKey_;
    $this->_appSBCode=$appSBCode_;
    $this->_curlMngr=CurlMngr::getInstance();
    $this->_SBMessage=new SBMessage($appSBCode_,$appKey_);
    $this->_SBAttachments=new SBAttachments($appSBCode_, $appKey_);
  }
  /**
   * Print a JSON encoded return message with a variable number of parameters then die
   * @param  Name of the return + N parameters
   * @return void
   */
  private function printJSON()
  {
    $array 		= array();
    $args 		= func_get_args();
    $num_args = func_num_args();
    if ($num_args>0){
      $array["CID"]=array_shift($args);
      for($i=2;$i<=$num_args;$i++)
      {
      $array["V".($i-1)] = array_shift($args);
      }
    }
    print json_encode($array);
    die;
  }
  /**
   * Print a JSON encoded error, then die
   * @param	errCode as per SBErrors and error description
   * @return void
   */
  private function printJError($errCode_,$errDesc_="")
  {
    $this->printJSON("Error",$errCode_,$errDesc_);
  }
  /**
   * Handles different error types
   * @param unknown_type $errno_	The error code as per SBErrors
   */
  private function onError($errno_)
  {
    switch($errno_)
    {
      case SBErrors::WRONG_PARAMS_FORMAT_ERROR: {
        $this->printJError($errno_,"Error parsing input params on serveRequest");
      }
      case SBErrors::UNABLE_TO_LOAD_MESSAGEID:{
        $this->printJError($errno_,"Unable to load messageId");
      }
      case SBErrors::UNABLE_TO_LOAD_USER:{
        $this->printJError($errno_,"Unable to load user");
      }
      default:{
        $this->printJError(SBErrors::UNKNOWN_ERROR,"Unknown error");
      }
    }
  }
  
  /**
   * Validate if all fields are present and the request looks as it should
   * @param array $params_
   */
  private function isValidParams(Array $params_)
  {
    if(isset($params_["eventType"]))
    {
      switch($params_["eventType"])
      {
        case SBAppEventType::NEW_MESSAGE:
          {
            return (
                    isset($params_["SBMessageId"]) &&
                    isset($params_["userEmail"]) &&
                    isset($params_["userPhoneKey"]) &&
                    isset($params_["userLatitude"]) &&
                    isset($params_["userLongitude"]) &&
                    isset($params_["userLanguage"])
                    );
          }
        case SBAppEventType::NEW_CONTACT_SUBSCRIPTION:
        case SBAppEventType::NEW_CONTACT_UNSUBSCRIPTION:
          {
            return (
                    isset($params_["userName"]) &&
                    isset($params_["userLastName"]) &&
                    isset($params_["userSBCode"]) &&
                    isset($params_["userEmail"]) &&
                    isset($params_["userPhoneKey"]) &&
                    isset($params_["userLatitude"]) &&
                    isset($params_["userLongitude"]) &&
                    isset($params_["userLanguage"]) 
                   );
          }
      }
    }
    return true;
  }
  /**
   * This function receives $_GET["params"], parses it and invokes the appropriate callBacks
   * @param  Name of the return + N parameters
   * @return void
   */
  public function serveRequest($params_)
  {
    error_log($params_);
    if((($requestData=json_decode($params_,true))!=null) && $this->isValidParams($requestData))
    {
     switch($requestData["eventType"])
      {
        case SBAppEventType::NEW_MESSAGE:
          {
            if($this->_SBMessage->loadSBMessageBySBMessageIdOrFalse($requestData["SBMessageId"]))
            {
              $fromUser=$this->_SBMessage->getSBMessageFromUserOrFalse();
              $fromUser->setSBUserEmail($requestData["userEmail"]);
              $fromUser->setSBUserLocation($requestData["userLatitude"], $requestData["userLongitude"]);
              $fromUser->setSBUserPhoneKey($requestData["userPhoneKey"]);
              $fromUser->setSBUserLanguage($requestData["userLanguage"]);
              $this->onNewMessage($this->_SBMessage);
            }
            else
            {
              $this->onError(SBErrors::UNABLE_TO_LOAD_MESSAGEID);
            }
            break;
          }
        case SBAppEventType::NEW_CONTACT_SUBSCRIPTION:
          { 
            $fromUser=new SBUser($this->_appSBCode,$this->_appKey);
            if($fromUser->loadUserBySBCodeOrFalse($requestData["userSBCode"]))
            {
              $fromUser->setSBUserEmail($requestData["userEmail"]);
              $fromUser->setSBUserLocation($requestData["userLatitude"], $requestData["userLongitude"]);
              $fromUser->setSBUserPhoneKey($requestData["userPhoneKey"]);
              $fromUser->setSBUserLanguage($requestData["userLanguage"]);
              $this->onNewContactSubscription($fromUser);
            }
            else
            {
              $this->onError(SBErrors::UNABLE_TO_LOAD_USER);
            }
            break;
          }
        case SBAppEventType::NEW_CONTACT_UNSUBSCRIPTION:
          {
            $fromUser=new SBUser($this->_appSBCode,$this->_appKey);
            if($fromUser->loadUserBySBCodeOrFalse($requestData["userSBCode"]))
            {
              $fromUser->setSBUserEmail($requestData["userEmail"]);
              $fromUser->setSBUserLocation($requestData["userLatitude"], $requestData["userLongitude"]);
              $fromUser->setSBUserPhoneKey($requestData["userPhoneKey"]);
              $fromUser->setSBUserLanguage($requestData["userLanguage"]);
              $this->onNewContactUnSubscription($fromUser);
            }
            else
            {
              $this->onError(SBErrors::UNABLE_TO_LOAD_USER);
            }
            break;
          }
        case SBAppEventType::NEW_VOTE:
          {
            $fromUser=new SBUser($this->_appSBCode,$this->_appKey);
            if($fromUser->loadUserBySBCodeOrFalse($requestData["userSBCode"]))
            {
              $fromUser->setSBUserEmail($requestData["userEmail"]);
              $fromUser->setSBUserLocation($requestData["userLatitude"], $requestData["userLongitude"]);
              $fromUser->setSBUserPhoneKey($requestData["userPhoneKey"]);
              $fromUser->setSBUserLanguage($requestData["userLanguage"]);
              if(is_array($rating=$requestData["rating"]))
              {
                $this->onNewVote($fromUser,$requestData["vote"],$rating["oldRating"],$rating["newRating"]);
              }
              else
              {$this->onError(SBErrors::WRONG_PARAMS_FORMAT_ERROR);}
            }
            else
            {
              $this->onError(SBErrors::UNABLE_TO_LOAD_USER);
            }
            break;            
          }
      }
    }
    else
    {$this->onError(SBErrors::WRONG_PARAMS_FORMAT_ERROR);}
  }
  
  /**
   * Send a text message to a group of App followers. If attachments are set, then they will be automatically embedded
   * into the message as a SBMail.
   * @param unknown_type $msgText_	The text of the message to be sent
   * @param unknown_type $toSBCode_	The SBCode of the App follower who will receive the message
   * @return Array with values (V1=date in ms,V2=message Id,V3=true if app received message, false if just server,V4=Msg unique Id)
   */
  public function sendTextMessageOrFalse($msgText_,$toSBCode_)
  {
    if(mb_strlen($msgText_,'UTF-8')>SBConstants::MAX_TEXT_SIZE)
    {
      $this->_SBAttachments->addExtendedText(substr($msgText_, SBConstants::MAX_TEXT_SIZE));
      $msgText_=substr($msgText_, 0,SBConstants::MAX_TEXT_SIZE);
    }
    $params=array(
                  "appSBCode"=>$this->_appSBCode,
                  "appKey"=>$this->_appKey,
                  "toSBCode"=>$toSBCode_,
                  "msgText"=>$msgText_,
                  "msgUniqueId"=>md5(rand(0,100000000).rand(0,100000000).rand(0,100000000).rand(0,100000000).microtime(1))
                 );
    if(count($this->_SBAttachments->getAttachmentRefs())>0)
    {
      $params["attachments"]=json_encode($this->_SBAttachments->getAttachmentRefs(),true);
    }
    
    $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse("http://".SBVars::SB_WEBSERVICE_ADDR."/public-api/sendSBMessage.php",$params,1000);
    if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(1000))!=false)
    {
      if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
      {
        print $responses[$handlerId]."\n";
        return (($responseData=json_decode($responses[$handlerId],true))!=null)&&(isset($responseData["CID"]) && $responseData["CID"]=="DeliveryStatus")?$responseData:false;
      }
    }
    return false;    
  }
  /**
   * Send a text message to a group of App followers. If attachments are set, then they will be automatically embedded
   * into the message as a SBMail.
   * @param unknown_type $msgText_	The text of the message to be sent
   * @param array $toSBCodes_				The SBCodes of the App followers who will receive the message
   * @return Array of DeliveryStatus (as per sendTextMessageOrFalse) or False
   */
  public function sendTextMessageToGroup($msgText_,Array $toSBCodes_)
  {
    $deliveryResults=array();
    foreach ($toSBCodes_ as $toSBCode)
    {
      $deliveryResults[$toSBCode]=$this->sendTextMessageOrFalse($msgText_, $toSBCode);
    }
    return $deliveryResults;
  }
  /**
   * Gets this app's followers' sbcodes
   * @return Ambigous <boolean, mixed>|boolean	An array with the followers' sbcodes or false if there was any error getting it
   */
  public function getFollowerSBCodesOrFalse()
  {
    $params=array(
                  "appSBCode"=>$this->_appSBCode,
                  "appKey"=>$this->_appKey
                 );
    $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse("http://".SBVars::SB_WEBSERVICE_ADDR."/public-api/getFollowerSBCodes.php",$params,1000);
    if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(1000))!=false)
    {
      if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
      {
        if(($responseData=json_decode($responses[$handlerId],true))!=null)
        {
          if(
              isset($responseData["CID"]) &&
              $responseData["CID"]=="FollowerSBCodes" &&
              isset($responseData["V1"])
            )
          {
            return is_array($responseData["V1"])?$responseData["V1"]:false;
          }
        }
      }
    }
    return false;
  }
  /**
   * Gets the number of followers for the current SBApp
   * @return The number of followers or false if there was any error while getting it
   */
  public function getFollowerNumOrFalse()
  {
    $params=array(
            "appSBCode"=>$this->_appSBCode,
            "appKey"=>$this->_appKey
    );
    $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse("http://".SBVars::SB_WEBSERVICE_ADDR."/public-api/getFollowerSBCodes.php",$params,1000);
    if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(1000))!=false)
    {
      if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
      {
        if(($responseData=json_decode($responses[$handlerId],true))!=null)
        {
          if(
                  isset($responseData["CID"]) &&
                  $responseData["CID"]=="FollowerSBCodes" &&
                  isset($responseData["V2"])
          )
          {
            return is_numeric($responseData["V2"])?$responseData["V2"]:false;
          }
        }
      }
    }
    return false;
  }
  /**
   * Sends a message to the user who just wrote you
   * @return false on error
   */
  public function replyOrFalse($msgText_)
  {
    if(
        (($user=$this->_SBMessage->getSBMessageFromUserOrFalse()) != false) &&
        (($userSBCode=$user->getSBUserSBCodeOrFalse()) != false)
      )
    {
      return $this->sendTextMessageOrFalse($msgText_, $userSBCode);
    }
    return false;
  }
}
?>