<!--hello I am a coment-->
<?php
include_once('/var/www/html/hw6/hw6-lib.php');
include_once('header.php');
connect($db);
icheck($s); // Function to check for INT
icheck($sid);
icheck($cid);
icheck($bid);
switch($s)
{
	case 0;
	default:
		{
			echo "<table> <tr> <td> <b> <u> Stories </b> </u> </td> </tr> \n";
			$query = "select storyid,story from stories";
			$result = mysqli_query($db,$query);
			while($row=mysqli_fetch_row($result))
			{
				echo "<tr>
					<td> <a href=index.php?sid=$row[0]&s=1> $row[1] </a>  
					</td>
				      </tr> \n";
			}
			echo "</table>";
		}
		break;
	case 1:
		{	
			echo "<table> <tr> <td> <b> <u> Books </b> </u> </td> </tr> \n";
			$sid=mysqli_real_escape_string($db,$sid);
			if ($stmt = mysqli_prepare($db,"select bookid, title from books where storyid = ?"))
				{
					mysqli_stmt_bind_param($stmt,"s",$sid);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt,$bid,$title);
					while(mysqli_stmt_fetch($stmt))
						{
							$bid=htmlspecialchars($bid);
							$title=htmlspecialchars($title);
							echo "<tr><td><a href=index.php?bid=$bid&s=2> $title </a></td></tr>\n";
						}
					mysqli_stmt_close($stmt);
				}
			echo "</table>";
		}
		break;
	case 2:
		{
			echo "<table> <tr> <td> <b> <u> Characters </b> </u> </td> </tr> \n";
			$bid=mysqli_real_escape_string($db,$bid);
			if ($stmt = mysqli_prepare($db,"select c.characterid,c.name from characters c,appears a,books b where a.bookid = b.bookid and a.characterid=c.characterid and b.bookid= ?"))
				{
					mysqli_stmt_bind_param($stmt,"s",$bid);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt,$cid,$name);
					while(mysqli_stmt_fetch($stmt))
						{
							$cid=htmlspecialchars($cid);
							$name=htmlspecialchars($name);
							echo "<tr><td><a href=index.php?cid=$cid&s=3> $name </a></td></tr>\n";
						}
					mysqli_stmt_close($stmt);
				}
			echo "</table>";
		}
		break;
	case 3:
		{
			echo "<table> <tr> <td colspan=3> <b> <u> Appearances </b> </u> </td> </tr> \n";
			echo "<tr> <td> Character </td> <td> Book </td> <td> Story </td></tr> \n";
			$cid=mysqli_real_escape_string($db,$cid);
			if ($stmt = mysqli_prepare($db,"select c.name,b.title,s.story from appears a,characters c,books b,stories s
where b.storyid = s.storyid and b.bookid=a.bookid and c.characterid=a.characterid and c.characterid = ?"))
				{
					mysqli_stmt_bind_param($stmt,"s",$cid);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt,$character,$book,$story);
					while(mysqli_stmt_fetch($stmt))
						{
							$character=htmlspecialchars($character);
							$book=htmlspecialchars($book);
							$story=htmlspecialchars($story);
							echo "<tr><td><a href=index.php> $character </a></td>";
							echo "<td><a href=index.php> $book </a></td>";
							echo "<td><a href=index.php> $story </a></td></tr>\n";
						}
					mysqli_stmt_close($stmt);
				}
			echo "</table>";
			
		}
		break;
	case 5:
		{
			 $cid=mysqli_real_escape_string($db,$cid);
			 // insert character details into character table
			 $characterName=mysqli_real_escape_string($db,$characterName);
			 $characterRace=mysqli_real_escape_string($db,$characterRace);
			 $characterSide=mysqli_real_escape_string($db,$characterSide);
			 if($stmt=mysqli_prepare($db,"insert into characters set characterid='',name=?,race=?,side=?"));
                                {
                                        mysqli_stmt_bind_param($stmt,"sss",$characterName,$characterRace,$characterSide);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);

                                }
                        if ($stmt = mysqli_prepare($db,"select characterid,name from characters where name=? and race=? and side=? order by characterid desc limit 1"))
                                {
                                        mysqli_stmt_bind_param($stmt,"sss",$characterName,$characterRace,$characterSide);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_bind_result($stmt,$cid,$characterName);
                                        while(mysqli_stmt_fetch($stmt))
                                                {
                                                        $cid=htmlspecialchars($cid);
                                                        $characterName=htmlspecialchars($characterName);
                                                }
                                        mysqli_stmt_close($stmt);
                                }
			echo "
			<form method=post action=index.php> 
			<table> <tr> <td colspan=2> Add Picture to Character  </td> </tr>
			<tr> <td> Character Picture URL </td> <td> <input type=text name=characterPicture value=\"\"> </td> </tr>
			<tr> <td colspan=2>
				 <input type=hidden name=s value=6>
				 <input type=hidden name=cid value=$cid>
				 <input type=hidden name=characterName value=$characterName>
				 <input type=submit name=submit value=submit> </td></tr>
			</table> 
			</form>
			";

		}
		break;
	case 6:
		{
			   $characterPicture=mysqli_real_escape_string($db,$characterPicture);
			   $cid=mysqli_real_escape_string($db,$cid);
			   if($stmt=mysqli_prepare($db,"insert into pictures set pictureid='',url=?,characterid=?"));
                                {
                                        mysqli_stmt_bind_param($stmt,"si",$characterPicture,$cid);
                                        mysqli_stmt_execute($stmt);
                                        mysqli_stmt_close($stmt);

                                }
			echo "
                        <form method=post action=index.php> 
                        <table> <tr> <td colspan=2> Added Picture for $characterName  </td> </tr>
                        <tr> <td colspan=4>
                                 <input type=hidden name=s value=7>
				 <input type=hidden name=cid value=$cid>
                                 <input type=submit name=submit value=\"Add Character to Books\"> </td></tr>
                        </table> 
                        </form>
                        ";

		}
		break;
	case 7:
		{
		        function my_func($db,$cid)	
			{
				echo "
				<form method=post action=index.php>
				<table>
				<tr> <td> Select Book </td>
				<td> <select name=\"bookid\">
				";
				$cid=mysqli_real_escape_string($db,$cid);
				if ($stmt = mysqli_prepare($db,"SELECT distinct(a.bookid), b.title FROM books b, appears a WHERE a.bookid NOT IN (SELECT bookid FROM appears WHERE characterid=?) AND b.bookid=a.bookid"))
				{
					mysqli_stmt_bind_param($stmt,"s",$cid);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_bind_result($stmt,$bid,$Booktitle);
					while(mysqli_stmt_fetch($stmt))
						{
							$bid=htmlspecialchars($bid);
							$Booktitle=htmlspecialchars($Booktitle);	
							echo"<option value=$bid>$Booktitle</option>";
						}
						mysqli_stmt_close($stmt);
					
				}
				echo "
					</select></td> </tr>
					<tr> <td colspan = 5>
					<input type=hidden name=s value=7>
					<input type=hidden name=bid value=$bid>
					<input type=hidden name=cid value=$cid>
					<input type=submit name=submit value=\"Add to Book\">
				";
			}
			if ($bid ==0)
			{
				my_func($db,$cid);
			}
			else
			{
				$cid=mysqli_real_escape_string($db,$cid);
				$bid=mysqli_real_escape_string($db,$bid);
				if($stmt=mysqli_prepare($db,"INSERT INTO appears set appearsid='',bookid=?,characterid=?"));
				{
					mysqli_stmt_bind_param($stmt,"ii",$bid,$cid);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
					
				}
				my_func($db,$cid);
				echo "<a href=index.php?cid=$cid&s=3> Done </a>";
			}
			echo "
					</td></tr>
					</form>
					</table>
			";
	 	}	
		break;
	case 50:
		{
			
			echo "<table> <tr> <td> <b> <u> Characters </b> </u> </td> </tr> \n";
			$query = "select characters.characterid,characters.name,pictures.url from characters,pictures where characters.characterid = pictures.characterid";
			$result = mysqli_query($db,$query);
			while($row=mysqli_fetch_row($result))
			{
				echo "<tr>
					<td> <a href=index.php?s=3&cid=$row[0]> $row[1] </a> 
					</td>
					<td> <img src=$row[2] </img>
					</td>
				      </tr> \n";
			}
			echo "</table>";
		}
		break;

}

function icheck($i) { //Check for numeric
	if ($i != null) {
		if(!is_numeric($i)) {
				print "<b> ERROR: </b>
				Invalid Syntax. ";
				exit;
			    }
			}
		}
?>
