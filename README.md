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
insert(array [DataSet]) : Db
### Parameter(s)
- DataSet: It is an array of objects. Each object will be saved to disk as a new record.

### Return Value 
    Command returns database handler.

### Example
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

## Update Data in a Document
Document::update( callable [Handler Function] ) : Db

## Parameter(s)
- Handler Function(callable) : It is a user defined void function which has [Scope](#scope-class) parameter.   

### Return Value 
    Command returns database handler.

### Example
    $db->document("students")->update(function(Scope $s) {
        $data = $s->data;
        if ($data["StudentId"] == 2) {
            $data["StudentName"] = "Batu";
            $s->accept($data);
        }
    });

## Delete Data From Document
    Document::delete(callable [Handler Function] ) : Db

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
    Document::list(callable [Handler Function]) : array

### Parameter(s)
- Handler Function(callable) : It is a user defined void function which has [Scope](#scope-class) parameter.

### Return Value 
    An array of returning datasets.

### Example
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
    
## Scope Class
    Scope {
        public data : complex
        public id : string
        public accept(data : complex) : void
        public denied() : void
        public stop() : void 
        public abort() : void
    }
- data: Indicates current record value in the document. It is a readonly property.
- id: Indicates current record id in the document. It is a readonly property. 
- accept: This method is used for accepting data for each element in the document. "data" parameter indicates an object which will be added to the query list.
- denied: This method is used for canceling the last "accept" command. When the "denied" method calls the current element of the document is dropped from the query list.
- stop: This method is used for stopping the loop. Accepted records before this command are reachable. But the loop will no be more continued.
- abort: This method is used for stopping the loop. Accepted records before this command will be removed. That means all query operation will be canceled.

## Other Commands

### Db Functions
- Db::drop([document name]) :void<br/>
    Removes the document which has the given document name from the data directory.
    #### Example:
        $db->drop("students");
- Db::getDataFolder() : string<br/>
    Returns the data directoy of the database.
    #### Example:
        echo $db->getDataFolder();
- Db::exists([document name]) : bool<br/>
    Returns TRUE if the document exists in the data directory. Otherwise returns FALSE
    #### Example:
        if ( $db->exists("courses") ) {
            echo "Document exists";
        } else {
            echo "Document dosen't exists";
        }

### Document Functions
- Document::find(callable [handler function]) : complex<br/>
    This function is definitely the same as the "Document:: list" method the only difference is "Find" return single data of document element rather than an array of elements. When the first document element is accepted loop will end.
    #### Example:
        $name = $db->document("students")->find(function(Scope $s) {
            if ( $s->data["StudentId"] == 1 ) {
                $s->accept($s->data["StudentName"]);
            }
        });

        echo $name;
        
- Document::updateOrInsert(callable [handler function],complex data) : Db<br/>
    This function is run for a single element of the document. When the handler function accepts the element the loop will end. If there is no accepted element. Data will be added as a new element.
    #### Example:
        $db->document("courses")->updateOrInsert(function(Scope $s) {
            $data = $s->data;
            if ($data["StudentId"] == 3 && $data["courseName"] == "Biology") {
                $data["courseName"] == "Biology 101";
                $s->accept($data);
            }
        },[ "courseName" => "Biology 101", "StudentId"=> 3 ]);