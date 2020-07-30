<?php

namespace Beluga {

    class ToolBox {
        public static function readFile($file) {
            $data = json_decode(file_get_contents($file),TRUE);
            if ( \json_last_error() !== JSON_ERROR_NONE ) {
                throw new \Beluga\Exception("File read error / $file");
            }
            return $data;
        }
    }

    class Rules {
        private array $rules = [];
        private array $uniques = [];

        public function __construct($indexFile)
        {
            $this->rules = ToolBox::readFile($indexFile);

            $jfiles = glob(dirname($indexFile)."/.json");
            $rawlist = [];
            for($i=0; $i<count($jfiles); $i++) {
                array_push($rawlist,ToolBox::readFile($jfiles[$i]));
            }

            for($i=0; $i<count($this->rules); $i++) {
                $rule = $this->rules[$i];
                if ( $rule["type"] === "unique" ) {
                    $path = explode(">", str_replace(["[",".","]"], [">",">",""], $rule["path"]) );
                    $arr = [];
                    for($j=0; $j<count($rawlist); $j++) {
                        array_push($arr,$this->getValue($path,$rawlist[$j]));
                    }
                    array_push($this->uniques,$arr);                    
                }
            }
        }

        private function getValue(array $path,$data) {
            $d = $data;
            for($i=0; $i<count($path); $i++) {
                $key = $path[$i];
                if ( isset($d[$key]) ) {
                    $d = $d[$key];
                } else {
                    return null;
                }
            }
        }


    }
}