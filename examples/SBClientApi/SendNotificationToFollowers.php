<?php
require_once('./SBClientApi/SBClientApi.php');
/**
 * Send notification to followers
 * 
 * Sends a notification to app's followers 
 * @author Spotbros <support@spotbros.com>
 */
class NotificationSender extends SBClientApi
{
	public function sendNotificationToFollowers($notificationToBeSent_)
	{
		if(!($followersSBCodes = $this->getFollowerSBCodesOrFalse()))
		{print ("There was an error while getting the sbcodes");}
		print_r($followersSBCodes);
		if (!($this-> sendTextMessageToGroupOrFalse($notificationToBeSent_, $followersSBCodes)))
		{print ("Could not send message to group");}
	}
}

$notificationSender = new NotificationSender($someAppSBCode,$someAppKey);
$notificationSender->sendNotificationToFollowers("How are you doing today?");
?>