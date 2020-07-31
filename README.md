# Beluga
PHP based NoSql Database Engine


## Create Database
new \Beluga\Db("[path]");
if the directory doesn't exist it will be created. But to do, this of course write-permission is required.

### Exemple
    $db = new \Beluga\Db("data");

## Creating or Connecting to Document
$db->document("[document name]");
