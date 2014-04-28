<?php
#   Copyright by: Tobias Schwarz
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );

echo '<table width="100%" border="0" cellpadding="2" cellspacing="0">';
$akttime = date('Y-m-d');
$erg = @db_query("SELECT DATE_FORMAT(datime,'&nbsp; %d.%m.%y - %H:%i Uhr') as time,oid, id, game, status, tid, owp, opp, wlp FROM prefix_wars WHERE topmatch = 1 ORDER BY datime LIMIT 5");
if ( @db_num_rows($erg) == 0 ) {
	echo '<tr><td>kein Topmatch gesetzt</td></tr>';
} else {
	while ($row = @db_fetch_object($erg) ) {
	 
	 if($row->status == 2) {
    $war = "next";
   } else {
    $war = "last";
   }
	 
	 if($row->wlp == 1) {
    $color = "green";
   }elseif ($row->wlp == 2) {
    $color = "red";
   } else {
    $color = "black";
   }
	 
		$row->gegner = '<img width="50" height="50" src="'.get_opponent_details('logo', $row->oid).'" alt="'.get_opponent_details('name', $row->oid).'" />';
		$row->team = '<img width="50" height="50" src="'.get_team_details('logo', $row->tid).'" alt="'.get_team_details('name', $row->tid).'" />';

		echo '<tr>
            <td>'.$row->team.'</td>
            <td style="vertical-align:middle;font-size:14px;"><b>VS</b></td>
            <td>'.$row->gegner.'</td>
          </tr>';
    if($war == "last") {
    echo '<tr>
            <td colspan="3" align="center" style="color: '.$color.'">'.$row->owp.':'.$row->opp.'</td>
          </tr>';
    }
    echo '<tr>
            <td colspan="3" style="font-size:10px;"><b>'.$row->time.'</b></td>
          </tr>
          <tr>
            <td align="center" colspan="3"><a href="index.php?wars-'.$war.'-'.$row->id.'" style="font-size: 10px;">MATCHANSICHT</a></td>
          </tr>';
	}
}
echo '</table>';
?>