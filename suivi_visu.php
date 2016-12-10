<?php///////////////////////////////////////////////////////////////////////
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

session_start(); ?> 
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
