<?php

namespace core\exception\xml;
// the Exception class is located somewhere in the php base package
use \Exception as Exception;

/**
 * This class describes exceptions which can occur using
 * XMLDocument.
 * @author Marc Bredt
 */
class XMLNotValidException extends Exception {

  /**
   * This constant indicates the mimetype of the XML file could
   * not be determined.
   */
  const XML_INVALID_MIMETYPE = 0;

  /**
   * This constant indicates the DTD file is invalid.
   */
  const XML_INVALID_DTDFILE = 1;

  /**
   * This constant indicates the XML file validation failed
   * using the DTD file passed. 
   */
  const XML_DTD_VALIDATION_FAILED = 2;

  /** 
   * Create a XMLNotValidException.
   * @param $message defaut exception message
   * @param $code reason for the exception raised
   * @param $previous previous exception causing this exception
   *                  to be raised.
   */
  public function __construct($message = "XML not valid", 
                              $code = 0, 
                              Exception $previous = null) {

    switch($code) {

      case 0:
        parent::__construct($message." (XML_INVALID_MIMETYPE).",
                            self::XML_INVALID_MIMETYPE,$previous);
        break;
     
      case 1:
        parent::__construct($message." (XML_INVALID_DTDFILE).",
                            self::XML_INVALID_DTDFILE,$previous);
        break;

      case 2:
        parent::__construct($message." (XML_DTD_VALIDATION_FAILED).",
                            self::XML_DTD_VALIDATION_FAILED,$previous);
        break;
     
      default: 
        parent::__construct($message." (XML_INVALID_MIMETYPE).",
                            self::XML_INVALID_MIMETYPE,$previous);
        break;

    }

  }

  /**
   * Get message and stack trace for this exception.
   + @return exception message and stack trace
   */
  public function __toString() {
    return __CLASS__." [".$this->code."]: { ".$this->message." }";
  }

}

?>
