<?php
require_once "src/Db.php";
require_once "src/Scope.php";
$db = new \Beluga\Db("data");


$list = $db->document("tbl1")->list(function(Beluga\Db $db, \Beluga\Scope $s){

    $r = $s->data();

    $r["gun"] = $db->document("tbl2")->first(function(Beluga\Db $db, \Beluga\Scope $s) use($r) {
        if ( $r["val"] == $s->data()["val"] ) {
            $s->accept($s->data()["gun"]);
        }
    });
    $s->accept($r);
});

print_r($list);

//print_r($db->info());