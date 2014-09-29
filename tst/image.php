<?php

header('Content-type: image/png'); 
$str1 = "abcdefghijklmnopqrstuvwxyz0123456789"; 
$str="";
	for($i=0;$i<6;$i++)
		{ 
		$pos = rand(0,35); 
		$str .= $str1{$pos}; 
		} 

$img_handle = ImageCreate (60, 20) or die ("Impossible de créer l'image");
//Image size (x,y) 
$back_color = ImageColorAllocate($img_handle, 255, 255, 255); 
//Background color RBG 
$txt_color = ImageColorAllocate($img_handle, 0, 0, 0); 
//Texte Couleur RBG
ImageString($img_handle, 31, 5, 0, $str, $txt_color); 
Imagepng($img_handle); 
imagedestroy($img_handle);
session_start(); 
$_SESSION['img_number'] = $str; 

?>  

