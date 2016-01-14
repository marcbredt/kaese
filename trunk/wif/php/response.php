<?php

// load necessary classes before continuing a session
// otherwise handling would result in __PHP_Incomplete_Class
// requirements for the file streaming
require_once("../classes/php/reader.class.php");
require_once("../classes/php/filestreamreader.class.php");
// requirements for the command execution
require_once("Log.php");
require_once("../classes/php/filelogger.class.php");
require_once("../classes/php/xmlnotvalidexception.class.php");
require_once("../classes/php/xmlnovaliddtdexception.class.php");
require_once("../classes/php/invalidxpathexpressionexception.class.php");
require_once("../classes/php/unresolvedxpathexception.class.php");
require_once("../classes/php/xmldocument.class.php");
require_once("../classes/php/executor.class.php");
require_once("../classes/php/shellcommandexecutor.class.php");

// continue a session
session_start();

// disable garbage collection, to check if one is able to access
// previous created instances in any way
gc_disable();

// no page timeout
set_time_limit(0);

// headers for plain text
header('Content-Type: text/plain');
header('Cache-Control: no-cache, must-revalidate');

// headers for chunks
//header('Transfer-Encoding: chunked');

// for IE's XDomainRequest instead of XMLHttpRequest
header('Access-Control-Allow-Origin: *');

// disable compression to immediately push data
@apache_setenv('no-gzip',1);
@ini_set('zlib.output_compression',0);
@ini_set('implicit_flush',1);
// disable output buffering
while(@ob_end_flush());
ini_set('implicit_flush',1);
ob_implicit_flush(1);

// for chrome to start processing progress events
echo(str_repeat(' ',2048)); 
//ob_flush(); flush();

$fl = new \core\util\log\FileLogger("response.php","../response.log");;
$fl->logge("query=%",array($_SERVER['QUERY_STRING']));

if(strncmp($_GET['action'],"read",4)==0) {

  // if the iptables logfile should be analyzed
  if(isset($_GET['stats_type']) 
     && strncmp($_GET['stats_type'],"logfile",7)==0){
    $fsr = new FileStreamReader();
    $fl->logge("streamer created");
    $fsr->read();
    $fl->logge("logfile state ended");

  // if the iptables command should be used
  } else if(isset($_GET['stats_type'])
            && strncmp($_GET['stats_type'],"command",7)==0) {

    // check the command prefix
    //$cmd = "sudo /sbin/iptables -t filter -nvx --list input-dns"; // dummy
    $xd = null;
    $cmd = null;
    try {

      $xd = new \core\util\xml\XMLDocument("../config/config.xml","../config/config.dtd");
      if(strncmp($_GET['stats_command_prefix'],"ipt-filter",10)==0) {
        $cmd = $xd->xpath("string(/config/var[@name='sudo'])") .  
                 " ".$xd->xpath("string(/config/var[@name='ipt-filter'])"); 
      } else if(strncmp($_GET['stats_command_prefix'],"ipt-nat",7)==0) {
        $cmd = $xd->xpath("string(/config/var[@name='sudo'])") .  
                 " ".$xd->xpath("string(/config/var[@name='ipt-nat'])"); 
      } else {
        $cmd = $xd->xpath("string(/config/var[@name='sudo'])") .  
                 " ".$xd->xpath("string(/config/var[@name='ipt-filter'])"); 
      }

      // adjust filter for chain
      if(isset($_GET['stats_filter']) 
         && strncmp($_GET['stats_filter'],"on",2)==0
         && isset($_GET['stats_filter_value'])
         && ($cpos=strpos($_GET['stats_filter_value'],"chain:"))>=0) {
        //echo "cposi=".$cpos."\n";
        $cp = substr($_GET['stats_filter_value'],($cpos+6));
        $cmd .= " ".substr($cp,0,($co=strpos($cp,";")?$co:strlen($cp)));
        //echo "cmd=".$cmd."\n"; exit;
      }

    } catch(Exception $e) { }

    // execute the command
    $sce = new ShellCommandExecutor();
    $sce->execute($cmd);

  } else {
    // error
  }

} elseif(strncmp($_GET['action'],"halt",4)==0) {
  session_write_close();
}

ob_flush(); flush();

?>
