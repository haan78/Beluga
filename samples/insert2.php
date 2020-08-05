<?php

require_once __DIR__ . "/../src/Db.php";

use \Beluga\Db;

$db = new Db(__DIR__."/../data2");

$db->document("students")->insert(
    [
      ["StudentName" => "Ali", "studentId" => 1],
      ["StudentName" => "Veli", "studentId" => 2],
      ["StudentName" => "Hande", "studentId" => 3]
    ]
  );

  $db->document("courses")->insert(
    [
      ["courseName" => "Math", "courseId" => 1],
      ["courseName" => "Chemistry", "courseId" => 2],
      ["courseName" => "Physics", "courseId" => 3],
      ["courseName" => "Literature", "courseId" => 4]
    ]
  );

  $db->document("studentcours")->insert(
    [
      ["studentId" => 1, "courseId" => 1],
      ["studentId" => 1, "courseId" => 2],
      ["studentId" => 2, "courseId" => 1],
      ["studentId" => 2, "courseId" => 4],
      ["studentId" => 3, "courseId" => 2],
      ["studentId" => 3, "courseId" => 3]
    ]
  );