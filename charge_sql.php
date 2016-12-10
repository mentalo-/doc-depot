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
  
    if (isset ($_GET["f"]))
		{  
		include 'general.php';
		include "connex_inc.php"; 
		include 'exploit.php';		
			
		$filename = $_GET["f"] ;
		echo "<p> Fichier -> '$filename' <br>";
		
		$nbc=0;
		$templine = '';
		$lines = file($filename); // Read entire file
		foreach ($lines as $line)
			{

			if (substr($line, 0, 2) == '--' || $line == '')   // Skip all comments ;
				$templine = '';
				else
				$templine .= $line;
				
			if (substr(trim($line), -1, 1) == ';')
				{
				$nbc++;
				mysql_query($templine) or print('Error: '.mysql_error() . '<br>$templine');
				$templine = '';
				}
				
			}
		echo "<hr> $nbc Requêtes exécutées.";

		}
	?>