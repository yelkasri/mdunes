<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Theme_Install_class extends WClasses {
public function installDefaultTheme($type,$theme,$themeName,$description=''){
$themeM=WModel::get('theme');
$themeM->tmid=null;
$themeM->setChild('themetrans','name',$themeName );
$themeM->setChild('themetrans','description',$description );
$themeTypefolderT=WType::get('theme.typefolder');
$destfolder=$themeTypefolderT->getName($type );
$themeM->namekey=$destfolder.'.'.$theme;
$themeM->folder=$theme;
$themeM->publish=1;
$themeM->core=1;
$themeM->availability=1;
$themeM->type=$type;
$themeM->ordering=1;
$themeM->alias=$themeName.' theme';
$themeM->created=time();
$themeM->modified=time();
$themeM->save();
return true;
}
}