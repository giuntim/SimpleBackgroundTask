<?php

/* *
 * 
 * SimpleBackgroundTask
 * 
 * Author: Maurizio Giunti (Codeguru Srl)
 * Date: 2019-01-08
 * 
 * Implements a simple background task interface
 * This classes has been tested on unix-like OS only (FreeBSD / Linux).
 * I don't think it works on Windows.
 * 
 * Use example:
 * $task=new SimpleBackgroundTask;
 * $task->run('ls /etc');
 * while($task->isRunning()) echo "wait until completes\n";
 * echo $task->getOutput();
 * unset($task);
 * 
 * Distributed under BSD license.
 * 
 * */

class SimpleBackgroundTask { 
	
    var $pid=0; // PID of the spwned process
    var $ofile; // stdout file
    var $efile; // stderr file

	/**
	 * Runs a shell command in background
	 * @param string $command The command line to run (Ex: "ls -la")
	 * @param string $tmp Path to directory where tmp files for stderr and stdout will be created (default /tmp)
	 * @return  integer the PID of the background process (or false if something wrong)
	 */
    function run($command,$tmp='/tmp') {

        $this->ofile = tempnam($tmp, 'SBTO-' );
        $this->efile = tempnam($tmp, 'SBTE-' );
        
        $command.= " >".$this->ofile;
        $command.= " 2>".$this->efile;
        
        $command.=' & echo $!';        
        $this->pid=exec($command);
        
        return $this->pid;
    }
    
    
	/**
	 * Check whether background task is still running (basically checks for PID existence)
	 * @return boolean true:task is running, false: task is no more running
	 */
    function isRunning() {
        if($this->pid>0) {
            return (posix_getpgid($this->pid)!==false);
        }
        return false;
    }
	
    
    /**
     * Get command output
     * @return string the output of the command run as redirected from stdout
     */
    function getOutput() {
        return @file_get_contents($this->ofile);
    }
    
	/**
     * Get command error
     * @return string the error output of the command run as redirected from stderr
     */
    function getError() {
        return @file_get_contents($this->efile);
    }
    
    
    
	/**
	 * Destructor: deletes tmp files
	 * TODO: kill command if still running?
	 */
    function __destruct() {
       @unlink($this->ofile);
       @unlink($this->efile);
    }
 
 
	/**
	 * Test function	 
	 */
    static function test() {
        $task=new SimpleBackgroundTask;
        $task->run('ls /etc');
        while($task->isRunning()) echo "wait\n";
        echo $task->getOutput();
        unset($task);
    }
    
}
 
// Autotest (for debugging)
//SimpleBackgroundTask::test();
