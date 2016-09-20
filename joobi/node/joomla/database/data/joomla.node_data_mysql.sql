/*Export of the extension joomla.node*/

DELETE `#__dataset_constraints`.* FROM `#__dataset_tables` LEFT JOIN `#__dataset_constraints` ON `#__dataset_constraints`.`dbtid` = `#__dataset_tables`.`dbtid` WHERE `#__dataset_tables`.`namekey` IN ('content','modules','extensions','menu','usergroups','user.usergroup.map') ;
DELETE `#__dataset_columns`.* FROM `#__dataset_tables` LEFT JOIN `#__dataset_columns` ON `#__dataset_columns`.`dbtid` = `#__dataset_tables`.`dbtid` WHERE `#__dataset_tables`.`namekey` IN ('content','modules','extensions','menu','usergroups','user.usergroup.map','sections','categories','users','model.trans','layout.mlinkstrans','layout.trans','theme.trans','eguillage.trans') AND `#__dataset_columns`.`namekey` NOT IN ('251id','251name','251username','251email','251password','251usertype','251block','251sendemail','251gid','251registerdate','251lastvisitdate','251activation','251params','356id','481yid','481lgid','481name','481description','485mid','485lgid','485name','559tmid','559name','559description','559lgid','481wname','481wdescription','618sid','618lgid','618name','695id','696id','714ctrid','714lgid','name714','description714','description485','auto481','fromlgid481','auto485','fromlgid485','auto559','fromlgid559','auto618','fromlgid618','auto714','fromlgid714','title356','created356','state356','title695','created695','state695','catid356','sectionid356','name695','title696','name696','section696','ordering696','published696','id595','title595','name595','image595','scope595','image_position595','description595','published595','check_out595','checked_out_time595','ordering595','access595','count595','params595','state595','modified356','published_up356','publish_down356','mask356','created_by356','created_by_alias356','modified_by356','checked_out356','checked_out_time356','images356','urls356','attribs356','version356','parentid356','ordering356','metakey356','metadesc356','access356','metadata356','hits356','915extension_id','916id','921id','922user_id','922group_id','message485','seotitle481','seodescription481','seokeywords481') AND `#__dataset_columns`.`core` = 1  ;
DELETE `#__dataset_foreign`.* FROM `#__dataset_columns` LEFT JOIN `#__dataset_foreign` ON `#__dataset_foreign`.`feid` = `#__dataset_columns`.`dbcid` WHERE `#__dataset_columns`.`namekey` IN ('catid356','sectionid356','id595') AND `#__dataset_foreign`.`namekey` NOT IN ('FK_joomla_content_catid','595_id_595','FK_joomla_content_sectionid') ;
SET @rolid_0 = (1);
SET @pk_549_356 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='content' LIMIT 1 );
SET @pk_549_595 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='modules' LIMIT 1 );
SET @pk_549_915 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='extensions' LIMIT 1 );
SET @pk_549_916 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='menu' LIMIT 1 );
SET @pk_549_921 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='usergroups' LIMIT 1 );
SET @pk_549_922 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='user.usergroup.map' LIMIT 1 );
SET @pk_549_695 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='sections' LIMIT 1 );
SET @pk_549_696 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='categories' LIMIT 1 );
SET @pk_549_251 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='users' LIMIT 1 );
SET @pk_549_618 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='model.trans' LIMIT 1 );
SET @pk_549_485 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='layout.mlinkstrans' LIMIT 1 );
SET @pk_549_481 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='layout.trans' LIMIT 1 );
SET @pk_549_559 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='theme.trans' LIMIT 1 );
SET @pk_549_714 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='eguillage.trans' LIMIT 1 );
REPLACE INTO `#__dataset_tables` (`name`,`namekey`,`dbtid`,`prefix`,`rolid`,`level`,`type`,`pkey`,`suffix`,`group`,`domain`,`export`,`noaudit`,`engine`,`node`) VALUES ('content','content',@pk_549_356,'',@rolid_0,'0','1','id','','content','250','2','1','7','joomla');
REPLACE INTO `#__dataset_tables` (`name`,`namekey`,`dbtid`,`prefix`,`rolid`,`level`,`type`,`pkey`,`suffix`,`group`,`domain`,`export`,`noaudit`,`engine`,`node`) VALUES ('modules','modules',@pk_549_595,'',@rolid_0,'0','1','id','','modules','250','0','1','7','joomla'),('extensions','extensions',@pk_549_915,'',@rolid_0,'0','1','extension_id','','extensions','250','2','1','7','joomla'),('menu','menu',@pk_549_916,'',@rolid_0,'0','40','id','','menu','250','2','1','7','joomla'),('usergroups','usergroups',@pk_549_921,'',@rolid_0,'0','40','id','','usergroups','250','2','1','7',''),('user_usergroup_map','user.usergroup.map',@pk_549_922,'',@rolid_0,'0','30','user_id,group_id','usergroup_map','user','250','2','1','7','joomla'),('sections','sections',@pk_549_695,'',@rolid_0,'0','1','id','','sections','250','2','1','7','joomla'),('categories','categories',@pk_549_696,'',@rolid_0,'0','40','id','','categories','250','2','0','7','joomla'),('users','users',@pk_549_251,'',@rolid_0,'0','1','id','','users','250','2','1','7','joomla'),('model_trans','model.trans',@pk_549_618,'',@rolid_0,'0','20','sid,lgid','trans','model','9','0','1','7','library'),('layout_mlinkstrans','layout.mlinkstrans',@pk_549_485,'',@rolid_0,'0','20','mid,lgid','mlinkstrans','layout','9','0','1','7','library'),('layout_trans','layout.trans',@pk_549_481,'',@rolid_0,'0','20','yid,lgid','trans','layout','9','0','1','7','library'),('theme_trans','theme.trans',@pk_549_559,'',@rolid_0,'0','20','tmid,lgid','trans','theme','51','0','0','7','theme'),('eguillage_trans','eguillage.trans',@pk_549_714,'',@rolid_0,'0','20','ctrid,lgid','trans','eguillage','9','0','1','7','library');
SET @dbtid_1 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='content' LIMIT 1);
SET @dbtid_2 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='modules' LIMIT 1);
SET @dbtid_3 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='user.usergroup.map' LIMIT 1);
SET @pk_454_65711 = ( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_content|main_joomla15' LIMIT 1 );
SET @pk_454_65748 = ( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_modules|main_joomla15' LIMIT 1 );
SET @pk_454_65847 = ( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_extensions|main_joomla15' LIMIT 1 );
SET @pk_454_65848 = ( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_menu|main_joomla15' LIMIT 1 );
SET @pk_454_65874 = ( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_user_usergroups|main_joomla16' LIMIT 1 );
SET @pk_454_65875 = ( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_user_usergroup_map|main_joomla16' LIMIT 1 );
REPLACE INTO `#__dataset_constraints` (`ctid`,`type`,`namekey`,`dbtid`) VALUES (@pk_454_65711,'3','PK_content|main_joomla15',@dbtid_1);
REPLACE INTO `#__dataset_constraints` (`ctid`,`type`,`namekey`,`dbtid`) VALUES (@pk_454_65748,'3','PK_modules|main_joomla15',@dbtid_2),(@pk_454_65847,'3','PK_extensions|main_joomla15',( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='extensions' LIMIT 1)),(@pk_454_65848,'3','PK_menu|main_joomla15',( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='menu' LIMIT 1)),(@pk_454_65874,'3','PK_user_usergroups|main_joomla16',( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='usergroups' LIMIT 1)),(@pk_454_65875,'3','PK_user_usergroup_map|main_joomla16',@dbtid_3);
SET @dbtid_4 = ( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='users' LIMIT 1);
SET @pk_616_2018 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='356id' LIMIT 1 );
SET @pk_616_7781 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='id595' LIMIT 1 );
SET @pk_616_8396 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='915extension_id' LIMIT 1 );
SET @pk_616_8397 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='916id' LIMIT 1 );
SET @pk_616_8448 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='921id' LIMIT 1 );
SET @pk_616_8449 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='922user_id' LIMIT 1 );
SET @pk_616_8450 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='922group_id' LIMIT 1 );
SET @pk_616_6675 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='695id' LIMIT 1 );
SET @pk_616_6676 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='696id' LIMIT 1 );
SET @pk_616_7764 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='catid356' LIMIT 1 );
SET @pk_616_7765 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='sectionid356' LIMIT 1 );
SET @pk_616_1935 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251id' LIMIT 1 );
SET @pk_616_1936 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251name' LIMIT 1 );
SET @pk_616_1937 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251username' LIMIT 1 );
SET @pk_616_1938 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251email' LIMIT 1 );
SET @pk_616_1939 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251password' LIMIT 1 );
SET @pk_616_1940 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251usertype' LIMIT 1 );
SET @pk_616_1941 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251block' LIMIT 1 );
SET @pk_616_1942 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251sendemail' LIMIT 1 );
SET @pk_616_1943 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251gid' LIMIT 1 );
SET @pk_616_1944 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251registerdate' LIMIT 1 );
SET @pk_616_1945 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251lastvisitdate' LIMIT 1 );
SET @pk_616_1946 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251activation' LIMIT 1 );
SET @pk_616_1947 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='251params' LIMIT 1 );
SET @pk_616_7408 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='title356' LIMIT 1 );
SET @pk_616_7409 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='created356' LIMIT 1 );
SET @pk_616_7410 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='state356' LIMIT 1 );
SET @pk_616_7782 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='title595' LIMIT 1 );
SET @pk_616_7783 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='name595' LIMIT 1 );
SET @pk_616_7784 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='image595' LIMIT 1 );
SET @pk_616_7785 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='scope595' LIMIT 1 );
SET @pk_616_7786 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='image_position595' LIMIT 1 );
SET @pk_616_7787 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='description595' LIMIT 1 );
SET @pk_616_7788 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='published595' LIMIT 1 );
SET @pk_616_7789 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='check_out595' LIMIT 1 );
SET @pk_616_7790 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='checked_out_time595' LIMIT 1 );
SET @pk_616_7791 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='ordering595' LIMIT 1 );
SET @pk_616_7792 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='access595' LIMIT 1 );
SET @pk_616_7793 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='count595' LIMIT 1 );
SET @pk_616_7794 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='params595' LIMIT 1 );
SET @pk_616_7795 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='state595' LIMIT 1 );
SET @pk_616_7797 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='modified356' LIMIT 1 );
SET @pk_616_7799 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='published_up356' LIMIT 1 );
SET @pk_616_7800 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='publish_down356' LIMIT 1 );
SET @pk_616_7801 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='mask356' LIMIT 1 );
SET @pk_616_7802 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='created_by356' LIMIT 1 );
SET @pk_616_7803 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='created_by_alias356' LIMIT 1 );
SET @pk_616_7804 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='modified_by356' LIMIT 1 );
SET @pk_616_7805 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='checked_out356' LIMIT 1 );
SET @pk_616_7806 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='checked_out_time356' LIMIT 1 );
SET @pk_616_7807 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='images356' LIMIT 1 );
SET @pk_616_7808 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='urls356' LIMIT 1 );
SET @pk_616_7809 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='attribs356' LIMIT 1 );
SET @pk_616_7810 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='version356' LIMIT 1 );
SET @pk_616_7811 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='parentid356' LIMIT 1 );
SET @pk_616_7812 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='ordering356' LIMIT 1 );
SET @pk_616_7813 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='metakey356' LIMIT 1 );
SET @pk_616_7814 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='metadesc356' LIMIT 1 );
SET @pk_616_7815 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='access356' LIMIT 1 );
SET @pk_616_7816 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='metadata356' LIMIT 1 );
SET @pk_616_7817 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='hits356' LIMIT 1 );
SET @pk_616_6039 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='618name' LIMIT 1 );
SET @pk_616_3419 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='485name' LIMIT 1 );
SET @pk_616_6797 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='description485' LIMIT 1 );
SET @pk_616_10823 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='message485' LIMIT 1 );
SET @pk_616_3408 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='481name' LIMIT 1 );
SET @pk_616_5579 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='481wname' LIMIT 1 );
SET @pk_616_5580 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='481wdescription' LIMIT 1 );
SET @pk_616_5473 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='559name' LIMIT 1 );
SET @pk_616_5474 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='559description' LIMIT 1 );
SET @pk_616_6794 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='name714' LIMIT 1 );
SET @pk_616_6795 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='description714' LIMIT 1 );
REPLACE INTO `#__dataset_columns` (`dbcid`,`dbtid`,`name`,`pkey`,`checkval`,`type`,`attributes`,`mandatory`,`default`,`ordering`,`level`,`rolid`,`extra`,`size`,`export`,`namekey`,`core`,`columntype`,`noaudit`,`readable`,`fieldtype`) VALUES (@pk_616_2018,@dbtid_1,'id','1','0','4','1','1','','1','1',@rolid_0,'1','11.0','1','356id','1','0','0','','');
REPLACE INTO `#__dataset_columns` (`dbcid`,`dbtid`,`name`,`pkey`,`checkval`,`type`,`attributes`,`mandatory`,`default`,`ordering`,`level`,`rolid`,`extra`,`size`,`export`,`namekey`,`core`,`columntype`,`noaudit`,`readable`,`fieldtype`) VALUES (@pk_616_7781,@dbtid_2,'id','1','1','4','1','1','','1','0','0','1','0.0','1','id595','1','0','0','',''),(@pk_616_8396,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='extensions' LIMIT 1),'extension_id','1','0','4','1','1','','0','0','0','1','0.0','0','915extension_id','1','0','0','',''),(@pk_616_8397,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='menu' LIMIT 1),'id','1','0','4','1','1','','0','0','0','1','0.0','1','916id','1','0','0','',''),(@pk_616_8448,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='usergroups' LIMIT 1),'id','1','0','4','1','1','','0','0','0','1','0.0','1','921id','1','0','0','',''),(@pk_616_8449,@dbtid_3,'user_id','1','0','4','1','1','','0','0','0','0','0.0','1','922user_id','1','0','0','',''),(@pk_616_8450,@dbtid_3,'group_id','1','0','4','1','1','','1','0','0','0','0.0','1','922group_id','1','0','0','',''),(@pk_616_6675,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='sections' LIMIT 1),'id','1','0','4','1','1','','1','0','0','1','11.0','1','695id','1','0','0','',''),(@pk_616_6676,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='categories' LIMIT 1),'id','1','0','4','1','1','','1','0','0','1','11.0','1','696id','1','0','0','',''),(@pk_616_7764,@dbtid_1,'catid','0','1','4','1','1','0','5','0','0','0','0.0','1','catid356','1','0','0','categoryID',''),(@pk_616_7765,@dbtid_1,'sectionid','0','1','4','1','1','0','6','0','0','0','11.0','1','sectionid356','1','0','0','',''),(@pk_616_1935,@dbtid_4,'id','1','0','4','0','1','','0','1',@rolid_0,'1','11.0','1','251id','1','0','0','',''),(@pk_616_1936,@dbtid_4,'name','0','0','14','0','1','','0','1',@rolid_0,'0','11.0','1','251name','1','0','0','',''),(@pk_616_1937,@dbtid_4,'username','0','0','14','0','1','','0','1',@rolid_0,'0','150.0','1','251username','1','0','0','',''),(@pk_616_1938,@dbtid_4,'email','0','0','14','0','1','','0','1',@rolid_0,'0','100.0','1','251email','1','0','0','',''),(@pk_616_1939,@dbtid_4,'password','0','0','14','0','1','','0','1',@rolid_0,'0','100.0','1','251password','1','0','0','',''),(@pk_616_1940,@dbtid_4,'usertype','0','0','14','0','1','','0','1',@rolid_0,'0','75.0','1','251usertype','1','0','0','',''),(@pk_616_1941,@dbtid_4,'block','0','0','1','0','1','0','0','1',@rolid_0,'0','4.0','1','251block','1','0','0','',''),(@pk_616_1942,@dbtid_4,'sendemail','0','0','1','0','1','0','0','1',@rolid_0,'0','4.0','1','251sendemail','1','0','0','',''),(@pk_616_1943,@dbtid_4,'gid','0','0','1','0','1','1','0','1',@rolid_0,'0','3.0','1','251gid','1','0','0','',''),(@pk_616_1944,@dbtid_4,'registerdate','0','0','10','0','1','0000-00-00 00:00:00','0','1',@rolid_0,'0','3.0','1','251registerdate','1','0','0','',''),(@pk_616_1945,@dbtid_4,'lastvisitdate','0','0','10','0','1','0000-00-00 00:00:00','0','1',@rolid_0,'0','3.0','1','251lastvisitdate','1','0','0','',''),(@pk_616_1946,@dbtid_4,'activation','0','0','14','0','1','','0','1',@rolid_0,'0','100.0','1','251activation','1','0','0','',''),(@pk_616_1947,@dbtid_4,'params','0','0','16','0','1','','0','1',@rolid_0,'0','100.0','1','251params','1','0','0','',''),(@pk_616_7408,@dbtid_1,'title','0','1','14','0','1','','2','0','0','0','255.0','1','title356','1','0','0','',''),(@pk_616_7409,@dbtid_1,'created','0','1','10','0','1','0000-00-00 00:00:00','3','0','0','0','0.0','1','created356','1','1','0','',''),(@pk_616_7410,@dbtid_1,'state','0','1','1','0','1','0','4','0','0','0','3.0','1','state356','1','0','0','',''),(@pk_616_7782,@dbtid_2,'title','0','1','14','0','1','','2','0','0','0','255.0','1','title595','1','0','0','',''),(@pk_616_7783,@dbtid_2,'name','0','1','14','0','1','','3','0','0','0','255.0','1','name595','1','0','0','',''),(@pk_616_7784,@dbtid_2,'image','0','1','16','0','1','','4','0','0','0','0.0','1','image595','1','0','0','',''),(@pk_616_7785,@dbtid_2,'scope','0','1','14','0','1','','5','0','0','0','50.0','1','scope595','1','0','0','',''),(@pk_616_7786,@dbtid_2,'image_position','0','1','14','0','1','','6','0','0','0','30.0','1','image_position595','1','0','0','',''),(@pk_616_7787,@dbtid_2,'description','0','1','16','0','1','','7','0','0','0','0.0','1','description595','1','0','0','',''),(@pk_616_7788,@dbtid_2,'published','0','1','1','0','1','0','8','0','0','0','11.0','1','published595','1','0','0','',''),(@pk_616_7789,@dbtid_2,'checked_out','0','1','4','1','1','0','9','0','0','0','11.0','1','check_out595','1','0','0','',''),(@pk_616_7790,@dbtid_2,'checked_out_time','0','1','10','0','1','','10','0','0','0','0.0','1','checked_out_time595','1','1','0','',''),(@pk_616_7791,@dbtid_2,'ordering','0','1','4','1','1','0','11','0','0','0','11.0','1','ordering595','1','0','0','',''),(@pk_616_7792,@dbtid_2,'access','0','1','1','1','1','0','12','0','0','0','3.0','1','access595','1','0','0','',''),(@pk_616_7793,@dbtid_2,'count','0','1','4','0','1','0','13','0','0','0','11.0','1','count595','1','0','0','',''),(@pk_616_7794,@dbtid_2,'params','0','1','16','0','1','','14','0','0','0','0.0','1','params595','1','0','0','',''),(@pk_616_7795,@dbtid_2,'state','0','1','1','0','1','0','15','0','0','0','3.0','1','state595','1','0','0','',''),(@pk_616_7797,@dbtid_1,'modified','0','1','4','1','1','0000-00-00 00:00:00','7','0','0','0','0.0','1','modified356','1','1','0','',''),(@pk_616_7799,@dbtid_1,'publish_up','0','1','10','0','1','0000-00-00 00:00:00','8','0','0','0','0.0','1','published_up356','1','1','0','',''),(@pk_616_7800,@dbtid_1,'publish_down','0','1','10','0','1','0000-00-00 00:00:00','9','0','0','0','0.0','1','publish_down356','1','1','0','',''),(@pk_616_7801,@dbtid_1,'mask','0','1','4','0','1','0','10','0','0','0','11.0','1','mask356','1','0','0','',''),(@pk_616_7802,@dbtid_1,'created_by','0','1','4','0','1','0','11','0','0','0','11.0','1','created_by356','1','0','0','',''),(@pk_616_7803,@dbtid_1,'created_by_alias','0','1','14','0','1','0','12','0','0','0','255.0','1','created_by_alias356','1','0','0','',''),(@pk_616_7804,@dbtid_1,'modified_by','0','1','4','0','1','0','13','0','0','0','11.0','1','modified_by356','1','0','0','',''),(@pk_616_7805,@dbtid_1,'checked_out','0','1','4','0','1','0','14','0','0','0','11.0','1','checked_out356','1','0','0','',''),(@pk_616_7806,@dbtid_1,'checked_out_time','0','1','10','0','1','0000-00-00 00:00:00','15','0','0','0','0.0','1','checked_out_time356','1','1','0','',''),(@pk_616_7807,@dbtid_1,'images','0','1','16','0','1','','16','0','0','0','0.0','1','images356','1','0','0','',''),(@pk_616_7808,@dbtid_1,'urls','0','1','16','0','1','','17','0','0','0','0.0','1','urls356','1','0','0','','');
REPLACE INTO `#__dataset_columns` (`dbcid`,`dbtid`,`name`,`pkey`,`checkval`,`type`,`attributes`,`mandatory`,`default`,`ordering`,`level`,`rolid`,`extra`,`size`,`export`,`namekey`,`core`,`columntype`,`noaudit`,`readable`,`fieldtype`) VALUES (@pk_616_7809,@dbtid_1,'attribs','0','1','16','0','1','','18','0','0','0','0.0','1','attribs356','1','0','0','',''),(@pk_616_7810,@dbtid_1,'version','0','1','4','1','1','1','19','0','0','0','11.0','1','version356','1','0','0','',''),(@pk_616_7811,@dbtid_1,'parentid','0','1','4','1','1','0','20','0','0','0','11.0','1','parentid356','1','0','0','',''),(@pk_616_7812,@dbtid_1,'ordering','0','1','4','1','1','0','21','0','0','0','11.0','1','ordering356','1','0','0','',''),(@pk_616_7813,@dbtid_1,'metakey','0','1','16','0','1','','22','0','0','0','0.0','1','metakey356','1','0','0','',''),(@pk_616_7814,@dbtid_1,'metadesc','0','1','16','0','1','','23','0','0','0','0.0','1','metadesc356','1','0','0','',''),(@pk_616_7815,@dbtid_1,'access','0','1','4','1','1','0','24','0','0','0','11.0','1','access356','1','0','0','',''),(@pk_616_7816,@dbtid_1,'metadata','0','1','16','0','1','','25','0','0','0','0.0','1','metadata356','1','0','0','',''),(@pk_616_7817,@dbtid_1,'hits','0','1','4','1','1','0','26','0','0','0','11.0','1','hits356','1','0','0','',''),(@pk_616_6039,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='model.trans' LIMIT 1),'name','0','0','14','0','1','','3','1',@rolid_0,'0','0.0','1','618name','1','0','0','',''),(@pk_616_3419,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='layout.mlinkstrans' LIMIT 1),'name','0','0','14','0','1','','3','1',@rolid_0,'0','255.0','1','485name','1','0','0','',''),(@pk_616_6797,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='layout.mlinkstrans' LIMIT 1),'description','0','1','16','0','1','','4','0','0','0','0.0','1','description485','1','0','0','',''),(@pk_616_10823,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='layout.mlinkstrans' LIMIT 1),'message','0','1','16','0','1','','5','0','0','0','0.0','1','message485','1','0','0','',''),(@pk_616_3408,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='layout.trans' LIMIT 1),'name','0','0','14','0','1','','3','1',@rolid_0,'0','255.0','1','481name','1','0','0','',''),(@pk_616_5579,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='layout.trans' LIMIT 1),'wname','0','0','14','0','1','','5','1',@rolid_0,'0','255.0','1','481wname','1','0','0','',''),(@pk_616_5580,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='layout.trans' LIMIT 1),'wdescription','0','0','16','0','1','','6','1',@rolid_0,'0','0.0','1','481wdescription','1','0','0','',''),(@pk_616_5473,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='theme.trans' LIMIT 1),'name','0','0','14','0','1','','3','1',@rolid_0,'0','255.0','1','559name','1','0','0','',''),(@pk_616_5474,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='theme.trans' LIMIT 1),'description','0','0','16','0','1','','4','1',@rolid_0,'0','0.0','1','559description','1','0','0','',''),(@pk_616_6794,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='eguillage.trans' LIMIT 1),'name','0','1','14','0','1','','3','0','0','0','255.0','1','name714','1','0','0','',''),(@pk_616_6795,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='eguillage.trans' LIMIT 1),'description','0','1','16','0','1','','4','0','0','0','0.0','1','description714','1','0','0','','');
SET @pk_5_1591 = ( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='joomla.node' LIMIT 1 );
UPDATE `#__extension_node`  SET `publish`='1',`folder`='joomla',`wid`=@pk_5_1591,`params`='',`type`='150',`name`='Joomla',`destination`='node',`trans`='1',`certify`='1',`namekey`='joomla.node',`version`='3803',`lversion`='3803',`pref`='0',`install`='',`core`='1',`showconfig`='0',`framework`='20' WHERE  `namekey`='joomla.node';
INSERT IGNORE INTO `#__extension_node` (`publish`,`folder`,`wid`,`params`,`type`,`name`,`destination`,`trans`,`certify`,`namekey`,`version`,`lversion`,`pref`,`install`,`core`,`showconfig`,`framework`) VALUES ('1','joomla',@pk_5_1591,'','150','Joomla','node','1','1','joomla.node','3803','3803','0','','1','0','20');

