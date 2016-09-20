/*Export of the extension lib.includes*/

SET @pk_5_1868 = ( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='lib.includes' LIMIT 1 );
UPDATE `#__extension_node`  SET `publish`='1',`folder`='lib',`wid`=@pk_5_1868,`params`='',`type`='175',`name`='Library Includes',`destination`='inc',`trans`='0',`certify`='1',`namekey`='lib.includes',`version`='2129',`lversion`='2129',`pref`='0',`install`='',`core`='1',`showconfig`='1',`framework`='0' WHERE  `namekey`='lib.includes';
INSERT IGNORE INTO `#__extension_node` (`publish`,`folder`,`wid`,`params`,`type`,`name`,`destination`,`trans`,`certify`,`namekey`,`version`,`lversion`,`pref`,`install`,`core`,`showconfig`,`framework`) VALUES ('1','lib',@pk_5_1868,'','175','Library Includes','inc','0','1','lib.includes','2129','2129','0','','1','1','0');

REPLACE INTO `#__extension_dependency` (`wid`,`ref_wid`) VALUES (( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='library.node' LIMIT 1),( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='lib.includes' LIMIT 1));
REPLACE INTO `#__extension_info` (`wid`,`author`,`userversion`,`userlversion`) VALUES (( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='lib.includes' LIMIT 1),'','','');