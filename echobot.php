<?php
  require_once('./SBClientApi/SBClientApi.php');
  class EchoBot extends SBClientApi
  {
    public function onNewVote(SBUser $user_,$newVote_,$oldRating_,$newRating_)
    {
    }
    public function onNewContactSubscription(SBUser $user_)
    {
      print "El usuario ".$user_->getSBUserNameOrFalse()." se acaba de suscribir";
    }
    public function onNewContactUnSubscription(SBUser $user_)
    {
      print "El usuario ".$user_->getSBUserNameOrFalse()." se acaba de desuscribir";
    }
    public function onNewMessage(SBMessage $msg_)
    {
      $this->sendTextMessageOrFalse("He leido: ".$msg_->getSBMessageTextOrFalse(), $msg_->getSBMessageFromUserOrFalse()->getSBUserSBCodeOrFalse());
    }    
  }
  $echoBot=new EchoBot("<SBCODE>","<APP_KEY>");
  $echoBot->serveRequest($_GET["params"]);
?>