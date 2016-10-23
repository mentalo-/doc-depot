  <?php  

	include "connex_inc.php";
	include 'general.php';

	include 'exploit.php';

	function supp($idx)
		{
		command("delete from  r_user  where idx = '$idx' ","x");
		command("delete from  r_attachement  where user = '$idx' ","x");
		command("delete from  r_referent  where user = '$idx' ","x");
		command("delete from  r_lien  where user = '$idx' ","x");
		command("delete from  log  where user = '$idx' or acteur = '$idx' ","x");
		}
	backup_tables(false);
	 
	supp(161);	
	supp(160);	
	supp(157);	
	supp(158);	
	supp(153);	
	supp(134);	
	supp(155);	
	supp(154);	
	supp(159);	
	supp(152);	
	supp(113);	
	supp(114);		
	supp(127);		
	supp(84);	
	supp(90);	
		
			
	command("delete from  effectif2  where  commentaire like 'ZZ_FORM%' ","x");

	command("drop table IF EXISTS z_version_old ","x");
	command("drop table IF EXISTS r_user_org  ","x");
	command("drop table IF EXISTS z_TTT_old   ","x");


	?> 