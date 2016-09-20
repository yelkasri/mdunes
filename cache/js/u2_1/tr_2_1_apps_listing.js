var tour={
id:"apps_listing",
steps:[
{
target:"toolbarBox"
,placement:"bottom"
,title:"Install / Upgrade Applications"
,content:"<br><span style=\"font-style: italic; text-decoration: underline; font-weight: bold;\">About Update and Install:</span><br><ul><li>First click on <span style=\"font-weight: bold;\">Check</span> to get an updated list of all Applications available.</li><li>To install or update an application click on the <span style=\"font-weight: bold;\">Name</span> of the application you want to install.  You will be redirected to the main page of the application.</li><li>To change the grade of an application, click on the link in the <span style=\"font-weight: bold;\">Grades</span> column.</li></ul><span style=\"text-decoration: underline; font-style: italic; font-weight: bold;\">About License:</span><br><ul><li>If you have a token you can simply click on <span style=\"font-weight: bold;\">Enter token</span> and follow instruction.</li><li>You can use the button <span style=\"font-weight: bold;\">Refresh</span> to refresh all your licenses. It will re-actualize all your licenses.<br></li></ul>"
,width:"500px"
,showCTAButton:"1"
,ctaLabel:"Finish"
,onCTA:function(){joobi.tr('index.php?option=com_japps&controller=output&task=tour&tract=end&yid=3&tmpl=component&type=raw');}
}
]
}
hopscotch.startTour(tour);