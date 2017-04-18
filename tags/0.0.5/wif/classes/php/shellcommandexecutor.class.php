<?php

class ShellCommandExecutor extends Executor {

  public function execute($cmd = null) {
    if(!is_null($cmd)) {
      $this->command = $cmd;
      // TODO: try to gen json responses for chunks only
      //       to reduce lags for long term sessions as 
      //       always the whole response will be passed
      while(true) {
        //echo shell_exec($this->command); // script needs to finish before echo
        passthru($this->command);
        sleep(1);
      }
    }
  }

}

?>
