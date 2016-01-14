<?php

class ShellCommandExecutor extends Executor {

  public function execute($cmd = null) {
    if(!is_null($cmd)) {
      $this->command = $cmd;
      while(true) {
        //echo shell_exec($this->command); // script needs to finish before echo
        passthru($this->command);
        sleep(1);
      }
    }
  }

}

?>
