<?php

function _md5($str)
{
    return $str;
} // no hash

function mysql_initiate()
{
    global $mysql_hostname, $mysql_username, $mysql_password, $mysql_database, $admin, $ajaxlogout, $sessionid, $admin_teamname, $admin_password, $link;
    
    //$link = mysqli_connect($mysql_hostname,$mysql_username,$mysql_password);
    $link = mysqli_connect("localhost", "admin", "admin", "aurora") or $link = mysqli_connect("localhost", "admin", "admin");
    if (!$link) {
        $_SESSION["message"][] = "SQL Error : Could Not Establish Connection.";
        return;
    }
    if (!mysqli_select_db($link, $mysql_database)) {
        mysqli_query($link, "CREATE DATABASE " . $mysql_database);
        if (!mysqli_select_db($link, $mysql_database)) {
            $_SESSION["message"][] = "SQL Error : Could Not Select Database.";
            return;
        }
    }
    $data  = array_column(mysqli_fetch_all($link->query('SHOW TABLES')), 0);
    //mysql_list_tables($mysql_database); 
    $table = array();
    if (is_resource($data))
        while ($temp = mysqli_fetch_row($data))
            $table[] = $temp[0];
    if (!in_array("teams", $table)) {
        mysqli_query($link, "CREATE TABLE teams (tid int not null primary key auto_increment,teamname tinytext,teamname2 tinytext,pass tinytext,status tinytext,score int,penalty bigint,name1 tinytext,roll1 tinytext,branch1 tinytext,email1 tinytext,phone1 tinytext,name2 tinytext,roll2 tinytext,branch2 tinytext,email2 tinytext,phone2 tinytext,name3 tinytext,roll3 tinytext,branch3 tinytext,email3 tinytext,phone3 tinytext,platform text,ip text,session tinytext,gid int not null)");
    }
    if (!in_array("problems", $table)) {
        mysqli_query($link, "CREATE TABLE problems (pid int not null primary key auto_increment,code tinytext,name tinytext,type tinytext,status tinytext,pgroup tinytext,statement longtext,image blob,imgext tinytext,input longtext,output longtext,timelimit int,score int,languages tinytext,options tinytext)");
    }
    if (!in_array("runs", $table)) {
        mysqli_query($link, "CREATE TABLE runs (rid int not null primary key auto_increment,pid int,tid int,language tinytext,name tinytext,code longtext,time tinytext,result tinytext,error text,access tinytext,submittime int,output longtext)");
    }
    if (!in_array("admin", $table)) {
        mysqli_query($link, "CREATE TABLE admin (variable tinytext,value longtext)");
    }
    if (!in_array("logs", $table)) {
        mysqli_query($link, "CREATE TABLE logs (time int not null primary key,ip tinytext,tid int,request tinytext)");
    }
    if (!in_array("clar", $table)) {
        mysqli_query($link, "CREATE TABLE clar (time int not null primary key,tid int,pid int,query text,reply text,access tinytext,createtime int)");
    }
    if (!in_array("groups", $table)) {
        mysqli_query($link, "CREATE TABLE groups (gid int not null primary key auto_increment, groupname tinytext, statusx int)");
    }
    
    // If empty tables
    $temp = mysqli_query($link, "SELECT * FROM teams");
    if (($temp instanceof mysqli_result) && mysqli_num_rows($temp) == 0) {
        error_log(print_r("Hurrrray", TRUE));
        
        mysqli_query($link, "INSERT INTO teams (teamname,pass,status,score,name1,roll1,branch1,email1,phone1) VALUES ('" . ($admin_teamname) . "','" . _md5($admin_password) . "','Admin',0,'Shivam Mishra','','','shivam@indeed.com','')");
        mysqli_query($link, "INSERT INTO teams (teamname,pass,status,score,name1,roll1,branch1,email1,phone1) VALUES ('ACM','" . _md5($admin_password) . "','Admin',0,'ACM Team','','','','')"); ###
    }
    $temp = mysqli_query($link, "SELECT * FROM problems");
    if (($temp instanceof mysqli_result) && mysqli_num_rows($temp) == 0) {
        mysqli_query($link, "INSERT INTO problems (pid,code,name,type,status,pgroup,statement,input,output,timelimit,score,languages) VALUES (1,'TEST','Squares','Ad-Hoc','Active','#00 Test','" . addslashes(file_get('data/example/problem.txt')) . "','" . addslashes(file_get('data/example/input.txt')) . "','" . addslashes(file_get('data/example/output.txt')) . "',1,0,'Brain,C,C++,C#,Java,JavaScript,Pascal,Perl,PHP,Python,Ruby,Text')");
    }
    $temp = mysqli_query($link, "SELECT * FROM runs");
    if (($temp instanceof mysqli_result) && mysqli_num_rows($temp) == 0) {
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (1,1,1,'C','code','" . (addslashes(file_get('data/example/code.c'))) . "',NULL,NULL,'public')");
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (2,1,1,'C++','code','" . (addslashes(file_get('data/example/code.cpp'))) . "',NULL,NULL,'public')");
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (3,1,1,'C#','code','" . (addslashes(file_get('data/example/code.cs'))) . "',NULL,NULL,'public')");
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (4,1,1,'Java','code','" . (addslashes(file_get('data/example/code.java'))) . "',NULL,NULL,'public')");
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (5,1,1,'JavaScript','code','" . (addslashes(file_get('data/example/code.js'))) . "',NULL,NULL,'public')");
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (6,1,1,'Pascal','code','" . (addslashes(file_get('data/example/code.pas'))) . "',NULL,NULL,'public')");
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (7,1,1,'Perl','code','" . (addslashes(file_get('data/example/code.pl'))) . "',NULL,NULL,'public')");
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (8,1,1,'PHP','code','" . (addslashes(file_get('data/example/code.php'))) . "',NULL,NULL,'public')");
        mysqli_query($link, $link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (9,1,1,'Python','code','" . (addslashes(file_get('data/example/code.py'))) . "',NULL,NULL,'public')");
        mysqli_query($link, "INSERT INTO runs (rid,pid,tid,language,name,code,time,result,access) VALUES (10,1,1,'Ruby','code','" . (addslashes(file_get('data/example/code.rb'))) . "',NULL,NULL,'public')");
    }
    $temp = mysqli_query($link, "SELECT * FROM admin");
    if (($temp instanceof mysqli_result) && mysqli_num_rows($temp) == 0) {
        mysqli_query($link, "INSERT INTO admin VALUES ('mode','Passive');");
        mysqli_query($link, "INSERT INTO admin VALUES ('lastjudge','0');");
        mysqli_query($link, "INSERT INTO admin VALUES ('ajaxrr','0');");
        mysqli_query($link, "INSERT INTO admin VALUES ('mode','Passive');");
        mysqli_query($link, "INSERT INTO admin VALUES ('penalty','20');");
        
        mysqli_query($link, "INSERT INTO admin VALUES ('mysublist','5');");
        mysqli_query($link, "INSERT INTO admin VALUES ('allsublist','10');");
        mysqli_query($link, "INSERT INTO admin VALUES ('ranklist','10');");
        mysqli_query($link, "INSERT INTO admin VALUES ('clarpublic','2');");
        mysqli_query($link, "INSERT INTO admin VALUES ('clarprivate','2');");
        
        mysqli_query($link, "INSERT INTO admin VALUES ('regautoauth','1');");
        mysqli_query($link, "INSERT INTO admin VALUES ('multilogin','0');");
        
        mysqli_query($link, "INSERT INTO admin VALUES ('clarpage','10');");
        mysqli_query($link, "INSERT INTO admin VALUES ('substatpage','25');");
        mysqli_query($link, "INSERT INTO admin VALUES ('probpage','25');");
        mysqli_query($link, "INSERT INTO admin VALUES ('teampage','25');");
        mysqli_query($link, "INSERT INTO admin VALUES ('rankpage','25');");
        mysqli_query($link, "INSERT INTO admin VALUES ('logpage','100');");
        mysqli_query($link, "INSERT INTO admin VALUES ('notice','Announcements\n Welcome to the Indeed Hive Mind.');");
    }
    
    // Other Inits
    $data = mysqli_query($link, "SELECT * FROM admin");
    if (($data instanceof mysqli_result))
        while ($temp = mysqli_fetch_array($data))
            if (!in_array($temp["variable"], array(
                "scoreboard"
            )))
                $admin[$temp["variable"]] = $temp["value"];
    if ($admin["mode"] == "Active" && time() >= $admin["endtime"]) {
        $admin["mode"] = "Disabled";
    }
    if ($admin["mode"] == "Lockdown" && $_SESSION["tid"] != 0 && $_SESSION["status"] != "Admin") {
        $_SESSION["message"][] = "Access Denied : You have been logged out as the contest has been locked down. Please try again again.";
        action_logout();
        $ajaxlogout = 1;
    }
    if (!$admin["multilogin"] && $_SESSION["tid"] && $_SESSION["status"] != "Admin") {
        $sess = mysqli_query($link, "SELECT session FROM teams WHERE tid=" . $_SESSION["tid"]);
        $sess = mysql_fetch_array($sess);
        $sess = $sess["session"];
        if ($sess != $sessionid) {
            $_SESSION["message"][] = "Multiple Login Not Allowed.";
            action_logout();
            $ajaxlogout = 1;
        }
    }
    if (1 || !isset($admin["adminwork"]) || $admin["adminwork"] < time()) {
        action_adminwork();
        $admin["adminwork"] = time() + 10;
    }
    
    return 0; // Success
}





function mysql_terminate()
{
    global $admin;
    global $link;
    //if($_SESSION["status"]=="Admin") print_r($admin);
    foreach ($admin as $key => $value) {
        $temp = mysqli_query($link, "SELECT * FROM admin WHERE variable='$key'");
        if (($temp instanceof mysqli_result) && mysqli_num_rows($temp) > 0)
            mysqli_query($link, "UPDATE admin SET value='" . addslashes($value) . "' WHERE variable='" . addslashes($key) . "'");
        else
            mysqli_query($link, "INSERT INTO admin VALUES ('" . addslashes($key) . "','" . addslashes($value) . "')");
    }
    $_SESSION["time"] = time();
    mysqli_close($link);
}


function mysql_getdata($query)
{
    global $link;
    $t = mysqli_query($link, $query);
    if (!($t instanceof mysqli_result))
        return NULL;
    $data = array();
    while ($row = mysqli_fetch_array($t))
        $data[] = $row;
    return $data;
}

?>