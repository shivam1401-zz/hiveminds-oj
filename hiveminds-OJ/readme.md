## Indeed Hive Mind

### Setup
* Get your project files inside your Apache's Document Root.
* Open (create, if required) the file "<path-to-document-root>/<project-name>/sys/system_config.php" and set the variables (with appropriate values) as shown below:

```php
<?php
$mysql_hostname = "127.0.0.1";
$mysql_username = "username";
$mysql_password = "password";
$mysql_database = "indeed";
$admin_teamname = "admin";
$admin_password = "password";
?>
```

* Using a browser, open "https://hostname/<project-name>/?display=doc" to read further instructions on how to use this software.
* To judge sumissions, run the script "brain.py" on (preferably).

### Created by: Shivam Mishra
