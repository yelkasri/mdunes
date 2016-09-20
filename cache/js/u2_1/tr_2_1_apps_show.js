var tour={
id:"apps_show",
steps:[
{
target:"toolbarBox"
,placement:"bottom"
,title:"Install / Update instructions"
,content:"<ul><li>Before installing / updating any application on the website, we recommend that you do a backup of the website files and database. This way, you will always be able to revert the changes made during the install / update in case of an error.</li><li>Setting the website in maintenance mode is also recommended for updates.</li></ul>"
,width:"500px"
,showCTAButton:"1"
,ctaLabel:"Finish"
,onCTA:function(){joobi.tr('index.php?option=com_japps&controller=output&task=tour&tract=end&yid=4&tmpl=component&type=raw');}
}
,{
target:"cf13"
,placement:"right"
,title:"Current version"
,content:"The version currently installed on your website<br>"
,showCTAButton:"1"
,ctaLabel:"Finish"
,onCTA:function(){joobi.tr('index.php?option=com_japps&controller=output&task=tour&tract=end&yid=4&tmpl=component&type=raw');}
}
,{
target:"cf14"
,placement:"right"
,title:"Latest version"
,content:"The latest version available<br>"
,showCTAButton:"1"
,ctaLabel:"Finish"
,onCTA:function(){joobi.tr('index.php?option=com_japps&controller=output&task=tour&tract=end&yid=4&tmpl=component&type=raw');}
}
]
}
hopscotch.startTour(tour);