<?php

require_once __DIR__ . "/../src/Db.php";

use \Beluga\Db;

$db = new Db(__DIR__."/../data");

$db->document("students")->insert(
    [
      [ "StudentName" => "Ali", "StudentId"=> 1 ],
      [ "StudentName" => "Veli", "StudentId"=> 2 ],
      [ "StudentName" => "Hande", "StudentId"=> 3 ]
    ]  
  );

  $db->document("courses")->insert(
      [
        [ "courseName" => "Math", "StudentId"=> 1 ],
        [ "courseName" => "Chemistry", "StudentId"=> 1 ],
        [ "courseName" => "Math", "StudentId"=> 2 ],
        [ "courseName" => "Physics", "StudentId"=> 2 ],
        [ "courseName" => "Literature", "StudentId"=> 2 ],
        [ "courseName" => "Literature", "StudentId"=> 3 ]
      ]      
  );