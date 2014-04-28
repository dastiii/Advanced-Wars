<?php
#   Copyright by: Manuel
#   Support www.ilch.de
#   codeedit by Rolf Berleth

defined ('main') or die ( 'no direct access' );

###Teambildauswahl###
function get_teampic_ar () {
	$ar = array();
	$o = opendir('include/images/teams');
	while ($f = readdir($o) ) {
		if ( $f != '.' AND $f != '..' ) {
			$ar['include/images/teams/'.$f] = $f;
		}
	}
	closedir($o);
	return ($ar);
}
function get_opponent_details ($what, $oid) {
	$abf = "SELECT * FROM prefix_opponents WHERE oid = $oid LIMIT 1";
	$erg = db_query($abf);
	
	$row = db_fetch_assoc($erg);
	if($what == "name") {
		$return = $row['name'];
	} elseif($what == "logo") {
		$return = $row['logo'];
	} elseif($what == "homepage") {
		$return = $row['homepage'];
	} elseif($what == "tag") {
		$return = $row['tag'];
	} elseif($what == "land") {
		$return = $row['land'];
	} elseif($what == "kontaktname") {
		$return = $row['kontaktname'];
	} elseif($what == "email") {
		$return = $row['kontaktemail'];
	} elseif($what == "icq") {
		$return = $row['kontakticq'];
	}
return $return;
} 
function get_team_details ($what, $tid) {
	$abf = "SELECT * FROM prefix_groups WHERE id = $tid LIMIT 1";
	$erg = db_query($abf);
	
	$row = db_fetch_assoc($erg);
	if($what == "name") {
		$return = $row['name'];
	} elseif($what == "logo") {
		$return = $row['logo'];
	}
	
return $return;
} 


?>