<?php
final class SBErrors
{
  const WRONG_PARAMS_FORMAT_ERROR   = "100000";
  const WEBSERVICE_TIMEOUT_ERROR    = "100001";
  const UNKNOWN_ERROR               = "100002";
  const UNABLE_TO_LOAD_MESSAGEID    = "100003";
  const UNABLE_TO_LOAD_USER         = "100004";
}

final class SBAppEventType{
  const NEW_MESSAGE                 = "NEW_MESSAGE";
  const NEW_CONTACT_SUBSCRIPTION    = "NEW_CONTACT_SUBSCRIPTION";
  const NEW_CONTACT_UNSUBSCRIPTION  = "NEW_CONTACT_UNSUBSCRIPTION";
  const NEW_VOTE 										= "NEW_VOTE";
}
final class SBVars{
  const SB_WEBSERVICE_ADDR = "test.spotbros.com";
}
final class SBAttachmentType{
  const AVOIDSHAREBAR								= 0;
  const IMAGE 											= 1;
  const AUDIO 											= 2;
  const VIDEO 											= 3;
  const MAP		 											= 4;
  const PARAGRAPH										= 5;
  const QUOTE	 											= 6;
  const TITLE	 											= 7;
  const LINK	 											= 8;
  const EXTENDED_MSG								= 9;
  const YOUTUBE_LINK								= 100;
  const VIMEO_LINK	 								= 101;
  const GOEAR_LINK									= 102;
  const LASTFM_LINK									= 103;
  const GOOGLE_VIDEO_LINK						= 104;
  const VIDDLER_LINK								= 105;
  const BLIP_LINK	 									= 106;
  const VEOH_LINK										= 107;
  const METACAFE_LINK								= 108;
  const SPIKE_LINK									= 109;
  const MYSPACE_VIDEO_LINK					= 110;
  const MYSPACE_AUDIO_LINK					= 111;
  const DAILY_MOTION_LINK	 					= 112;
}
final class StorageLocation{
  const AMAZON_S3						= "AS3";
  const CASSANDRA_CLUSTER		= "CAS";
}
final class SBConstants{
  const MAX_TEXT_SIZE = 200;
}
?>