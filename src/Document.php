<?php

namespace Beluga {
    class Document
    {
        private $target;
        private Db $db;
        private string $name;

        public function __construct(Db $db,$name)
        {
            $target = $db->getDataFolder()."/$name";
            if (is_dir($target)) {
                $this->name = $name;
                $this->target = $target;
                $this->db = $db;
            } else {
                throw new \Beluga\Exception("Folder not found!");
            }
        }

        public function delete(callable $fnc): Document {
            $arr = $this->get($fnc);
            $j = 0;
            foreach($arr as $id => $v) {
                $file = $this->target . "/$id.json";
                IO::remove($file);
            }
            return $this;
        }

        public function update(callable $fnc) : Document {
            $arr = $this->get($fnc);
            foreach ($arr as $id => $v) {
                $file = $this->target . "/$id.json";
                IO::write($file,$v);
            }
            return $this;
        }

        public function updateOrInsert(callable $fnc,$data) : Document {

            $arr = $this->get(function (Scope $s) use ($fnc,$data) {
                $fnc($s,$data);
            },1);
            $id = null;
            if ( count($arr) === 0 ) {
                $id = IO::createId($this->name);          
            } else {
                $id = array_keys($arr)[0];
                $file = $this->target . "/" . $id . ".json";
            }
            $file = $this->target . "/$id.json";
            IO::write($file,$data);
            return $this;
        }

        public function find(callable $fnc) {
            $arr = $this->get(function (Scope $s) use ($fnc) {
                $fnc($s);
                if (count($s->__getResult())>0) {
                    $s->stop();
                }
            },1);
            if ( count($arr) > 0 ) {
                return array_values($arr)[0];
            } else {
                return null;
            }
            
        }

        public function insert(array $datalist) : Document {
            $ids = [];
            for($i=0;$i<count($datalist); $i++) {
                $id = $id = IO::createId($this->name); 
                $file = $this->target . "/" . $id . ".json";
                IO::write($file,$datalist[$i]);
                array_push($ids,$id); 
            }
            $this->db->__setAffectedIds($ids);
            return $this;
        }

        public function list(callable $fnc): array
        {
            $resultset = $this->get($fnc);            
            return array_values($resultset);
        }

        private function get(callable $fnc,?int $limit = null) {
            $scope = new Scope();
            $dir_handle = opendir($this->target);
            if ( $dir_handle ) {
                $i = 0;
                while ($fn = readdir($dir_handle)) {
                    $file = $this->target."/$fn";
                    $info = pathinfo($file);
                    //var_dump($info);
                    if ($info["extension"]=="json") {
                        $id = $info['filename'];
                        $data = IO::read($file); 
                        $scope->__setData($data, $id); 
                        $fnc($scope);
                        if ($scope->isStopped()) {
                            break;
                        }
                        $i++;    
                        if ( !is_null($limit) && $i==$limit ) {
                            break;
                        }              
                    }
                }
                closedir($dir_handle);
                $this->db->__setAffectedIds(array_keys($scope->__getResult()));
                return $scope->__getResult();
            } else {
                new \Beluga\Exception("Folder can't read ".$this->target);
            }
        }
    }
}
