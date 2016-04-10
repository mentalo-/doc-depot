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
				"homo", "homosexuel","heterosexuel", "zoophile", "gérontophile", "tapette", "pedé", "tarlouze","prostitue",
				"bisexuel","partouze",	"frigide", "coureur de jupon" , "fornique" , "don juan" , "obsédé sexuel", "cul", "bite","couille",
				"travesti","transexuel","putain","pute", "lopette", "PD", "baise", "homophobe", "pédale",

				// maladie
				"VIH","V I H","sida","sideen","hepatite ","séropositif","Cancer","Alzheimer","parkinson","chimiothérapie","cardiaque","MST","M S T", 
				"IVG", "I V G", "IST", "I S T", "vénérien", "vénérienne", "Maladie sexuellement transmissible", "vaginal", 
				"anorexie","anorexique","boderline","ejaculation","frigidité","Kleptomanie","Kleptomane","Narcolepsie","Phobie sociale","Phobique",
				"pyromanie","pyromane","mycose","infection", "Herpès", "pustule" ,"Tumeur", "Syphilis", "Eczéma", "Zona", "Tuberculose", 
				"Varicelle", "grippe", "Méningite", "Angine","Teigne", "Verrue", "Paludisme","palu", "infectieux", "Appendicite","Septicémie",
				"allergie","allergique", "purulent", 
				
				// medicaments "",
				"ventoline","anxiolytique", "antidepresseur", "anti depresseur", "Methadone","Subutex","Temesta","Effexor","Seresta", "lexomil", 
				
				// drogues
				"addiction","alcoolique","alcoolisé","cas social","drogué","se pique","sniffe" ,"snife" ,"problème d’alcool", 
				"se shoute","se shoote", "toxicomane", "toxicomanie", "cocaïne", "LSD", "amphétamine", "methamphétamine", "cannabis", 
				"marijuana", "joint", "psychotropes",  "hallucinogène", "hallucination", "hallucinatoire",  "haschisch","hasch",	"bad trip", 	
				
				// Médical
				"pathologie","psychiatrique","psychiatre","hôpital","hosto","en dépression","cancéreux","sales betes","petites betes","gale",
				"rendez-vous medical","rdv medical","r d v medical","rendez-vous medecin" ,"rdv medecin" , "maladie contagieuse", "poux",
				"senile", "hospitalise", "dermathologue", "dermathologie", "Escarre", "Hémorragie", "Glaucome", "Strabisme", "congenital", 
				"gynecologue", "ophtalmo", "ophtalmologue", "Scanner", "IRM", "I R M", "analyses medicales", "analyse medicale", 
				"medecine", 
				
				// Psychiatrie
				"parano","paranoia","paranoiaque","schizophrénie","chizophréne","schizo","bipolaire","bi polaire","psycho","psychotique",
				"malade mental","psychiatrique","maladie mentale","atardé","sadique","sadisme","autiste","autisme","Trouble de la personnalité",
				"Trouble cognitif","psychotique","psy", "niais", "nigaud", "imbécile", "idiot", "sot",  "depressif", "déjanté",
				"imbecile","cretin","fou","folle","debile","manque une case","stupide","cyclothymie", "cyclothymique", 
				"pervers","perversion","deviation sexuelle", "Psychothérapie","psychanalyse", "neurologue","dentiste", "suicide", "suicidaire",
				"Psychothérapeute", "persécution","sociopathe",
				
				// race
				"raciste","race","negre","bougnoule",  "négro",
				
				// Hygiene
				"sent mauvais","pue","sent la transpiration","sudation","manque d'hygiene","crade","crado", "crasseuse", "crasseux", "immonde","transpire", 
				"hygiene limite","hygiene douteuse","mauvaise hygiene", "hygiene--",
				
				// religion
				"catholique","catho","juif","juive","musulman","orthodoxe","athé","agnostique","epicurien", "sale arabe", 
				"islam","islamiste","jihadiste","jihad", "non croyant",	"Judaïsme","Judaïque","Bouddhiste",
				
				// politique
				"fachiste","facho","le peniste","gauchiste","socialiste","communiste","coco","FN","front national","nationaliste","royaliste","anarchiste",
				"extrême droite","radicaliste","engagé politiquement","adhérent politique", "nazi", "hitler",  "nazillon",
				
				// qualificatif comportement 
				"agressif","psycho rigide","sexy","grossier","radin","dépensier","arogant","obése","obésité",
				"gras","nonchalant","non chalant","viulgaire", "menteur","insupportable","infidele","fourbe",
				"violent", "méprisable",  "détestable", "écoeurant", "exécrable", "honteux", "acariâtre",
				"excentrique", "grincheux", "provocateur", "misanthrope", "dissimulateur", "querelleur", "peureux", "solitaire", "instable",
				"belliqueux", "condescendant", "Beau parleur", "Capricieux", 
				"mythomane", "mytho", "Cinglé", "Cleptomane", "Cynique", "Fainéant", "Narcissique", "Puérile",  "Vantard",
				
				
				// dénigrement
				"ringuard","feignant","feignasse","petasse","chieur","vaurien","racaille","hypocrite",
				"odieux","rustre","glandeur" , "racoleur",  "racole", "se degrade", 
				
				// insulte
				"pauvre type","conne","con","c o n","connard","connasse","conasse","salope","salo","salaud","emmerdeur","emmerdeuse","merde",
				"abruti","andouille","avorton","bâtard","bouffon","couillon","crétin","crevure","enculé","enfoiré","fumier", "garce", "gogol", "mongol",
				"gouine","gourde","grognasse", "lâche", "lavette", "looser", "misérable", "morveux",  "mauviette", "minable", "minus",
				"un moins que rien",

				
				//police
				"SRPJ","S R P J","garde à vue","incarcéré","prison","emprisonne","sequestre","sequestration","viol","meurtre","violent","laceration","menotte",
				"pédophile","pedophilie","assassin","violeur","viol","inceste","incestueux","meurtrier","delinquant","recidiviste",
				 "truqueur", "filou", "arnaqueur", "escroc", "roublard","malhonnête","tricheur","voleur","tortionnaire","torture","trafiquant","traffiquant",
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
			$mots[$i] = strtr($mots[$i], 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ-()<>;.', 'aaaaaaceeeeiiiinooooouuuuyy      ');	
			
	echo "$i mots à tester<br>";
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
				$commentaire = strtr($commentaire, 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ-()<>;.', 'aaaaaaceeeeiiiinooooouuuuyy      ');
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