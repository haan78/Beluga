<?php

require_once __DIR__ . "/../src/Db.php";

use \Beluga\Db;
use \Beluga\Scope;

$db = new Db(__DIR__."/../data");

$name = $db->document("students")->find(function(Scope $s) {
    if ( $s->data["StudentId"] == 1 ) {
        $s->accept($s->data["StudentName"]);
    }
  });

echo $name;