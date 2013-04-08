<?php 
/** 
 * Sender gossip application
 * 
 * Prints the new message's sender information
 * @author Spotbros <support@spotbros.com> 
 */ 
class SenderGossipApp extends SBClientApi 
{ 
	public function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){} 
	public function onNewContactSubscription(SBUser $sbUser_){} 
	public function onNewContactUnSubscription(SBUser $sbUser_){} 
	public function onNewMessage(SBMessage $message_)
	{
		// get the message's sender SBCode
		$senderSBCode = $message_->getSBMessageFromUserOrFalse()->getSBUserSBCodeOrFalse();
		// create new SBUser instance
		$senderSBUser = new SBUser($senderGossipAppSBCode, $senderGossipAppKey);
		// load user with the previous SBCode. When you load a user this way, neither location, latitude
		// nor longitude are set.
		$senderSBUser->loadUserBySBCodeOrFalse($senderSBCode);
		// print user information
		print ("Here you have the sender's information\n");
		print ("\tUser name: ".$senderSBUser->getSBUserNameOrFalse()."\n");
		print ("\tUser last name: ".$senderSBUser->getSBUserLastNameOrFalse()."\n");
		print ("\tUser gender: ".$senderSBUser->getSBUserGenderOrFalse()."\n");
		print ("\tUser profile picture MD5: ".$senderSBUser->getSBUserProfilePicMD5OrFalse()."\n");
		print ("\tUser rating: ".$senderSBUser->getSBUserRatingOrFalse()."\n");
		print ("\tUser email: ".$senderSBUser->getSBUserEmailOrFalse()."\n");
		print ("\tUser phone key: ".$senderSBUser->getSBUserPhoneKeyOrFalse()."\n");
		// you can change the user's email, phonekey, location and language
		// this changes will not have any effect on Spotbros database, only within your application
		$senderSBUser->setSBUserEmail("foo@bar.com");
		print ("\tThe new user email: ".$senderSBUser->getSBUserEmailOrFalse()."\n");
		$senderSBUser->setSBUserPhoneKey("00017788999000");
		print ("\tThe new user phone key: ".$senderSBUser->getSBUserPhoneKeyOrFalse()."\n");
		$senderSBUser->setSBUserLocation("","");
		print ("\tThe new user location: ".$senderSBUser->getSBUserLocationOrFalse()."\n");
		$senderSBUser->setSBLanguage("EN");
		print ("\tThe new user language: ".$senderSBUser->getSBUserLanguageOrFalse()."\n");
		// with this, we verify that these changes only have effect in the application context
		// reload the user
		$senderSBUser->loadUserBySBCodeOrFalse($senderSBCode);
		print ("\tOriginal user email: ".$senderSBUser->getSBUserEmailOrFalse()."\n");
	}
}
$senderGossipApp = new SenderGossipApp($senderGossipAppSBCode,$senderGossipAppKey); 
$senderGossipApp->serveRequest($_GET["params"]); 
?>