<?php

namespace core\exception\xml;
use \Exception as Exception;

/**
 * This class is used to raise exceptions for invalid DTD files.
 * @author Marc Bredt
 */
class XMLNoValidDTDException extends Exception {

  /** 
   * Create a XMLNoValidDTDException.
   * @param $message defaut exception message
   * @param $code reason for the exception raised
   * @param $previous previous exception causing this exception
   *                  to be raised.
   */
  public function __construct($message = "Provided DTD file is not valid.", 
                              $code = 0, 
                              Exception $previous = null) {

      parent::__construct($message,$code,$previous);
  }

  /**
   * Prints out exception information and stack trace.
   * @return a string representing exception information and stack trace
   */
  public function __toString() {
    return __CLASS__." [".$this->code."]: { ".$this->message." }";
  }

}

?>
