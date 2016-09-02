<?php 

function ob_finalisation($buffer)
	{
	$buffer=str_ireplace("<hr><hr>","<hr>",$buffer);		
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
	
	return($buffer);
	}
	
//ob_start("ob_finalisation");
ob_start();
header('Content-Type: text/html; charset=ISO-8859-1'); // écrase l'entête utf-8 envoyé par php
ini_set( 'default_charset', 'ISO-8859-1' );
		?>