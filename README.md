# Beluga
PHP based NoSql Database Engine

## Create Database
    Db:Constructor(string [Data Directory]) : [database handler]
if the directory doesn't exist it will be created.

### Parameter(s)
- Data Directory(String): It is the path of the directory which is storing data. Write-permission is required to create ,insert, update, delete, or drop actions.

### Return Value
    Command returns database handler.

### Example
    $db = new \Beluga\Db("data");

## Creating or Connecting to Document
document(string [Document Name]);
if the document does not exist it will be created.
### Parameter(s)
- Document Name(string): It is the name of the document which is focusing on.

### Return Value
    Command returns database handler.

### Example
    $db->document("document1");
    
## Insert Data into Document
insert(array [DataSet])
### Parameter(s)
- DataSet: It is an array of objects. Each object will be saved to disk as a new record.

### Return Value 
    Command returns database handler.

### Example
    $db->document("students")->insert(
      [
        [ "StudentName" => "Ali", "StudentId"=> 1 ],
        [ "StudentName" => "Veli", "StudentId"=> 2 ],
        [ "StudentName" => "Erdem", "StudentId"=> 3 ]
      ]  
    );

    $db->document("courses")->insert(
        [ "courseName" => "Math", "StudentId"=> 1 ],
        [ "courseName" => "Chemistry", "StudentId"=> 1 ]
        [ "courseName" => "Math", "StudentId"=> 2 ],
        [ "courseName" => "Physics", "StudentId"=> 2 ],
        [ "courseName" => "Literature", "StudentId"=> 2 ]
        [ "courseName" => "Literature", "StudentId"=> 3 ]
    );

## Update Data in a Document
update( callable [Handler Function] ) : [database handler]

## Parameter(s)
- Handler Function(callable) : It is a user defined void function which has [Scope](#scope-class) parameter.   

### Return Value 
    Command returns database handler.

### Example
    $db->document("students")->update(function(Scope $s) {
        $data = $s->data;
        if ($data["StudentId"] == 3) {
            $data["StudentName"] = "Didem";
            $s->accept($data);
        }
    });

## Delete Data From Document
    delete(callable [Handler Function] ) : [database handler]

### Parameter(s)
- Handler Function(callable) : It is a user defined void function which has [Scope](#scope-class) parameter. 

### Return Value 
    Command returns database handler.

### Example
    $db->document("students")->delete(function(Scope $s) {
        $data = $s->data;
        if ($data["StudentId"] == 2) {
            $s->accept(true);
        }
    });

## List Data From Document
    list(callable [Handler Function]) : array

### Parameter(s)
- Handler Function(callable) : It is a user defined void function which has [Scope](#scope-class) parameter.

### Return Value 
    An array of returning datasets.

### Example
    $db->document("students")->list(function(Scope $s) use($db) {
        $d = $s->data;
        $d["courses"] = $db->document("courses")->list(function(Scope $s) use($d) {
            if ( $s->data["StudentId"] == $d["StudentId"] ) {
                $s->accept($s->data["courseName"]);
            }
        });
        $s->accept($d);
    });
    
## Scope Class
    Scope {
        public data : complex   //Indicates each record value in the document
        public id : string      //Indicates each record id in the document
        public accept(data : complex) : void  //This method is used for accepting data
        public denied() : void //This method is used for cancelling last accept command
        public stop() : void  //This method is used for stopping the loop. Accepted records before this command are readable  
        public abort() : void //This method is used for stopping the loop. Accepted records before this command will be removed  
    }