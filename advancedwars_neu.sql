ALTER TABLE `prefix_wars` CHANGE `gegner` `oid` INT( 11 ) NOT NULL ;

CREATE TABLE IF NOT EXISTS `prefix_fightus` (
 `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `oname` VARCHAR( 100 ) NOT NULL ,
 `otag` VARCHAR( 100 ) NOT NULL ,
 `opage` VARCHAR( 100 ) NOT NULL ,
 `oland` VARCHAR( 100 ) NOT NULL ,
 `okontaktperson` VARCHAR( 100 ) NOT NULL ,
 `oemail` VARCHAR( 100 ) NOT NULL ,
 `oicq` VARCHAR( 100 ) NOT NULL ,
 `server` VARCHAR( 100 ) NOT NULL ,
 `datum` datetime NOT NULL ,
 `tid` VARCHAR( 100 ) NOT NULL ,
 `xonx` VARCHAR( 100 ) NOT NULL ,
 `spiel` VARCHAR( 100 ) NOT NULL ,
 `matchtyp` VARCHAR( 100 ) NOT NULL ,
 `nachricht` TEXT NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE IF NOT EXISTS `prefix_opponents` (
  `oid` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `tag` varchar(20) NOT NULL,
  `homepage` varchar(100) NOT NULL,
  `logo` text NOT NULL,
  `land` varchar(50) NOT NULL,
  `kontaktname` varchar(50) NOT NULL,
  `kontaktemail` varchar(50) NOT NULL,
  `kontakticq` varchar(50) NOT NULL,
  PRIMARY KEY  (`oid`)
) ;

INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`) VALUES 
('wars_matchlink_recht', 'grecht', 'Wars Optionen', 'Matchlink sichtbar ab', '-4'),
('wars_server_recht', 'grecht', 'Wars Optionen', 'Serverdaten sichtbar ab', '-4'),
('wars_password_recht', 'grecht', 'Wars Optionen', 'Server-Password sichtbar ab', '-4'),
('wars_tv_recht', 'grecht', 'Wars Optionen', 'TV (z.B. HLTV, SourceTV) sichtbar ab', '0');

ALTER TABLE `prefix_wars` ADD `mlink` VARCHAR( 200 ) NOT NULL ;
ALTER TABLE `prefix_wars` ADD `lineupopp` VARCHAR( 200 ) NOT NULL ;
ALTER TABLE `prefix_wars` ADD `lineupowp` VARCHAR( 200 ) NOT NULL ;
ALTER TABLE `prefix_fightus` ADD  `oid` INT( 10 ) NOT NULL ;
ALTER TABLE `prefix_wars` ADD `oppstate` TEXT NOT NULL ;
ALTER TABLE `prefix_wars` ADD `owpstate` TEXT NOT NULL ;
ALTER TABLE `prefix_wars` ADD `maps` VARCHAR( 100 ) NOT NULL ;
ALTER TABLE `prefix_wars` ADD `tv` VARCHAR( 100 ) NOT NULL ;
ALTER TABLE `prefix_wars` ADD `pw` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE `prefix_wars` ADD `topmatch` INT( 1 ) NOT NULL ;
ALTER TABLE `prefix_groups` ADD `logo` VARCHAR ( 50 ) NOT NULL ;
ALTER TABLE `prefix_news` ADD `war` INT ( 10 ) NOT NULL ;