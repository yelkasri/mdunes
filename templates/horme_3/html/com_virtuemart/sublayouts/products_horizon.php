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
JHtml::_('bootstrap.tooltip');
$products_per_row = $viewData['products_per_row'];
$currency = $viewData['currency'];
$showRating = $viewData['showRating'];
$verticalseparator = " vertical-separator";
echo shopFunctionsF::renderVmSubLayout('askrecomjs');

$ItemidStr = '';
$Itemid = shopFunctionsF::getLastVisitedItemId();
if(!empty($Itemid)){
	$ItemidStr = '&Itemid='.$Itemid;
}

foreach ($viewData['products'] as $type => $products ) {

	$rowsHeight = shopFunctionsF::calculateProductRowsHeights($products,$currency,$products_per_row);

	if(!empty($type) and count($products)>0){
		$productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>
	<div class="<?php echo $type ?>-view">
	  <h3 class="page-header"><?php echo $productTitle ?></h3>
	<?php // Start the Output
  }

	// Calculating Products Per Row
	$cellwidth = ' col-md-'. floor ( 12 / $products_per_row ) . ' col-sm-'. floor ( 12 / $products_per_row ) . ' span' . floor ( 12 / $products_per_row );

	$BrowseTotalProducts = count($products);

	$col = 1;
	$nb = 1;
	$row = 1;

	foreach ( $products as $product ) {

		// Show the horizontal seperator
		if ($col == 1 && $nb > $products_per_row) { ?>
		<div class="horizontal-separator"></div>
		<?php }

		// this is an indicator wether a row needs to be opened or not
		if ($col == 1) { ?>
		<div class="vm-row">
		<?php }

		// Show the vertical seperator
		if ($nb == $products_per_row or $nb % $products_per_row == 0) {
			$show_vertical_separator = ' ';
		} else {
			$show_vertical_separator = $verticalseparator;
		}

    // Show Products
		?>
			<div class="product vm-col<?php echo ' vm-col-' . $products_per_row; ?> col-md-12 product-horizon">
				<div class="thumbnail row">
		      <div class="col-md-3" data-mh="list">
		  			<div class="vm-product-rating-container row">
		  				<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
		  				if ( VmConfig::get ('display_stock', 1)) { ?>
		          <div class="text-right col-md-4 pull-right">
		  					<span class="vmicon vm2-<?php echo $product->stock->stock_level ?> glyphicon glyphicon-signal hasTooltip" title="<?php echo $product->stock->stock_tip ?>"></span>
		          </div>
		  				<?php }
		  				//echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
		  				?>
		  			</div>

		        <hr class="product-horizon-hr">

		  			<div class="vm-product-media-container" style="min-height: <?php echo VmConfig::get ('img_height'); ?>px">
		    			<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
		    				<?php
		    				echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
		    				?>
		    			</a>
		  			</div>
		      </div>
		      <div class="col-md-6" data-mh="list">
		        <h4 class="vm-product-title page-header product-name"><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h4>
		        <?php // Product Short Description
		        if (!empty($product->product_s_desc)) { ?>
		        <p class="product_s_desc text-muted small" data-mh="sdesc-<?php echo $type ?>">
		          <?php echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 60, ' ...') ?>
		        </p>
		        <?php } ?>
		  			<div class="vm-details-button small">
		  				<?php // Product Details Button
		  				$link = empty($product->link)? $product->canonical:$product->link;
		  				echo JHtml::link($link,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS') . ' &hellip;', array ('title' => $product->product_name, 'class' => 'product-details margin-top-15' ) );
		  				?>
		  			</div>
		      </div>
		      <div class="col-md-3" data-mh="list">
		  			<div class="vm3pr- small vm-price-wrapper">
						<?php	echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency)); ?>
		  			</div>
		  			<div class="small product-horizon-addtocart">
		          <?php	echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row], 'position' => array('ontop', 'addtocart'))); ?>
		  			</div>
		      </div>
				</div>
			</div>

		<?php
	    $nb ++;

    // Do we need to close the current row now?
    if ($col == $products_per_row || $nb>$BrowseTotalProducts) { ?>
	  </div>
	<?php
   	$col = 1;
		$row++;
    } else {
      $col ++;
    }
  }

  if(!empty($type)and count($products)>0){
    // Do we need a final closing row tag?
    //if ($col != 1) {
  ?>
  </div>
	<?php
  }
}