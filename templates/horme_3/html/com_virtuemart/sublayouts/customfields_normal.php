<?php
/**
* sublayout products
*
* @package	VirtueMart
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
* @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
*/

defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];
$position = $viewData['position'];
$customTitle = isset($viewData['customTitle'])? $viewData['customTitle']: false;;
if(isset($viewData['class'])){
	$class = $viewData['class'];
} else {
	$class = 'product-fields';
}

if (!empty($product->customfieldsSorted[$position])) {
	?>
	<div class="<?php echo $class?> clearfix small margin-top-15">
		<?php
		if($customTitle and isset($product->customfieldsSorted[$position][0])){
			$field = $product->customfieldsSorted[$position][0]; ?>
		<div class="product-fields-title-wrapper">
      <h6 class="product-fields-title">
        <?php echo vmText::_ ($field->custom_title) ?>
  			<?php if ($field->custom_tip) {
          echo '<span class="glyphicon glyphicon-exclamation-sign hasTooltip" title="' . vmText::_ ($field->custom_title) . '"></span>';
  			} ?>
      </h6>
		</div> <?php
		}
		$custom_title = null;
		foreach ($product->customfieldsSorted[$position] as $field) {
			if ( $field->is_hidden ) //OSP http://forum.virtuemart.net/index.php?topic=99320.0
			continue;
			?>
      <div class="product-field product-field-type-<?php echo $field->field_type ?> panel panel-default">
				<?php if (!$customTitle and $field->custom_title != $custom_title and $field->show_title) { ?>
					<div class="product-fields-title-wrapper panel-heading">
            <!--<h6 class="product-fields-title">-->
              <strong><?php echo vmText::_ ($field->custom_title) ?></strong>
  						<?php if ($field->custom_tip) {
                echo '<span class="glyphicon glyphicon-exclamation-sign hasTooltip small" title="' . vmText::_($field->custom_tip) . '"></span>';
  						} ?>
            <!--</h6> -->
          </div>
				<?php }
				if (!empty($field->display)){
				?>
        <div class="product-field-display panel-body"><?php echo $field->display ?></div>
        <?php
				}
				if (!empty($field->custom_desc)){
				?>
        <div class="product-field-desc panel-footer small"><?php echo vmText::_($field->custom_desc) ?></div>
        <?php
				}
				?>
			</div>
		<?php
			$custom_title = $field->custom_title;
		} ?>
	</div>
<?php
} ?>