<?php

namespace Beluga {

    require_once "IO.php";
    require_once "Document.php";
    require_once "Exception.php";

    class Db {

        private string $dataFolder;
        
        private array $affectedIds = [];

        public function __construct($target)
        {
            if (!is_dir($target)) {
                mkdir($target);
            }
            
            $this->dataFolder = $target;
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

        public function exist($documentName) {
            return is_dir($this->dataFolder."/$documentName");
        }

        public function info() : array {
            $list = scandir($this->dataFolder);
            $l = [];
            $tcount = 0;
            $tsize = 0;
            for ($i=0; $i<count($list); $i++) {
                $item = $list[$i];
                //echo $item.PHP_EOL;
                $d = $this->dataFolder."/".$item;
                if ($item !="." && $item != ".." && is_dir($d) ) {
                    $files = glob("$d/*.json");
                    $size = 0;
                    $count = count($files);
                    for ($j = 0; $j < $count; $j++) {
                        $size += filesize($files[$j]);
                    }
                    $tcount += $count;
                    $tsize += $size;
                    $l[$item] = [
                        "SIZE"=>$size,
                        "COUNT"=>$count
                    ];
                }
            }
            $l["TOTAL"] = [
                "SIZE"=>$tsize,
                "COUNT"=>$tcount
            ];
            return $l;
        }
    }
}

