  <?php  

		$autorises = array(
		"catholique" => "secours catholique",
		"cul" => "cul de sac",
		"hygiene" => "produits d hygiene",
		"culte" => "denier du culte",
		"queue" => "queue de cerise",
		"queue" => "queue de billard",
		"lache" => "lach�",	
		"alcoolique" => "hydro-alcoolique",
		);
		
		
		$mots= array(
				"syndrome", "sympt�me",
				
				// orientation sexuelles
				"homo", "homosexuel","heterosexuel", "zoophile", "g�rontophile", "tapette", "ped�", "tarlouze","prostitue",
				"bisexuel","partouze",	"frigide", "coureur de jupon" , "fornique" , "don juan" , "obs�d� sexuel", "cul", "bite","couille",
				"travesti","transexuel","putain","pute", "lopette", "PD","P D", "baise", "homophobe", "p�dale", "sodomie","sodome", "cunnilingus", 
				"fellation", "une pipe", "pourri", "lesbienne", "gouine", "goudou", "gonzesse", "tantouse", "gay", "pederaste", "androgine",
				"pine", "dard", "exhibitionniste", "prostitution", "anus", "trou de balle", "fiotte", "phoque", "tafiole", "tante", "lesbo",
				"zizi", "biroute", "bistouquette", "braquemard", "gourdin", "kiki", "qu�quette", "queue", "teub", "z�zette", "zgu�gue", 
				"zigounette", "zobe", "fion", "popotin", "glaouis", "coucougnette", "valseuses", "libertin", "partouzeur", "infecte",
				"le tapin","tapinne", "n�n�", "nibard", "nichon", "entube", "foufoune", "foufounette","pede", "infidele", "misogyne",
				"libertin", "libertinage", "teup", "teupu", "frivole", "deviation sexuelle", "obsc�ne", "d�vergond�","d�voy�", "d�bauch�",
				"d�prav�", "sado", "maso","sm", "mazo", "machiste", "miso", "matcho", "macho", "�mascul�", "ch�tr�","eunuque",
				"deviant sexuel", "baisable", "d�penance a", "d�penance au", 
				
				// maladie
				"hiv","H I V","VIH","V I H","sida","sideen","hepatite ","s�ropositif","s�ro+","Cancer","Alzheimer","parkinson","chimioth�rapie","cardiaque",
				"MST","M S T", "v�n�rien", "v�n�rienne", "Maladie sexuellement transmissible","IST", "I S T", "tremblement",
				"IVG", "I V G",  "vaginal",  "leucemie", "sclerose", "mucoviscidose", "allergie", "allergique", "purulent", "maladif", 
				"anorexie","anorexique","boderline","ejaculation","frigidit�","Kleptomanie","Kleptomane","Narcolepsie","Phobie sociale","Phobique",
				"mycose","infection", "Herp�s", "pustule" ,"Tumeur", "Syphilis", "Ecz�ma", "Zona", "Tuberculose", 
				"Varicelle", "grippe", "M�ningite", "Angine","Teigne", "Verrue", "Paludisme","palu", "infectieux", "Appendicite","Septic�mie",
				"sales betes","petites betes","gale","poux", "morpion", "bien foutu", "chancre mou", "chtouille", "parasite", "puce", "punaise", 
				"v�role",  
				
				// medicaments "",
				"anxiolytique", "antidepresseur", "anti depresseur", "Methadone","Subutex","Temesta","Effexor","Seresta", "lexomil", 
				"Aerius","Atarax","clarityne","Toplexil","Xyzall",	"Advil","Dafalgan","Doliprane","Efferalgan","Spasfon","Di Antalvic",
				"Paracetamol","Aspegic","Efferalgan","Codeine","Ixprim","Lamaline","Dialgirex","Dextropropoxyphene", "Biprofenid",
				"Celestene","Solupred","Voltarene",	"Arimidex","Decapeptyl","Enantone","Glivec","Neulasta","Speciafoldine",			
				"Rhinofluimucil","Daflon","Endotelon","Tahor","Vastarel","Amlor","Dextropropoxyphene","Norlevo","Dexeryl",
				"Diprosone","Lamisil","Levothyrox","Puregon","Forlax","Gaviscon","Inexium","Meteospasmyl","Motilium",
				"Duphaston","Lutenyl","Kardegic","Lovenox","Plavix","Previscan","Cellcept","Enbrel","humira","prograf",
				"Amoxicilline","Fucidine","Orelox","Pyostacine","Inipomp",	"Crestor","Mediator","Renutryl","Metformine","Lucentis","Xalatan",							
				"Derinox","Eludril","Lysopaine","Pivalone","Rhinofluimucil","Pneumorel","Nasonex",	"Coaprovel", "Topalgic",	
				"Seretide","Ventoline","Tanakan","Tubertest","Actonel","Fosavance","Hexaquine","Ketum","Dextropropoxyphene",
				"Betadine","Oropivalone","Bacitracine",	"Coversyl","Toxicarb",	"Propofan","Cialis",	"Levitra","Viagra",						
				"Rhino Sulforgan",	"Hexaquine","Magne","Diffu K",	"Smecta","Helicidine",	"Aprovel","Piascledine","Pariet",
				"Ginkor","Zaldiar","Diamicron",	"Betadine",	"Lysanxia",	"Alodont",	"Lexomil",	"Dacryoserum",	"Stilnox",											
				"Stablon",	"Biocalyptol",	"Thiovalone","Debridat","Pyostacine","Tiorfan","Speciafoldine",	"Ogast","Rivotril",	
	
	
			
				// drogues
				"addiction","alcoolique","alcoolis�","cas social","drogu�","se pique","sniffe" ,"snife" ,"probl�me d�alcool", 
				"se shoute","se shoote", "toxicomane", "toxicomanie", "coca�ne", "LSD", "amph�tamine", "methamph�tamine", "cannabis", 
				"marijuana", "joint", "psychotrope",  "hallucinog�ne", "hallucination", "hallucinatoire",  "haschisch","hasch",	"bad trip", 
				"alcool++", "stup�fiants", "pillule", "herbe", "heroine", "LSD", "L S D",  "shite", "shit", "emprise de l alcool", "dealer", 
				"psychothrope", "alcolo", "pochtron", "beuze", "drepou", "coke", "keco", "cocaine", "hero", "came", "dope", "accro", 
				"d�fonc�", "junky", "pete", "toxico", "explos�", "teuchi", "schnouf", "chnouf", "ivre","chicon", "kif", 
				 
				// M�dical       
				"pathologie","h�pital","hosto","hospitalise","analyses medicales", "analyse medicale", 
				"rendez-vous medical","rdv medical","r d v medical","rendez vous medecin" ,"rdv medecin" , "medecin", "docteur", "dr", 
				"maladie contagieuse",  "Escarre", "H�morragie", "Glaucome", "Strabisme", "congenital", "canc�reux",
				"Scanner", "IRM", "I R M", "contagieux", "puanteur", "septic�mie", "medoc", "asphyxie", "h�mipl�gie", "h�mipl�gique",   
				"soins palliatif",  "scarification", "scarifie", "contamine",  "grossesse difficile",  "grossesse � risque", 
				"convulsion", "spasme",  
				 
				 // les terminaison en ...gue sont aussi test�es
				 "Endocrinologie", "Gastroent�rologie", "G�riatrie","Oncologie", "Pneumologie", "Urologie", "Rhumatologie", "Oto-rhino-laryngologie",
				 "pediatrie", "Neurochirurgie", "N�phrologie", "Neuropathologie", "H�matologie", "Immunologie","cancerologie","dermathologie", "gynecologie", 
				 "ophtalmologie", "ophtalmo",  "Psychoth�rapie", "psychiatre", "neurologue","dentiste","psychanalyse","Psychoth�rapeute", 

				
				// Psychiatrie
				"parano","paranoia","paranoiaque","schizophr�nie","chizophr�ne","schizo","bipolaire","bi polaire","psycho","psychotique",
				"malade mental","psychiatrique","maladie mentale","atard�","sadique","sadisme","autiste","autisme","Trouble de la personnalit�",
				"Trouble cognitif","psychotique","psy", "niais", "nigaud", "imb�cile", "idiot", "sot",  "depressif", "d�jant�",
				"imbecile","cretin","fou","folle","debile","manque une case","stupide","cyclothymie", "cyclothymique", "hysterie", "hysterique",
				"pervers","perversion",  "suicide", "suicidaire","senile", "tar�", "amoch�", "arri�r�", "qi", "quotient intellectuel", 
				"pers�cution","sociopathe",  "faible d'esprit" , "infirme", "handicap� mental","handicap�e mental",
				"d�c�r�br�", "lobotomise", "en d�pression", "psychose", "nevrose", "demence", "burn out",  "hyperactif",
				"barge", "barjo","destroy", "dingue", "flipp�", "givr�", "guedin", "ouf", "stone", "tap�", "veugra", "vegra",
				"furax", "furieux", "pyromanie","pyromane",  "d�lirant", "Troubles affectifs", "Troubles de la pessonnalit�", 
				"obsessionnel", "anxi�t�", "parano�de", "compulsif", "d�mence", "d�ment", "Machiav�lique", "Machiav�lisme", "illumin�", 
				"p�t� un plomb" , "p�t� un cable" ,"Cingl�", "mythomane", "mytho", "d�sorient�",
				
				// race
				"raciste", "race", "negre", "bougnoul",  "n�gro", "rebeu", "robeu",  "arabe",  "noir",  "black",  "bico",  "beure", 
				"niacou�", "niac", "chinetoque", "chinetoc", "brid�", "nawache", "youpin", "youde", "maghr�bin", "bamboula", "kebla", "renoi", 
				"portos", "gitan", "tzigane", "rom", "ethnie", "b�douin",  "mahom�tan", "arien", "arienne", 
				
				// Hygiene
				"sent mauvais","pue","sent la transpiration","sudation","manque d hygiene","crade","crado", "crasse", "crasseux", "immonde","transpire", 
				"hygiene limite","hygiene douteuse","mauvaise hygiene", "hygiene--", "odeur", "hygiene", "se lave", 
				"chlingue", "daube", "fouette",
				
				// religion
				"catholique","catho","juif","juive","musulman","orthodoxe","ath�","agnostique", "salafiste", "integriste", 
				"islam","islamiste","jihadiste","jihad", "non croyant",	"Juda�sme","Juda�que","Bouddhiste", "sunite", "sunnite", "chiite",
				"d�votion", "envout�","culte","religion","croyance","idolatrie", "vaudou", "mystique", "chretien", "chretienne", "vaudou", "en trans", 
				"p�cheur", "impie","h�r�tique", "pa�en", "ren�gat",  "blasph�matoire", "blasph�me", "imp�nitent",   "profanateur", "irr�ligieux", 
				"pieux", "devot", "hallal", "ramadam", "excision", 
				
				// phylosophique
				"epicurien","libre penseur", "feministe", "Transhumaniste", "altruiste", "Sto�cisme","Sto�cien", "libertaire",
				"Universaliste", "idealiste", "H�doniste", 
				
				// politique
				"fachiste","fasciste","facho","le peniste","gauchiste","socialiste","communiste","coco","FN","front national","nationaliste","royaliste","anarchiste",
				"extr�me droite","extr�me gauche","radicaliste","engag� politiquement","adh�rent politique", "nazi", "hitler",  "nazillon", "carte du parti", "le pen",
				"anar", "Marxiste", "leniniste",  "r�ac", "phalangiste", "r�actionnaire",
				 
				// physique
				"bal�ze", "brun",  "blond",  "roux",  "rousse",  "sexy","gras","ob�se","ob�sit�", "nonchalant","non chalant", "bien foutu", 
				"armoire � glace", "baraque", "chetron", "gueule", "tronche", "bien roule", "laid", "moche", "hideux", 
								
				// qualificatif comportement 
				"psycho rigide","radin","d�pensier","arogant", "bizarre", "asocial", "sectaire", "secte", 
				"vulgaire", "menteur","insupportable",  "cynique", "Humaniste", "Teigneux","virulent",
				"m�prisable",  "d�testable", "�coeurant", "ex�crable", "honteux", "acari�tre", "extraverti", "intraverti",
				"excentrique", "grincheux", "provocateur", "misanthrope", "dissimulateur",  "peureux", "solitaire", "instable",
				"condescendant", "Beau parleur", "Capricieux", "fourbe","malicieux", "Pessimiste", "materialiste", 
				"Cleptomane", "Cynique", "Fain�ant", "Narcissique", "Pu�rile",  "Vantard", "bagarreur" , "antipathique", 
				"inculte", "vicieux", "vermine",  "gibier de potence", "saligaud", "malveillant", "haineux", "m�disant", 
				"maitre chanteur",  "chiant", "emmerdant", "mal eleve","chier", "pisse", "provocant", "destructeur",
				"mal bais�", "gonflant", "relou", "naze", "ringard", "poisseux", " connerie", "pipeau", "dangereux", 
				"colereux", "colerique", "violent", "grossier", "querelleur","belliqueux",  "agressif", "violence conjugal", 
				"d�sobligeant", "hostile", "rebutant",  "repoussant",  "vil", "villain", "dissolu", "larmoyant",
				 "amoral","antimoral", "moral",   "impur", "raclure", "lascif", "immonde", "malpropre", "injustifiable", "�hont�", 
				"d�shonorant",	"coupable",  	 "�nervant", "aga�ant", "enquiquinant", "horripilant" , "exc�dant", "exasp�rant", 
				"narquois", "pu�rile", "enfantin", 
				
				// d�nigrement
				"ringuard","feignant","feignasse","petasse","chieur","vaurien","racaille","hypocrite",
				"odieux", "rustre", "glandeur" , "racoleur", "racole", "se degrade", "d�blat�re", "saoul", "soul", 
				"baratineur",  "pipeauteur", "lavette","mauviette","bon � rien", "boulet", "pleurnichard","gueulard", "gueulard",
				"gourde","grognasse","femmelette", "gonzesse","pouf", "poufiasse", "immoral", "ignoble" ,"ignominieux", "indigne",
				"abject","abominable","affreux","atroce","avilissant", "d�figur�","effrayant","effroyable","enlaidi","guenon",
				"ingrat","laideron","laideur","monstre","monstrueux", "branque", "concupiscent", "bonne femme", "boniche", 
				
				// insulte
				"pauvre type","conne","c o n","connard","connasse","conasse","salope","salo","salaud","emmerdeur","emmerdeuse","merde",
				"abruti","andouille","avorton","b�tard","bouffon","couillon","cr�tin","crevure","encul�","enfoir�","fumier", "garce", "gogol", "mongol",
				 "l�che",  "looser", "mis�rable", "morveux",   "minable", "minus",  "radasse", "radeuse", "tchebi", "tcheubi", 
				"un moins que rien",  "pourriture", "chique molle", "foutre",  "mechant", "infect", 
				"br�le",  "faux derche", "faux cul", "frime", "frimeur", "arriviste", "mauvaise foi", "bon a rien", "bonne a rien", 

				//police
				"SRPJ","S R P J","garde � vue","incarc�r�","emprisonne","sequestre","sequestration","meurtre","violent","laceration","mutilation",
				"p�dophile","pedophilie","assassin","violeur","viol","viole","inceste","incestueux","meurtrier","delinquant","recidiviste",
				 "truqueur", "filou", "arnaqueur", "escroc", "roublard","malhonn�te","tricheur","voleur","tortionnaire","torture","trafiquant","traffiquant",
				"petite frappe","petit calibre","gros calibre","dealer","revendeur de drogue",
				"maison d arr�t", "centre de d�tention", "prison", "menotte", "cachot", "d�tention provisoire","d�tention", "en tole",
				"p�nitencier", "emprisonnement", "maison centrale", "casseur", "chourave", "escroc", "truand",
				"terreur", "terrorise","terroriste", "mal-honn�te", "fripouille", "cambrioleur", "cambriolage", "crapule","keuf", "poulet",
				"coffre", "flic", "flicard", "k�pi", "keuf", "gnouf", "pointeur", "hors-la-loi",  "crapuleux",  
				
				// Syndicat
				"force ouvriere","syndicaliste",
				"FO","CGT","CGC","CFDT","CFTC","CFE-CGC","FSU","UNSA","UNEF","FNSEA","UIMM","CGPME","UNL","UNSA", 
				"F O","C G T","C G C","C F D T","C F T C","C F E C G C","F S U","U N S A","U N E F","F N S E A","U I M M","C G P M E","U N L","U N I","U N S A",
				);	
	

	?>