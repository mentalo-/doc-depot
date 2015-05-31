<?php

if ($_SESSION['pass']==true) 
	{
	$q = variable_s('query');
		
	if ($q=="") 
		$q="select * from ";
	else
		{
		if (isset($_GET['query']))
			$q=variable_get_ltd('query');
		else
			{
			if ($_POST['mdp']!="X531_")
				$q="select * from ";
			}
		}
		
		$tables = array();
		$result = command('SHOW TABLES');
		while($row = nbre_enreg($result))
				$tables[] = $row[0];

		//cycle through
		echo "<table><tr>";
		foreach($tables as $table)
			{
			$cmd=encrypt_ltd("select * from $table");
			echo "<td>";
			echo "<a href=\"?action=cmd_sql&query=$cmd\">";
			echo "$table </a></td><td>|</td>";
			
			if ( ( strpos($q,$table) ) && ( strpos($q,"select ")===false ) )
				backup_tables(false,$table);
				
			}
		echo "</table>";

	
	// SQL access
	formulaire ("cmd_sql");
	echo "<p><textarea name=\"query\" cols=\"80\" rows=\"4\">$q</textarea>
			<input type=\"submit\" name=\"Submit\" value=\"Submit\" />
			<input  type=\"password\" name=\"mdp\" value=\"\"/></p></form>";
	echo "<a href=\"?action=cmd_sql_backup&query=\"> ==> Sauvegardes tables </a>";
	
	if ($q!="select * from ")
		{
		ajout_log_tech( "Commande SQL : $q")	;	
		echo  "<p>Commande SQL : $q";
		$results = command($q)
		or die("<br>$q<br>".mysql_error());

		if($results == false) die("Some problem occured :<br>$q<br>");
		

		foreach($tables as $table)
			{
	
			if ( strpos($q," $table") || strpos($q,"'$table'")) 
				{

				if ( strpos($q,"select ")===false )
					{
					$q="select * from $table";
					$results = command($q);
					}
				
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
						
						if ((strlen($v)>32) && (strpos($v," ")===false) )
							{
							$decrp=decrypt($v);
							if ($decrp!="")
								$v="$decrp (*)";
							}
			
						if ((strlen($v)==10) && ($v[0]=='1') && (is_numeric($v))  )
							$v=date ("d/m/Y H:i",$v);


						echo "<td>$v </td><td>|</td>";
						}
					  $i++;
					}

				echo "</table><p> $i Lignes";
				}
			}
		}
	}
?>

