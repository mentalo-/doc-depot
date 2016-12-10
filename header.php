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


function ob_finalisation($buffer)
	{
	$buffer=str_ireplace("<hr><hr>","<hr>",$buffer);	
/*	
	$buffer=str_ireplace("<p","\n\r\n\r<p",$buffer);	
	$buffer=str_ireplace("<hr","\n\r\n\r<hr",$buffer);	
	
	$buffer=str_ireplace("<table", "\n\r<table",$buffer);
	$buffer=str_ireplace("</table","\n\r</table",$buffer);
	
	$buffer=str_ireplace("<tr", "\n\r   <tr",$buffer);
	$buffer=str_ireplace("</tr","\n\r   </tr",$buffer);
	
	$buffer=str_ireplace("<td", "\n\r      <td",$buffer);
	$buffer=str_ireplace("</td","\n\r       </td",$buffer);

	$buffer=str_ireplace("<form" ,"\n\r           <form",$buffer);
	$buffer=str_ireplace("</form","\n\r           </form",$buffer);
	
	$buffer=str_ireplace("<input","\n\r                <input",$buffer);
	*/
	return($buffer);
	}
	
ob_start("ob_finalisation");

header('Content-Type: text/html; charset=ISO-8859-1'); // écrase l'entête utf-8 envoyé par php
ini_set( 'default_charset', 'ISO-8859-1' );
		?>