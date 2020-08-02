<?php

require_once __DIR__ . "/../src/Db.php";

use \Beluga\Db;
use \Beluga\Scope;

$db = new Db(__DIR__."/../data");

$list = $db->document("students")->list(function(Scope $s) use($db) {
    $d = $s->data;
    $d["courses"] = $db->document("courses")->list(function(Scope $s) use($d) {
        if ( $s->data["StudentId"] == $d["StudentId"] ) {
            $s->accept($s->data["courseName"]);
        }
    });
    $s->accept($d);
});

print_r($list);