SET @pk_621_251 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.users' LIMIT 1 );
SET @pk_621_356 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.content' LIMIT 1 );
SET @pk_621_595 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.modules' LIMIT 1 );
SET @pk_621_997 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.extensions' LIMIT 1 );
SET @pk_621_998 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.menu' LIMIT 1 );
SET @pk_621_1017 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.aclgroups' LIMIT 1 );
SET @pk_621_1018 = ( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.aclgroupuser' LIMIT 1 );
UPDATE `#__model_node`  SET `sid`=@pk_621_251,`dbtid`=@dbtid_4,`path`='0',`namekey`='joomla.users',`folder`='users',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='',`fields`='0',`core`='1',`faicon`='',`pnamekey`='',`import`='0',`export`='0' WHERE  `namekey`='joomla.users';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_251,@dbtid_4,'0','joomla.users','users',@rolid_0,'0','1','','0','1','','','0','0');
UPDATE `#__model_node`  SET `sid`=@pk_621_356,`dbtid`=@dbtid_1,`path`='0',`namekey`='joomla.content',`folder`='content',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='',`fields`='0',`core`='1',`faicon`='',`pnamekey`='',`import`='0',`export`='0' WHERE  `namekey`='joomla.content';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_356,@dbtid_1,'0','joomla.content','content',@rolid_0,'0','1','','0','1','','','0','0');
UPDATE `#__model_node`  SET `sid`=@pk_621_595,`dbtid`=@dbtid_2,`path`='0',`namekey`='joomla.modules',`folder`='modules',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='',`fields`='0',`core`='1',`faicon`='',`pnamekey`='',`import`='0',`export`='0' WHERE  `namekey`='joomla.modules';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_595,@dbtid_2,'0','joomla.modules','modules',@rolid_0,'0','1','','0','1','','','0','0');
UPDATE `#__model_node`  SET `sid`=@pk_621_997,`dbtid`=( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='extensions' LIMIT 1),`path`='0',`namekey`='joomla.extensions',`folder`='joomla.extensions',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='autofld=1',`fields`='0',`core`='1',`faicon`='',`pnamekey`='',`import`='0',`export`='0' WHERE  `namekey`='joomla.extensions';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_997,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='extensions' LIMIT 1),'0','joomla.extensions','joomla.extensions',@rolid_0,'0','1','autofld=1','0','1','','','0','0');
UPDATE `#__model_node`  SET `sid`=@pk_621_998,`dbtid`=( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='menu' LIMIT 1),`path`='1',`namekey`='joomla.menu',`folder`='joomla.menu',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='cachedata=1\nautofld=1\nparentmap=parent_id\nskipdepth=1',`fields`='0',`core`='1',`faicon`='fa-link',`pnamekey`='',`import`='0',`export`='0' WHERE  `namekey`='joomla.menu';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_998,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='menu' LIMIT 1),'1','joomla.menu','joomla.menu',@rolid_0,'0','1','cachedata=1\nautofld=1\nparentmap=parent_id\nskipdepth=1','0','1','fa-link','','0','0');
UPDATE `#__model_node`  SET `sid`=@pk_621_1017,`dbtid`=( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='usergroups' LIMIT 1),`path`='0',`namekey`='joomla.aclgroups',`folder`='joomla.aclgroups',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='autofld=1',`fields`='0',`core`='1',`faicon`='',`pnamekey`='',`import`='0',`export`='0' WHERE  `namekey`='joomla.aclgroups';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_1017,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='usergroups' LIMIT 1),'0','joomla.aclgroups','joomla.aclgroups',@rolid_0,'0','1','autofld=1','0','1','','','0','0');
UPDATE `#__model_node`  SET `sid`=@pk_621_1018,`dbtid`=@dbtid_3,`path`='0',`namekey`='joomla.aclgroupuser',`folder`='joomla.aclgroupuser',`rolid`=@rolid_0,`level`='0',`publish`='1',`params`='autofld=1',`fields`='0',`core`='1',`faicon`='',`pnamekey`='',`import`='0',`export`='0' WHERE  `namekey`='joomla.aclgroupuser';
INSERT IGNORE INTO `#__model_node` (`sid`,`dbtid`,`path`,`namekey`,`folder`,`rolid`,`level`,`publish`,`params`,`fields`,`core`,`faicon`,`pnamekey`,`import`,`export`) VALUES (@pk_621_1018,@dbtid_3,'0','joomla.aclgroupuser','joomla.aclgroupuser',@rolid_0,'0','1','autofld=1','0','1','','','0','0');

