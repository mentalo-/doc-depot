<?php  
///////////////////////////////////////////////////////////////////////
//   This file is part of doc-depot.
//
//   doc-depot is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
//
//   doc-depot is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License along with doc-depot.  If not, see <http://www.gnu.org/licenses/>.
///////////////////////////////////////////////////////////////////////

	include "connex_inc.php";
	include 'general.php';

	function purge_doublons($table)
		{
		echo "<p> - - - - - - - - - - - - - - - - - - - - - - - - ";
		command("drop table tmp");
		command("CREATE TABLE tmp LIKE $table ;","x"); 
		
		$reponse = command("SELECT count(*) as TOTAL FROM $table "); 
		$donnees = fetch_command($reponse) ;
		$avant=$donnees["TOTAL"];
		
		command("INSERT INTO tmp SELECT distinct * FROM $table ;","x");
		
		command("drop table $table","x");
		command("rename table tmp to $table","x");		 
		
		$reponse = command("SELECT count(*) as TOTAL FROM $table "); 
		$donnees = fetch_command($reponse) ;
		$apres=$donnees["TOTAL"];
		echo "<p> $table  : $avant ==> $apres ";		
		}
	
	command("drop table tmp","x");
	$reponse =command("select * from fct_fissa  ");
	while ($donnees = mysql_fetch_array($reponse) )
			{
			$support=$donnees["support"];
			purge_doublons($support);
			}

	?> 