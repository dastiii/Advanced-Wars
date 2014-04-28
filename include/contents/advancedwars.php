<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
$title = $allgAr['title'].' :: Installation...';
$hmenu = 'Advanced Wars Installation';
$design = new design ( $title , $hmenu );
$design->header();

if(is_admin()) {
	if($_POST['step'] == 1)
		{
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="border">
				<tr class="Chead">
					<td colspan="2">Überprüfung der Schreibrechte</td>
				</tr>
				<tr class="Cmite">
					<td>Ordner: <i>"include/images/clanlogos/"</i> benötigt CHMOD 0777</td>
					<td><?php if ( @is_writeable ( 'include/images/clanlogos' ) ) { echo '<font color="#40aa00"><b>RICHTIG</b></font>'; } else { echo '<font color="#FF0000"><b>FALSCH</b></font>'; } ?></td>
				</tr>
				<tr class="Cnorm">
					<td>Ordner: <i>"include/images/teams/"</i> benötigt CHMOD 0777</td>
					<td><?php if ( @is_writeable ( 'include/images/teams' ) ) { echo '<font color="#40aa00"><b>RICHTIG</b></font>'; } else { echo '<font color="#FF0000"><b>FALSCH</b></font>'; } ?></td>
				</tr>
				<tr>
					<td colspan="2" class="Chead" style="text-align:center;">
						<form name="form" method="post" action="index.php?advancedwars">
							<input type="hidden" name="step" value="2" />
							<input type="submit" name="submit" value="Weiter zur Installations-Art..." />
						</form>
					</td>
				</tr>
			</table>
			<?php
		}
	elseif($_POST['step'] == 2)
		{
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="border">
				<tr class="Chead">
					<td colspan="2">Art der Installation</td>
				</tr>
				<tr class="Cnorm">
					<td>
					<form name="form" method="post" action="index.php?advancedwars">
						<p class="Cnorm" style="margin:2px; padding:2px;">
							Wenn Sie bereits eine ältere Version des "Advanced Wars" installiert haben, wählen Sie bitte ihre aktuelle Version aus.<br />
							<input name="update" type="radio" value="1beta" style="margin-left: 30px;" /> Advanced Wars 1 BETA <br />
							<input name="update" type="radio" value="10" style="margin-left: 30px;" /> Advanced Wars 1.0 <br />
							<input name="update" type="radio" value="11" style="margin-left: 30px;"/> Advanced Wars 1.1<br>
							<input name="update" type="radio" value="12" style="margin-left: 30px;"/> Advanced Wars 1.2<br>
						</p>
						<p class="Cmite" style="margin:2px; padding:2px;">
							Wenn Sie "Advanced Wars" noch nicht installiert haben, wählen Sie bitte "Neue Installation" aus.<br />
							<input name="update" type="radio" value="neu" style="margin-left: 30px;" /> Neue Installation (1.2.1)
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="Chead" style="text-align:center;">
							<input type="hidden" name="step" value="3" />
							<input type="submit" name="submit" value="Weiter..." />
						</form>
					</td>
				</tr>
			</table>
			<?php
		}
	elseif($_POST['step'] == 3)
		{
			if(isset($_POST['update']))
			{
				$updateFile = "advancedwars_".escape($_POST['update'], 'string').".sql";

				if(file_exists($updateFile))
				{
					$sql_file = implode('',file(''.$updateFile.''));
					$sql_file = preg_replace ("/(\015\012|\015|\012)/", "\n", $sql_file);
					$sql_statements = explode(";\n",$sql_file);
					foreach ( $sql_statements as $sql_statement ) {
		 				if ( trim($sql_statement) != '' ) {
		  				  #echo '<pre>'.$sql_statement.'</pre><hr>';
		    			db_query($sql_statement);
						}
					}
				}

				@unlink('advancedwars_neu.sql');
				@unlink('advancedwars_10.sql');
				@unlink('advancedwars_1beta.sql');
				@unlink('include/contents/advancedwars.php');
			}
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="border">
				<tr class="Chead">
					<td colspan="2">Installation abgeschlossen.</td>
				</tr>
				<tr class="Cnorm">
					<td><p>Falls keine Fehler aufgetreten sind, können Sie das Modul jetzt benutzen, falls Fehler aufgetreten sind, bitte auf www.ilch.de im Forum melden.</p></td>
				</tr>
				<tr>
					<td colspan="2" class="Chead" style="text-align:center;">
						&nbsp;
					</td>
				</tr>
			</table>
			<?php
		}
	else
		{
			?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="border">
				<tr class="Chead">
					<td colspan="2">Advanced Wars Installations Assistent</td>
				</tr>
				<tr class="Cnorm">
					<td><br /><table border="0"><tbody><tr><td valign="top"><br /></td><td valign="top" style="padding-left: 10px"> <p>Die 'Advanced Wars' Erweiterung für IlchClan (<a href="http://ilch.de">www.ilch.de</a>) verbessert die Warsansicht und macht das erstellen von Wars komfortabler und einfacher.</p><p><strong>Features:</strong></p><ul><li>Eine vollkommen überarbeitete Warsansicht</li><li>Logo-Upload</li><li>Gegner erstellung im AdminPanel</li><li>uvm.</li></ul><p>&nbsp;</p></td></tr></tbody></table></td>
				</tr>
				<tr>
					<td colspan="2" class="Chead" style="text-align:center;">
						<form name="form" method="post" action="index.php?advancedwars">
							<input type="hidden" name="step" value="1" />
							<input type="submit" name="submit" value="Fortfahren..." />
						</form>
					</td>
				</tr>
			</table>
			<?php
		}
} else {
	wd('index.php?news', 'Keine Berechtigung...',2);
}

$design->footer();

?>