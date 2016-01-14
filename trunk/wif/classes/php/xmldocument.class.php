<?php

namespace core\util\xml;

/**
 * This class is used to read XML documents. 
 * It belongs to the core. As it is used by AccessibleObject
 * AccessChecker respectively it could not extend AccessibleObject
 * otherwise there are probably loops generated.
 * @author Marc Bredt
 * @see <a href="http://php.net/manual/en/class.domdocument.php">DOMDocument</a>
 */ 
class XMLDocument {

  /**
   * Store the XML document as DOMDocument.
   */
  private $doc = null;
  
  /**
   * Logger for this class.
   */
  private $logger = null;

  /**
   * Load the XML file into a DOMDocument.
   * @param $xmlfile path to the XML file to be loaded.
   * @param $dtdfile path to dtd file to validate the XML
   */
  public function __construct($xmlfile = null, $dtdfile = null) {

    // initialize the logger
    $this->logger = new \core\util\log\FileLogger("XMLDocument","../xmldocument.log");
 
    // initialize the document
    if(!is_null($xmlfile) && file_exists($xmlfile)
       && strncmp(mime_content_type($xmlfile),"application/xml",15)==0) {

      // create a validatable DOMDocument first and
      // run validation on the xmlfile using the dtd provided
      if(!is_null($dtdfile) && file_exists($dtdfile)) { 
        
        $dvalidatable = $this->createValidatableDocument($xmlfile,$dtdfile);

        if(!is_null($dvalidatable) && @$dvalidatable->validate()) { 
          $this->doc = $dvalidatable;
          $dvalidatable = null;

        } else {
          $this->logger->logge("%",array(new \core\exception\xml\XMLNotValidException("XML not valid",2)),"ERR");
          throw(new \core\exception\xml\XMLNotValidException("XML not valid",2));
        }

      } else {
        $this->logger->logge("%",array(new \core\exception\xml\XMLNoValidDTDException()),"ERR");
        throw(new \core\exception\xml\XMLNoValidDTDException());

      }

    } else {
      $this->logger->logge("%",array(new \core\exception\xml\XMLNotValidException()),"ERR");
      throw(new \core\exception\xml\XMLNotValidException());
    
    }

  }

  /**
   * Create document with corresponding document description.
   * @param $xmlfile XML file to create document from
   * @param $dtdfile DTD file to create document from
   * @param $root tagname of the root element. 
   *              should be gathered from original XML file.
   * @return DOMDocument with $dtdfile attached
   */
  private function createValidatableDocument($xmlfile = null, $dtdfile = null) {

    $d = null;
    $dx = new \DOMDocument();
    if($dx->load($xmlfile)) {
      $di = new \DOMImplementation();
      $dtd = $di->createDocumentType($dx->documentElement->tagName,'',$dtdfile);
      $d = $di->createDocument("",$dx->documentElement->tagName,$dtd);
      $d->xmlStandalone = false;
      $d->xmlVersion = "1.0";
      $d->removeChild($d->documentElement);
      $d->appendChild($d->importNode($dx->documentElement->cloneNode(true),true));
      $d->formatOutput = true;
    }
 
    $this->logger->logge("%",array($d));
    return $d;
  }
  
  /**
   * Get the currently loaded XML document.
   * @return the currently loaded XML document.
   */
  public function getDocument() {
    return $this->doc;
  }

  /**
   * Get the string representation of the loaded XML document.
   * @return string representation of the XML file.
   */  
  public function __toString() {
    return (!is_null($this->doc) ? $this->doc->saveXML() : ""); 
  }

  /**
   * Get string representation for a DOMDocument.
   * @param $doc DOMDocument to be transformed into string
   * @return string representation for <code>$doc</code> if it is a valid
   *                DOMDocument otherwise an empty string
   */
  public static function getDocumentString($doc = null) {

    if (!is_null($doc) 
        && strncmp(gettype($doc),"object",6)==0
        && strncmp(get_class($doc),"DOMDocument",11)==0)
      return $doc->saveXML(); 

    return "";

  }

