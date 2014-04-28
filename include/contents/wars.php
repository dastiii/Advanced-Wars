<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );


function get_erg_liste($wid) {
	$list = ''; $enar = array ( 'jpg', 'gif', 'png', 'jpeg');
	$erg = db_query("SELECT * FROM prefix_warmaps WHERE wid = ".$wid);
	$list .= '<div style="width: 100%;">';
	while($row = db_fetch_assoc($erg) ) {
		if ( $row['opp'] == $row['owp'] ) {
			$farbe = 'FDFBB7'; #pat
			$farbe2= 'orange';
		} elseif ( $row['opp'] < $row['owp'] ) {
			$farbe = 'C8E1B8'; #win
			$farbe2= 'green';
		} elseif ( $row['opp'] > $row['owp'] ) {
			$farbe = 'D8B9B9'; #los
			$farbe2= 'red';
		}
		foreach ($enar as $v) {
			if(file_exists('include/images/warmaps/'.$row['map'].'.'.$v)) {
				$row['map2'] = $row['map'].'.'.$v;
			}
		}
		
		$list .= '<div style="float:left; width: 48%; margin: 2px;" class="Cmite">
				  <table><tr>';
		$list .= '<td colspan="2"><img src="include/images/warmaps/'.$row['map2'].'" border="0" alt="'.$row['map'].'"/></td>';
		$list .= '</tr><tr style="color: '.$farbe2.';" class="Cnorm">';
		$list .= '<td style="text-align: center; font-size: 16px; width: 50%;"><strong>'.$row['owp'].'</strong></td>';
		$list .= '<td style="text-align: center; font-size: 16px; width: 50%;"><strong>'.$row['opp'].'</strong></td>';
		$list .= '</tr></table></div>';
	}
	$list .= '</div>';
return ($list);
}
function get_screen_liste($wid) {
	$list = '<div>';
	$enar = array ( 'jpg', 'gif', 'png', 'jpeg');
	$erg = db_query("SELECT * FROM prefix_warmaps WHERE wid = ".$wid);
	while($row = db_fetch_assoc($erg) ) {
		foreach($enar as $v) {
			if ( file_exists ( 'include/images/wars/'.$wid.'_'.$row['mnr'].'.'.$v ) ) {
				$row['mappics1'] = '<a href="include/images/wars/'.$wid.'_'.$row['mnr'].'.'.$v.'" target="_blank" style="padding:2px;"><img src="include/images/wars/'.$wid.'_'.$row['mnr'].'.'.$v.'" alt="Plant '.$row['mnr'].'" width="100" height="80" border="0" /></a>';
			}
		}
		
				$row['mnr'] = $row['mnr'] + 1;
				
		foreach($enar as $v) {
			if ( file_exists ( 'include/images/wars/'.$wid.'_'.$row['mnr'].'.'.$v ) ) {
				$row['mappics2'] = '<a href="include/images/wars/'.$wid.'_'.$row['mnr'].'.'.$v.'" target="_blank" style="padding:2px;"><img src="include/images/wars/'.$wid.'_'.$row['mnr'].'.'.$v.'" alt="Plant '.$row['mnr'].'" width="100" height="80" border="0"  /></a>';
				break;
			}
		}

			$list .= $row['mappics1'].$row['mappics2'];

	}
	$list .= '</div>';
return ($list);
}
function lastwars_get_memberlist ( $id ) {
	$l = '';
	$erg = db_query("SELECT prefix_user.id,prefix_user.name,prefix_user.avatar FROM prefix_user LEFT JOIN prefix_warmember ON prefix_warmember.uid = prefix_user.id AND prefix_warmember.wid = ".$id." WHERE wid = ".$id." ORDER BY prefix_user.name ASC");
	while($r = db_fetch_assoc($erg)) {
		$l .= '<div style="float:left; padding: 3px auto 3px auto; position: relative; background: #c0c0c0; margin: 1px;"><div><a href="index.php?user-details-'.$r['id'].'"><img src="'.$r['avatar'].'" alt=""/></a><div align="center" style="clear: left;">'.$r['name'].'</div></div></div>';
		#$l .= '<a href="index.php?user-details-'.$r['id'].'">'.$r['name'].'</a>, ';
	}
	if(empty($l)) {
		$l .= "- Kein Lineup eingetragen -";
	}
//return (substr($l,0,-2));
return $l;
}

