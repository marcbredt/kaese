<?php

namespace core\exception\xml\xpath;
use \Exception as Exception;

/**
 * This class is used to raise exceptions for invalid DOMXpath
 * expressions. 
 * @see XMLDocument
 * @author Marc Bredt
 */
class InvalidXPathExpressionException extends Exception {

  /**
   * Default exception code. Represenst any invalid xpath expression.
   */
  const XPATH_EXPRESSION_INVALID = 0;

  /**
   * This exception code represents xpath expression that are null.
   */
  const XPATH_EXPRESSION_NULL = 1;

  /**
   * This exception code represents xpath empty expression.
   */
  const XPATH_EXPRESSION_EMPTY = 2;
 
  /** 
   * Create a InvalidXPathExpressionException.
   * @param $message default exception message
   * @param $code reason for the exception raised
   * @param $previous previous exception causing this exception
   *                  to be raised.
   */
  public function __construct($message = "Invalid XPath expression", 
                              $code = 0, 
                              Exception $previous = null) {

    switch($code) {
      case 0:
        parent::__construct($message." (XPATH_EXPRESSION_INVALID).",0,$previous);
        break;
      case 1:
        parent::__construct($message." (XPATH_EXPRESSION_NULL).",1,$previous);
        break;
      case 2:
        parent::__construct($message." (XPATH_EXPRESSION_EMPTY).",2,$previous);
        break;
      default:
        parent::__construct($message." (XPATH_EXPRESSION_INVALID).",0,$previous);
        break;
    }

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
