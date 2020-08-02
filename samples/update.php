<?php

require_once __DIR__ . "/../src/Db.php";

use \Beluga\Db;
use \Beluga\Scope;

$db = new Db(__DIR__."/../data");

$db->document("students")->update(function(Scope $s) {
    $data = $s->data;
    if ($data["StudentId"] == 2) {
        $data["StudentName"] = "Batu";
        $s->accept($data);
    }
});