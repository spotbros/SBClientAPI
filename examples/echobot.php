<?php
require_once('../SBClientApi/SBApp.php');
class EchoBot extends SBApp
{
	protected function onError($errorType_)
	{
		error_log($errorType_);
	}
	 
	protected function onNewVote(SBUser $user_,$newVote_,$oldRating_,$newRating_)
	{
		if(($userSBCode=$user_->getSBUserSBCodeOrFalse()))
		{
			$this->sendTextMessageOrFalse("You voted me with ".$newVote_.", my oldRating was: ".$oldRating_." and now is ".$newRating_, $userSBCode);
		}
	}
	protected function onNewContactSubscription(SBUser $user_)
	{
		if(($userName=$user_->getSBUserNameOrFalse()) && ($userSBCode=$user_->getSBUserSBCodeOrFalse()))
		{
			$this->sendTextMessageOrFalse("Hi ".$userName.", thanks for becoming my friend", $userSBCode);
		}
	}
	protected function onNewContactUnSubscription(SBUser $user_)
	{
		error_log("The user ".$user_->getSBUserNameOrFalse()." has just unsubscribed");
	}

	protected function onNewMessage(SBMessage $msg_)
	{
		return $this->replyOrFalse($msg_->getSBMessageTextOrFalse());
	}
}

$echoBot=new EchoBot("","");
$echoBot->serveRequest($_GET["params"]);
?>