if ( $menu->get(2) == '' OR $menu->getA(2) == 'p' ) {
	$title = $allgAr['title'].' :: Wars';
	$hmenu = 'Wars';
	$design = new design ( $title , $hmenu );
	$design->header();

	
	$ergWin = db_query('SELECT COUNT(id) FROM `prefix_wars` WHERE wlp = "1"');
	$anzWin = db_result($ergWin,0);
	$ergLos = db_query('SELECT COUNT(id) FROM `prefix_wars` WHERE wlp = "2"');
	$anzLos = db_result($ergLos,0);
	$ergPat = db_query('SELECT COUNT(id) FROM `prefix_wars` WHERE wlp = "3"');
	$anzPat = db_result($ergPat,0);
	$ergGes = db_query('SELECT COUNT(id) FROM `prefix_wars` WHERE status= "3"');
	$anzGes = db_result($ergGes,0);
	if($anzGes == 0) {
		$percentWin = 0;
		$percentLos = 0;
		$percentPat = 0;
	} else {
		$percentWin = round($anzWin / $anzGes * 100);
		$percentLos = round($anzLos / $anzGes * 100);
		$percentPat = round($anzPat / $anzGes * 100);
	}
	$tpl = new tpl ( 'wars.htm' );
	$tpl->set_ar_out ( array('PATH' => $percentPat, 'WINH' => $percentWin, 'LOSH' => $percentLos, 'PAT' => $anzPat, 'WIN' => $anzWin, 'LOS' => $anzLos, 'GES' => $anzGes, 'TITLE'=> $allgAr['title'] ) , 0 );
	$akttime = date('Y-m-d');
	$class = '';
	$erg = db_query("SELECT a.id,a.oid,a.game,b.name as team,DATE_FORMAT(datime,'%d.%m.%Y - %H:%i:%s') as time FROM prefix_wars a left join prefix_groups b ON a.tid = b.id WHERE status = 2 AND a.datime >= '".$akttime."' ORDER BY a.datime");
	if ( db_num_rows ( $erg ) == 0 ) {
		echo '<tr class="Cmite"><td colspan="4"><i>There are no upcoming Matches!</i></td></tr>';
	} else {
		while ($row = db_fetch_assoc($erg) ) {
			if ( $class == 'Cmite' ) { $class = 'Cnorm'; } else { $class = 'Cmite'; }
			$row['page'] = get_opponent_details('homepage', $row['oid']);
			$row['land'] = get_opponent_details('land', $row['oid']);
            $row['team'] = get_wargameimg($row['game']).'&nbsp;'.$row['team'];
			$row['class'] = $class;
			$row['gegner'] = get_opponent_details('name',$row['oid']);
			$tpl->set_ar_out($row,1);
		}
	}
	$tpl->out(2);
	$class = '';
	if ( $menu->get(1) == '' ) {
		$teams = dblistee ( '', "SELECT id,name FROM prefix_groups ORDER BY name");
		$game= dblistee ('', "SELECT DISTINCT `game`,`game` FROM prefix_wars ORDER BY `game`" );
		$mtype= dblistee ( '', "SELECT DISTINCT `mtyp`,`mtyp` FROM prefix_wars ORDER BY `mtyp`" );
		$tpl->set_ar_out ( array('tid' => $teams, 'game' => $game, 'typ' => $mtype ) , 3 );
	} elseif ($menu->get(1) == 'last') {
		$tpl->out(4);
		$sqla='WHERE status = 3 ';
		if(!empty($_POST['tid'])){
			$sqla.= 'AND tid="'.$_POST['tid'].'" ';
		}
		if(!empty($_POST['wpl'])){
			$sqla.= 'AND wlp="'.$_POST['wpl'].'" ';
		}
		if(!empty($_POST['spiel'])){
			$sqla.= 'AND game="'.$_POST['spiel'].'" ';
		}
		if(!empty($_POST['typ'])){
			$sqla.= 'AND mtyp="'.$_POST['typ'].'" ';
		}
		# seiten funktion
		$limit = $allgAr['wars_last_limit'];  // Limit
		$page = ( $menu->getA(2) == 'p' ? $menu->getE(2) : 1 );
		$MPL = db_make_sites ($page , "WHERE status = 3" , $limit , "?wars-last" , 'wars' );
		$anfang = ($page - 1) * $limit;
		# seiten funktion
		$farbe1wlpar = array(1=>'C8E1B8',2=>'D8B9B9',3=>'FDFBB7');
		$farbe2wlpar = array(1=>'00FF00',2=>'FF0000',3=>'FFFF00');
		$farbe3wlpar = array(1=>'green',2=>'red',3=>'orange');
		$erg = db_query("SELECT a.owp,a.opp,a.wlp,a.mtyp,a.game,a.id,a.oid,b.name as team,DATE_FORMAT(datime,'%d.%m.%Y %H:%i') as time FROM prefix_wars a left join prefix_groups b ON a.tid = b.id ".$sqla." ORDER BY a.datime DESC, id DESC LIMIT ".$anfang.",".$limit);
		while ($row = db_fetch_assoc($erg) ) {
			$row['erg'] = $row['owp'].':'.$row['opp'];
			$row['farbe'] = $farbe1wlpar[$row['wlp']];
			$row['farbe2'] = $farbe3wlpar[$row['wlp']];
			$row['gegner'] = get_opponent_details('name', $row['oid']);
			if ( $class == 'Cmite' ) { $class = 'Cnorm'; } else { $class = 'Cmite'; }
			$row['land'] = get_opponent_details('land', $row['oid']);
			$row['page'] = get_opponent_details('homepage', $row['oid']);
		
      $row['team'] = get_wargameimg($row['game']).'&nbsp;'.$row['team'];
			$row['class'] = $class;
			$tpl->set_ar_out($row,5);
		}
		$tpl->out(6);
		echo $MPL;
	}
  $design->footer();
} elseif ( is_numeric($menu->get(2)) ) {
	$_GET['mehr'] = escape($menu->get(2),'integer');
  
	$erg = @db_query("SELECT
	DATE_FORMAT(datime,'%d.%m.%Y') as datum,
	tid, status, owp, opp, wlp,
	DATE_FORMAT(datime,'%H:%i:%s') as zeit,
	oid, wo, prefix_wars.`mod`, mtyp,
	game, txt,mlink,lineupopp,tv,pw, prefix_wars.id,
	name as team
	FROM prefix_wars
	left join prefix_groups ON prefix_wars.tid = prefix_groups.id
	WHERE prefix_wars.id = ".$_GET['mehr']);
	
  db_check_erg ($erg);
  
  $row = db_fetch_assoc($erg);
  
  ## Gegnerdaten ##
  $row['page'] = get_opponent_details('homepage', $row['oid']);
  $row['gegner'] = get_opponent_details('name', $row['oid']);
  $row['logo'] = get_opponent_details('logo', $row['oid']);
  $row['land'] = get_opponent_details('land', $row['oid']);
  
  ## Gegnerlogo ##
  if(empty($row['logo'])) {
	$row['logo'] = 'include/images/clanlogos/na.gif';
  }
  ## Teamlogo ##
  $row['teamlogo'] = get_team_details('logo', $row['tid']);
  
  if(empty($row['logo'])) {
	$row['teamlogo'] = 'include/images/clanlogos/na.gif';
  }
	
  $row['txt'] = bbcode($row['txt']);
	
	if ( $row['status'] == 2 ) {
	# nextwars
		$title = $allgAr['title'].' :: Wars :: Nextwars';
		$hmenu = '<a href="?wars" class="smalfont">Wars</a><b> &raquo; </b>Nextwars';
		$design = new design ( $title , $hmenu );
		$design->header();
		$tpl = new tpl ('wars_next');
		$row['tag'] = ( empty($row['tag']) ? $row['gegner'] : $row['tag'] );
		$row['owp'] = "xx";
		$row['opp'] = "xx";
		
		if(has_right($allgAr['wars_matchlink_recht'])) {
			$row['mlink'] = '<a href="'.$row['mlink'].'" style="font-size: 10px;" target="_blank" />Matchlink</a>';
		} else {	
			$row['mlink'] = "";
		}
		if(has_right($allgAr['wars_server_recht'])) {
			$row['wo'] = $row['wo'];
		} else {	
			$row['wo'] = "<i>Keine Berechtigung</i>";
		}
		if(has_right($allgAr['wars_password_recht'])) {
			$row['pw'] = $row['pw'];
		} else {	
			$row['pw'] = "******";
		}
		if(has_right($allgAr['wars_tv_recht'])) {
			$row['tv'] = $row['tv'];
		} else {	
			$row['tv'] = "<i>Keine Berechtigung</i>";
		}
		
		$tpl->set_ar_out($row,0);

		if ( $_SESSION['authright'] <= -2 ) {
			# get benoetige member
			$bm = substr($row['mod'],0,3); $needed = '';
			for($i=0;$i<strlen($bm);$i++) {
				if ( is_numeric($bm{$i}) ) {
					$needed .= $bm{$i};
				}
			}

      $uid = $_SESSION['authid'];
      if ($menu->get(3) == 'delete') { $uid = $menu->get(4); }
      $ck = db_count_query("SELECT COUNT(wid) FROM prefix_warmember WHERE wid = ".$_GET['mehr']." AND uid = ".$uid);

			# eine zu bzw. absage loeschen
			if ( $menu->get(3) == 'delete' AND ((has_right(array($row['tid'])) === true AND $uid == $_SESSION['authid']) OR is_siteadmin('wars')) AND $ck == 1) {
				db_query("DELETE FROM prefix_warmember WHERE wid = ".$_GET['mehr']." AND uid = ".$uid );
				$ck = 0;
			}

			$available = db_count_query("SELECT COUNT(uid) FROM prefix_warmember WHERE wid = ".$_GET['mehr']." AND aktion = 1");
			$aout1 = array (
					'needed' => $needed,
					'available' => $available,
					'id' => $_GET['mehr']
				);
			$tpl->set_ar_out($aout1,1);
			if ( $ck == 0 AND has_right(array($row['tid'])) === true) {
				if ( isset ($_POST['sub']) ) {
					$aktion = ( $_POST['sub'] == 'zusagen' ? 1 : 0 );
					$kom = escape($_POST['kom'],'string');
					db_query("INSERT INTO prefix_warmember (uid,wid,aktion,kom) VALUES (".$_SESSION['authid'].",".$_GET['mehr'].",".$aktion.",'".$kom."')");
				} else {
					$tpl->out(2);
				}
			}
			$class = '';
			$aktionar = array ('<font style="color:#FF0000; background:#666666; font-weight:bold;">abgesagt</font>','<font style="font-weight:bold; color:#00FF00; background:#666666;">zugesagt</font>');
			$erg1 = db_query("SELECT b.id as uid, b.name, a.aktion, a.kom FROM prefix_warmember a left join prefix_user b ON b.id = a.uid WHERE a.wid = ".$_GET['mehr']);
			while ($row1 = db_fetch_assoc($erg1) ) {
				if ( $class == 'Cmite' ) { $class = 'Cnorm'; } else { $class = 'Cmite'; }
				$row1['class'] = $class;
				$row1['aktion'] = $aktionar[$row1['aktion']];
				if ( $row1['uid'] == $_SESSION['authid'] OR is_siteadmin('wars')) {
					$row1['name'] = '<a href="index.php?wars-more-'.$_GET['mehr'].'-delete-'.$row1['uid'].'"><img src="include/images/icons/del.gif" border="0" title="l&ouml;schen" /></a> &nbsp; '.$row1['name'];
				}
				$tpl->set_ar_out($row1,3);
			}
		}
  	$tpl->out(4);
    
	} elseif ($row['status'] == 3) {
		# lastwars
		$row['memberliste'] = lastwars_get_memberlist($_GET['mehr']);
		$wlpar = array(1=>'gewonnen',2=>'verloren',3=>'unentschieden');
		$row['erg'] = $row['owp'].' zu '.$row['opp'];
		$row['ergliste'] = get_erg_liste($_GET['mehr']);
		$row['screenliste'] = get_screen_liste($_GET['mehr']);
		
		if($row['screenliste'] == "<div></div>") {
			$row['screenliste'] = "- Keine Screenshots vorhanden -";
		}
		if(empty($row['lineupopp'])) {
			$row['lineupopp'] = "- Kein Lineup eingetragen -";
		}
		if(empty($row['txt'])) {
			$row['txt'] = '<div style="text-align:center;">- Kein Bericht vorhanden -</div>';
		}
		$row['wlp'] = $wlpar[$row['wlp']];
		$title = $allgAr['title'].' :: Wars :: Lastwars';
		$hmenu = '<a href="?wars" class="smalfont">Wars</a><b> &raquo; </b>Lastwars';
		$design = new design ( $title , $hmenu );
		$design->header();
		$tpl = new tpl ('wars_last');
		$row['tag'] = ( empty($row['tag']) ? $row['gegner'] : $row['tag'] );
		if($row['owp'] > $row['opp']) {
			$row['owp'] = '<span style="color: green;">'.$row['owp'].'</span>';
			$row['opp'] = '<span style="color: red;">'.$row['opp'].'</span>';
		} elseif($row['owp'] < $row['opp']) {
			$row['owp'] = '<span style="color: red;">'.$row['owp'].'</span>';
			$row['opp'] = '<span style="color: green;">'.$row['opp'].'</span>';
		} else {
			$row['owp'] = '<span style="color: yellow;">'.$row['owp'].'</span>';
			$row['opp'] = '<span style="color: yellow;">'.$row['opp'].'</span>';
		}
		if(has_right($allgAr['wars_matchlink_recht'])) {
			$row['mlink'] = '<a href="'.$row['mlink'].'" style="font-size: 10px;" target="_blank" />Matchlink</a>';
		} else {	
			$row['mlink'] = "";
		}
		if(has_right($allgAr['wars_server_recht'])) {
			$row['wo'] = $row['wo'];
		} else {	
			$row['wo'] = "<i>Keine Berechtigung</i>";
		}
		if(has_right($allgAr['wars_password_recht'])) {
			$row['pw'] = $row['pw'];
		} else {	
			$row['pw'] = "******";
		}
		if(has_right($allgAr['wars_tv_recht'])) {
			$row['tv'] = $row['tv'];
		} else {	
			$row['tv'] = "<i>Keine Berechtigung</i>";
		}
		
		$tpl->set_ar_out($row,0);
		# kommentare fuer lastwars
		if ($allgAr['wars_last_komms'] < 0 AND has_right ($allgAr['wars_last_komms'])) {
			# aktion
			if (isset ($_POST['kommentar_fuer_last_wars'])) {
				$name = $_SESSION['authname'];
				$text = escape($_POST['text'],'textarea');
				db_query("INSERT INTO prefix_koms (name,cat,text,uid) VALUES ('".$name."','WARSLAST', '".$text."', ".$_GET['mehr']." )");
			}
			if (isset ($_GET['kommentar_fuer_last_wars_loeschen']) AND is_siteadmin('wars')) {
				db_query("DELETE FROM prefix_koms WHERE cat = 'WARSLAST' AND uid = ".$_GET['mehr']." AND id = ".$_GET['kommentar_fuer_last_wars_loeschen']);
			}
			# anzeigen
			$tpl->out(1);
			$class = '';
			$erg = db_query("SELECT name,text,id FROM prefix_koms WHERE cat = 'WARSLAST' AND uid = ".$_GET['mehr']." ORDER BY id DESC");
			while($r = db_fetch_assoc($erg)) {
				$class = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
				$r['text']  = bbcode($r['text']);
				if (is_siteadmin('wars')) { $r['text'] .= '<a href="index.php?wars-more-'.$_GET['mehr'].'=0&amp;kommentar_fuer_last_wars_loeschen='.$r['id'].'"><img src="include/images/icons/del.gif" title="l&ouml;schen" alt="l&ouml;schen" border="0"></a>'; }
				$r['class'] = $class;
				$tpl->set_ar_out($r,2);
			}
			$tpl->out(3);
		}
	}
  $design->footer();
}
?>