<?php
require_once('./SBClientApi/php-classes/class_CurlMngr.php');
require_once('./SBClientApi/includes/SBTypes.php');
class SBAttachments
{
  public $_attachmentRefs;
  private $_curlMngr;
  private $_appKey;
  private $_appSBCode;
  private $_extendedTextRef;
  public function __construct($appSBCode_,$appKey_)
  {
    $this->_appKey=$appKey_;
    $this->_appSBCode=$appSBCode_;
    $this->_curlMngr=CurlMngr::getInstance();
    $this->_attachmentRefs=array();
    $this->_extendedTextRef="";
  }
  
  private function  uploadAttachment($attachmentType_,$payload_)
  {
    $params=array(
                  "appSBCode"=>$this->_appSBCode,
                  "appKey"=>$this->_appKey,
                  "attachmentType"=>$attachmentType_,
                  "attachmentPayload"=>$payload_
                 );
    print "Uploading ".$payload_." de tipo: ".$attachmentType_."\n";
    $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse("http://".SBVars::SB_WEBSERVICE_ADDR."/public-api/uploadAttachment.php",$params,5000);
    if((($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(5000))!=false) && isset($responses[$handlerId]) && $responses[$handlerId]!=false)
    {
      print $responses[$handlerId]."\n";
      if(($attachmentInfoArray=json_decode($responses[$handlerId],true))!=null)
      {
        if(
            isset($attachmentInfoArray["CID"]) &&
            $attachmentInfoArray["CID"] == "attachmentUploadedOk" &&
            isset($attachmentInfoArray["V1"])
          )
        {
          if($attachmentType_==SBAttachmentType::EXTENDED_MSG)
          {
            array_unshift($this->_attachmentRefs, $attachmentInfoArray["V1"]);
            $this->_extendedTextRef=$attachmentInfoArray["V1"];
          }
          else
          {
            $this->_attachmentRefs[]=$attachmentInfoArray["V1"];
          }
          return $attachmentInfoArray["V1"];
        }
      }
    }
    return false;
  }
  /**
   * Gets all the attachment references
   */
  public function getAttachmentRefs()
  {
    return $this->_attachmentRefs;
  }
  /**
   * Adds a title to the SBMessage attachment
   * @param unknown_type $title_			the title to be added
   * @return
   */
  public function addTitleOrFalse($title_)
  {
    return $this->uploadAttachment(SBAttachmentType::TITLE, $title_);
  }
  /**
   * Adds a paragraph to the SBMessage attachment
   * @param unknown_type $paragraph_	the paragraph to be added
   * @return
   */
  public function addParagraphOrFalse($paragraph_)
  {
    return $this->uploadAttachment(SBAttachmentType::PARAGRAPH, $paragraph_);
  }
  /**
   * Adds a quote to the SBMessage attachment
   * @param unkown_type $quote_				the quote to be added
   * @return 
   */
  public function addQuoteOrFalse($quote_)
  {
    return $this->uploadAttachment(SBAttachmentType::QUOTE, $quote_);
  }
  /**
   * Adds a quote to the SBMessage attachment
   * @param unkown_type $quote_				the quote to be added
   * @return
   */
  public function addLinkOrFalse($link_)
  {
    return $this->uploadAttachment(SBAttachmentType::LINK, $link_);
  }
  /**
   * Adds extended text to the SBMessage attachment
   * @param unkown_type $text_				the text to be added
   * @return
   */
  public function addExtendedText($text_)
  {
    if($pos=array_search($this->_extendedTextRef, $this->_attachmentRefs))
    {
      unset($this->_attachmentRefs[$pos]);
    }
    return $this->uploadAttachment(SBAttachmentType::EXTENDED_MSG, $text_);
  }
  /**
   * Clears all the SBMessage attachments
   */
  public function clearAttachments()
  {
    $this->_attachmentRefs=array();
  }
}
?>