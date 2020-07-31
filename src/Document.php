<?php

namespace Beluga {
    class Document
    {
        private $target;
        private Db $db;

        public function __construct(string $target, Db $db)
        {
            if (is_dir($target)) {
                $this->target = $target;
                $this->db = $db;
            } else {
                throw new \Beluga\Exception("Folder not found!");
            }
        }

        private function createId() {
            return date('ymdHis') . "-" . uniqid();
        }

        public function delete(callable $fnc): Document
        {
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

            $arr = $this->get(function (Db $db,Scope $s) use ($fnc,$data) {
                $fnc($db,$s,$data);
            },1);
            $id = null;
            if ( count($arr) === 0 ) {
                $id = $this->createId();          
            } else {
                $id = array_keys($arr)[0];
                $file = $this->target . "/" . $id . ".json";
            }
            $file = $this->target . "/$id.json";
            IO::write($file,$data);
            return $this;
        }

        public function multiInsert(array $datalist) : Document {
            $ids = [];
            for($i=0;$i<count($datalist); $i++) {
                $id = $id = $this->createId();
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

        public function first(callable $fnc) {
            $resultset = $this->get($fnc);
            return ( count($resultset) > 0 ? array_values($resultset)[0] : null );
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
                        $fnc($this->db,$scope);
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
