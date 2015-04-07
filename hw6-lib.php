<?php
isset($_REQUEST['s'] ) ? $s = strip_tags($_REQUEST['s']) : $s = "";
isset($_REQUEST['sid'] ) ? $sid = strip_tags($_REQUEST['sid']) : $sid = "";
isset($_REQUEST['bid'] ) ? $bid = strip_tags($_REQUEST['bid']) : $bid = "";
isset($_REQUEST['cid'] ) ? $cid = strip_tags($_REQUEST['cid']) : $cid = "";
isset($_REQUEST['uid'] ) ? $uid = ($_REQUEST['uid']) : $uid = "";
isset($_REQUEST['characterName'] ) ? $characterName = strip_tags($_REQUEST['characterName']) : $characterName = ""; 
isset($_REQUEST['characterRace'] ) ? $characterRace = strip_tags($_REQUEST['characterRace']) : $characterRace = ""; 
isset($_REQUEST['characterSide'] ) ? $characterSide = strip_tags($_REQUEST['characterSide']) : $characterSide = ""; 
isset($_REQUEST['characterPicture'] ) ? $characterPicture = strip_tags($_REQUEST['characterPicture']) : $characterPicture = ""; 
isset($_REQUEST['Booktitle'] ) ? $Booktitle = strip_tags($_REQUEST['Booktitle']) : $Booktitle = ""; 
isset($_REQUEST['postUser'] ) ? $postUser = strip_tags($_REQUEST['postUser']) : $postUser = ""; 
isset($_REQUEST['newUser'] ) ? $newUser = strip_tags($_REQUEST['newUser']) : $newUser = ""; 
isset($_REQUEST['postPass'] ) ? $postPass = strip_tags($_REQUEST['postPass']) : $postPass = ""; 
isset($_REQUEST['newPass'] ) ? $newPass = strip_tags($_REQUEST['newPass']) : $newPass = ""; 
isset($_REQUEST['newEmail'] ) ? $newEmail = $_REQUEST['newEmail'] : $newEmail = ""; 
function connect(&$db){
	$mycnf="/etc/hw5-mysql.conf";
	if (!file_exists($mycnf)) 
	{
		echo "ERROR: DB Config file not found: $mycnf";
		exit;
	}
	$mysql_ini_array=parse_ini_file($mycnf);
	$db_host=$mysql_ini_array["host"];
	$db_user=$mysql_ini_array["user"];
	$db_pass=$mysql_ini_array["pass"];
	$db_port=$mysql_ini_array["port"];
	$db_name=$mysql_ini_array["dbName"];
	$db = mysqli_connect($db_host, $db_user, $db_pass,$db_name,$db_port);

	if (!$db) 
	{
		print "Error connecting to DB: " . mysql_error();
		exit;
	}
	mysql_select_db($db_name, $db);
}
function authenticate($db,$postUser,$postPass)
	{
		$postUser=mysqli_real_escape_string($db,$postUser);
		$postPass=mysqli_real_escape_string($db,$postPass);
		if ($stmt = mysqli_prepare($db,"select userid,password,salt from users where username = ?"))
			{
				mysqli_stmt_bind_param($stmt,"s",$postUser);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_bind_result($stmt,$userid,$password,$salt);
				while(mysqli_stmt_fetch($stmt))
					{
						$password=$password;
						$salt=$salt;
						$userid=$userid;
					}
				mysqli_stmt_close($stmt);
				$epass=hash('sha256',$postPass.$salt);
				if($epass==$password)
				{
					$_SESSION['userid'] = $userid;
					$_SESSION['authenticated'] = "yes";
				}
				else
				{
					header("Location: /hw6/login.php");

				}
			}
	}
?>