SET @wid_5 = ( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='joomla.node' LIMIT 1);
INSERT IGNORE INTO `#__translation_en` (`text`,`auto`,`imac`) VALUES ('Article','2','1212761231MDKH');
INSERT IGNORE INTO `#__translation_en` (`text`,`auto`,`imac`) VALUES ('Joomla Modules','1','1298350305KUTV'),('Joomla 1.6 Extensions','1','1298350306NYBQ'),('Joomla 1.6 Menu','1','1298350306NYBR'),('Joomla User','1','1298350334ECWR'),('ACL Users Groups J16','1','1298350408NRRZ'),('ACL User Groups Map','1','1298618260QLYK'),('','1','1304525968YBL'),('Joomla - Joobi quickicons','1','1316671593INDP'),('Your tag must contain the ID of your module','1','1380142885PHVI'),('Example : {widget:module|id=12}','1','1380142885PHVJ'),('The module ID $ID could not be found','1','1380142885PHVK'),('Joomla 3.2 Responsive Admin Theme','1','1395437916MBPM'),('No item to list','1','1404396090RVSP'),('No order to list','1','1404396090RVSQ'),('A responsive template to integrate Joobi Apps in Joomla.','1','1416812868DUDF'),('Select a Site','1','1425439010GLOC'),('jApps','1','1429656248TBCH');
REPLACE INTO `#__dataset_constraintsitems` (`ctid`,`ordering`,`sort`,`dbcid`) VALUES (( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_content|main_joomla15' LIMIT 1),'1','0',( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='356id' LIMIT 1));
REPLACE INTO `#__dataset_constraintsitems` (`ctid`,`ordering`,`sort`,`dbcid`) VALUES (( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_modules|main_joomla15' LIMIT 1),'1','0',( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='id595' LIMIT 1)),(( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_extensions|main_joomla15' LIMIT 1),'1','0',( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='915extension_id' LIMIT 1)),(( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_menu|main_joomla15' LIMIT 1),'1','0',( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='916id' LIMIT 1)),(( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_user_usergroups|main_joomla16' LIMIT 1),'1','0',( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='921id' LIMIT 1)),(( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_user_usergroup_map|main_joomla16' LIMIT 1),'1','0',( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='922user_id' LIMIT 1)),(( SELECT `ctid` FROM `#__dataset_constraints`  WHERE `namekey`='PK_user_usergroup_map|main_joomla16' LIMIT 1),'2','0',( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='922group_id' LIMIT 1));
SET @pk_77_10903 = ( SELECT `fkid` FROM `#__dataset_foreign`  WHERE `ref_dbtid`=( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='categories' LIMIT 1) AND `feid`=( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='catid356' LIMIT 1) AND `dbtid`=@dbtid_1 LIMIT 1 );
SET @pk_77_10902 = ( SELECT `fkid` FROM `#__dataset_foreign`  WHERE `ref_dbtid`=( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='sections' LIMIT 1) AND `feid`=( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='sectionid356' LIMIT 1) AND `dbtid`=@dbtid_1 LIMIT 1 );
SET @pk_77_10907 = ( SELECT `fkid` FROM `#__dataset_foreign`  WHERE `ref_dbtid`=@dbtid_2 AND `feid`=( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='id595' LIMIT 1) AND `dbtid`=( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='modules' LIMIT 1) LIMIT 1 );
REPLACE INTO `#__dataset_foreign` (`map`,`publish`,`namekey`,`fkid`,`feid`,`ref_feid`,`onupdate`,`ondelete`,`map2`,`ref_dbtid`,`dbtid`,`ordering`) VALUES ('catid','1','FK_joomla_content_catid',@pk_77_10903,( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='catid356' LIMIT 1),( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='696id' LIMIT 1),'3','3','id',( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='categories' LIMIT 1),@dbtid_1,'2');
REPLACE INTO `#__dataset_foreign` (`map`,`publish`,`namekey`,`fkid`,`feid`,`ref_feid`,`onupdate`,`ondelete`,`map2`,`ref_dbtid`,`dbtid`,`ordering`) VALUES ('sectionid','1','FK_joomla_content_sectionid',@pk_77_10902,( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='sectionid356' LIMIT 1),( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='695id' LIMIT 1),'3','3','id',( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='sections' LIMIT 1),@dbtid_1,'1'),('id','1','595_id_595',@pk_77_10907,( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='id595' LIMIT 1),( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='id595' LIMIT 1),'1','1','id',@dbtid_2,( SELECT `dbtid` FROM `#__dataset_tables`  WHERE `namekey`='modules' LIMIT 1),'99');
REPLACE INTO `#__extension_dependency` (`wid`,`ref_wid`) VALUES (( SELECT `wid` FROM `#__extension_node`  WHERE `namekey`='api.node' LIMIT 1),@wid_5);
INSERT IGNORE INTO `#__theme_node` (`type`,`namekey`,`publish`,`core`,`wid`,`alias`,`filid`,`created`,`modified`,`folder`,`rolid`,`uid`,`ordering`,`framework`) VALUES ('2','joomla30.admin.theme','1','1',@wid_5,'Joomla 3.2 Responsive Admin Theme','0','1394145311','1420564205','joomla30',@rolid_0,'0','26','20');
INSERT IGNORE INTO `#__translation_reference` (`wid`,`load`,`imac`) VALUES (@wid_5,'0','1380142885PHVI');
INSERT IGNORE INTO `#__translation_reference` (`wid`,`load`,`imac`) VALUES (@wid_5,'0','1380142885PHVJ'),(@wid_5,'0','1380142885PHVK'),(@wid_5,'1','1404396090RVSP'),(@wid_5,'1','1404396090RVSQ'),(@wid_5,'1','1425439010GLOC');
REPLACE INTO `#__extension_info` (`wid`,`author`,`userversion`,`userlversion`) VALUES ((@wid_5),'','','');
SET @dbcid_6 = ( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='618name' LIMIT 1);
INSERT IGNORE INTO `#__translation_populate` (`dbcid`,`eid`,`imac`,`wid`) VALUES (@dbcid_6,( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.users' LIMIT 1),'1298350334ECWR',@wid_5);
INSERT IGNORE INTO `#__translation_populate` (`dbcid`,`eid`,`imac`,`wid`) VALUES (@dbcid_6,( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.content' LIMIT 1),'1212761231MDKH',@wid_5),(@dbcid_6,( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.modules' LIMIT 1),'1298350305KUTV',@wid_5),(@dbcid_6,( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.extensions' LIMIT 1),'1298350306NYBQ',@wid_5),(@dbcid_6,( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.menu' LIMIT 1),'1298350306NYBR',@wid_5),(@dbcid_6,( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.aclgroups' LIMIT 1),'1298350408NRRZ',@wid_5),(@dbcid_6,( SELECT `sid` FROM `#__model_node`  WHERE `namekey`='joomla.aclgroupuser' LIMIT 1),'1298618260QLYK',@wid_5),(( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='559name' LIMIT 1),( SELECT `tmid` FROM `#__theme_node`  WHERE `namekey`='joomla30.admin.theme' LIMIT 1),'1395437916MBPM',@wid_5),(( SELECT `dbcid` FROM `#__dataset_columns`  WHERE `namekey`='559description' LIMIT 1),( SELECT `tmid` FROM `#__theme_node`  WHERE `namekey`='joomla30.admin.theme' LIMIT 1),'1416812868DUDF',@wid_5);