/*Export of the extension editor.node*/

SET @rolid_0 = (1);
SET @pk_549_5 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='extension.node' LIMIT 1 );
REPLACE INTO `#__dataset_tables` (`name`,`namekey`,`dbtid`,`prefix`,`rolid`,`level`,`type`,`pkey`,`suffix`,`group`,`domain`,`export`,`noaudit`,`engine`,`node`) VALUES ('extension_node','extension.node',@pk_549_5,'',@rolid_0,'0','1','wid','node','extension','9','2','1','7','apps');
SET @pk_621_5 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='extension' LIMIT 1 );
UPDATE `#__model_node`  SET `sid`=@pk_621_5,`dbtid`=( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='extension.node' LIMIT 1),`path`='1',`namekey`='extension',`folder`='extension',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='ordrg=1\ngrpmap=core',`fields`='0',`core`='1',`faicon`='fa-mobile',`pnamekey`='',`import`='0',`export`='0' WHERE  `namekey`='extension';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_5,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='extension.node' LIMIT 1),'1','extension','extension',@rolid_0,'0','1','ordrg=1\ngrpmap=core','0','1','fa-mobile','','0','0');

SET @pk_5_731 = ( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='editor.node' LIMIT 1 );
UPDATE `#__extension_node`  SET `publish`='1',`folder`='editor',`wid`=@pk_5_731,`params`='',`type`='150',`name`='Editors',`destination`='node',`trans`='1',`certify`='0',`namekey`='editor.node',`version`='5023',`lversion`='5023',`pref`='0',`install`='',`core`='1',`showconfig`='1',`framework`='0' WHERE  `namekey`='editor.node';
INSERT IGNORE INTO `#__extension_node` (`publish`,`folder`,`wid`,`params`,`type`,`name`,`destination`,`trans`,`certify`,`namekey`,`version`,`lversion`,`pref`,`install`,`core`,`showconfig`,`framework`) VALUES ('1','editor',@pk_5_731,'','150','Editors','node','1','0','editor.node','5023','5023','0','','1','1','0');

SET @wid_1 = ( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='editor.node' LIMIT 1);
INSERT IGNORE INTO `#__translation_en` (`text`,`auto`,`imac`) VALUES ('Text Only','1','1237260381RHQN');
INSERT IGNORE INTO `#__translation_en` (`text`,`auto`,`imac`) VALUES ('NiceEdit','1','1378320294IHEE'),('Framework Editors','1','1378320294IHEF'),('CK Editor','1','1384345472OOFE'),('Please enter text here...','1','1460990092NBVH');
REPLACE INTO `#__extension_dependency` (`wid`,`ref_wid`) VALUES (( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='library.node' LIMIT 1),@wid_1);
INSERT IGNORE INTO `#__translation_reference` (`wid`,`load`,`imac`) VALUES (@wid_1,'1','1237260381RHQN');
INSERT IGNORE INTO `#__translation_reference` (`wid`,`load`,`imac`) VALUES (@wid_1,'1','1378320294IHEE'),(@wid_1,'1','1378320294IHEF'),(@wid_1,'1','1384345472OOFE'),(@wid_1,'1','1460990092NBVH');
REPLACE INTO `#__extension_info` (`wid`,`author`,`userversion`,`userlversion`) VALUES ((@wid_1),'','','');