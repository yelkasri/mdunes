<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<hr>
<h4><?php echo JText::_('COM_CONTENT_MORE_ARTICLES'); ?></h4>
<ol class="nav nav-pills nav-stacked small">
<?php foreach ($this->link_items as &$item) : ?>
	<li>
    <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
		<?php echo $item->title; ?>
    </a>
	</li>
<?php endforeach; ?>
</ol>

