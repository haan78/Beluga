<?php

namespace Beluga {
    class Document
    {
        private $target;
        private Db $db;
        private bool $aborted = false;

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

        public function delete($c = null): Document
        {
            $arr = $this->get($c);
            if ( $this->aborted ) {
                $this->db->__setAffectedIds([]);
                return $this;
            }
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

        public function save($c,array $datalist = []) : Document {
            $arr = $this->get($c);
            if ( $this->aborted ) {
                $this->db->__setAffectedIds([]);
                return $this;
            }
            $ids = [];
            foreach ($arr as $id => $row) {
                $file = $this->target . "/" . $id . ".json";
                IO::write($file,$row);
                array_push($ids,$id);
            }
            if (count($ids)==0) {
                for($j=0; $j<count($datalist); $j++) {
                    $id = $this->createId();
                    $file = $this->target . "/$id.json";
                    IO::write($file,$datalist[$j]);
                    array_push($ids,$id);
                }
            }
            $this->db->__setAffectedIds($ids);
            return $this;
        }

        public function list($c = null): array
        {
            $resultset = $this->get($c); 
            $this->db->__setAffectedIds(array_keys($resultset));
            return array_values($resultset);
        }

        public function first($c = null)
        {
            $arr = $this->get($c);
            $k = array_key_first($arr);
            return is_null($k) ? null : $arr[$k];
        }

        public function last($c = null)
        {
            $arr = $this->get($c);
            $k = array_key_last($arr);
            return is_null($k) ? null : $arr[$k];
        }

        private function get($c = null): array
        {
            $this->aborted = false;
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
                    if ( $scope->isAborted() ) {
                        $this->aborted = true;
                        return [];
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
