<?php
namespace Beluga {

    class Scope {
 
        public $data;
        private string $id;
        private array $resultset = [];
        private bool $stopped = false;
        public function __construct()
        {
        }


        public function abort() : void {
            $this->resultset = [];
            $this->stopped = true;
        }

        public function stop() : void {
            $this->stop = true;
        }

        public function isStopped() {
            return $this->stopped;
        }

        public function data()  {
            return $this->data;
        }

        public function accept($obj = FALSE) {
            if ($obj === FALSE) {
                $this->resultset[$this->id] = $this->data;
            }  else {
                $this->resultset[$this->id] = $obj;
            }                  
        }

        public function getId() : string {
            return $this->id;
        }

        public function __setData($data,string $id) {
            $this->data = $data;
            $this->id = $id;
        }

        public function __getResult() : array {
            return $this->resultset;
        }
    }
}