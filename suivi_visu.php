<?php session_start(); ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<?php 
include 'header.php';	  
include 'general.php';

$f=variable_s('f');
$d=variable_s('d');

    echo "<head>";
	echo "<title> $f </title>";
	echo " <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />";
	echo "</head>";
	echo "<body>";

//	echo "<frameset rows=\"50,*\" framespacing=\"0\" border=\"0\" frameborder=\"0\">";
 // 	echo "<frame name=\"banner\" target=\"main\" src=\"fixeo.htm\" marginwidth=\"8\" marginheight=\"10\" scrolling=\"no\">";
//  	echo "<noframes>";
	echo "<hr><img src=\"$f\"  >";


  	echo "</body>";
  	echo "</noframes>";
	echo "</frameset>";
?> 
