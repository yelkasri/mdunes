<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('behavior.core');
//JHtml::_('formbehavior.chosen', 'select');

// Get the user object.
$user = JFactory::getUser();

// Check if user is allowed to add/edit based on tags permissions.
// Do we really have to make it so people can see unpublished tags???
$canEdit = $user->authorise('core.edit', 'com_tags');
$canCreate = $user->authorise('core.create', 'com_tags');
$canEditState = $user->authorise('core.edit.state', 'com_tags');
$items = $this->items;
$n = count($this->items);

?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php if ($this->params->get('show_headings') || $this->params->get('filter_field') || $this->params->get('show_pagination_limit')) : ?>
  <hr>
  <fieldset class="filters">
    <div class="row">
		  <?php if ($this->params->get('filter_field')) :?>
      <div class="col-md-6">
    		<label class="filter-search-lbl sr-only" for="filter-search">
    			<?php echo JText::_('COM_TAGS_TITLE_FILTER_LABEL') . '&#160;'; ?>
    		</label>
  			<div class="input-group">
  				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" title="<?php echo JText::_('COM_TAGS_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>" />
          <span class="input-group-btn">
            <button class="btn btn-primary" type="submit">
              <span class="glyphicon glyphicon-search"></span>
            </button>
          </span>
        </div>
      </div>
		  <?php endif; ?>
		  <?php if ($this->params->get('show_pagination_limit')) : ?>
			<div class="col-md-6 text-right">
				<label for="limit">
					<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		  <?php endif; ?>
    </div>
		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="limitstart" value="" />
		<input type="hidden" name="task" value="" />
	</fieldset>
  <hr>
	<?php endif; ?>

	<?php if ($this->items == false || $n == 0) : ?>
		<p class="alert alert-info"> <?php echo JText::_('COM_TAGS_NO_ITEMS'); ?></p>
	<?php else : ?>

	<ul class="category list-unstyled">
		<?php foreach ($items as $i => $item) : ?>
			<?php if ($item->core_state == 0) : ?>
				<li class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
			<?php else: ?>
				<li class="cat-list-row<?php echo $i % 2; ?> clearfix" >
				<h3>
					<a href="<?php echo JRoute::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
						<span class="glyphicon glyphicon-tag"></span> <?php echo $this->escape($item->core_title); ?>
					</a>
				</h3>
			<?php endif; ?>
			<?php echo $item->event->afterDisplayTitle; ?>
			<?php $images  = json_decode($item->core_images);?>
			<?php if ($this->params->get('tag_list_show_item_image', 1) == 1 && !empty($images->image_intro)) :?>
				<a href="<?php echo JRoute::_(TagsHelperRoute::getItemRoute($item->content_item_id, $item->core_alias, $item->core_catid, $item->core_language, $item->type_alias, $item->router)); ?>">
				<img class="thumbnail" src="<?php echo htmlspecialchars($images->image_intro);?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>">
				</a>
			<?php endif; ?>
			<?php if ($this->params->get('tag_list_show_item_description', 1)) : ?>
				<?php echo $item->event->beforeDisplayContent; ?>
				<div class="tag-body">
					<?php echo JHtml::_('string.truncate', $item->core_body, $this->params->get('tag_list_item_maximum_characters')); ?>
				</div>
				<?php echo $item->event->afterDisplayContent; ?>
			<?php endif; ?>
				</li>
		<?php endforeach; ?>
	</ul>

	<?php endif; ?>
</form>
