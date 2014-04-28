<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$um = $menu->get(1);

# get Flag list
# 1 akt flag
function get_wlp_array () {
	$ar = array (
		1 => 'gewonnen',
		2 => 'verloren',
		3 => 'unentschieden'
	);
return ($ar);
}

function get_teamlist ($tid) {
			$abf = "SELECT * FROM prefix_groups";
			$erg = db_query($abf);
			$ar = array();
			while($row = db_fetch_assoc($erg)) {
			if($row['id'] == $tid) {
				$selected = "selected";
			}
				$ar[$row[id]] = $row['name'];
			}

 return $ar;
}

function get_opponents ($oid) {
			$abf = "SELECT * FROM prefix_opponents";
			$erg = db_query($abf);
			$ar = array();
			while($row = db_fetch_assoc($erg)) {
			if($row['oid'] == $oid) {
				$selected = "selected";
			}
				$ar[$row[oid]] = ''.$row['name'].' ('.$row['tag'].')';
			}

 return $ar;
}

function get_datime() {
	$own = true;
	$_POST['day'] = escape($_POST['day'],'integer');
	$_POST['mon'] = escape($_POST['mon'],'integer');
	$_POST['jahr'] = escape($_POST['jahr'],'integer');
	$_POST['stu'] = escape($_POST['stu'],'integer');
	$_POST['min'] = escape($_POST['min'],'integer');
	$_POST['sek'] = escape($_POST['sek'],'integer');
	if ( checkdate ($_POST['mon'], $_POST['day'] , $_POST['jahr']) == FALSE ) {
		$own = false;
	} elseif ( $_POST['stu'] > 24 OR $_POST['min'] > 60 OR $_POST['sek'] > 60 ) {
		$own = false;
	}
	if ( $own ) {
		return ( $_POST['jahr'].'-'.$_POST['mon'].'-'.$_POST['day'].' '.$_POST['stu'].':'.$_POST['min'].':'.$_POST['sek'] );
	} else {
		return ( date('Y-m-d H:i:s' ) );
	}
}

