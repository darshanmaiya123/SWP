<?php
session_start();
include_once('/var/www/html/hw6/hw6-lib.php');
include_once('header.php');
connect($db);
if(!isset($_SESSION['authenticated']))
	{
		authenticate($db,$postUser,$postPass);
	}
switch($s)
	{
		default;
		case 4:
			{
				echo "
				<form method=post action=index.php>
				<table> <tr> <td colspan=2> Add Character to Books </td> </tr>
				<tr> <td> Character Name </td> <td> <input type=text name=characterName value=\"\"> </td> </tr>
				<tr> <td> Race </td> <td> <input type=text name=characterRace value=\"\"> </td> </tr>
				<tr> <td> Side </td> <td> <input type=\"radio\" name=\"characterSide\" value=\"good\"> Good  <input type=\"radio\" name=\"characterSide\" value=\"evil\"> Evil </td> </tr>
				<tr> <td colspan=2> 
					<input type=hidden name=s value=5> 
					<input type=submit name=submit value=submit> </td>
				</tr>
				</table> 
				</form>
				<br> <br> <a href=add.php?s=99> Logout </a> <br> <a href=add.php?s=90> Add New Users </a> <br>
				<br> <a href=add.php?s=92> View Users </a> <br>
				";
			}
			break;
		case 99:
			{
				session_destroy();
				echo "You have been logged out successfully";
			} 
			break;
		case 90:
			{
				echo "
					<form method=post action=add.php> 
					<table> <tr> <td colspan=2> Add Users to Tolkien App </td> </tr> 
					<tr> <td> Username: </td> <td> <input type=text name=newUser value=\"\"> </td></tr>
					<tr> <td> Password: </td> <td> <input type=text name=newPass value=\"\"> </td></tr>
					<tr> <td> Email: </td> <td> <input type=text name=newEmail value=\"\"> </td></tr>
					<tr> <td colspan=2> <input type=hidden name=s value=\"91\"> <input type=submit name=submit value=submit> </td></tr>
					</table>
					</form> <br> <br> <a href=add.php?s=99> Logout </a> <br> <a href=add.php?s=90> Add New Users </a> <br><br> <a href=add.php?s=92> View Users </a> <br>
					";
			}
			break;
		case 91:
			{
				if($_SESSION['userid'] != 1)
				{
					echo "you shall not pass!";	
				}
				else
				{
					$salt=hash('sha256',rand(0,500));
					$newPass=hash('sha256', $newPass.$salt);
					$newUser=mysqli_real_escape_string($db,$newUser);
					$newPass=mysqli_real_escape_string($db,$newPass);
					$newEmail=mysqli_real_escape_string($db,$newEmail);
					if($stmt=mysqli_prepare($db,"insert into users set userid='',username=?,password=?,salt=?,email=?"));
					{
						mysqli_stmt_bind_param($stmt,"ssss",$newUser,$newPass,$salt,$newEmail);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_close($stmt);

					}
					echo "Added new user ".$newUser;
					echo "<br> <br> <a href=add.php?s=99> Logout </a> 											            <br> <a href=add.php?s=90> Add New Users </a> <br>
					      <br> <a href=add.php?s=92> View Users </a> <br>
                                        ";
				}
			}
			break;
		case 92:
			{
				if($_SESSION['userid'] == 1)
				{
					$query = "select userid,username from users";
					$result = mysqli_query($db,$query);
					echo "<table>
						<tr> <td> List of Users/Click to change Password  </td> </tr>
					     ";
					while($row=mysqli_fetch_row($result))
					{
						echo "
							<tr>
							<td> <a href=add.php?uid=$row[0]&s=93> $row[1] </a>  
							</td>
						      </tr> \n";
					}
					echo "</table>";
					echo "<br> <a href=add.php?s=99> Logout </a> </br> 											                     <br>    <a href=add.php?s=90> Add New Users </a> </br>
					      <a href=add.php?s=92> View Users </a> </br>
					";
				}
				else
				{
					echo "you shall not pass!";
					echo "<br> <a href=add.php?s=99> Logout </a> </br>";
				}
			}
			break;
		case 93:
			{
				if($_SESSION['userid']==1)	
				{
					echo "
					<form method=post action=add.php> 
					<table> <tr> <td colspan=2> Enter New Password </td> </tr>
					<tr> <td> <input type=text name=newPass value=\"\"> </td> </tr>
					<tr> <td colspan=2>
					<input type=hidden name=s value=94>
					<input type=hidden name=uid value=$uid>
					<input type=submit name=submit value=submit> </td></tr>
					</table> 
					</form>
					";
					echo "<br> <a href=add.php?s=99> Logout </a> </br> 											                     <br>    <a href=add.php?s=90> Add New Users </a> </br>
					      <a href=add.php?s=92> View Users </a> </br>
					";
				}
				else
				{
					echo "you shall not pass!";
					echo "<br> <a href=add.php?s=99> Logout </a> </br>";
				}
			}
			break;
		case 94:
			{
				
				if ($_SESSION['userid'] == 1)
				{
				$uid=mysqli_real_escape_string($db,$uid);
				if ($stmt = mysqli_prepare($db,"select username,salt from users where userid = ?"))
				{
					mysqli_stmt_bind_param($stmt,"s",$uid);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt,$username,$salt);
					while(mysqli_stmt_fetch($stmt))
						{
							$username=$username;
							$salt=$salt;
						}
				mysqli_stmt_close($stmt);
				$newPass=hash('sha256',$newPass.$salt);
				}
				if($stmt=mysqli_prepare($db,"update users set password=? where userid=?"));
				{
					mysqli_stmt_bind_param($stmt,"ss",$newPass,$uid);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);

				}
				echo "Password Successfully Updated for ".$username;
				echo "<br> <br> <a href=add.php?s=99> Logout </a>                                                                                                   <br> <a href=add.php?s=90> Add New Users </a> <br>
				      <br> <a href=add.php?s=92> View Users </a> <br>
				";
				}
				else
				{
					echo "you shall not pass!";
					echo "<br> <a href=add.php?s=99> Logout </a> </br>";
				}
	
			}
			break;
	}
?>
