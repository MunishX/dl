<?php 
/* *
   * @filename downloader.class.php
   * @author PsyKzz
   * @version 1.0.0
   * @description Simple class to rate limit your downloads, while also providing a custom tickrate to combat timeout issues.
   * @url http://www.psykzz.co.uk
   *
   * @license 'WTFPL ' - "Do What The Fuck You Want To Public License". 
   * This program is free software. It comes without any warranty, to
   * the extent permitted by applicable law. You can redistribute it
   * and/or modify it under the terms of the Do What The Fuck You Want
   * To Public License, Version 2, as published by Sam Hocevar. See
   * http://sam.zoy.org/wtfpl/COPYING for more details. */
   
      class Downloader {
         private $file_path;
         private $downloadRate;
         private $file_pointer;
         private $error_message;
         private $_tickRate = 4; // Ticks per second.
         private $_oldMaxExecTime; // saving the old value.
         function __construct($file_to_download = null) {
            $this->_tickRate = 4;
            $this->downloadRate = 1024; // in Kb/s (default: 1Mb/s)
            $this->file_pointer = 0; // position of current download.
            $this->setFile($file_to_download);
         }  
         public function setFile($file) {
            if (file_exists($file) && is_file($file))
               $this->file_path = $file;
            else 
               throw new Exception("Error finding file ({$this->file_path}).");
         }
         public function setRate($kbRate) {
            $this->downloadRate = $kbRate;
         }
         private function sendHeaders() {
            if (!headers_sent($filename, $linenum)) {
               header("Content-Type: application/octet-stream");
               header("Content-Description: file transfer");
               header('Content-Disposition: attachment; filename="' . $this->file_path . '"');
               header('Content-Length: '. $this->file_path);
            } else {
               throw new Exception("Headers have already been sent. File: {$filename} Line: {$linenum}");
            }
         }
         public function download() {
            if (!$this->file_path) {
               throw new Exception("Error finding file ({$this->file_path}).");
            }
            flush();    
            $this->_oldMaxExecTime = ini_get('max_execution_time');
            ini_set('max_execution_time', 0);
            $file = fopen($this->file_path, "r");     
            while(!feof($file)) {
               print fread($file, ((($this->downloadRate*1024)*1024)/$this->_tickRate);    
               flush();
               usleep((1000/$this->_tickRate)); 
            }    
            fclose($file);
            ini_set('max_execution_time', $this->_oldMaxExecTime);
            return true; // file downloaded.
         }
      }