switch ( $um ) {

  default :
  	$design = new design ( 'Admins Area', 'Admins Area', 2 );
		$design->header();
    ?>
    Folgende Auswahlm&ouml;glichkeiten:
    <ul>
    <li><a href="admin.php?wars-last">Lastwars</a></li>
    <li><a href="admin.php?wars-next">Nextwars</a></li>
    </ul>
    <?php
    $design->footer();
    break;
	# last wars
	case 'last' :
		# image upload
		if ( $menu->get(2) == 'upload' ) {
			$tpl = new tpl ( 'wars/upload', 1 );
			$msg = '';
			# aktion
			if ( isset ($_FILES['f']['name']) ) {
				$tmp = explode('.',$_FILES['f']['name']);
				if ( $tmp[1] == 'gif' OR $tmp[1] == 'png' OR $tmp[1] == 'jpg' OR $tmp[1] == 'jpeg')  {
					$nname = $_POST['wid'].'_'.$_POST['mid'].'.'.$tmp[1];
					if ( move_uploaded_file ( $_FILES['f']['tmp_name'], 'include/images/wars/'.$nname) ) {
					  @chmod('include/images/wars/'.$nname, 0777);
						$ar = array ( 'gif'=>'gif','png'=>'png','jpg'=>'jpg','jpeg'=>'jpeg' );
						unset($ar[$tmp[1]]);
						foreach($ar as $v) {
							@unlink ( 'include/images/wars/'.$_POST['wid'].'_'.$_POST['mid'].'.'.$v );
						}
						$msg = 'Datei ('.$_FILES['f']['name'].' ) <font color="#00FF00">erfolgreich hochgeladen</font><br />';
					} else {
						$msg = 'Datei ( '.$_FILES['f']['name'].' ) <font color="#FF0000">nicht erfolgreich hochgeladen</font><br />';
					}
				} else {
					$msg = 'Bitte nur Bilder mit der Endung: .gif, .png, .jpg oder .jpeg!';
				}
			}
			if ( isset($_GET['d']) ) {
				if ( @unlink ( $_GET['d'] ) ) {
					$msg = 'Datei <font color="#00FF00">erfolgreich gel&ouml;scht</font><br />';
				} else {
					$msg = 'Datei <font color="#FF0000">konnte nicht gel&ouml;scht werden</font><br />';
				}
			}
			# anzeigen
			if ( !is_writeable ( 'include/images/wars' ) ) {
				$msg = 'Bitte erst dem Ordner "images/wars" Schreibrechte (chmod 777) geben.';
			}
			$mid = $_REQUEST['mid'];
			$wid = $_REQUEST['wid'];
			$file = 'Noch kein Bild hochgeladen... ';
			$ar = array ( 'gif','png','jpg','jpeg' );
			foreach($ar as $v) {
				if ( file_exists ( 'include/images/wars/'.$wid.'_'.$mid.'.'.$v ) ) {
					$size=getimagesize('include/images/wars/'.$wid.'_'.$mid.'.'.$v);
					$breite=$size[0];
					$hoehe=$size[1];
					$file  = '<a href="javascript:openImgWindow(\''.$v.'\','.$hoehe.','.$breite.')">include/images/wars/'.$wid.'_'.$mid.'.'.$v.'</a>';
					$file .= '&nbsp; &nbsp; <a href="javascript:deleteMap(\''.$v.'\')"><img src="include/images/icons/del.gif" border="0" title="l&ouml;schen" /></a>';
					break;
				}
			}
		$tpl->set('wid' ,$wid);
		$tpl->set('mid' ,$mid);
		$tpl->set('file', $file );
		$tpl->set('msg' ,$msg);
		$tpl->out(0);
		exit ();
		}
		# manag member for war...
		if ( $menu->get(2) == 'members' ) {
			$tpl = new tpl ( 'wars/last_member', 1 );
			$msg = '';
			# aktion
			if (isset($_POST['add_uid']) AND !empty($_POST['add_uid'])) {
				db_query("INSERT INTO prefix_warmember (wid,uid,aktion) VALUES (".$_REQUEST['wid'].",".$_POST['add_uid'].",1)");
			}
			if (isset($_GET['delete_uid']) AND !empty($_GET['delete_uid'])) {
				db_query("DELETE FROM prefix_warmember WHERE wid = ".$_REQUEST['wid']." AND uid = ".$_GET['delete_uid']);
			}
			# anzeigen
			$tpl->set('msg',$msg);
			$tpl->set('wid',$_REQUEST['wid']);
			$tpl->set('liste', dblistee ( 0, "SELECT prefix_user.id,name FROM prefix_user LEFT JOIN prefix_warmember ON prefix_warmember.uid = prefix_user.id AND prefix_warmember.wid = ".$_REQUEST['wid']." WHERE prefix_warmember.aktion is NULL AND recht <= -2 ORDER BY `name`" ) );
			$tpl->out(0);
			$class = '';
			$erg = db_query("SELECT prefix_user.id, prefix_user.name FROM prefix_warmember LEFT JOIN prefix_user ON prefix_user.id = prefix_warmember.uid WHERE wid = ".$_REQUEST['wid']." ORDER BY prefix_user.name ASC");
			while($r = db_fetch_assoc($erg)) {
				$class = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
				$r['class'] = $class;
				$tpl->set_ar_out($r,1);
			}
			$tpl->out(2);
			exit();
		}
		# last wars
		$design = new design ( 'Admins Area', 'Admins Area', 2 );
		$design->header();
		$show = true;
		$tpl = new tpl ( 'wars/last', 1);


		if ( !empty ( $_GET['delete'] ) ) {
			# aus kalender loeschen fals vorhanden
			$_GET['delete'] = escape($_GET['delete'],'integer');
      db_query("DELETE FROM prefix_kalender WHERE text like '%more-".$_GET['delete']."]%'");
			db_query("DELETE FROM prefix_wars WHERE id = '".$_GET['delete']."'");
			$wid = $_GET['delete'];
			$imgar = array ('gif','png','jpg','jpeg');
      for($i=1;$i<=5;$i++) {
				db_query("DELETE FROM prefix_warmaps WHERE wid = ".$wid." AND mnr = ".$i);
        foreach ($imgar as $v) {
          if ( file_exists('include/images/wars/'.$wid.'_'.$i.'.'.$v) ) {
					  unlink ('include/images/wars/'.$wid.'_'.$i.'.'.$v);
				  }
        }
			}
			$msg = '<tr class="Cmite"><td colspan="2">Erfolgreich gel&ouml;scht</td></tr>';
		}


		#TOPMATCH
		if( !empty ( $_GET['topmatch'] ) ) {
			$_GET['topmatch'] = escape($_GET['topmatch'],'integer');
      $row = db_fetch_assoc(db_query("SELECT topmatch FROM prefix_wars WHERE id = $_GET[topmatch]"));

      if($row['topmatch'] == 1) {
        db_query("UPDATE prefix_wars Set topmatch = 0 WHERE id = ".$_GET['topmatch']."");
        $msg = '<tr class="Cmite"><td colspan="2">Topmatch entfernt.</td></tr>';
      } else {
        db_query("UPDATE prefix_wars Set topmatch = 0 WHERE topmatch = 1");
        db_query("UPDATE prefix_wars Set topmatch = 1 WHERE id = ".$_GET['topmatch']."");
        $msg = '<tr class="Cmite"><td colspan="2">Topmatch gesetzt.</td></tr>';
      }
    }


		if ( !empty($_POST['sub']) ) {
			if ( !empty($_POST['newmod']) ) {
				$_POST['mod'] = $_POST['newmod'];
			}
			if ( !empty($_POST['newgame']) ) {
				$_POST['game'] = $_POST['newgame'];
			}
			if ( !empty($_POST['newmtyp']) ) {
				$_POST['mtyp'] = $_POST['newmtyp'];
			}
			if ( empty($_POST['tid']) ) {
				$_POST['tid'] = 0;
			}

			$_POST['oid'] = escape($_POST['oid'], 'integer');
			$_POST['tid'] = escape($_POST['tid'], 'integer');
			$_POST['mod'] = escape($_POST['mod'], 'string');
			$_POST['game'] = escape($_POST['game'], 'string');
			$_POST['mtyp'] = escape($_POST['mtyp'], 'string');
			$_POST['txt'] = escape($_POST['txt'], 'string');
			$_POST['wo'] = escape($_POST['wo'], 'string');
			$_POST['lineupopp'] = escape($_POST['lineupopp'], 'string');
			$_POST['lineupowp'] = escape($_POST['lineupowp'], 'string');
			$_POST['pw'] = escape($_POST['pw'], 'string');
			$_POST['oppstate'] = escape($_POST['oppstate'], 'string');
			$_POST['owpstate'] = escape($_POST['owpstate'], 'string');
			$_POST['maps'] = escape($_POST['maps'], 'string');
			$_POST['tv'] = escape($_POST['tv'], 'string');

			if ( empty ($_POST['pkey']) ) {
				db_query("INSERT INTO prefix_wars (`datime`,`status`,wlp,`owp`,`opp`,oid,wo,tid,`mod`,game,mtyp,txt,mlink,lineupopp,lineupowp,oppstate,owpstate,maps,tv,pw) VALUES ('".get_datime()."',3,'".$_POST['wlp']."','".$_POST['sumowp']."','".$_POST['sumopp']."','".$_POST['oid']."','".$_POST['wo']."','".$_POST['tid']."','".$_POST['mod']."','".$_POST['game']."','".$_POST['mtyp']."','".$_POST['txt']."','".$_POST['mlink']."','".$_POST['lineupopp']."','".$_POST['lineupowp']."','".$_POST['oppstate']."','".$_POST['owpstate']."','".$_POST['maps']."','".$_POST['tv']."','".$_POST['pw']."')");
				$wid = db_last_id();
				for($i=1;$i<=5;$i++) {
					if ( $_POST['map'][$i] != '' AND $_POST['opp'][$i] != '' AND $_POST['owp'][$i] != '' ) {
						db_query("INSERT INTO prefix_warmaps (wid,mnr,map,opp,owp) VALUES (".$wid.",".$i.",'".$_POST['map'][$i]."',".$_POST['opp'][$i].",".$_POST['owp'][$i].")");
					}
				}

        # in den kalender eintragen wenn gewuenscht
        if (isset($_POST['kalender']) AND $_POST['kalender'] == 'yes') {
          $timestamp = strtotime(get_datime());
          $page = str_replace('admin.php','index.php',$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]);
          db_query("INSERT INTO prefix_kalender (time, title, text, recht) VALUES (".$timestamp.",'Lastwar gegen ".get_opponent_details('name', $_POST['oid'])."', '".$_POST['mtyp']." ".$_POST['mod']." in ".$_POST['game']." gegen [url=".$_POST['page']."]".$_POST['gegner']."[/url]\n\n[url=http://".$page."?wars-more-".$wid."]details des Wars[/url]', 0)");
        }
				$msg = '<tr class="Cmite"><td colspan="2">Erfolgreich eingetragen</td></tr>';
			} else {
				db_query("UPDATE prefix_wars SET datime = '".get_datime()."', status = 3,wlp = '".$_POST['wlp']."',owp = '".$_POST['sumowp']."',opp = '".$_POST['sumopp']."',oid = '".$_POST['oid']."',wo = '".$_POST['wo']."',tid = '".$_POST['tid']."',`mod` = '".$_POST['mod']."',game = '".$_POST['game']."',mtyp = '".$_POST['mtyp']."',txt = '".$_POST['txt']."',mlink = '".$_POST['mlink']."', lineupopp = '".$_POST['lineupopp']."', lineupowp = '".$_POST['lineupowp']."', oppstate = '".$_POST['oppstate']."', owpstate = '".$_POST['owpstate']."', maps = '".$_POST['maps']."', tv = '".$_POST['tv']."', pw = '".$_POST['pw']."' WHERE id = '".$_POST['pkey']."'");
				$wid = $_POST['pkey'];
				for($i=1;$i<=7;$i++) {
					$a = db_count_query("SELECT COUNT(*) FROM prefix_warmaps WHERE mnr = ".$i." AND wid = ".$wid);
					if ( $a == 0 AND $_POST['map'][$i] != '' AND $_POST['opp'][$i] != '' AND $_POST['owp'][$i] != '' ) {
            db_query("INSERT INTO prefix_warmaps (wid,mnr,map,opp,owp) VALUES (".$wid.",".$i.",'".$_POST['map'][$i]."',".$_POST['opp'][$i].",".$_POST['owp'][$i].")");
					} elseif ( $a == 1 AND ( $_POST['map'][$i] == '' OR $_POST['opp'][$i] == '' AND $_POST['owp'][$i] == '') ) {
						db_query("DELETE FROM prefix_warmaps WHERE wid = ".$wid." AND mnr = ".$i);
						if ( file_exists('include/images/wars/'.$wid.'_'.$i.'.gif') ) { unlink ('include/images/wars/'.$wid.'_'.$i.'.gif'); }
						if ( file_exists('include/images/wars/'.$wid.'_'.$i.'.png') ) { unlink ('include/images/wars/'.$wid.'_'.$i.'.png'); }
						if ( file_exists('include/images/wars/'.$wid.'_'.$i.'.jpg') ) { unlink ('include/images/wars/'.$wid.'_'.$i.'.jpg'); }
						if ( file_exists('include/images/wars/'.$wid.'_'.$i.'.jpeg') ) { unlink ('include/images/wars/'.$wid.'_'.$i.'.jpeg'); }
					} elseif ( $a == 1 AND $_POST['map'][$i] != '' AND $_POST['opp'][$i] != '' AND $_POST['owp'][$i] != '' ) {
						db_query("UPDATE prefix_warmaps SET map = '".$_POST['map'][$i]."', opp = ".$_POST['opp'][$i].", owp = ".$_POST['owp'][$i]." WHERE wid = ".$wid." AND mnr = ".$i);
					}
				}
		if($_POST['news'] == 0) {

			$gegner = get_opponent_details("name", $_POST['oid']);
			$glogo = get_opponent_details("logo", $_POST['oid']);
			$team = get_team_details ('name', $_POST['tid']);
			$_POST['day'] = escape($_POST['day'],'integer');
			$_POST['mon'] = escape($_POST['mon'],'integer');
			$_POST['jahr'] = escape($_POST['jahr'],'integer');
			$_POST['stu'] = escape($_POST['stu'],'integer');
			$_POST['min'] = escape($_POST['min'],'integer');
			$_POST['sek'] = escape($_POST['sek'],'integer');

			$warstamp = mktime($_POST['stu'],$_POST['min'],$_POST['sek'],$_POST['mon'],$_POST['day'],$_POST['jahr']);
			$wardate = date("d.m.Y", $warstamp);
			$wardate .= ' um ';
			$wardate .= date("H:i", $warstamp);
			$wardate .= ' Uhr';

			if(empty($_POST['oppstate'])) {
				$_POST['oppstate'] == 'Kein Statement abgegeben';
			}
			if(empty($_POST['owpstate'])) {
				$_POST['owpstate'] == 'Kein Statement abgegeben';
			}
			if(empty($_POST['lineupopp'])) {
				$_POST['lineupopp'] == 'tba';
			}
			if(empty($_POST['lineupowp'])) {
				$_POST['lineupowp'] == 'tba';
			}
			$text = '';
			$text .= '[size=15][b][center]'.$team.'   vs.   '.$gegner.'      [/center]  [/b]     [/size]';
			$text .= '\n \n';
			$text .= '[center][img]include/images/teams/'.$_POST['tid'].'.jpg[/img]           [size=14][b]VS[/b][/size]           [img]'.$glogo.'[/img][/center]
[center][url='.$_POST['mlink'].']Matchlink[/url][/center]
[center][color=#00FF00][size=18]'.$_POST['sumowp'].'[/size][/color] - [color=#FF0000][size=18]'.$_POST['sumopp'].'[/size][/color][/center]';
			$text .= '\n \n';
			$text .='[b]Datum[/b] :   '.$wardate;
			$text .= '\n';
			$text .='[b]Map[/b] :   '.$_POST['maps'];
			$text .= '\n \n';
			$text .= '[ktext=Statement und Lineup '.$gegner.'][u]Lineup[/u]: '.$_POST['lineupopp'].' \n \n [u]Statement[/u]: '.$_POST['oppstate'].'[/ktext]';
			$text .= '\n \n';
			$text .= '[ktext=Statement und Lineup '.$team.'][u]Lineup[/u]: '.$_POST['lineupowp'].' \n \n [u]Statement[/u]: '.$_POST['owpstate'].'[/ktext]';
			$text .= '\n \n';


			$date = date("Y-m-d H-i-s", time());

				$getid = "SELECT * FROM prefix_wars ORDER BY id DESC LIMIT 1";
				$getid2= db_query($getid);
				$r=db_fetch_assoc($getid2);



			if(!empty($_POST['pkey'])) {
			$exists = db_result(db_query("SELECT COUNT(news_id) FROM prefix_news WHERE war = $_POST[pkey]"),0);
				if($exists == 1) {
					$write = "UPDATE prefix_news Set news_title = '$team vs. $gegner', news_text = '$text' WHERE war = $_POST[pkey]";
					$wirteerg = db_query($write);
				} else {
					$write = "INSERT INTO prefix_news (news_title,user_id,news_time,news_recht,news_kat,news_text,war) VALUES ('".$team." vs. ".$gegner."', 0, '".$date."', 0, 'Wars', '".$text."', '".$_POST['pkey']."')";
					$writeerg = db_query($write);
				}
			} else {
				$write = "INSERT INTO prefix_news (news_title,user_id,news_time,news_recht,news_kat,news_text,war) VALUES ('".$team." vs. ".$gegner."', 0, '".$date."', 0, 'Wars', '".$text."', '".$r['id']."')";
				$writeerg = db_query($write);
			}
		}
        # in den kalender eintragen wenn gewuenscht
        if (isset($_POST['kalender']) AND $_POST['kalender'] == 'yes') {
          $timestamp = strtotime(get_datime());
          $page = str_replace('admin.php','index.php',$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]);
          if (1 == db_result(db_query("SELECT COUNT(*) FROM prefix_kalender WHERE text like '%more-".$wid."]%'"),0)) {
            db_query("UPDATE prefix_kalender SET time = ".$timestamp.", title = 'Lastwar gegen ".get_opponent_details('name',$_POST['oid'])."', text = '".$_POST['mtyp']." ".$_POST['mod']." in ".$_POST['game']." gegen [url=".$_POST['page']."]".$_POST['gegner']."[/url]\n\n[url=http://".$page."?wars-more-".$wid."]details des Wars[/url]' WHERE text like '%more-".$wid."]%'");
          } else {
            db_query("INSERT INTO prefix_kalender (time, title, text, recht) VALUES (".$timestamp.",'Lastwar gegen ".get_opponent_details('name',$_POST['oid'])."', '".$_POST['mtyp']." ".$_POST['mod']." in ".$_POST['game']." gegen [url=".$_POST['page']."]".$_POST['gegner']."[/url]\n\n[url=http://".$page."?wars-more-".$wid."]details des Wars[/url]', 0)");
          }
        }
				$msg = '<tr class="Cmite"><td colspan="2">Erfolgreich ver&auml;ndert</td></tr>';
			}
		}
		if ( !empty ($_GET['pkey']) ) {
			$erg = db_query("SELECT DATE_FORMAT(datime,'%d.%m.%Y.%H.%i.%s') as datime, id,status,wlp,owp,opp,oid,tid,wo,`mod`,game,mtyp,txt,mlink,lineupopp,lineupowp,oppstate,owpstate,maps,tv,pw FROM prefix_wars WHERE id = '".$_GET['pkey']."'");

			$_ilch = db_fetch_assoc($erg);

			if(empty($_ilch['logo'])) {
				$_ilch['logo'] = "include/images/logos/";
			}
			$_ilch['pkey'] = $_GET['pkey'];
			list($_ilch['day'],$_ilch['mon'],$_ilch['jahr'],$_ilch['stu'],$_ilch['min'],$_ilch['sek']) = explode('.',$_ilch['datime']);
      $_ilch['kalck'] = (db_result(db_query("SELECT COUNT(*) FROM prefix_kalender WHERE text like '%more-".$_GET['pkey']."]%'"),0,0) == 1 ? ' checked' : '');
			$wid = $_GET['pkey'];
			for($i=1;$i<=7;$i++) {
				$erg =  db_query("SELECT map,opp,owp FROM prefix_warmaps WHERE mnr = ".$i." AND wid = ".$wid);
				if ( db_num_rows($erg) == 0 ) {
					$_ilch['map'.$i] = '';
					$_ilch['opp'.$i] = '';
					$_ilch['owp'.$i] = '';
				} else {
					$mpr = db_fetch_assoc($erg);
					$_ilch['map'.$i] = $mpr['map'];
					$_ilch['opp'.$i] = $mpr['opp'];
					$_ilch['owp'.$i] = $mpr['owp'];
				}
			}
		} else {
			$_ilch = array ('wo'=>'','pkey' => 0, 'wlp' => '', 'opp' => '', 'owp' => '', 'oid' => '', 'mtyp' => '', 'tid' => 0, 'txt' => '', 'mod' => '', 'game' => '', 'day' => date('d'), 'mon' => date('m'), 'jahr' => date('Y'), 'stu' => date('H'), 'min' => date('i'), 'sek' => date('s'), 'kalck' => '', 'mlink' => 'http://', 'lineupopp' => '', 'lineupowp' => '', 'oppstate' => '', 'owpstate' => '', 'maps' => '', 'tv' => '', 'pw' => '' );
			for($i=1;$i<=7;$i++) {
				$_ilch['map'.$i] = '';
				$_ilch['opp'.$i] = '';
				$_ilch['owp'.$i] = '';
			}
		}
		$_ilch['msg'] = ( isset ($msg) ? $msg : '' );
		$_ilch['oid'] = arlistee ( $_ilch['oid'] , get_opponents($_ilch['oid']) );
		$_ilch['tid'] = dblistee ( $_ilch['tid'], "SELECT id, name FROM prefix_groups ORDER BY name");
		$_ilch['mod'] = dblistee ( $_ilch['mod'], "SELECT DISTINCT `mod`,`mod` FROM prefix_wars ORDER BY `mod`" );
		$_ilch['game'] = dblistee ( $_ilch['game'], "SELECT DISTINCT `game`,`game` FROM prefix_wars ORDER BY `game`" );
		$_ilch['mtyp'] = dblistee ( $_ilch['mtyp'], "SELECT DISTINCT `mtyp`,`mtyp` FROM prefix_wars ORDER BY `mtyp`" );
		$_ilch['land'] = arlistee ( $_ilch['land'] , get_nationality_array() );
		$_ilch['wlp'] = arlistee ( $_ilch['wlp'] , get_wlp_array() );
		$tpl->set_ar_out($_ilch,0);
    $page = ( $menu->getA(2) == 'p' ? $menu->getE(2) : 1 );
		$limit = 20; $class = '';
		$MPL = db_make_sites ($page , 'WHERE status = 3' , $limit , '?wars-last' , 'wars' );
		$anfang = ($page - 1) * $limit;
		$abf = "SELECT id,oid,game,owp,opp,tid,topmatch FROM prefix_wars WHERE status = 3 ORDER BY id DESC LIMIT ".$anfang.",".$limit;
		$erg = db_query($abf);
		while ($row = db_fetch_assoc($erg) ) {
			$class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
			$row['class'] = $class;
      $row['game'] = get_wargameimg($row['game']);
	  $row['gegner'] = get_opponent_details('name', $row['oid']);
	  $row['land2'] = get_opponent_details('land', $row['oid']);
	  if($row['topmatch'] == 1) {
      $row['topmatchstatus'] = '<img src="include/images/icons/jep.gif" alt="JA" />';
    } else {
      $row['topmatchstatus'] = '<img src="include/images/icons/nop.gif" alt="NEIN" />';
    }
	  if($row['opp'] > $row['owp']) {
		$color = "red";
	  } elseif($row['opp'] < $row['owp'])
	  {
		$color = "green";
	  } else {
		$color = "orange";
	}
	  $row['erg'] = '<span style="color: '.$color.'">'.$row['owp'].':'.$row['opp'].'</span>';
	  $row['team'] = get_team_details('name', $row['tid']);
			$tpl->set_ar ( $row );
			$tpl->out(1);
		}
		$tpl->set ( 'MPL', $MPL );
		$tpl->out(2);
		$design->footer();
	break;

	# Next wars
	case 'next' :

		$design = new design ( 'Admins Area', 'Admins Area', 2 );
		$design->header();
		$show = true;
		$tpl = new tpl ( 'wars/next', 1);
		if ( !empty ( $_GET['delete'] ) ) {
		$_GET['delete'] = escape($_GET['delete'],'integer');
			# aus kalender loeschen fals vorhanden
      db_query("DELETE FROM prefix_kalender WHERE text like '%more-".$_GET['delete']."]%'");
      db_query("DELETE FROM prefix_wars WHERE id = '".$_GET['delete']."'");
			$msg = '<tr class="Cmite"><td colspan="2">Erfolgreich gel&ouml;scht</td></tr>';
		}

		#TOPMATCH
		if( !empty ( $_GET['topmatch'] ) ) {
			$_GET['topmatch'] = escape($_GET['topmatch'],'integer');
      $row = db_fetch_assoc(db_query("SELECT topmatch FROM prefix_wars WHERE id = $_GET[topmatch]"));

      if($row['topmatch'] == 1) {
        db_query("UPDATE prefix_wars Set topmatch = 0 WHERE id = ".$_GET['topmatch']."");
        $msg = '<tr class="Cmite"><td colspan="2">Topmatch entfernt.</td></tr>';
      } else {
        db_query("UPDATE prefix_wars Set topmatch = 0 WHERE topmatch = 1");
        db_query("UPDATE prefix_wars Set topmatch = 1 WHERE id = ".$_GET['topmatch']."");
        $msg = '<tr class="Cmite"><td colspan="2">Topmatch gesetzt.</td></tr>';
      }
    }


		if ( !empty($_POST['sub']) ) {
			if ( !empty($_POST['newmod']) ) {
				$_POST['mod'] = $_POST['newmod'];
			}
			if ( !empty($_POST['newgame']) ) {
				$_POST['game'] = $_POST['newgame'];
			}
			if ( !empty($_POST['newmtyp']) ) {
				$_POST['mtyp'] = $_POST['newmtyp'];
			}
			if ( empty($_POST['tid']) ) {
				$_POST['tid'] = 0;
			}

			$_POST['oid'] = escape($_POST['oid'], 'integer');
			$_POST['tid'] = escape($_POST['tid'], 'integer');
			$_POST['mod'] = escape($_POST['mod'], 'string');
			$_POST['game'] = escape($_POST['game'], 'string');
			$_POST['mtyp'] = escape($_POST['mtyp'], 'string');
			$_POST['txt'] = escape($_POST['txt'], 'string');
			$_POST['mlink'] = get_homepage(escape($_POST['mlink'], 'string'));
			$_POST['wo'] = escape($_POST['wo'], 'string');
			$_POST['lineupopp'] = escape($_POST['lineupopp'], 'string');
			$_POST['lineupowp'] = escape($_POST['lineupowp'], 'string');
			$_POST['oppstate'] = escape($_POST['oppstate'], 'string');
			$_POST['owpstate'] = escape($_POST['owpstate'], 'string');
			$_POST['maps'] = escape($_POST['maps'], 'string');
			$_POST['tv'] = escape($_POST['tv'], 'string');
			$_POST['pw'] = escape($_POST['pw'], 'string');



			if ( empty ($_POST['pkey']) ) {
				db_query("INSERT INTO prefix_wars (datime,`status`,oid,wo,tid,`mod`,game,mtyp,txt,mlink,lineupopp,lineupowp,oppstate,owpstate,maps,tv,pw) VALUES ('".get_datime()."',2,'".$_POST['oid']."','".$_POST['wo']."','".$_POST['tid']."','".$_POST['mod']."','".$_POST['game']."','".$_POST['mtyp']."','".$_POST['txt']."','".$_POST['mlink']."','".$_POST['lineupopp']."','".$_POST['lineupowp']."','".$_POST['oppstate']."','".$_POST['owpstate']."','".$_POST['maps']."','".$_POST['tv']."','".$_POST['pw']."')");
        $wid = db_last_id();
        # in den kalender eintragen wenn gewuenscht
        if (isset($_POST['kalender']) AND $_POST['kalender'] == 'yes') {
          $timestamp = strtotime(get_datime());
          $page = str_replace('admin.php','index.php',$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]);
          db_query("INSERT INTO prefix_kalender (time, title, text, recht) VALUES (".$timestamp.",'Nextwar gegen ".get_opponent_details('name',$_POST['oid'])."', '".$_POST['mtyp']." ".$_POST['mod']." in ".$_POST['game']." gegen [url=".$_POST['page']."]".$_POST['gegner']."[/url]\n\n[url=http://".$page."?wars-more-".$wid."]details des Wars[/url]', 0)");
        }
				$msg = '<tr class="Cmite"><td colspan="2">Erfolgreich eingetragen</td></tr>';
			} else {
				db_query("UPDATE prefix_wars SET datime = '".get_datime()."', status = 2,oid = '".$_POST['oid']."',wo = '".$_POST['wo']."',tid = '".$_POST['tid']."',`mod` = '".$_POST['mod']."',game = '".$_POST['game']."',mtyp = '".$_POST['mtyp']."',txt = '".$_POST['txt']."', mlink = '".$_POST['mlink']."', lineupopp = '".$_POST['lineupopp']."', lineupowp = '".$_POST['lineupowp']."', oppstate = '".$_POST['oppstate']."', owpstate = '".$_POST['owpstate']."', maps = '".$_POST['maps']."', tv = '".$_POST['tv']."', pw = '".$_POST['pw']."' WHERE id = '".$_POST['pkey']."'");
        $wid = $_POST['pkey'];
        # in den kalender eintragen wenn gewuenscht
        if (isset($_POST['kalender']) AND $_POST['kalender'] == 'yes') {
          $timestamp = strtotime(get_datime());
          $page = str_replace('admin.php','index.php',$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]);
          if (1 == db_result(db_query("SELECT COUNT(*) FROM prefix_kalender WHERE text like '%more-".$wid."]%'"),0)) {
            db_query("UPDATE prefix_kalender SET time = ".$timestamp.", title = 'Nextwar gegen ".get_opponent_details('name',$_POST['oid'])."', text = '".$_POST['mtyp']." ".$_POST['mod']." in ".$_POST['game']." gegen [url=".$_POST['page']."]".$_POST['gegner']."[/url]\n\n[url=http://".$page."?wars-more-".$wid."]details des Wars[/url]' WHERE text like '%more-".$wid."]%'");
          } else {
            db_query("INSERT INTO prefix_kalender (time, title, text, recht) VALUES (".$timestamp.",'Nextwar gegen ".$_POST['gegner']."', '".$_POST['mtyp']." ".$_POST['mod']." in ".$_POST['game']." gegen [url=".$_POST['page']."]".$_POST['gegner']."[/url]\n\n[url=http://".$page."?wars-more-".$wid."]details des Wars[/url]', 0)");
          }
        }
        $msg = '<tr class="Cmite"><td colspan="2">Erfolgreich ver&auml;ndert</td></tr>';
			}
		if($_POST['news'] == 0) {

			$gegner = get_opponent_details("name", $_POST['oid']);
			$glogo = get_opponent_details("logo", $_POST['oid']);
			$tlogo = get_team_details('logo', $_POST['tid']);
			$team = get_team_details ('name', $_POST['tid']);
			$_POST['day'] = escape($_POST['day'],'integer');
			$_POST['mon'] = escape($_POST['mon'],'integer');
			$_POST['jahr'] = escape($_POST['jahr'],'integer');
			$_POST['stu'] = escape($_POST['stu'],'integer');
			$_POST['min'] = escape($_POST['min'],'integer');
			$_POST['sek'] = escape($_POST['sek'],'integer');

			$warstamp = mktime($_POST['stu'],$_POST['min'],$_POST['sek'],$_POST['mon'],$_POST['day'],$_POST['jahr']);
			$wardate = date("d.m.Y", $warstamp);
			$wardate .= ' um ';
			$wardate .= date("H:i", $warstamp);
			$wardate .= ' Uhr';

			if(empty($_POST['oppstate'])) {
				$_POST['oppstate'] == 'Kein Statement abgegeben';
			}
			if(empty($_POST['owpstate'])) {
				$_POST['owpstate'] == 'Kein Statement abgegeben';
			}
			if(empty($_POST['lineupopp'])) {
				$_POST['lineupopp'] == 'tba';
			}
			if(empty($_POST['lineupowp'])) {
				$_POST['lineupowp'] == 'tba';
			}

			$text = '';
			$text .= '[size=15][b][center]'.$team.'   vs.   '.$gegner.'      [/center]  [/b]     [/size]';
			$text .= '\n \n';
			$text .= '[center][img]'.$tlogo.'[/img]           [size=14][b]VS[/b][/size]           [img]'.$glogo.'[/img][/center]
[center][url='.$_POST['mlink'].']Matchlink[/url][/center]';
			$text .= '\n \n';
			$text .='[b]Matchbeginn[/b] :   '.$wardate;
			$text .= '\n';
			$text .='[b]Map[/b] :   '.$_POST['maps'];
			$text .= '\n';
			$text .='[b]Server [/b] :   '.$_POST['wo'];
			$text .= '\n';
			$text .='[b]Source TV [/b] :   '.$_POST['tv'];
			$text .= '\n \n';
			$text .= '[ktext=Statement und Lineup '.$gegner.'][u]Lineup[/u]: '.$_POST['lineupopp'].' \n \n [u]Statement[/u]: '.$_POST['oppstate'].'[/ktext]';
			$text .= '\n \n';
			$text .= '[ktext=Statement und Lineup '.$team.'][u]Lineup[/u]: '.$_POST['lineupowp'].' \n \n [u]Statement[/u]: '.$_POST['owpstate'].'[/ktext]';
			$text .= '\n \n';

			$date = date("Y-m-d H-i-s", time());

				$getid = "SELECT * FROM prefix_wars ORDER BY id DESC LIMIT 1";
				$getid2= db_query($getid);
				$r=db_fetch_assoc($getid2);


			if(!empty($_POST['pkey'])) {
			$exists = db_result(db_query("SELECT COUNT(news_id) FROM prefix_news WHERE war = $_POST[pkey]"),0);
				if($exists == 1) {
					$write = "UPDATE prefix_news Set news_title = '$team vs. $gegner', news_text = '$text' WHERE war = $_POST[pkey]";
					$wirteerg = db_query($write);
				} else {
					$write = "INSERT INTO prefix_news (news_title,user_id,news_time,news_recht,news_kat,news_text,war) VALUES ('".$team." vs. ".$gegner."', 0, '".$date."', 0, 'Wars', '".$text."', '".$_POST['pkey']."')";
					$writeerg = db_query($write);
				}
			} else {
				$write = "INSERT INTO prefix_news (news_title,user_id,news_time,news_recht,news_kat,news_text,war) VALUES ('".$team." vs. ".$gegner."', 0, '".$date."', 0, 'Wars', '".$text."', '".$r['id']."')";
				$writeerg = db_query($write);
			}

		}
		}
		if ( !empty ($_GET['pkey']) ) {
			$erg = db_query("SELECT DATE_FORMAT(datime,'%d.%m.%Y.%H.%i.%s') as datime, id,status,oid,wo,tid,`mod`,game,mtyp,txt,mlink,lineupopp,lineupowp,oppstate,owpstate,maps,tv,pw FROM prefix_wars WHERE id = '".$_GET['pkey']."'");
			$_ilch = db_fetch_assoc($erg);
			list($_ilch['day'],$_ilch['mon'],$_ilch['jahr'],$_ilch['stu'],$_ilch['min'],$_ilch['sek']) = explode('.',$_ilch['datime']);
      $_ilch['kalck'] = (db_result(db_query("SELECT COUNT(*) FROM prefix_kalender WHERE text like '%more-".$_GET['pkey']."]%'"),0,0) == 1 ? ' checked' : '');
			$_ilch['pkey'] = $_GET['pkey'];
		} else {
			$_ilch = array ('wo'=>'','pkey' => '', 'wlp' => '', 'erg1' => '', 'erg2' => '', 'oid' => '', 'lineupopp' => '', 'lineupowp' => '', 'oppstate' => '', 'owpstate' => '', 'maps' => '', 'tv' => '', 'mtyp' => '', 'tid' => 0, 'txt' => '','pw' => '', 'mlink' => 'http://', 'mod' => '', 'game' => '', 'day' => date('d'), 'mon' => date('m'), 'jahr' => date('Y'), 'stu' => date('H'), 'min' => date('i'), 'sek' => date('s'), 'kalck' => '' );
		}
		$_ilch['msg'] = ( isset ($msg) ? $msg : '' );
		$_ilch['oid'] = arlistee ( $_ilch['oid'] , get_opponents($_ilch['oid']) );
		$_ilch['tid'] = dblistee ( $_ilch['tid'], "SELECT id, name FROM prefix_groups ORDER BY name");
		$_ilch['mod'] = dblistee ( $_ilch['mod'], "SELECT DISTINCT `mod`,`mod` FROM prefix_wars ORDER BY `mod`" );
		$_ilch['game'] = dblistee ( $_ilch['game'], "SELECT DISTINCT `game`,`game` FROM prefix_wars ORDER BY `game`" );
		$_ilch['mtyp'] = dblistee ( $_ilch['mtyp'], "SELECT DISTINCT `mtyp`,`mtyp` FROM prefix_wars ORDER BY `mtyp`" );
		$_ilch['land'] = arlistee ( $_ilch['land'] , get_nationality_array() );
		$tpl->set_ar_out($_ilch,0);

    $page = ( $menu->getA(2) == 'p' ? $menu->getE(2) : 1 );

    $class = '';
    if ($page == 1) {
    $abf = "SELECT * FROM prefix_wars WHERE status = 1 ORDER BY id DESC";
    $erg = db_query($abf);
    while ($r = db_fetch_assoc($erg)) {
      $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
      $r['class'] = $class;
      $r['game'] = get_wargameimg($r['game']);
	  $r['gegner'] = get_opponent_details('name', $r['oid']);
	  $r['land2'] = get_opponent_details('land', $r['oid']);
		$r['team'] = get_team_details('name', $r['tid']);
		if($r['topmatch'] == 1) {
      $r['topmatchstatus'] = '<img src="include/images/icons/jep.gif" alt="JA" />';
    } else {
      $r['topmatchstatus'] = '<img src="include/images/icons/nop.gif" alt="NEIN" />';
    }

      $tpl->set_ar ($r);
      $tpl->out(1);
    }
    }

		$limit = 20;
		$MPL = db_make_sites ($page , 'WHERE status = 2' , $limit , '?wars-next' , 'wars' );
		$anfang = ($page - 1) * $limit;
		$abf = "SELECT id,oid,game,tid,topmatch FROM prefix_wars WHERE status = 2 ORDER BY id DESC LIMIT ".$anfang.",".$limit;
		$erg = db_query($abf);
		while ($row = db_fetch_assoc($erg) ) {
			$class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
			$row['class'] = $class;
      $row['game'] = get_wargameimg($row['game']);
	  $row['land2'] = get_opponent_details('land', $row['oid']);
	  $row['gegner'] = get_opponent_details('name', $row['oid']);
	  $row['land2'] = get_opponent_details('land', $row['oid']);
	  $row['team'] = get_team_details('name', $row['tid']);
	  if($row['topmatch'] == 1) {
      $row['topmatchstatus'] = '<img src="include/images/icons/jep.gif" alt="JA" />';
    } else {
      $row['topmatchstatus'] = '<img src="include/images/icons/nop.gif" alt="NEIN" />';
    }
			$tpl->set_ar ( $row );
			$tpl->out(2);
		}
		$tpl->set ( 'MPL', $MPL );
		$tpl->out(3);
		$design->footer();
	break;
  case 'info' :
		$design = new design ( 'Admins Area', 'Admins Area', 2 );
		$design->header();
    $erg = db_query("SELECT DATE_FORMAT(datime,'%d.%m.%Y.%H.%i.%s') as datime, id,status,oid,wo,tid,`mod`,game,mtyp,txt FROM prefix_wars WHERE id = '".$menu->get(2)."'");
		$_ilch = db_fetch_assoc($erg);
		if(!empty($_ilch['oid'])) {
			$_ilch['land'] = get_opponent_details('land', $_ilch['oid']);
			$_ilch['gegner'] = get_opponent_details('name', $_ilch['oid']);
			$_ilch['tag'] = get_opponent_details('tag', $_ilch['oid']);
			$_ilch['page'] = get_opponent_details('homepage', $_ilch['oid']);
			$_ilch['kname'] = get_opponent_details('kontaktname', $_ilch['oid']);
			$_ilch['mail'] = get_opponent_details('email', $_ilch['oid']);
			$_ilch['icq'] = get_opponent_details('icq', $_ilch['oid']);
		}
		$_ilch['tid'] = get_teamname($_ilch['tid']);
		list($_ilch['day'],$_ilch['mon'],$_ilch['jahr'],$_ilch['stu'],$_ilch['min'],$_ilch['sek']) = explode('.',$_ilch['datime']);
    $tpl = new tpl ('wars/info', 1);
    $tpl->set_ar_out($_ilch,0);
    $design->footer();
  break;
  case 'check' :
		$design = new design ( 'Admins Area', 'Admins Area', 2 );
		$design->header();
		$tpl = new tpl ('wars/check', 1);

		$tpl->set_out('VERSION', '1.2.1', 0);
		$design->footer();
  break;
  case 'teams' :
  	$design = new design ( 'Admins Area', 'Admins Area', 2 );
		$design->header();
		$tpl = new tpl ('wars/teams', 1);

		$_ilch['tid'] = arlistee ( $_ilch['tid'] , get_teamlist($_ilch['tid']) );
		$tpl->set_ar_out($_ilch,0);

		if(isset($_POST['uplogo'])) {
			$id = $_POST['id'];
				$avatar_sql_update = '';
      if ( !empty ( $_FILES['avatarfile']['name'] ) ) {
				$file_tmpe = $_FILES['avatarfile']['tmp_name'];
        $rile_type = ic_mime_type ($_FILES['avatarfile']['tmp_name']);
				$file_type = $_FILES['avatarfile']['type'];
				$file_size = $_FILES['avatarfile']['size'];
        $fmsg = $lang['avatarisnopicture'];
        $size  = @getimagesize ($file_tmpe);
        $endar = array (1 => 'gif', 2 => 'jpg', 3 => 'png');
				if ( ($size[2] == 1 OR $size[2] == 2 OR $size[2] == 3) AND $size[0] > 10 AND $size[1] > 10 AND substr ( $file_type , 0 , 6 ) == 'image/' AND substr ( $rile_type , 0 , 6 ) == 'image/' ) {
				  $endung = $endar[$size[2]];
          $breite = $size[0];
          $hoehe  = $size[1];
          $fmsg = $lang['avatarcannotupload'];
				  if ( $breite = 100 AND $hoehe = 100 ) {
					  $neuer_name = 'include/images/teams/'.$id.'.'.$endung;
						@unlink (db_result(db_query("SELECT logo FROM prefix_groups WHERE id = ".$id),0));
            move_uploaded_file ( $file_tmpe , $neuer_name );
            @chmod($neuer_name, 0777);
            $avatar_sql_update = "logo = '".$neuer_name."'";
            $fmsg = $lang['pictureuploaded'];
					}
				}
			$abf = "UPDATE prefix_groups Set $avatar_sql_update WHERE id = $id";
			$erg = db_query($abf);
			echo $lang['pictureuploaded'];
			}

		}

			if($menu->get(2) == "edit") {
				$tid = escape($_POST['tid'], "integer");
					$abf = "SELECT * FROM prefix_groups WHERE id = $tid";
					$erg = db_query($abf);

					$row = db_fetch_assoc($erg);

					$tpl->set_ar_out($row,1);
			}
		$design->footer();
  break;
  case 'opponent' :
		$design = new design ( 'Admins Area', 'Admins Area', 2 );
		$design->header();

		$tpl = new tpl ( 'wars/opponent', 1);
			$oid = escape($menu->get(3), "integer");
		$tpl->set_out("","",0);

		if($menu->get(2) == "") {
		$ar['land'] = '<option></option>'.arliste ( '' , get_nationality_array() , $tpl , 'land' );

		$tpl->set_ar_out($ar,1);
		}
		if($menu->get(2) == "edit") {


			$abf = "SELECT * FROM prefix_opponents WHERE oid = $oid";
			$erg = db_query($abf);

			$row = db_fetch_assoc($erg);

			$row['land'] = '<option></option>'.arliste ( $row['land'] , get_nationality_array() , $tpl , 'land' );
			if(!empty($row['logo'])) {
			$row['logo2'] = '<img src="'.$row['logo'].'" />';
			} else {
			$row['logo2'] = '';
			}
			$tpl->set_ar_out($row, 2);

		}
		if($menu->get(2) == "save") {
			if(isset($_POST['new'])) {
				$abf = "INSERT INTO prefix_opponents (name, tag, homepage, land, kontaktname,kontaktemail,kontakticq) VALUES ('$_POST[name]', '$_POST[tag]', '$_POST[hp]', '$_POST[land]', '$_POST[kontaktname]', '$_POST[kontaktemail]','$_POST[kontakticq]')";
				$erg = db_query($abf);
				$id = db_last_id();

				$avatar_sql_update = '';
		      if ( !empty ( $_FILES['avatarfile']['name'] ) ) {
						$file_tmpe = $_FILES['avatarfile']['tmp_name'];
		        $rile_type = ic_mime_type ($_FILES['avatarfile']['tmp_name']);
						$file_type = $_FILES['avatarfile']['type'];
						$file_size = $_FILES['avatarfile']['size'];
		        $fmsg = $lang['avatarisnopicture'];
		        $size  = @getimagesize ($file_tmpe);
		        $endar = array (1 => 'gif', 2 => 'jpg', 3 => 'png');
						if ( ($size[2] == 1 OR $size[2] == 2 OR $size[2] == 3) AND $size[0] > 10 AND $size[1] > 10 AND substr ( $file_type , 0 , 6 ) == 'image/' AND substr ( $rile_type , 0 , 6 ) == 'image/' ) {
						  $endung = $endar[$size[2]];
		          $breite = $size[0];
		          $hoehe  = $size[1];
		          $fmsg = $lang['avatarcannotupload'];
						  if ( $breite = 100 AND $hoehe = 100 ) {
							  $neuer_name = 'include/images/clanlogos/'.$id.'.'.$endung;
								@unlink (db_result(db_query("SELECT logo FROM prefix_opponents WHERE oid = ".$id),0));
		            move_uploaded_file ( $file_tmpe , $neuer_name );
		            @chmod($neuer_name, 0777);
		            $avatar_sql_update = "logo = '".$neuer_name."'";
		            $fmsg = $lang['pictureuploaded'];
							}
						}
					$abf = "UPDATE prefix_opponents Set $avatar_sql_update WHERE oid = $id";
					$erg = db_query($abf);
					}



				echo 'Erfolgreich eingetragen';
			}
			if(isset($_POST['edit'])) {

			  $avatar_sql_update = '';
		      if ( !empty ( $_FILES['avatarfile']['name'] ) ) {
						$file_tmpe = $_FILES['avatarfile']['tmp_name'];
		        $rile_type = ic_mime_type ($_FILES['avatarfile']['tmp_name']);
						$file_type = $_FILES['avatarfile']['type'];
						$file_size = $_FILES['avatarfile']['size'];
		        $fmsg = $lang['avatarisnopicture'];
		        $size  = @getimagesize ($file_tmpe);
		        $endar = array (1 => 'gif', 2 => 'jpg', 3 => 'png');
						if ( ($size[2] == 1 OR $size[2] == 2 OR $size[2] == 3) AND $size[0] > 10 AND $size[1] > 10 AND substr ( $file_type , 0 , 6 ) == 'image/' AND substr ( $rile_type , 0 , 6 ) == 'image/' ) {
						  $endung = $endar[$size[2]];
		          $breite = $size[0];
		          $hoehe  = $size[1];
		          $fmsg = $lang['avatarcannotupload'];
						  if ( $breite = 100 AND $hoehe = 100 ) {
							  $neuer_name = 'include/images/clanlogos/'.$oid.'.'.$endung;
								@unlink (db_result(db_query("SELECT logo FROM prefix_opponents WHERE oid = ".$oid),0));
		            move_uploaded_file ( $file_tmpe , $neuer_name );
		            @chmod($neuer_name, 0777);
		            $avatar_sql_update = "logo = '".$neuer_name."',";
		            $fmsg = $lang['pictureuploaded'];
							}
						}
					} elseif ( isset($_POST['avatarloeschen']) ) {
		        $fmsg = $lang['picturedelete'];
		        @unlink (db_result(db_query("SELECT logo FROM prefix_opponents WHERE oid = ".$oid),0));
		        $avatar_sql_update = "logo = '',";
		      }


				$abf = "UPDATE prefix_opponents Set name = '$_POST[name]', tag = '$_POST[tag]', homepage = '$_POST[hp]', land = '$_POST[land]', kontaktname = '$_POST[kontaktname]', $avatar_sql_update kontaktemail = '$_POST[kontaktemail]', kontakticq = '$_POST[kontakticq]' WHERE oid = $oid";
				$erg = db_query($abf);




				echo 'Erfolgreich editiert';
			}
		}
		$tpl->set_out("","",3);

		$select = "SELECT * FROM prefix_opponents ORDER BY name";
		$query = db_query($select);

			while($r = db_fetch_assoc($query)) {


			$tpl->set_ar_out($r, 4);
			}
		$tpl->set_out("","",5);
		if($menu->get(2) == "del") {
			 @unlink (db_result(db_query("SELECT logo FROM prefix_opponents WHERE oid = ".$menu->get(3)),0));
			 db_query('DELETE FROM `prefix_opponents` WHERE oid = "'.$menu->get(3).'" LIMIT 1');
		}

		$design->footer();
  break;
  case 'fightus' :
		$design = new design ( 'Admins Area', 'Admins Area', 2 );
		$design->header();

		$tpl = new tpl ( 'wars/fightus', 1);

		$tpl->out(0);
		if(is_numeric($menu->get(2))) {

			$id = escape($menu->get(2), "integer");
			$abf = "SELECT * FROM prefix_fightus WHERE id = $id";
			$erg = db_query($abf);

			$row = db_fetch_assoc($erg);

			$row['team'] = get_team_details('name', $row['tid']);
			if(isset($_POST['createop'])) {
				$result = db_result(db_query("SELECT count(oid) FROM prefix_opponents WHERE name = '$row[oname]'"),0);
				if($result == 0) {
					db_query("INSERT INTO prefix_opponents (name, tag, homepage, land, kontaktname, kontaktemail, kontakticq) VALUES ('".$row['oname']."','".$row['otag']."','".$row['opage']."','".$row['oland']."','".$row['okontaktperson']."','".$row['oemail']."','".$row['oicq']."')");
					$lastoid = db_last_id();
				} else {
					$abf = "SELECT oid FROM prefix_opponents WHERE name = '$row[oname]' LIMIT 1";
					$erg = db_query($abf);
					$r = db_fetch_assoc($erg);
					$lastoid = $r['oid'];
				}

				db_query("INSERT INTO prefix_wars (datime,`status`,oid,wo,tid,`mod`,game,mtyp,txt,mlink,lineupopp,lineupowp,oppstate,owpstate,maps,tv,pw) VALUES ('".$row['datum']."',2,'".$lastoid."','".$row['server']."','".$row['tid']."','".$_row['xonx']."','".$row['spiel']."','".$row['matchtyp']."','".$row['nachricht']."','','','','','','','','')");

				db_query("DELETE FROM prefix_fightus WHERE id = $row[id]");

				wd("admin.php?wars-fightus", "Nextwar erfolgreich eingetragen");
			} else {
				$tpl->set_ar_out($row,3);
			}



		} else {
			$abf = "SELECT id,oname,otag,opage,oland,okontaktperson,oemail,oicq,server,DATE_FORMAT(datum,'%d.%m.%Y - %H:%i:%s') as time,tid,xonx,spiel,matchtyp,nachricht FROM prefix_fightus";
			$erg = db_query($abf);

			while($row = db_fetch_assoc($erg)) {

			$row['tid'] = get_team_details('name',$row['tid']);
				$tpl->set_ar_out($row,1);

			}
			$tpl->out(2);
		}


		$design->footer();
  break;
  }
?>
