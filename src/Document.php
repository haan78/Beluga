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

        public function delete($fnc): Document
        {
            $arr = $this->get(function(Scope $s) use ($fnc){
                if ($fnc($s->data())) {
                    $s->accept($s->data());
                }
            });
            $ids = array_keys($arr);
            $j = 0;
            for ($i = 0; $i < count($ids); $i++) {
                $id = $ids[$i];
                $file = $this->target . "/$id.json";
                IO::remove($file);
            }
            $this->db->__setAffectedIds($ids);
            return $this;
        }

        public function update($fnc) : Document {
            $arr = $this->get(function (Scope $s) use ($fnc) {
                $result = $fnc($s->data());
                if ($result !==FALSE) {
                    $s->accept($result);                    
                }
            });
            $ids = [];
            foreach ($arr as $id => $v) {
                $file = $this->target . "/$id.json";
                IO::write($file,$v);
                array_push($ids,$id);
            }
            $this->db->__setAffectedIds($ids);
            return $this;
        }

        public function updateOrInsert($fnc,$data) : Document {

            $arr = $this->get(function (Scope $s) use ($fnc,$data) {
                if ($fnc($s->data(),$data)) {
                    $s->accept($data);
                    $s->stop();
                }
            });
            $id = null;
            $ids = array_keys($arr);
            if ( count($ids) === 0 ) {
                $id = $this->createId();          
            } else {
                $id = $ids[0];
                $file = $this->target . "/" . $id . ".json";
            }
            $file = $this->target . "/$id.json";
            IO::write($file,$data);   
            $this->db->__setAffectedIds([$id]);
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

        public function list($fnc = null): array
        {
            $resultset = [];
            if ( is_null($fnc) ) {
                $resultset = $this->get(); 
            } else {
                $resultset = $this->get(function(Scope $s) use($fnc){
                    if ( $fnc($s->data()) ) {
                        $s->accept($s->data()) ;
                    }
                });
            }
            
            $this->db->__setAffectedIds(array_keys($resultset));
            return array_values($resultset);
        }

        private function get($c = null): array
        {
            $files = glob($this->target . "/*.json");
            $scope = new Scope($this->db);
            $j = 0;
            for ($i = 0; $i < count($files); $i++) {
                $file = $files[$i];
                $id = basename($file,".json");
                $data = json_decode(file_get_contents($file), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Beluga\Exception("Data read error / $file");
                }
                $scope->__setData($data, $id);
                if (isset($c)) {
                    $c($scope);
                    if ( $scope->isStopped() ) {
                        return $scope->__getResult();
                    }
                } else {
                    $scope->accept($data);
                }
                $j++;
            }
            return $scope->__getResult();
        }
    }
}
