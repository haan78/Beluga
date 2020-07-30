<?php
use Beluga\Scope;

require_once "src/Db.php";
require_once "src/Scope.php";
$db = new \Beluga\Db("data");

$db->document("tbl1")->save(function(\Beluga\Scope $s){
    if ( $s->data()["name"] == "Günay Öztürk" ) {
        $s->aborte();
    }
},[ ["name"=>"Günay Öztürk", "val"=>4] ]);

$db->document("tbl2")->save(function(\Beluga\Scope $s){
    if ( $s->data()["val"] == 4 ) {
        $s->aborte();
    }
},[ ["val"=>4, "gun"=>"4 Temmuz"] ]);


$list = $db->document("tbl1")->list(function(\Beluga\Scope $s){

    $r = $s->data();

    $s->keep("tbl1",$r);

    $r["gun"] = $s->db()->document("tbl2")->first(function(\Beluga\Scope $s) {
        $tbl1 = $s->keep("tbl1");
        //var_dump($tbl1);
        if ( $tbl1["val"] == $s->data()["val"] ) {
            $s->accept($s->data()["gun"]);
        }
    });
    $s->accept($r);
});

$db->clear();

print_r($list);

print_r($db->info());