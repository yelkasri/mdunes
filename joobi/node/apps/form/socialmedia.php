<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
class Apps_CoreSocialmedia_form extends WForms_default {
function create(){
$this->content='<table style="text-align: left;" border="0" cellpadding="10" cellspacing="2">
  <tbody>
<tr>
<td style="width:150px; padding:30; vertical-align: top; text-align: left;">
<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FJoobiWiki&width&layout=box_count&action=like&show_faces=false&share=true&height=65&appId=760256454058012" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:65px;" allowTransparency="true"></iframe>
</td>
<td style="width:150px; padding:30; height:80px; vertical-align: middle; text-align: left;">
<a href="https://twitter.com/ijoobi" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @ijoobi</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\'https://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>
</td>
<td style="width:150px; padding:30; vertical-align: top; text-align: left;">
<script src="https://apis.google.com/js/platform.js" async defer></script>
<div class="g-follow" data-annotation="vertical-bubble" data-height="24" data-href="https://plus.google.com/u/0/b/106079931847128580279/106079931847128580279" data-rel="publisher"></div>
</td>
</tr>
  </tbody>
</table>';
return true;
}
function show(){
return $this->create();
}
}