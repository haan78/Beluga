<?php
require_once __DIR__ . "/../src/Db.php";

use \Beluga\Db;
use \Beluga\Scope;

$db = new Db(__DIR__."/../data");

$db->document("courses")->updateOrInsert(function(Scope $s) {
    $data = $s->data;
    if ($data["StudentId"] == 3 && $data["courseName"] == "Biology") {
        $data["courseName"] == "Biology 101";
        $s->accept($data);
    }
},[ "courseName" => "Biology 101", "StudentId"=> 3 ]);