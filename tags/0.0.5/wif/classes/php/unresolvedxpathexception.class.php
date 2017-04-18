<?php

namespace core\exception\xml\xpath;
use \Exception as Exception;

/**
 * This class is used to raise exceptions for unresolved DOMXpath
 * evaluations. Used to face uncatched classes in DOMNodeList or
 * uncatched types using method XMLDocument::xpath
 * @see XMLDocument
 * @author Marc Bredt
 */
class UnresolvedXPathException extends Exception {

  /** 
   * Create a UnresolvedXPathException.
   * @param $message defaut exception message
   * @param $code reason for the exception raised
   * @param $previous previous exception causing this exception
   *                  to be raised.
   */
  public function __construct($message = "Unresolved XPath expression.", 
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
