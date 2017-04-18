<?php

/**
 * Namespace for this class. Should follow directory structure to
 * let the autoloader find namespace addressions. Addtionally use
 * namespace alias definition to inherit from external classes 
 * without namespace definition.
 * @see http://stackoverflow.com/questions/32478962
 */
namespace core\util\log;
use \Log as Log;

/**
 * This class wraps the Log class from package php-log
 * @author Marc Bredt
 * @package Logger
 */
class FileLogger extends Log {

  /**
   * Stores the name of the class the logger is generated for.
   */ 
  private $class = NULL;

  /**
   * Stores the file name of the file the logs are written to.
   */ 
  private $file = NULL;

  /**
   * Contains the configuration for the logger.
   */ 
  private $conf = NULL;

  /**
   * The logger itself.
   */ 
  private $logger = NULL;
 
  /**
   * Construct a file logger for a specified class that is accessable.
   * @param string $class Classname the logger is created for.
   * @param string $file file to log too.
   */ 
  function __construct($class = 'Unknown', $file = '@dir-log-file@') {

    $this->file = $file;
    $this->class = $class;
    $this->conf = array('mode' => 0640, 'timeFormat' => '   %x %X');
    $this->logger = Log::singleton('file', $this->file, $class, $this->conf);
  }  

  /**
   * Destruct the logger.
   */ 
  function __destruct() {
    unset($this->logger);
    unset($this->conf);
    unset($this->file);
    unset($this->class);
  }

  /**
   * Make the logger accessible to the world. It is accessible through
   * the functions of @{Log}.
   * @return Log $logger file logger 
   */ 
  public function getLogger() {
    return $this->logger;
  }

  /**
   * Get the current location of the file logs are written to.
   */
  public function getFile() {
    return $this->file;
  }

  /**
   * Clean the logfile. Used for content testing purposes.
   * @return true if truncate was successful otherwise false
   */
  public function clean() {
    $fh = fopen($this->getFile(),"w");
    $tb = ftruncate($fh,0);
    fclose($fh);
    return $tb;
  }

  /**
   * Returns first line from logfile. Used for content testing purposes.
   * @return first
   */
  public function getFirstLine() {
    $fh = fopen($this->getFile(),"r");
    $fline = fgets($fh);
    fclose($fh);
    return preg_replace("/[\r\n]/","",$fline);
  }

  /**
   * Returns first line from logfile. Used for content testing purposes.
   * @return first
   */
  public function getLastLine() {
    $fh = fopen($this->getFile(),"r");

    // get the newline offset from back, skips newlines at the end
    $fline = ""; $c = ""; $fpos = 0;
    do { $fpos--; $fs = fseek($fh, $fpos, SEEK_END); $c = fgetc($fh); 
    } while(strncmp($c,PHP_EOL,1)==0 && ftell($fh)>1); 
    
    // get the last line character/byte-wise
    $fline = ""; $c = ""; 
    do {
      $fline = $c.$fline; 
      $fs = fseek($fh, $fpos--, SEEK_END);
      $c = fgetc($fh);
    } while(strncmp($c,PHP_EOL,1)!=0 && ftell($fh)>1);

    fclose($fh);

    return $fline;
  }

  /**
   * Get a string representation for a FileLogger instance.
   * @return string FileLogger representation
   */
  public function __toString() {
    return "l.".__LINE__.": ".
           get_class($this).spl_object_hash($this)."=(".
           "file=".$this->file.",".
           "class=".$this->class.",".
           "conf=".preg_replace("/( |\r|\n|\t)+/", 
                                " ", var_export($this->conf,true)).")";
  }

  /**
   * Function to flat several objects for logging. Arrays will be flattened.
   * For other objects there will be looked after a class method named 
   * __toString(). If no such method exists the object signature will be used.
   * @param object $obj object to flatte for logging.
   * @param boolean $debug indicates to get detailed information for $obj
   * @return string flattened object, its string representation or a unique id
   */
  public function flatten($obj, $debug = false) {
    $a = array();
    // NOTE: get_class_methods invokes autoloader for a string value
    if(is_object($obj) 
       && strncmp(gettype(get_class_methods($obj)),"array",5)==0) {
      $a = get_class_methods($obj);
    }

    $ret = ""; 
    if(in_array("__toString", $a)) {
      $ret = $obj->__toString();
    } else if(strncmp(gettype($obj),"array",5)==0) {
      $ret = preg_replace("/[\r\n]/", " ", var_export($obj, true));
    } else if(strncmp(gettype($obj),"string",6)==0) {
      $ret = $obj;
    } else if(is_null($obj)) {
      $ret = "NULL";
    } else if(is_object($obj)){
      $ret = get_class($obj).spl_object_hash($obj); 
    }

    if($debug) $ret = "OBJECT(".preg_replace("/[\r\n]/", "", print_r($obj, true)).")=".$ret;
  
    // replace multiple whitespaces by a single one 
    return preg_replace("/(\t| )+/"," ", $ret);
  }

  /**
   * Wrap the log function from Log to be able to log arrays without 
   * throwing conversion errors.
   * <pre>
   *  $l->logge("eins=%, zwei=%, drei=%, vier=%", 
   *            array("dsgsg",array(2,44),new Set(),array(array(1,2),3)), "DEBUG");
   * </pre>
   * @param string $msg Message string like 'Convert % to loggable string.'
   * @param string $type Maskerades the Log fuctions 
   *                     (debug,info,notice,warning,err,crit,alert,emerg) 
   * @param array $replaces contains replacement (any type f object) 
   *                        for each % placeholder in $msg
   * @return string flattened/detailes object information for $obj
   */
  public function logge($msg, $replaces = array(), $type = "INFO") {

    $toreps = preg_match_all("/%/", $msg);
 
    // replaces have to be an array 
    if(strncmp(gettype($replaces),"array",5)!=0) { 

      // TODO: probably try to log with the class' logge method
      //       but be aware of the loop
      $this->logger->log(get_class($this)." line ".__LINE__.
                         ": Message replacements are not provided as an array.", 
                         PEAR_LOG_WARNING);
      throw(new \core\exception\ParamNotArrayException());

    // number of replacement elements should not differ 
    // in msg string and replacement array
    } else if($toreps != count($replaces)) {

      // TODO: probably try to log with the class' logge method
      //       but be aware of the loop
      $this->logger->log(get_class($this)." line ".__LINE__.
                         ": Number of message replacements do not equal.", 
                         PEAR_LOG_WARNING);
      throw(new \core\exception\ParamNumberException());

    } else if($toreps == count($replaces)) {

      // replace % with flattened replacements
      foreach($replaces as $r) {
        if(constant("PEAR_LOG_".$type) == PEAR_LOG_DEBUG) $msg = preg_replace("/%/", $this->flatten($r,true), $msg, 1);
        else $msg = preg_replace("/%/", $this->flatten($r), $msg, 1);
      }      
    } 
   
    $this->logger->log(get_class($this)." ".$msg, constant("PEAR_LOG_".$type));
  }
}
