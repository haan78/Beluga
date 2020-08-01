<?php

var_dump( ["name"=>"Ali Barış Öztürk"] == ["name"=>"Ali Barış Öztürk"] );
var_dump( ["name"=>"Ali Barış Öztürk", "val"=>1 ] == [ "val"=>2, "name"=>"Ali Barış Öztürk"] );

var_dump(array_keys([]));
var_dump(array_key_first([]));

class Test1 {
    private $v = 1;

    public function __get($property) {
        if (property_exists($this, $property)) {
            // We return value here as non public properties are "readonly" in this class
            return $this->$property;
        }
        return null;
    }
}
$c = new Test1();
$c->v = 2;
echo $c->v;






