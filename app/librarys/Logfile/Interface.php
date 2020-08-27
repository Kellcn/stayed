<?php
namespace Local\Logfile;
interface LogInterface
{
	public function check($intLevel);
	
	public function log($intLogLevel, $strLog, $strFile, $intLine, $intLogId);
	
	public function flush();
}