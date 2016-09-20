<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Including fallback code for the placeholder attribute in the search field.
JHtml::_('jquery.framework');
//JHtml::_('script', 'system/html5fallback.js', false, true);
?>
<div class="search<?php echo $moduleclass_sfx ?>">
	<form action="<?php echo JRoute::_('index.php');?>" method="post" >
		<?php
			$output = '<input name="searchword" id="mod-search-searchword" maxlength="' . $maxlength . '"  class="inputbox search-query form-control" type="search" size="' . $width . '"';
			$output .= ' placeholder="' . $text . '" />';

			if ($button) {
			  $btn_output = ' <span class="input-group-btn"><button class="button btn btn-primary" onclick="this.form.searchword.focus();"><span class="glyphicon glyphicon-search"></span></button></span>';

				switch ($button_pos) :
					case 'top' :
						$output = $btn_output . '<br />' . $output;
						break;

					case 'bottom' :
						$output .= '<br />' . $btn_output;
						break;

					case 'right' :
						$output .= $btn_output;
						break;

					case 'left' :
					default :
						$output = $btn_output . $output;
						break;
				endswitch;

		 }

		?>
    <?php echo '<label for="mod-search-searchword" class="element-invisible">' . $label . '</label> '; ?>
    <div class="input-group">
     <?php echo $output; ?>
    </div>
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="option" value="com_search" />
		<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
	</form>
</div>
