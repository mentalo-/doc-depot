<?php  
///////////////////////////////////////////////////////////////////////
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

		$autorises = array(
		"travail au noir",		
		"travaille au noir",		
		"travaille au black",		
		"travail au black",		
		"secours catholique",
		"cul de sac",
		"produits d hygiene",
		"hydro alcoolique",
		"hydro alcolique",		
		"pieces jointes",
		"piece jointe",
		"sms"
		);
		
		// ne pas mettre de ponctualtion ni d'apostrophe
		$mots= array(
				"syndrome", "symptôme",
				
				// orientation sexuelles
				"homo", "homosexuel","heterosexuel", "zoophile", "gérontophile", "tapette", "pedé","P D", "tarlouze","prostitue",
				"bisexuel","partouze",	"frigide", "coureur de jupon" , "fornique" , "don juan" , "obsédé sexuel", "cul", "bite","couille",
				"travesti","transexuel","putain","pute", "lopette", "PD","P D", "baise", "homophobe", "pédale", "sodomie","sodome", "cunnilingus", 
				"fellation", "une pipe", "pourri", "lesbienne", "gouine", "goudou", "gonzesse", "tantouse", "gay", "pederaste", "androgine",
				"pine", "dard", "exhibitionniste", "prostitution", "anus", "trou de balle", "fiotte", "phoque", "tafiole", "tante", "lesbo",
				"zizi", "biroute", "bistouquette", "braquemard", "gourdin", "kiki", "quéquette", "queue", "teub", "zézette", "zguègue", 
				"zigounette", "zobe", "fion", "popotin", "glaouis", "coucougnette", "valseuses", "libertin", "partouzeur", "infecte",
				"le tapin","tapinne", "néné", "nibard", "nichon", "entube", "foufoune", "foufounette","pede", "infidele", "misogyne",
				"libertin", "libertinage", "teup", "teupu", "frivole", "deviation sexuelle", "obscène", "dévergondé","dévoyé", "débauché",
				"dépravé", "sado", "maso","sm", "mazo", "machiste", "miso", "matcho", "macho", "émasculé", "châtré","eunuque",
				"deviant sexuel", "baisable", "dépenance a", "dépenance au", 
				
				// maladie
				"H I V","V I H","s i d a","sideen","hepatite ","séropositif","séro+","Cancer","Alzheimer","parkinson","chimiothérapie","cardiaque",
				"MST","M S T", "vénérien", "vénérienne", "Maladie sexuellement transmissible", "I S T", "tremblement",
				"IVG", "I V G",  "vaginal",  "leucemie", "sclerose", "mucoviscidose", "allergie", "allergique", "purulent", "maladif", 
				"anorexie","anorexique","boderline","ejaculation","frigidité","Kleptomanie","Kleptomane","Narcolepsie","Phobie sociale","Phobique",
				"mycose","infection", "Herpès", "pustule" ,"Tumeur", "Syphilis", "Eczéma", "Zona", "Tuberculose", 
				"Varicelle", "grippe", "Méningite", "Angine","Teigne", "Verrue", "Paludisme","palu", "infectieux", "Appendicite","Septicémie",
				"sales betes","petites betes","gale","poux", "morpion", "bien foutu", "chancre mou", "chtouille", "parasite", "puce", "punaise", 
				"vérole",  
				
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
				"addiction","alcoolique","alcoolisé","cas social","drogué","se pique","sniffe" ,"snife" ,"problème d’alcool", 
				"se shoute","se shoote", "toxicomane", "toxicomanie", "cocaïne", "LSD", "amphétamine", "methamphétamine", "cannabis", 
				"marijuana", "joint", "psychotrope",  "hallucinogène", "hallucination", "hallucinatoire",  "haschisch","hasch",	"bad trip", 
				"alcool++", "stupéfiants", "pillule", "herbe", "heroine", "L S D",  "shite", "shit", "emprise de l alcool", "dealer", 
				"psychothrope", "alcolo", "pochtron", "beuze", "drepou", "coke", "keco", "cocaine", "hero", "came", "dope", "accro", 
				"défoncé", "junky", "pete", "toxico", "explosé", "teuchi", "schnouf", "chnouf", "ivre","chicon", "kif", 
				 
				// Médical       
				"pathologie","hopital","hosto","hospitalise","analyses medicales", "analyse medicale", 
				"rendez-vous medical","rdv medical","r d v medical","rendez vous medecin" ,"rdv medecin" , "medecin", "docteur", "dr", 
				"maladie contagieuse",  "Escarre", "Hémorragie", "Glaucome", "Strabisme", "congenital", "cancéreux",
				"Scanner", "IRM", "I R M", "contagieux", "puanteur", "septicémie", "medoc", "asphyxie", "hémiplégie", "hémiplégique",   
				"soins palliatif",  "scarification", "scarifie", "contamine",  "grossesse difficile",  "grossesse à risque", 
				"convulsion", "spasme", "circoncision", "circoncire", "polyclinique", "clinique", "E C G" ,"E C B U" , "asthme" ,"asthmatique" ,
				"electro cardiogramme",
				 
				 // les terminaison en ...gue sont aussi testées
				 "Endocrinologie", "Gastroentérologie", "Gériatrie","Oncologie", "Pneumologie", "Urologie", "Rhumatologie", "Oto-rhino-laryngologie",
				 "pediatrie", "Neurochirurgie", "Néphrologie", "Neuropathologie", "Hématologie", "Immunologie","cancerologie","dermathologie", "gynecologie", 
				 "ophtalmologie", "ophtalmo",  "Psychothérapie", "psychiatre", "neurologue","dentiste","psychanalyse","Psychothérapeute", 

				
				// Psychiatrie
				"parano","paranoia","paranoiaque","schizophrénie","chizophréne","schizo","bipolaire","bi polaire","psycho","psychotique",
				"malade mental","psychiatrique","maladie mentale","atardé","sadique","sadisme","autiste","autisme","Trouble de la personnalité",
				"Trouble cognitif","psychotique","psy", "niais", "nigaud", "imbécile", "idiot", "sot",  "depressif", "déjanté",
				"imbecile","cretin","fou","folle","debile","manque une case","stupide","cyclothymie", "cyclothymique", "hysterie", "hysterique",
				"pervers","perversion",  "suicide", "suicidaire","senile", "taré", "amoché", "arriéré", "qi", "quotient intellectuel", 
				"persécution","sociopathe",  "faible d esprit" , "infirme", "handicapé mental","handicapée mental",
				"décérébré", "lobotomise", "en dépression", "psychose", "nevrose", "demence", "dement", "burn out",  "hyperactif",
				"barge", "barjo","destroy", "dingue", "flippé", "givré", "guedin", "ouf", "stone", "tapé", "veugra", "vegra",
				"furax", "furieux", "pyromanie","pyromane",  "délirant", "Troubles affectifs", "Troubles de la pessonnalité", 
				"obsessionnel", "anxiété", "paranoïde", "compulsif", "démence", "dément", "Machiavélique", "Machiavélisme", "illuminé", 
				"pété un plomb" , "pété un cable" ,"Cinglé", "mythomane", "mytho", "désorienté",
				
				// race
				"raciste", "race", "negre", "bougnoul",  "négro", "rebeu", "robeu",  "arabe",  "noir",  "black",  "bico",  "beure", 
				"niacoué", "niac", "chinetoque", "chinetoc", "bridé", "nawache", "youpin", "youde", "maghrébin", "bamboula", "kebla", "renoi", 
				"portos", "gitan", "tzigane", "rom", "ethnie", "bédouin",  "mahométan", "arien", "arienne", 
				
				// Hygiene
				"sent mauvais","pue","sent la transpiration","sudation","manque d hygiene","crade","crado", "crasse", "crasseux", "immonde","transpire", 
				"hygiene limite","hygiene douteuse","mauvaise hygiene", "hygiene--", "odeur", "hygiene", "se lave", 
				"chlingue", "daube", "fouette",
				
				// religion
				"catholique","catho","juif","juive","musulman","orthodoxe","athé","agnostique", "salafiste", "integriste", 
				"islam","islamiste","jihadiste","jihad", "non croyant",	"Judaïsme","Judaïque","Bouddhiste", "sunite", "sunnite", "chiite",
				"dévotion", "envouté","culte","religion","croyance","idolatrie", "vaudou", "mystique", "chretien", "chretienne", "vaudou", "en trans", 
				"pécheur", "impie","hérétique", "païen", "renégat",  "blasphématoire", "blasphéme", "impénitent",   "profanateur", "irréligieux", 
				"pieux", "devot", "hallal", "ramadam", "excision", "excisée", "imam",
				
				// phylosophique
				"epicurien","libre penseur", "feministe", "Transhumaniste", "altruiste", "Stoïcisme","Stoïcien", "libertaire",
				"Universaliste", "idealiste", "Hédoniste", 
				
				// politique
				"fachiste","fasciste","facho","le peniste","gauchiste","socialiste","communiste","coco","FN","front national","nationaliste","royaliste","anarchiste",
				"extrême droite","extrême gauche","radicaliste","engagé politiquement","adhérent politique", "nazi", "hitler",  "nazillon", "carte du parti", "le pen",
				"anar", "Marxiste", "leniniste",  "réac", "phalangiste", "réactionnaire",
				 
				// physique
				"balèze", "brun",  "blond",  "roux",  "rousse",  "sexy","gras","obése","obésité", "nonchalant","non chalant", "bien foutu", 
				"armoire à glace", "baraque", "chetron", "gueule", "tronche", "bien roule", "laid", "moche", "hideux", 
								
				// qualificatif comportement 
				"psycho rigide","radin","dépensier","arogant", "bizarre", "asocial", "sectaire", "secte", 
				"vulgaire", "menteur","insupportable",  "cynique", "Humaniste", "Teigneux","virulent",
				"méprisable",  "détestable", "écoeurant", "exécrable", "honteux", "acariâtre", "extraverti", "intraverti",
				"excentrique", "grincheux", "provocateur", "misanthrope", "dissimulateur",  "peureux", "solitaire", "instable",
				"condescendant", "Beau parleur", "Capricieux", "fourbe","malicieux", "Pessimiste", "materialiste", 
				"Cleptomane", "Cynique", "Fainéant", "Narcissique", "Puérile",  "Vantard", "bagarreur" , "antipathique", 
				"inculte", "vicieux", "vermine",  "gibier de potence", "saligaud", "malveillant", "haineux", "médisant", 
				"maitre chanteur",  "chiant", "emmerdant", "mal eleve","chier", "pisse", "provocant", "destructeur",
				"mal baisé", "gonflant", "relou", "naze", "ringard", "poisseux", " connerie", "pipeau", "dangereux", 
				"colereux", "colerique", "violent", "grossier", "querelleur","belliqueux",  "agressif", "violence conjugal", 
				"désobligeant", "hostile", "rebutant",  "repoussant",  "vil", "villain", "dissolu", "larmoyant",
				 "amoral","antimoral", "moral",   "impur", "raclure", "lascif", "immonde", "malpropre", "injustifiable", "éhonté", 
				"déshonorant",	"coupable",  	 "énervant", "agaçant", "enquiquinant", "horripilant" , "excédant", "exaspérant", 
				"narquois", "puérile", "enfantin", 
				
				// dénigrement
				"ringuard","feignant","feignasse","petasse","chieur","vaurien","racaille","hypocrite",
				"odieux", "rustre", "glandeur" , "racoleur", "racole", "se degrade", "déblatére", "saoul", "soul", 
				"baratineur",  "pipeauteur", "lavette","mauviette","bon à rien", "boulet", "pleurnichard","gueulard", "gueulard",
				"gourde","grognasse","femmelette", "gonzesse","pouf", "poufiasse", "immoral", "ignoble" ,"ignominieux", "indigne",
				"abject","abominable","affreux","atroce","avilissant", "défiguré","effrayant","effroyable","enlaidi","guenon",
				"ingrat","laideron","laideur","monstre","monstrueux", "branque", "concupiscent", "bonne femme", "boniche", 
				
				// insulte
				"pauvre type","conne","c o n","connard","connasse","conasse","salope","salo","salaud","emmerdeur","emmerdeuse","merde",
				"abruti","andouille","avorton","bâtard","bouffon","couillon","crétin","crevure","enculé","enfoiré","fumier", "garce", "gogol", "mongol",
				 "lâche",  "looser", "misérable", "morveux",   "minable", "minus",  "radasse", "radeuse", "tchebi", "tcheubi", 
				"un moins que rien",  "pourriture", "chique molle", "foutre",  "mechant", "infect", 
				"brêle",  "faux derche", "faux cul", "frime", "frimeur", "arriviste", "mauvaise foi", "bon a rien", "bonne a rien", 

				//police
				"SRPJ","S R P J","garde à vue","incarcéré","emprisonne","sequestre","sequestration","meurtre","violent","laceration","mutilation",
				"pédophile","pedophilie","assassin","violeur","viol","viole","inceste","incestueux","meurtrier","delinquant","recidiviste",
				 "truqueur", "filou", "arnaqueur", "escroc", "roublard","malhonnête","tricheur","voleur","tortionnaire","torture","trafiquant","traffiquant",
				"petite frappe","petit calibre","gros calibre","dealer","revendeur de drogue","retention", 
				"maison d arrêt", "centre de détention", "prison", "menotte", "cachot", "détention provisoire","détention", "en tole","de tole",
				"pénitencier", "emprisonnement", "maison centrale", "casseur", "chourave", "escroc", "truand",
				"terreur", "terrorise","terroriste", "mal-honnête", "fripouille", "cambrioleur", "cambriolage", "crapule","keuf", "poulet",
				"coffre", "flic", "flicard", "képi", "keuf", "gnouf", "pointeur", "hors-la-loi",  "crapuleux",  
				
				// Syndicat
				"force ouvriere","syndicaliste",
				"F O","C G T","C G C","C F D T","C F T C","C F E C G C","F S U","U N S A","U N E F","F N S E A","U I M M","C G P M E","U N L","U N I","U N S A",
				);	
	

	?>