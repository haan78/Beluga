# Beluga
PHP based NoSql Database Engine

## Create Database
    Db:Constructor(string [Data Directory])
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
    $db->document("document1")
    
## Insert Data into Document
insert(array [DataSet])
### Parameter(s)
- DataSet: It is an array of objects. Each object will be saved to disk as a new record.

### Return Value 
    Command returns database handler.

### Example
    $db->insert(
      [
        [ "StudentName" => "Ali", "StudentId"=> 1 ],
        [ "StudentName" => "Veli", "StudentId"=> 2 ],
        [ "StudentName" => "Erdem", "StudentId"=> 3 ]
      ]  
    );

## Update Data in a Document
update( callable [Handler Function] )

## Parameter(s)
- Handler Function(callable) : It is a user defined void function which has [Scope](#scope-class) parameter.   

### Return Value 
    Command returns database handler.

### Example
    $db->update(function(Scope $s) {
        $data = $s->data();
        if ($data["StudentId"] == 3) {
            $data["StudentName"] = "Didem";
            $s->accept($data);
        }
    });

## Delete Data From Document
    delete(callable [Handler Function] )

### Parameter(s)
- Handler Function(callable): It is a user defined void function which has Scope parameter. 

### Return Value 
    Command returns database handler.

### Example
    $db->delete(function(Scope $s) {
        $data = $s->data();
        if ($data["StudentId"] == 2) {
            $s->accept(true);
        }
    });
    
## Scope Class
    lkjşlkjş