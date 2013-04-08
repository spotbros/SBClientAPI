<?php
require_once('./SBClientApi/SBApp.php');
/**
 * Group Messenger application
 * 
 * Says hello to a group of users (different sbcodes) when it receives a message from any subscriber
 * @author Spotbros <support@spotbros.com>
 */
class GroupMessengerApp extends SBApp
{
	protected function onError($errorType_){}
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){}
	protected function onNewContactSubscription(SBUser $sbUser_){}
	protected function onNewContactUnSubscription(SBUser $sbUser_){}
	protected function onNewMessage(SBMessage $message_)
	{	
		// destination users' sbcodes
		$sbcodeA = "TESTSB0";
		$sbcodeB = "TESTSB1";
		$groupSBCodes = array($sbcodeA, $sbcodeB);
		// send message
		if (!$this-> sendTextMessageToGroupOrFalse("Hello there!", $groupSBCodes))
		{print ("Could not send message to group");}
	}
}

$groupMessengerAppSBCode = "WB2U4XL";
$groupMessengerAppKey = "8812b359149e67ac8b308a51cc1b18992c819ab0d867a34ab2cd252faa7e86c5";
$groupMessengerApp = new GroupMessengerApp($groupMessengerAppSBCode,$groupMessengerAppKey);
$groupMessengerApp->serveRequest($_GET["params"]);
?>