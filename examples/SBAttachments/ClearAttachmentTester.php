<?php 
require_once('./SBClientApi/SBClientApi.php'); 
/** 
 * Clear attachment tester application 
 * 
 * Sends messages with different attachments to different users 
 * @author Spotbros <support@spotbros.com> 
 */ 
class ClearAttachmentTesterApp extends SBClientApi 
{ 
        public function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){} 
        public function onNewContactSubscription(SBUser $sbUser_){} 
        public function onNewContactUnSubscription(SBUser $sbUser_){} 
        public function onNewMessage(SBMessage $message_) 
        { 
                // destination users' sbcodes 
                $sbcodeA = "TESTSB1"; 
                $sbcodeB = "TESTSB2"; 
                // set attachments for recipient A 
                $this->_SBAttachments->addTitleOrFalse("This is a title"); 
                $this->_SBAttachments->addParagraphOrFalse("And this is a paragraph"); 
                // send message to recipient A 
                if (!$this-> sendTextMessageOrFalse("Hello A!", $sbcode)) 
                {print "Could not send message to the user with sbcode: ".$sbcode;} 
                $this->_SBAttachments->clearAttachments(); 
                // set attachments for recipient B 
                $this->_SBAttachments->addTitleOrFalse("This is another title"); 
                $this->_SBAttachments->addParagraphOrFalse("And this is another paragraph"); 
                // send message 
                if (!$this-> sendTextMessageOrFalse("Hello B!", $sbcode)) 
               {print "Could not send message to the user with sbcode: ".$sbcode;} 
        } 
} 
$clearAttachmentTesterApp = new ClearAttachmentTesterApp($clearAttachmentTesterAppSBCode,$clearAttachmentTesterAppKey); 
$clearAttachmentTesterApp->serveRequest($_GET["params"]); 
?>