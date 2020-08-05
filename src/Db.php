<?php

namespace Beluga {

    require_once "Exception.php";
    require_once "IO.php";
    require_once "Document.php";
    require_once "Scope.php";

    class Db {

        private string $dataFolder;
        
        private array $affectedIds = [];

        private float $time;

        public function __construct($target)
        {
            if (!is_dir($target)) {
                if (mkdir($target)=== FALSE) {
                    throw new \Beluga\Exception("Failed to create directory : ".$target);
                }
            }
            $this->dataFolder = realpath( $target );
            $this->scope = new Scope($this);
        }

        public function getDataFolder() : string {
            return $this->dataFolder;
        }

        public function getAffectedIds() : array {
            return $this->affectedIds;
        }

        public function getTime() : float {
            return $this->time;
        }

        public function __setAffectedIds(array $ids) {
            $this->affectedIds = $ids;
        }

        public function __setTime(float $time) : void {
            $this->time = $time;
        }

        public function drop($name) : Db {
            if (!IO::delete_directory($this->dataFolder."/$name")) {
                throw new \Beluga\Exception("The document could not be dropped");
            }
            return $this;
        }

        public function document($name) : Document {
            if ( !is_dir($this->dataFolder."/".$name) ) {
                if ( mkdir($this->dataFolder."/".$name) === FALSE ) {
                    throw new \Beluga\Exception("The document could not be created");
                }
            }
            return new \Beluga\Document($this,$name);
        }

        public function exists($documentName) : bool {
            return is_dir($this->dataFolder."/$documentName");
        }

    }
}

