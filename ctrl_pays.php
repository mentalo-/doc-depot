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

	// on teste le pays d'origine
	if ($_SERVER['REMOTE_ADDR']!="127.0.0.1")
		{
		$pays= strtoupper($_SERVER['GEOIP_COUNTRY_CODE']); 
		if (
			(($pays!="FR")&& ($pays!="RE")&& ($pays!="MQ")&& ($pays!="GF")&& ($pays!="GP")&& ($pays!="")			&& ($pays!="UNKNOWN") )
			&&
			(! ( ($pays=="PT") && (time() < mktime(0,0,0 , 12, 1, 2016 )) )  )
			)
			{
			//ajout_log_tech ( "Rejet connexion ".$_SERVER['REMOTE_ADDR']." car pays = '$pays' ", "P1" );
			aff_logo("x");
			echo "<p>".traduire('Service only available from France ');
			pied_de_page(); 
			}
		}
?>