<?php

if ($_SESSION['pass']==true) 
		{

	if(!isset($_POST)) die("Bad behavior");

	$q = variable_s('query');
	if ($q=="") 
		$q="select * from ";
		
		$tables = array();
		$result = command('SHOW TABLES');
		while($row = nbre_enreg($result))
				$tables[] = $row[0];

		//cycle through
		echo "<table><tr>";
		foreach($tables as $table)
			{
			echo "<td>";
			echo "<a href=\"?action=cmd_sql&query=select * from $table\">";

			echo "$table </a></td><td>|</td>";
			}
		echo "</table>";
		
	// SQL access
	formulaire ("cmd_sql");
	echo "<p><textarea name=\"query\" cols=\"80\" rows=\"4\">$q</textarea>
	  <input type=\"submit\" name=\"Submit\" value=\"Submit\" /></p></form>";

	if ($q!="select * from ")
		{

		$results = command($q)
		or die("<br>$q<br>".mysql_error());

		if($results == false) die("Some problem occured :<br>$q<br>");
		  
		echo "<table><tr>";
		$i=0;
		while ($arr = fetch_command($results))
			{

			 if ($i==0)
				{
				 echo "<tr>";
				  foreach($arr as $k => $v)
					{
					if(intval($k) != 0 || $k == '0') continue;

					echo "<td>$k </td><td>|</td>";
					}	
				}
			 echo "<tr>";
			 foreach($arr as $k => $v)
				{
				if(intval($k) != 0 || $k == '0') continue;
				
				if (strlen($v)>=12)
					{
					$decrp=decrypt($v);
					if ($decrp!="")
						$v="$decrp (*)";
					}

				echo "<td>$v </td><td>|</td>";
				}
			  $i++;
			}

		echo "</table><p> $i Lignes";
		}
	}
?>

