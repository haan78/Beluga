<?php
namespace Beluga {

    class Scope {
 
        private $data;
        private string $id;
        private array $resultset = [];
        private bool $stopped = false;

        public function __construct() {
        }

        public function __get($name)
        {
            if ( $name == "data" ) {
                return $this->data;
            } elseif ($name == "id") {
                return $this->id;
            } else {
                throw new \Beluga\Exception("Unsupported access variable on scope");
            }
        }

        public function __set($name, $value)
        {
            if ( $name == "data" || $name == "id") {
                throw new \Beluga\Exception("The read-only variable can not set on the scope. Please use the `accept` method");
            } else {
                throw new \Beluga\Exception("Unsupported access variable on scope");
            }
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

        public function accept($obj = FALSE) : void {
            if ($obj === FALSE) {
                $this->resultset[$this->id] = $this->data;
            }  else {
                $this->resultset[$this->id] = $obj;
            }
        }

        public function denied() : void {
            if ( isset($this->resultset[$this->id]) ) {
                unset($this->resultset[$this->id]);
            }
        }

        public function __setData($data,string $id) : void {
            $this->data = $data;
            $this->id = $id;
        }

        public function __getResult() : array {
            return $this->resultset;
        }
    }
}