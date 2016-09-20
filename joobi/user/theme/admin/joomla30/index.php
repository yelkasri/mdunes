<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */
defined('JOOBI_SECURE') or die('J....');
?>
<?php
$isPopUp = WGlobals::get( 'is_popup', false, 'global' );
$showHeader = ( $isPopUp ) ? false : true;
$viewBodyClass = $this->getContent('viewBodyClass');
WPage::addCSS( '#element-box{padding-top:0;}' );	$topClass = 'PG_' . str_replace( '-', '_', WGlobals::get( 'controller' ) . '_' . WGlobals::get( 'task' ) );
?>
<div id="pageWrapper" class="<?php echo $topClass; ?> clearfix">
<?php if( $showHeader ):
	$tabS = $this->getContent('tabs');
	$devModuleHTML = WGlobals::get( 'devModuleHTML', '', 'global' );
	if ( !empty($tabS) || !empty($devModuleHTML) ) :
	?>
<div id="pageHeader" class="clearfix">
	<div class="col-md-12">
	<?php if( $tabS ) : echo $tabS; endif; ?>
	</div>
</div>
<?php
	endif;
	endif;
?>
	<div class="viewBody<?php  echo $viewBodyClass; ?> clearfix">
		<?php if($breadS = $this->getContent('breadcrumbs') ) : ?>
		<div id="pathway">
			<?php  echo $breadS; ?>
		</div>
		<?php endif; ?>
		<?php if( $beforeHeaderS = $this->getContent('beforeHeader') ) echo $beforeHeaderS; ?>
		<?php if( $messageS = $this->message() ) : ?>
		<div id="message" class="clearfix">
		<?php  echo $messageS; ?>
		</div>
		<?php endif; ?>
		<div class="panel panel-default">
			<?php if( $headerMenuS = $this->getContent('headerMenu') ) : ?>
			<div id="panelHeader" class="panel-heading clearfix">
				<div id="toolbarBox">
					<?php echo $headerMenuS; ?>
				</div>
			</div>
			<?php endif; ?>
			<div class="panel-body">
				<?php if( $infoS = $this->getContent('information') ) : echo $infoS; endif; ?>
				<?php if( $wizS = $this->getContent('wizard') ) : echo $wizS; endif; ?>
				<div id="helpArea" style="display:none;" class="clearfix"></div>
				<?php if( $applicationS = $this->getContent('application') ) echo $applicationS; ?>
				<?php if( $bottomMenuS = $this->getContent('bottomMenu') ) : ?>
				<div class="panel-footer">
					<div class="clearfix viewBottom">
						<div class="bottomButtons">
							<?php echo $bottomMenuS; ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<?php if( $legendS = $this->getContent('legend') ) : ?>
				<div class="clearfix">
					<?php echo $legendS; ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if( $menuS = $this->getContent('footer') ) echo '<div class="clearfix">' . $menuS . '</div>'; ?>
		<?php if( $debugS = $this->getContent('debugTrace') ) : ?>
		<div class="panel-group clearfix" id="debugTrace">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<h3 class="panel-title">
						<a data-toggle="collapse" data-parent="#debugTrace" href="#debugCollapseOne">Debug Traces</a>
					</h3>
				</div>
				<div id="debugCollapseOne" class="panel-collapse collapse in">
				<div class="panel-body">
				<?php echo $debugS; ?>
				</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<?php if( $configS = $this->getContent('configHelper') ) : ?>
		<div class="panel-group clearfix" id="configHelper">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">
						<a data-toggle="collapse" data-parent="configHelper" href="#configCollapseOne"><?php echo $this->getContent('configHelperTitle'); ?></a>
					</h3>
				</div>
				<div id="#configCollapseOne" class="panel-collapse collapse in">
				<div class="panel-body">
				<?php echo $configS; ?>
				</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<?php if( $viewS = $this->getContent('viewDetails') ) : ?>
		<div class="panel-group clearfix" id="viewDetails">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">
						<a data-toggle="collapse" data-parent="viewDetails" href="#viewCollapseOne"><?php echo $this->getContent('viewDetailsTitle'); ?></a>
					</h3>
				</div>
				<div id="#viewCollapseOne" class="panel-collapse collapse in">
				<div class="panel-body">
				<?php echo $viewS; ?>
				</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>