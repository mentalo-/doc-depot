<?PHP
	Echo "Lancement RSYNC<p>";
	exec ( "rsync -avz --password-file=rsync.txt /upload_chi  cj277894-28971827@inbox.pca.ovh.net:inbox " ) ;
	Echo "Fin RSYNC<p>";
	?> 