  /**
   * Send a xpath query to the XML document <code>$doc</code>.
   * @param $query XPath query
   * @param $test should always be false, used to test UnresolvedXPathException
   *              for typed results not being string, double or boolean.
   * @return  representing the nodes found evaluating $query.
   */
  public function xpath($query = "/", $test = false) {

    $nodeeval = "";
    $domx = new \DOMXpath($this->doc);
    $nodedoc = new \DOMDocument();
    $unresolved = false;    

    // catch some invalid xpath expressions before evaluation
    if(is_null($query)) {
      $this->logger->logge("%",array( 
        new \core\exception\xml\xpath\InvalidXPathExpressionException(
          "Invalid XPath expression - Query=".$query,1)),"ERR");
      throw(new \core\exception\xml\xpath\InvalidXPathExpressionException(
        "Invalid XPath expression - Query=".$query,1));

    } else if (strncmp($query,"",1)==0) {
      $this->logger->logge("%",array( 
        new \core\exception\xml\xpath\InvalidXPathExpressionException(
          "Invalid XPath expression - Query=".$query,2)),"ERR");
      throw(new \core\exception\xml\xpath\InvalidXPathExpressionException(
        "Invalid XPath expression - Query=".$query,2));

    }

    // run the evaluation, $test should always be false and only set from
    // test classes to check the unrecognized typed result branch
    // NOTE: as NULL defaults to false the following lines will throw an
    //       InvalidXPathExpressionException. therefor use type 'array'
    //       when $test = true or expand !$nlist with this $test check 
    $nlist = null;
    if(!$test) $nlist = @$domx->evaluate($query);

    // if it still fails raise a general exception
    // NOTE: $nlist is boolean 'false' if evaluation fails
    //         so evaluting 'false()' simply always throws an exception too
    //       https://bugs.php.net/bug.php?id=70523
    if(!$test && !$nlist) {
      $this->logger->logge("%",array( 
        new \core\exception\xml\xpath\InvalidXPathExpressionException()),"ERR");
      throw(new \core\exception\xml\xpath\InvalidXPathExpressionException());
    }

    // if we got some usable values returned
    if(strncmp(gettype($nlist),"object",6)==0
       && strncmp(get_class($nlist),"DOMNodeList",11)==0) { 
 
      foreach($nlist as $n) {
 
        if (strncmp(get_class($n),"DOMDocument",11)==0) {
          $nodeeval = $nodeeval." ".preg_replace("/<\?xml.*"."\?".">/","",
                                                 $n->saveXML());

        } else if (strncmp(get_class($n),"DOMElement",10)==0) {
          $nodedoc->appendChild($nodedoc->importNode($n->cloneNode(TRUE),TRUE));
          $nodeeval = $nodeeval." ".preg_replace("/<\?xml.*"."\?".">/","",
                                                 $nodedoc->saveXML());
 
        } else if (strncmp(get_class($n),"DOMAttr",7)==0) {
          $nodeeval = $nodeeval." ".$n->name."=\"".$n->value."\"";
        
        } else if (strncmp(get_class($n),"DOMText",7)==0) {
          $nodeeval = $nodeeval." ".$n->wholeText."";

        // NOTE: DOMComment is used to throw an UnresolvedXPathException
        //       as comments can be ignored. therefor the followng lines 
        //       are dead code. remove comment prefix to enable DOMComment 
        //       detection
        //} else if (strncmp(get_class($n),"DOMComment",10)==0) {
        //  $nodedoc->appendChild($nodedoc->importNode($n->cloneNode(TRUE),TRUE));
        //  $nodeeval = $nodeeval." ".preg_replace("/<\?xml.*"."\?".">/","",
        //                                         $nodedoc->saveXML());
 
        } else {

          $unresolved = true;
          break;

        }

      }
   
    } else if (strncmp(gettype($nlist),"string",6)==0) {
      $nodeeval = $nodeeval."".$nlist;

    } else if (strncmp(gettype($nlist),"double",6)==0) {
      $nodeeval = $nodeeval."".$nlist;

    } else if (strncmp(gettype($nlist),"boolean",7)==0) {
      $nodeeval = $nodeeval."".($nlist ? "true" : "false");

    } else {

      $unresolved = true;

    }

    // throw an exception if there was an object class or
    // return type unresolved by this function
    if($unresolved) {

      $this->logger->logge("%",array(new \core\exception\xml\xpath\UnresolvedXPathException(
            "Unresolved XPath expression for ".
            (strncmp(gettype($nlist),"object",6)!=0 ? "type " : "").
            gettype($nlist).
            (strncmp(gettype($nlist),"object",6)==0 ? " class " : "").
            (strncmp(gettype($nlist),"object",6)==0 ? get_class($nlist) : ""))),
          "ERR");

      throw(new \core\exception\xml\xpath\UnresolvedXPathException(
            "Unresolved XPath expression for ".
            (strncmp(gettype($nlist),"object",6)!=0 ? "type " : "").
            gettype($nlist).
            (strncmp(gettype($nlist),"object",6)==0 ? " class " : "").
            (strncmp(gettype($nlist),"object",6)==0 ? get_class($nlist) : ""))); 

    }

    // replace (multiple) white spaces and newline characters
    $nodeeval = preg_replace("/> </","><",
                  preg_replace("/^([ \t])+|([ \t])+$/","",
                    preg_replace("/([ \t])+/"," ",
                      preg_replace("/[\r\n]/"," ",$nodeeval))));

    // log the query result
    $this->logger->logge("%",array($nodeeval));
    
    return $nodeeval;

  }

}

?>
