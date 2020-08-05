<?php
require_once __DIR__ . "/../src/Db.php";

use \Beluga\Db;
use \Beluga\Scope;

$db = new Db(__DIR__."/../data2");

$list = $db->document("students")->list(function(Scope $s) use($db) {
    $d = $s->data;
    $d["courses"] = $db->document("studentcours")->list(function(Scope $s) use($db,$d){
        $sc = $s->data;
        if ( $sc["studentId"] == $d["studentId"] ) {
            $cn = $db->document("courses")->find(function(Scope $s) use($sc) {
                $c = $s->data;
                if ($sc["courseId"] == $c["courseId"]) {
                    $s->accept($c["courseName"]);
                }
            });
            $s->accept($cn);
        }
    });
    $s->accept($d);
});

print_r($list);

echo $db->getTime();