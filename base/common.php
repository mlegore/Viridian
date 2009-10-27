<?php

define('ENV_DEVELOPMENT',0);
define('ENV_TEST',1);
define('ENV_PRODUCTION',2);
define('WILDCARD',0);
define('REGEX',1);

function getUri() {
	if ( !isset($_GET['content']) ) {
		return '/';
	}
	return $_GET['content'];
}

function getHost() {
	return $_SERVER['SERVER_NAME'];
}

function returnMIMEType($filename)
{
	preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);

	switch(strtolower($fileSuffix[1]))
	{
		case "js" :
			return "application/x-javascript";

		case "json" :
			return "application/json";

		case "jpg" :
		case "jpeg" :
		case "jpe" :
			return "image/jpg";

		case "png" :
		case "gif" :
		case "bmp" :
		case "tiff" :
			return "image/".strtolower($fileSuffix[1]);

		case "css" :
			return "text/css";

		case "xml" :
			return "application/xml";

		case "doc" :
		case "docx" :
			return "application/msword";

		case "xls" :
		case "xlt" :
		case "xlm" :
		case "xld" :
		case "xla" :
		case "xlc" :
		case "xlw" :
		case "xll" :
			return "application/vnd.ms-excel";

		case "ppt" :
		case "pps" :
			return "application/vnd.ms-powerpoint";

		case "rtf" :
			return "application/rtf";

		case "pdf" :
			return "application/pdf";

		case "html" :
		case "htm" :
		case "php" :
			return "text/html";

		case "txt" :
			return "text/plain";

		case "mpeg" :
		case "mpg" :
		case "mpe" :
			return "video/mpeg";

		case "mp3" :
			return "audio/mpeg3";

		case "wav" :
			return "audio/wav";

		case "aiff" :
		case "aif" :
			return "audio/aiff";

		case "avi" :
			return "video/msvideo";

		case "wmv" :
			return "video/x-ms-wmv";

		case "mov" :
			return "video/quicktime";

		case "zip" :
			return "application/zip";

		case "tar" :
			return "application/x-tar";

		case "swf" :
			return "application/x-shockwave-flash";

		default :
		if(function_exists("mime_content_type"))
		{
			$fileSuffix = mime_content_type($filename);
		}

		return "unknown/" . trim($fileSuffix[0], ".");
	}
}
   
function printr($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}

function vardump($var) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

function match($rule,$host,$path) {
	if ( $rule['type'] == WILDCARD ) {
		$host_part = array_reverse(explode('.',$host));
		$rule_host_part = array_reverse(explode('.',$rule['host']));
		$path_part = explode('/',$path);
		$rule_path_part = explode('/',$rule['path']);
		$matches = array();
		foreach($rule_host_part as $part) {
			
			if ( $part == '*' or $part == '' ) {
				
				array_unshift($matches,array_shift($host_part));
				continue;
			}
			if ( $part == array_shift($host_part) ) {
				continue;
			}
			return false;
		}
		foreach($rule_path_part as $part) {
			if ( $part == '*' or $part == '' ) {
				array_unshift($matches,array_shift($path_part));
				continue;
			}
			if ( $part == array_shift($path_part) ) {
				continue;
			}
			return false;
		}
		return array('matches'=>$matches,'path'=>implode('/',$path_part));
	} else {
		$host_matches = $path_matches = array();
		if ( !(preg_match_all($rule['host'],$host,$host_matches,PREG_SET_ORDER) and preg_match_all($rule['path'],$path,$path_matches,PREG_SET_ORDER)) ) {
			return false;
		}
		$host_matches = $host_matches[0];
		$path_matches = $path_matches[0];
		array_shift($host_matches);
		$path = str_replace(array_shift($path_matches),'',$path);
		return array('matches'=>array_merge($host_matches,$path_matches),'path'=>$path);
	}
}

?>