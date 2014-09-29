    <?php

	$td_envoi = 1411297753;

	
	$max= (time()-$td_envoi) ;
	echo "<p>".$max;
	
	$max= rand(0,500-min(499,(time()-$td_envoi) ));
	echo "<p>".$max;
	

?>