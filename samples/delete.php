<?php

require_once __DIR__ . "/../src/Db.php";

use \Beluga\Db;
use \Beluga\Scope;

$db = new Db(__DIR__ . "/../data");
$db->document("students")->delete(function (Scope $s) {
    $data = $s->data;
    if ($data["StudentId"] == 2) {
        $s->accept(true);
    }
});
