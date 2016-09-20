<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Files_Scriptsjwplayer_type extends WTypes {
public $scriptsjwplayer = array(
'flv1' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',
'mp3' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',
'm4a' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',		
'mp4' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',
'm4v' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',
'webm' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',
'aac' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',
'ogg' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',
'oga' => '<div style="clear:both;"></div>
<div id="{ID}">{TEXTLOADING}</div>
<script type="text/javascript">
jwplayer("{ID}").setup({
file:"{FILESOURCE}",
autostart:"{AUTOPLAY}",
width:"{WIDTH}",
height:"{HEIGHT}"
});
</script>
',
"flv" => '
<object id="{ID}" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" style="width:{WIDTH};height:{HEIGHT};" type="application/x-shockwave-flash" title="{NAME}" data="{URLINC}main/mediaplayer/allplayer.swf">
<param name="src" value="{FILESOURCE}" />
<param name="quality" value="best" />
<param name="allowfullscreen" value="true" />
<param name="scale" value="showall" />
<param name="allowscriptaccess" value="always" />
<param name="bgcolor" value="{BACKGROUND}" />
<param name="wmode" value="opaque" />
<param name="autostart" value="false" />
<param name="controller" value="true" />
<param name="flashvars" value="file={FILESOURCE}&autostart={AUTOPLAY}" />
<embed src="{FILESOURCE}" width="{WIDTH}" height="{HEIGHT}" name="{ID}" CONTROLLER="false" bgcolor = "{BACKGROUND}" quality ="Best" allowfullscreen="true" wmode = "{TRANSPARENCY}" allowscriptaccess="always"></embed>
</object>
',
"swf" => "
<object id=\"{ID}\" type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"{FILESOURCE}\" title=\"{NAME}\">
<param name=\"movie\" value=\"{FILESOURCE}\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
<param name=\"play\" value=\"{AUTOPLAY}\" />
<param name=\"loop\" value=\"false\" />
<embed src=\"{FILESOURCE}\" width=\"{WIDTH}\" height=\"{HEIGHT}\" name=\"{ID}\" CONTROLLER=\"false\" bgcolor = \"{BACKGROUND}\" quality =\"Best\" allowfullscreen=\"true\" wmode = \"{TRANSPARENCY}\" allowscriptaccess=\"always\"></embed>
</object>
",
"wmv" => "
<span id=\"jmediaID_{FILEID}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" title=\"{NAME}\"></span>
<script type=\"text/javascript\">
var cnt = document.getElementById('jmediaID_{FILEID}');
var src = '{URLINC}main/mediaplayer/wmvplayer/wmvplayer.xaml';
var cfg = {
file:'{FILESOURCE}',
width:'{WIDTH}',
height:'{HEIGHT}',
autostart:'{AUTOPLAY}'
};
var ply = new jeroenwijering.Player(cnt,src,cfg);
</script>
",
"wma" => "
<span id=\"avID_{SOURCEID}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" title=\"{NAME}\"></span>
<script type=\"text/javascript\">
var cnt = document.getElementById(\"avID_{SOURCEID}\");
var src = '{URLINC}main/mediaplayer/wmvplayer/wmvplayer.xaml';
var cfg = {
file:'{FILESOURCE}',
width:'{WIDTH}',
height:'{HEIGHT}',
autostart:'{AUTOPLAY}',
usefullscreen:'false'
};
var ply = new jeroenwijering.Player(cnt,src,cfg);
</script>
",
"mov" => "
<script type=\"text/javascript\">
QT_WriteOBJECT_XHTML('{FILESOURCE}', '{WIDTH}', '{HEIGHT}', '', 'autoplay', '{AUTOPLAY}', 'bgcolor', '{BACKGROUNDQT}', 'scale', 'aspect');
</script>
",
"3gp" => "
<script type=\"text/javascript\">
QT_WriteOBJECT_XHTML('{FILESOURCE}', '{WIDTH}', '{HEIGHT}', '', 'autoplay', '{AUTOPLAY}', 'bgcolor', '{BACKGROUNDQT}', 'scale', 'aspect');
</script>
",
"divx" => "
<object id=\"{ID}\" type=\"video/divx\" title=\"{NAME}\" data=\"{FILESOURCE}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\">
<param name=\"type\" value=\"video/divx\" />
<param name=\"src\" value=\"{FILESOURCE}\" />
<param name=\"data\" value=\"{FILESOURCE}\" />
<param name=\"codebase\" value=\"{FILESOURCE}\" />
<param name=\"url\" value=\"{FILESOURCE}\" />
<param name=\"mode\" value=\"full\" />
<param name=\"pluginspage\" value=\"http://go.divx.com/plugin/download/\" />
<param name=\"allowContextMenu\" value=\"true\" />
<param name=\"autoPlay\" value=\"{AUTOPLAY}\" />
<param name=\"minVersion\" value=\"1.0.0\" />
<param name=\"custommode\" value=\"none\" />
<p>No video? Get the DivX browser plug-in for <a href=\"http://download.divx.com/player/DivXWebPlayerInstaller.exe\">Windows</a> or <a href=\"http://download.divx.com/player/DivXWebPlayer.dmg\">Mac</a></p>
<embed src=\"{FILESOURCE}\" width=\"{WIDTH}\" height=\"{HEIGHT}\" name=\"{ID}\" CONTROLLER=\"false\" bgcolor = \"{BACKGROUND}\" quality =\"Best\" allowfullscreen=\"true\" wmode = \"{TRANSPARENCY}\" allowscriptaccess=\"always\"></embed>
</object>
",
"youtube" => "<iframe width=\"{WIDTH}\" height={HEIGHT}\" src=\"//www.youtube-nocookie.com/embed/{MEDIAID}?rel=0\" frameborder=\"0\" allowfullscreen></iframe>
",
"espn" => "
<object id=\"{ID}\" type=\"application/x-shockwave-flash\" title=\"{NAME}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://sports.espn.go.com/broadband/player.swf?mediaId={MEDIAID}\">
<param name=\"movie\" value=\"http://sports.espn.go.com/broadband/player.swf?mediaId={MEDIAID}\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
<param name=\"allowfullscreen\" value=\"true\" />
<param name=\"allowscriptaccess\" value=\"always\" />
<embed src=\"http://sports.espn.go.com/broadband/player.swf?mediaId={MEDIAID}\"
 width=\"{WIDTH}\"
 height=\"{HEIGHT}\"
 name=\"{ID}\"
 CONTROLLER=\"false\"
 bgcolor = \"{BACKGROUND}\"
 quality =\"Best\"
 allowfullscreen=\"true\"
 wmode = \"{TRANSPARENCY}\"
 allowscriptaccess=\"always\"> </embed>
</object>
",
"gametrailers" => "
<object id=\"{ID}\" type=\"application/x-shockwave-flash\" title=\"{NAME}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.gametrailers.com/remote_wrap.php?mid={MEDIAID}\">
<param name=\"movie\" value=\"http://www.gametrailers.com/remote_wrap.php?mid={MEDIAID}\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
<param name=\"allowfullscreen\" value=\"true\" />
<param name=\"allowscriptaccess\" value=\"sameDomain\" />
<embed src=\"http://www.gametrailers.com/remote_wrap.php?mid={MEDIAID}\"
 width=\"{WIDTH}\"
 height=\"{HEIGHT}\"
 name=\"{ID}\"
 CONTROLLER=\"false\"
 bgcolor = \"{BACKGROUND}\"
 quality =\"Best\"
 allowfullscreen=\"true\"
 wmode = \"{TRANSPARENCY}\"
 allowscriptaccess=\"always\">
</embed>
</object>
",
"livevideo" => "
<object id=\"{ID}\" type=\"application/x-shockwave-flash\" title=\"{NAME}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.livevideo.com/flvplayer/embed/{MEDIAID}\">
<param name=\"movie\" value=\"http://www.livevideo.com/flvplayer/embed/{MEDIAID}\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
<param name=\"allowfullscreen\" value=\"true\" />
<param name=\"allowscriptaccess\" value=\"always\" />
<embed src=\"http://www.livevideo.com/flvplayer/embed/{MEDIAID}\"
 width=\"{WIDTH}\"
 height=\"{HEIGHT}\"
 name=\"{ID}\"
 CONTROLLER=\"false\"
 bgcolor = \"{BACKGROUND}\"
 quality =\"Best\"
 allowfullscreen=\"true\"
 wmode = \"{TRANSPARENCY}\"
 allowscriptaccess=\"always\">
</embed>
</object>
",
"myvideo" => "
<object id=\"{ID}\" type=\"application/x-shockwave-flash\" title=\"{NAME}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.myvideo.de/movie/{MEDIAID}\">
<param name=\"movie\" value=\"http://www.myvideo.de/movie/{MEDIAID}\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
<param name=\"allowfullscreen\" value=\"true\" />
<param name=\"allowscriptaccess\" value=\"always\" />
<embed src=\"http://www.myvideo.de/movie/{MEDIAID}\"
 width=\"{WIDTH}\"
 height=\"{HEIGHT}\"
 name=\"{ID}\"
 CONTROLLER=\"false\"
 bgcolor = \"{BACKGROUND}\"
 quality =\"Best\"
 allowfullscreen=\"true\"
 wmode = \"{TRANSPARENCY}\"
 allowscriptaccess=\"always\">
</embed>
</object>
",
"vimeo" => "
<iframe src=\"//player.vimeo.com/video/{MEDIAID}?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff&amp;\" width=\"{WIDTH}\" height=\"{HEIGHT}\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
",
"yahoovideo" => "
<object id=\"{ID}\" type=\"application/x-shockwave-flash\" title=\"{NAME}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf\">
<param name=\"movie\" value=\"http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf\" />
<param name=\"quality\" value=\"high\" />
<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
<param name=\"allowfullscreen\" value=\"true\" />
<param name=\"allowscriptaccess\" value=\"always\" />
<param name=\"flashvars\" value=\"{SOURCEURL}\" />
</object>
"
);
}