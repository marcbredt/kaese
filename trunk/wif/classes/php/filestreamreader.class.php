<?php

class FileStreamReader extends Reader {

  public function __construct() {
    $this->sfile = "/var/log/kaese/iptables.log";
    $this->sfh = fopen($this->sfile,"r");
    $ssuc = fseek($this->sfh,0,SEEK_END);
  }

  public function read() {
    //while(!feof($this->sfh)) {
    while(true) {
      //$s = stream_get_contents($this->sfh,-1); // mem out
      fpassthru($this->sfh); // works but lags without sleep
      //$s = fgets($this->sfh, 64); // does not work, mem out
      //$s = fread($this->sfh, 64); // lags and snips parts
      //if(!(strncmp($s,"",1)==0)) echo $s;
      sleep(1); // lag reduction
      // set ob_implicit_flush(1) to not force explicit flushs here
      //ob_flush();
      //flush();
    }
  }

  public function geth() {
    return $this->sfh;
  }

  // TODO: access already created objects for a session/connection
  //       to e.g. close all file handles
  public function halt() {
    fclose($this->sfh);
  }

}

?>
