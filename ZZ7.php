  <?php  

	include "connex_inc.php";
	include 'general.php';
	
		$autorises = array(
		"catholique" => "secours catholique",
		"cul" => "cul de sac",
		);
		$mots= array(
				"syndrome",
				
				// orientation sexuelles
				"homo", "homosexuel","heterosexuel", "zoophile", "g�rontophile", "tapette", "ped�", "tarlouze","prostitue",
				"bisexuel","partouze",	"frigide", "coureur de jupon" , "fornique" , "don juan" , "obs�d� sexuel", "cul", "bite","couille",
				"travesti","transexuel","putain","pute", "lopette", "PD", "baise", "homophobe", "p�dale",

				// maladie
				"VIH","V I H","sida","sideen","hepatite ","s�ropositif","Cancer","Alzheimer","parkinson","chimioth�rapie","cardiaque","MST","M S T", 
				"IVG", "I V G", "IST", "I S T", "v�n�rien", "v�n�rienne", "Maladie sexuellement transmissible", "vaginal", 
				"anorexie","anorexique","boderline","ejaculation","frigidit�","Kleptomanie","Kleptomane","Narcolepsie","Phobie sociale","Phobique",
				"pyromanie","pyromane","mycose","infection", "Herp�s", "pustule" ,"Tumeur", "Syphilis", "Ecz�ma", "Zona", "Tuberculose", 
				"Varicelle", "grippe", "M�ningite", "Angine","Teigne", "Verrue", "Paludisme","palu", "infectieux", "Appendicite","Septic�mie",
				"allergie","allergique", "purulent", 
				
				// medicaments "",
				"ventoline","anxiolytique", "antidepresseur", "anti depresseur", "Methadone","Subutex","Temesta","Effexor","Seresta", "lexomil", 
				
				// drogues
				"addiction","alcoolique","alcoolis�","cas social","drogu�","se pique","sniffe" ,"snife" ,"probl�me d�alcool", 
				"se shoute","se shoote", "toxicomane", "toxicomanie", "coca�ne", "LSD", "amph�tamine", "methamph�tamine", "cannabis", 
				"marijuana", "joint", "psychotropes",  "hallucinog�ne", "hallucination", "hallucinatoire",  "haschisch","hasch",	"bad trip", 	
				
				// M�dical
				"pathologie","psychiatrique","psychiatre","h�pital","hosto","en d�pression","canc�reux","sales betes","petites betes","gale",
				"rendez-vous medical","rdv medical","r d v medical","rendez-vous medecin" ,"rdv medecin" , "maladie contagieuse", "poux",
				"senile", "hospitalise", "dermathologue", "dermathologie", "Escarre", "H�morragie", "Glaucome", "Strabisme", "congenital", 
				"gynecologue", "ophtalmo", "ophtalmologue", "Scanner", "IRM", "I R M", "analyses medicales", "analyse medicale", 
				"medecine", 
				
				// Psychiatrie
				"parano","paranoia","paranoiaque","schizophr�nie","chizophr�ne","schizo","bipolaire","bi polaire","psycho","psychotique",
				"malade mental","psychiatrique","maladie mentale","atard�","sadique","sadisme","autiste","autisme","Trouble de la personnalit�",
				"Trouble cognitif","psychotique","psy", "niais", "nigaud", "imb�cile", "idiot", "sot",  "depressif", "d�jant�",
				"imbecile","cretin","fou","folle","debile","manque une case","stupide","cyclothymie", "cyclothymique", 
				"pervers","perversion","deviation sexuelle", "Psychoth�rapie","psychanalyse", "neurologue","dentiste", "suicide", "suicidaire",
				"Psychoth�rapeute", "pers�cution","sociopathe",
				
				// race
				"raciste","race","negre","bougnoule",  "n�gro",
				
				// Hygiene
				"sent mauvais","pue","sent la transpiration","sudation","manque d'hygiene","crade","crado", "crasseuse", "crasseux", "immonde","transpire", 
				"hygiene limite","hygiene douteuse","mauvaise hygiene", "hygiene--",
				
				// religion
				"catholique","catho","juif","juive","musulman","orthodoxe","ath�","agnostique","epicurien", "sale arabe", 
				"islam","islamiste","jihadiste","jihad", "non croyant",	"Juda�sme","Juda�que","Bouddhiste",
				
				// politique
				"fachiste","facho","le peniste","gauchiste","socialiste","communiste","coco","FN","front national","nationaliste","royaliste","anarchiste",
				"extr�me droite","radicaliste","engag� politiquement","adh�rent politique", "nazi", "hitler",  "nazillon",
				
				// qualificatif comportement 
				"agressif","psycho rigide","sexy","grossier","radin","d�pensier","arogant","ob�se","ob�sit�",
				"gras","nonchalant","non chalant","viulgaire", "menteur","insupportable","infidele","fourbe",
				"violent", "m�prisable",  "d�testable", "�coeurant", "ex�crable", "honteux", "acari�tre",
				"excentrique", "grincheux", "provocateur", "misanthrope", "dissimulateur", "querelleur", "peureux", "solitaire", "instable",
				"belliqueux", "condescendant", "Beau parleur", "Capricieux", 
				"mythomane", "mytho", "Cingl�", "Cleptomane", "Cynique", "Fain�ant", "Narcissique", "Pu�rile",  "Vantard",
				
				
				// d�nigrement
				"ringuard","feignant","feignasse","petasse","chieur","vaurien","racaille","hypocrite",
				"odieux","rustre","glandeur" , "racoleur",  "racole", "se degrade", 
				
				// insulte
				"pauvre type","conne","con","c o n","connard","connasse","conasse","salope","salo","salaud","emmerdeur","emmerdeuse","merde",
				"abruti","andouille","avorton","b�tard","bouffon","couillon","cr�tin","crevure","encul�","enfoir�","fumier", "garce", "gogol", "mongol",
				"gouine","gourde","grognasse", "l�che", "lavette", "looser", "mis�rable", "morveux",  "mauviette", "minable", "minus",
				"un moins que rien",

				
				//police
				"SRPJ","S R P J","garde � vue","incarc�r�","prison","emprisonne","sequestre","sequestration","viol","meurtre","violent","laceration","menotte",
				"p�dophile","pedophilie","assassin","violeur","viol","inceste","incestueux","meurtrier","delinquant","recidiviste",
				 "truqueur", "filou", "arnaqueur", "escroc", "roublard","malhonn�te","tricheur","voleur","tortionnaire","torture","trafiquant","traffiquant",
				"petite frappe","petit calibre","gros calibre","dealer","revendeur de drogue",
				
				// Syndicat
				"force ouvriere","syndicaliste",
				"FO","CGT","CGC","CFDT","CFTC","CFE-CGC","FSU","UNSA","UNEF","FNSEA","UIMM","CGPME","UNL","UNI","UNSA","SUD", 
				"F O","C G T","C G C","C F D T","C F T C","C F E C G C","F S U","U N S A","U N E F","F N S E A","U I M M","C G P M E","U N L","U N I","U N S A","S U D",
				);	
				
	function test($commentaire, $m)
		{
		global $autorises,  $i, $user, $modif;
					
					if ( 
							(
							(strstr($commentaire," ".$m."e ")) || 
							(strstr($commentaire," ".$m."es ")) || 
							(strstr($commentaire," ".$m."s ")) || 
							(strstr($commentaire," $m "))
							 )
				/*
					&&
							(
							(!strstr($commentaire," pas ".$m."e ")) && 
							(!strstr($commentaire," pas ".$m."s ")) &&
							(!strstr($commentaire," pss $m "))	&&
							(!strstr($commentaire," pas une ".$m."e ")) && 
							(!strstr($commentaire," pas des ".$m."s ")) &&
							(!strstr($commentaire," pss un $m "))
							 )	
								*/
						)
							{
							if ( 
								(!isset ($autorises[$m] ))
								||
								( !strstr($commentaire,$autorises[$m] ) )
								)
									{
									echo "<tr> ";

									$reponse =command("select * from  r_user where idx='$user'  ");		
									if ($donnees = fetch_command($reponse) ) 
										$nom=stripcslashes($donnees["nom"])." ".stripcslashes($donnees["prenom"]);	
									else
										$nom="???";
										
									echo "<td>  $nom </td> ";	
									$date=date('Y-m-d H:i',  $modif );
									echo "<td>  $date </td> ";								

									$commentaire=str_replace ( $m , "<B><FONT color=\"red\"> $m </FONT></b>", $commentaire);
									echo "<td>  $commentaire </td> ";		
					
									}
							}

		}
	
	$t0=time();
	for ($i=0; isset($mots[$i]); $i++)
			$mots[$i] = strtr($mots[$i], '���������������������������-()<>;.', 'aaaaaaceeeeiiiinooooouuuuyy      ');	
			
	echo "$i mots � tester<br>";
	echo "<table border=\"2\"><tr>";
	$reponse =command("select * from fct_fissa  ");
	while ($donnees = mysql_fetch_array($reponse) )
			{
			$support=$donnees["support"];
			$debut = mktime(0,0,0 , date("m")-9, 1, date ("Y"));
			$fin = mktime(0,0,0 , date("m"), 1, date ("Y"));
			$r1 = command("SELECT *  FROM $support where commentaire<>'' and modif>'$debut' and modif<'$fin' "); 
			while ($d1 = mysql_fetch_array($r1) )
				{
				$commentaire=" ".strtolower($d1["commentaire"]);
				$user=$d1["user"];
				$modif=$d1["modif"];
				$commentaire = strtr($commentaire, '���������������������������-()<>;.', 'aaaaaaceeeeiiiinooooouuuuyy      ');
				$commentaire = str_replace ("&apos","", $commentaire);
				for ($i=0; isset($mots[$i]); $i++)
					{
					$m=strtolower($mots[$i]);
					
					test($commentaire, $m);
					
					if 	(strstr($m,"ph"))
						test($commentaire,str_replace ("ph","f", $m) );	

					if 	(strstr($m,"tt"))
						test($commentaire,str_replace ("tt","t", $m) );

					if 	(strstr($m,"th"))
						test($commentaire,str_replace ("th","t", $m) );
						
					if 	(strstr($m,"eux"))
						test($commentaire,str_replace ("eux","euse", $m) );		
						
					if 	(strstr($m,"eux"))
						test($commentaire,str_replace ("eur","euse", $m) );
						
					if 	(strstr($m,"iste"))
						test($commentaire,str_replace ("iste","isme", $m) );			
							
					if 	(strstr($m,"xuel"))
						test($commentaire,str_replace ("xuel","xuelle", $m) );						
					}
				
				}
			}
	echo "</table>";
			echo "Temps traitement : ";
			echo time()-$t0;
				echo " sec. ";
/*
















	*/		
	?>