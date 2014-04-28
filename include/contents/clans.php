<?php
#   Copyright by: dastiac.de
#   Support: www.ilch.de

defined ('main') or die ( 'no direct access' );

if(is_numeric($menu->get(1))) {

	$oid = escape($menu->get(1), 'integer');
	
	$title = $allgAr['title'].' :: Clans :: '.get_opponent_details('name',$oid);
  	$hmenu = '<a class="smalfont" href="?clans">Clandatenbank</a> &raquo; '.get_opponent_details('name',$oid);
  	$design = new design ( $title , $hmenu );
  	$design->header();
  	$tpl = new tpl ('clans');
	
	$abf = "SELECT * FROM prefix_opponents WHERE oid = $oid";
	$erg = db_query($abf);
	
	$row = db_fetch_assoc($erg);
	
		$row['wins'] = db_result(db_query("SELECT count(id) FROM prefix_wars WHERE oid = $oid AND wlp = 1"),0);
		$row['draws']= db_result(db_query("SELECT count(id) FROM prefix_wars WHERE oid = $oid AND wlp = 3"),0);
		$row['loss'] = db_result(db_query("SELECT count(id) FROM prefix_wars WHERE oid = $oid AND wlp = 2"),0);
		$row['ges'] = db_result(db_query("SELECT count(id) FROM prefix_wars WHERE oid = $oid AND status = 3"),0);
	if($row['ges'] == 0) {
		$row['percentWin'] = 0;
		$row['percentLos'] = 0;
		$row['percentPat'] = 0;
	} else {
		$row['percentWin'] = round($row['wins'] / $row['ges'] * 100);
		$row['percentLos'] = round($row['loss'] / $row['ges'] * 100);
		$row['percentPat'] = round($row['draws'] / $row['ges'] * 100);
	}
	$tpl->set_ar_out($row,0);
	
	if($row['ges'] == 0) {
	
		$tpl->set_out('','',1);
	} else {
		$abf = "SELECT * FROM prefix_wars WHERE oid = $oid ORDER BY datime DESC LIMIT 5";
		$erg = db_query($abf);
		
		while($r = db_fetch_assoc($erg)) {
			
			if($r['wlp'] == 1) {	
				$color = "green";
			} elseif($r['wlp'] == 2) {
				$color = "red";
			} else {
				$color = "orange";
			}
			
			$r['team'] = get_team_details('name',$r['tid']);
			$r['gegner'] = get_opponent_details('name', $r['oid']);
			$r['land2'] = get_opponent_details('land', $r['oid']);
			$r['erg'] = '<span style="color: '.$color.';">'.$r['owp'].':'.$r['opp'].'</span>';
			$tpl->set_ar_out($r,2);
		}
	}
	$tpl->set_out('','',3);
	
	$design->footer();
} else {
	$title = $allgAr['title'].' :: Clandatenbank';
  	$hmenu = '<a class="smalfont" href="?clans">Clandatenbank</a>';
  	$design = new design ( $title , $hmenu );
  	$design->header();
  	$tpl = new tpl ('clandb');
	
	$limit = 10;
    $page = ( $menu->getA(1) == 'p' ? $menu->getE(1) : 1 );
    $MPL = db_make_sites ($page , "" , $limit , '?clans' , 'opponents' );
    $anfang = ($page - 1) * $limit;
	
	$abf = "SELECT * FROM prefix_opponents LIMIT $anfang,$limit";
	$erg = db_query($abf);
	
	$tpl->set_out("","",0);
	$row['ges'] = db_result(db_query("SELECT count(oid) FROM prefix_opponents"),0);
	
	if($row['ges'] == 0) {
		$tpl->set_out("","",1);
	}
	
	while($row = db_fetch_assoc($erg)) {
	
		$row['land2'] = get_opponent_details('land',$row['oid']);
		$row['name'] = get_opponent_details('name',$row['oid']);
		$tpl->set_ar_out($row, 2);
	}
	$tpl->set_out('SITELINK',$MPL,3);
	
	
	$design->footer();
}
