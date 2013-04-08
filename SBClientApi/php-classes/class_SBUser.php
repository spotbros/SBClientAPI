<?php
class SBUser
{
  private $_appSBCode;
  private $_appKey;
  private $_curlMngr;
  private $_userSBCode;
  private $_userName;
  private $_userLastName;
  private $_userGender;
  private $_userProfilePicMD5;
  private $_userRating;
  private $_userEmail;
  private $_userPhoneKey;
  private $_userLatitude;
  private $_userLongitude;
  private $_userLanguage;
  private $_userInitialized;
  /**
   * Creates an instance of SBUser, keeping it uninitialized until the user is loaded
   * @param unknown_type $appSBCode_			the app's sbcode
   * @param unknown_type $appKey_					the app's key
   */
  public function __construct($appSBCode_,$appKey_)
  {
    $this->_appSBCode=$appSBCode_;
    $this->_appKey=$appKey_;
    $this->_curlMngr=CurlMngr::getInstance();
    $this->_userInitialized=false;
  }
  /**
   * Initializes a new user given all its attributes
   * @param unknown_type $userSBCode_					the user's sbcode
   * @param unknown_type $userName_						the user's first name
   * @param unknown_type $userLastName_				the user's last name
   * @param unknown_type $userGender_					the user's gender
   * @param unknown_type $userProfilePicMD5_	the user's profile pic MD5
   * @param unknown_type $userRating_					the user's rating
   * @param unknown_type $userEmail_					the user's email
   * @param unknown_type $userPhoneKey_				the user's phone key
   * @param unknown_type $userLatitude_				the user's latitude
   * @param unknown_type $userLongitude_			the user's longitude
   * @param unknown_type $userLanguage_				the user's language
   */
  public function initUser(
                            $userSBCode_,
                            $userName_,
                            $userLastName_,
                            $userGender_,
                            $userProfilePicMD5_,
                            $userRating_,
                            $userEmail_="",
                            $userPhoneKey_="",
                            $userLatitude_="",
                            $userLongitude_="",
                            $userLanguage_=""
                           )
  {
    $this->_userSBCode=$userSBCode_;
    $this->_userName=$userName_;
    $this->_userLastName=$userLastName_;
    $this->_userGender=$userGender_;
    $this->_userProfilePicMD5=$userProfilePicMD5_;
    $this->_userRating=$userRating_;
    $this->_userEmail=$userEmail_;
    $this->_userPhoneKey=$userPhoneKey_;
    $this->_userLatitude=$userLatitude_;
    $this->_userLongitude=$userLongitude_;
    $this->_userInitialized=true;
  }
  /**
   * Sets all user's attributes
   * @param string $SBUserData_		json encoded string containing all the user's attributes
   */
  private function loadSBUserDataOrFalse($SBUserData_)
  {
    if(($SBUserDataArray=json_decode($SBUserData_,true))!=null)
    {
      if(
              isset($SBUserDataArray["CID"]) &&
              $SBUserDataArray["CID"] == "SBUser" &&
              isset($SBUserDataArray["V1"]) &&
              isset($SBUserDataArray["V1"]["userSBCode"]) &&
              isset($SBUserDataArray["V1"]["userProfilePicMD5"]) &&
              isset($SBUserDataArray["V1"]["userName"]) &&
              isset($SBUserDataArray["V1"]["userLastName"]) &&
              isset($SBUserDataArray["V1"]["userGender"]) &&
              isset($SBUserDataArray["V1"]["userRating"])
      )
      {
        $this->_userSBCode=$SBUserDataArray["V1"]["userSBCode"];
        $this->_userProfilePicMD5=$SBUserDataArray["V1"]["userProfilePicMD5"];
        $this->_userName=$SBUserDataArray["V1"]["userName"];
        $this->_userLastName=$SBUserDataArray["V1"]["userLastName"];
        $this->_userGender=$SBUserDataArray["V1"]["userGender"];
        $this->_userRating=$SBUserDataArray["V1"]["userRating"];
        return ($this->_userInitialized=true);
      }
    }
    return false;
  }
  /**
   * Loads an user by his/her sbcode, setting all its attributes
   * @param string $userSBCode_		the user's sbcode
   */
  public function loadUserBySBCodeOrFalse($userSBCode_)
  {
    $params=array(
            "appSBCode"=>$this->_appSBCode,
            "appKey"=>$this->_appKey,
            "userSBCode"=>$userSBCode_
            );
    $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse("http://".SBVars::SB_WEBSERVICE_ADDR."/public-api/getSBUser.php",$params,1000);
    if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(1000))!=false)
    {
      return (isset($responses[$handlerId]) && $responses[$handlerId]!=false)?$this->loadSBUserDataOrFalse($responses[$handlerId]):false;
    }
    return false;
  }                              
  /**
   * Checks whether user data is loaded (i.e. user is initialized)
   */
  public function isDataLoaded()
  {
    return $this->_userInitialized;
  }
  /**
   * Get user's sbcode
   * @return	the user's sbcode or false if user data was not loaded
   */
  public function getSBUserSBCodeOrFalse()
  {
    return $this->isDataLoaded()?$this->_userSBCode:false;
  }
  /**
   * Gets user's first name
   * @return	the user's first name or false if user data was not loaded
   */
  public function getSBUserNameOrFalse()
  {
    return $this->isDataLoaded()?$this->_userName:false;
  }
  /**
   * Gets user's last name
   * @return	the user's last name or false if user data was not loaded
   */
  public function getSBUserLastNameOrFalse()
  {
    return $this->isDataLoaded()?$this->_userLastName:false;
  }
  /**
   * Gets user's gender
   * @return	the user's gender or false if user data was not loaded
   */
  public function getSBUserGenderOrFalse()
  {
    return $this->isDataLoaded()?$this->_userGender:false;
  }
  /**
   * Gets user's profile picture md5
   * @return	the user's profile picture md5 or false if user data was not loaded
   */
  public function getSBUserProfilePicMD5OrFalse()
  {
    return $this->isDataLoaded()?$this->_userProfilePicMD5:false;
  }
  /**
   * Gets user's rating
   * @return	the user's rating or false if user data was not loaded
   */
  public function getSBUserRatingOrFalse()
  {
    return $this->isDataLoaded()?$this->_userRating:false;
  }
  /**
   * Gets user's email
   * @return	the user's email or false if user data was not loaded
   */
  public function getSBUserEmailOrFalse()
  {
    return $this->isDataLoaded()?$this->_userEmail:false;
  }
  /**
   * Gets user's phonekey
   * @return	the user's phone key or false if user data was not loaded
   */
  public function getSBUserPhoneKeyOrFalse()
  {
    return $this->isDataLoaded()?$this->_userPhoneKey:false;
  }
  /**
   * Gets user's latitude
   * @return	the user's latitude or false if user data was not loaded
   */
  public function getSBUserLatitudeOrFalse()
  {
    return $this->isDataLoaded()?$this->_userLatitude:false;
  }
  /**
   * Gets user's longitude
   * @return	the user's longitude or false if user data was not loaded
   */
  public function getSBUserLongitudeOrFalse()
  {
    return $this->isDataLoaded()?$this->_userLongitude:false;
  }
  /**
   * Gets user's location
   * @return	the user's location as latitude, longitude or false if user data was not loaded
   */
  public function getSBUserLocationOrFalse()
  {
    return $this->isDataLoaded()?($this->getSBUserLatitude().",".$this->getSBUserLongitude()):false;
  }
  /**
   * Sets user's email
   * @param unknown_type $userEmail_	the user's new email
   */
  public function setSBUserEmail($userEmail_)
  {
    $this->_userEmail=$userEmail_;
  }
  /**
   * Sets user's phone key
   * @param unknown_type $phoneKey_		the user's new phone key
   */
  public function setSBUserPhoneKey($phoneKey_)
  {
    $this->_userPhoneKey=$phoneKey_;
  }
  /**
   * Set user's location
   * @param unknown_type $latitude_		the user's new latitude
   * @param unknown_type $longitude_	the user's new longitude
   */
  public function setSBUserLocation($latitude_,$longitude_)
  {
    $this->_userLatitude=$latitude_;
    $this->_userLongitude=$longitude_;
  }
  /**
   * Sets user's language_
   * @param unknown_type $language_		the new user's language
   */
  public function setSBUserLanguage($language_)
  {
    $this->_userLanguage=$language_;
  }
}
?>