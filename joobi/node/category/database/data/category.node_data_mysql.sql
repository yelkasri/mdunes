/*Export of the extension category.node*/

SET @rolid_0 = (1);
SET @pk_549_310 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='productcat.node' LIMIT 1 );
REPLACE INTO `#__dataset_tables` (`name`,`namekey`,`dbtid`,`prefix`,`rolid`,`level`,`type`,`pkey`,`suffix`,`group`,`domain`,`export`,`noaudit`,`engine`,`node`) VALUES ('productcat_node','productcat.node',@pk_549_310,'',@rolid_0,'0','40','catid','node','productcat','51','2','0','7','item');
SET @pk_621_310 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='product.category' LIMIT 1 );
UPDATE `#__model_node`  SET `sid`=@pk_621_310,`dbtid`=( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='productcat.node' LIMIT 1),`path`='0',`namekey`='product.category',`folder`='product.category',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='ordrg=1\ngrpmap=parent\nprtname=product.categorytrans\nautofld=1',`fields`='0',`core`='1',`faicon`='fa-folder-open',`pnamekey`='item.category',`import`='0',`export`='0' WHERE  `namekey`='product.category';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_310,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='productcat.node' LIMIT 1),'0','product.category','product.category',@rolid_0,'0','1','ordrg=1\ngrpmap=parent\nprtname=product.categorytrans\nautofld=1','0','1','fa-folder-open','item.category','0','0');

SET @pk_5_171 = ( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='category.node' LIMIT 1 );
UPDATE `#__extension_node`  SET `publish`='1',`folder`='category',`wid`=@pk_5_171,`params`='',`type`='150',`name`='Category',`destination`='node',`trans`='1',`certify`='1',`namekey`='category.node',`version`='5129',`lversion`='5129',`pref`='0',`install`='',`core`='1',`showconfig`='1',`framework`='0' WHERE  `namekey`='category.node';
INSERT IGNORE INTO `#__extension_node` (`publish`,`folder`,`wid`,`params`,`type`,`name`,`destination`,`trans`,`certify`,`namekey`,`version`,`lversion`,`pref`,`install`,`core`,`showconfig`,`framework`) VALUES ('1','category',@pk_5_171,'','150','Category','node','1','1','category.node','5129','5129','0','','1','1','0');

SET @wid_1 = ( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='category.node' LIMIT 1);
INSERT IGNORE INTO `#__translation_en` (`text`,`auto`,`imac`) VALUES ('It is not permitted to put a category as its own parent.','1','1213020899EZVY');
INSERT IGNORE INTO `#__translation_en` (`text`,`auto`,`imac`) VALUES ('The parent can not be one of the child of this category','1','1220361707FSCI'),('This $NAME is not empty... All sub-elements were added to the parent $NAME.','1','1235560364LCKV'),('The parent of the category could not be found!','1','1406069322OHER');
REPLACE INTO `#__extension_dependency` (`wid`,`ref_wid`) VALUES (( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='library.node' LIMIT 1),@wid_1);
INSERT IGNORE INTO `#__translation_reference` (`wid`,`load`,`imac`) VALUES (@wid_1,'0','1213020899EZVY');
INSERT IGNORE INTO `#__translation_reference` (`wid`,`load`,`imac`) VALUES (@wid_1,'0','1220361707FSCI'),(@wid_1,'0','1235560364LCKV'),(@wid_1,'0','1406069322OHER');