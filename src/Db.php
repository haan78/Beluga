<?php

namespace Beluga {

    require_once "Exception.php";
    require_once "IO.php";
    require_once "Document.php";
    require_once "Scope.php";

    class Db {

        private string $dataFolder;
        
        private array $affectedIds = [];

        public function __construct($target)
        {
            $this->dataFolder = realpath( $target );
            if (!is_dir($this->dataFolder)) {
                if (mkdir($this->dataFolder)=== FALSE) {
                    throw new \Beluga\Exception("Failed to create directory");
                }
            }
            
            
            $this->scope = new Scope($this);
        }

        public function getDataFolder() : string {
            return $this->dataFolder;
        }

        public function getAffectedIds() : array {
            return $this->affectedIds;
        }

        public function __setAffectedIds(array $ids) {
            $this->affectedIds = $ids;
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

