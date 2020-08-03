<?php

var_dump( ["name"=>"Ali Barış Öztürk"] == ["name"=>"Ali Barış Öztürk"] );
var_dump( ["name"=>"Ali Barış Öztürk", "val"=>1 ] == [ "val"=>2, "name"=>"Ali Barış Öztürk"] );

var_dump(array_keys([]));
var_dump(array_key_first([]));

echo realpath( "C:\code\Beluga\data" );






