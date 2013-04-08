<?php
require_once(__DIR__.'/SBTypes.php');

function getConstantValues($object_, $reversed_=false)
{
	$className = get_class($object_);
	$r = new ReflectionClass($className);
	$result = $r->getConstants();
	if ($reversed_)
	{
		$reversedResult = array();
		foreach ($result as $value => $key)
		{
			$reversedResult[$key] = $value;
		}
		$result = $reversedResult;
	}
	return $result;
}
/**
 * Verifies if the attachment type is valid
 * @param SBAttachmentType $attachmentType_ the attachment type to be verified
 * @return bool true if the attachemnt type is valid or false if it is not
 */
function isValidAttachmentType($attachmentType_)
{
	return in_array($attachmentType_,getConstantValues(new SBAttachmentType()));
}

/**
 * Print a JSON encoded return message with a variable number of parameters then die
 * @param  Name of the return + N parameters
 * @return void
 */
function printJSON()
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
 * @param $errCode_ SBErrors error code
 * @param $errDesc_ string error description
 * @return void
 */
function printJError($errCode_,$errDesc_="")
{
  printJSON("Error",$errCode_,$errDesc_);
}

/**
 * Thanks to http://us.php.net/manual/en/function.json-encode.php#74878
 * @param unknown_type $arr
 * @return Ambigous <string, unknown>
 */
function php_json_encode($arr)
{
	$json_str = "";
	if(is_array($arr))
	{
		$pure_array = true;
		$array_length = count($arr);
		for($i=0;$i<$array_length;$i++)
		{
			if(! isset($arr[$i]))
			{
				$pure_array = false;
				break;
			}
		}
		if($pure_array)
		{
			$json_str ="[";
			$temp = array();
			for($i=0;$i<$array_length;$i++)
			{
				$temp[] = sprintf("%s", php_json_encode($arr[$i]));
			}
			$json_str .= implode(",",$temp);
			$json_str .="]";
		}
		else
		{
			$json_str ="{";
			$temp = array();
			foreach($arr as $key => $value)
			{
				$temp[] = sprintf("\"%s\":%s", $key, php_json_encode($value));
			}
			$json_str .= implode(",",$temp);
			$json_str .="}";
		}
	}else if (is_object($arr)){
		return php_json_encode(get_object_vars($arr));
	}else
	{
		if (is_null($arr)){
			$json_str = "null";
		}else if (is_bool($arr))
		{
			$json_str = $arr?"true":"false";
		}
		else
		{	
			if (is_string($arr))
			{
				/**
				 * Thanks to http://stackoverflow.com/questions/1048487/phps-json-encode-does-not-escape-all-json-control-characters
				 * found at http://yahowto.blogspot.com.es/2011/10/escaping-json-special-characters-using.html
				 * @var unknown_type
				 */
				$arr = '"'.
							str_replace(
													array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c"),
													array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b"),
													$arr).
							'"';
			}
			$json_str = $arr;
		}
	}
	return $json_str;
}

function getCurrentDayNum()
{
	return floor(time()/(60*60*24));
}
function getCurrentTimeMS()
{
	return (string)round((microtime(1)*1000),0);
}
function getCurrentTimeS()
{
	return (string)time();
}

function findMostSimilarWord($needle_,Array $stack_)
{
	$p=$hP=$hN=0;
	$msw=$msk="";
	$needle2=str_replace(' ', '', $needle_);
	foreach($stack_ as $key=>$str)
	{
		$str2 = str_replace(' ', '', $str);
		$n=similar_text(strtolower($needle2), strtolower($str2), $p);
		if($n>$hN)
		{
			$msk=$key;
			$msw=$str;
			$hP=$p;
			$hN=$n;
		}
		else if($n == $hN) //mismo numero de letras coinciden, tiro a porcentaje
		{
			if($p>$hP)
			{
				$msk=$key;
				$msw=$str;
				$hP=$p;
				$hN=$n;
			}
		}
	}
	return array($msk,$msw,$hP,$hN);
}
function preprocessMessage($message_)
{
	return strtolower(rtrim(ltrim(str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $message_))));
}

?>