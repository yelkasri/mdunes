<?php

/**
 *
 * Modify user form view, User info
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Spyros Petrakis
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2015 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: edit_address_userfields.php 8763 2015-02-27 09:06:01Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Status Of Delimiter
$closeDelimiter = false;
$openTable = true;
$hiddenFields = '';
$countfields = count($this->userFields['fields']);
$task = vRequest::getCmd('task');
$addrtype = vRequest::getCmd('addrtype');
$counter= 0;
// Set the col width depending on checkout settings
if (VmConfig::get ('oncheckout_show_register', 1) && !VmConfig::get ('oncheckout_only_registered', 0)) {
	$col_md = 'col-md-6';
} elseif (!VmConfig::get ('oncheckout_only_registered', 0)) {
	$col_md = 'col-md-12';
} else {
 	$col_md = 'col-md-6';
}

// if ($task != 'editaddress' && $addrtype != 'ST') {
if ($task != 'addST') {
	echo '<div class="row">';
}
// Output: Userfields
foreach($this->userFields['fields'] as $field) {
  $counter++;
	if($field['type'] == 'delimiter') {

		// For Every New Delimiter
		// We need to close the previous
		// table and delimiter
		if($closeDelimiter) { ?>
    </div>
		<?php
			$closeDelimiter = false;
		} //else {
			?>
      <div class="<?php echo $col_md; ?>">
			<h4 class="userfields_info page-header"><?php echo $field['title'] ?></h4>

			<?php
			$closeDelimiter = true;
			$openTable = true;
		//}

	} elseif ($field['hidden'] == true) {

		// We collect all hidden fields
		// and output them at the end
		$hiddenFields .= $field['formcode'] . "\n";

	} else {

		// If we have a new delimiter
		// we have to start a new table
		if($openTable) {
			$openTable = false;
		}

		$descr = empty($field['description'])? $field['title']:$field['description'];
		// Output: Userfields
		?>
			<div class="form-group" title="<?php echo strip_tags($descr) ?>">
				<label class="<?php echo $field['name'] ?>" for="<?php echo $field['name'] ?>_field">
					<?php echo $field['title'] . ($field['required'] ? ' *' : '') ?>
				</label>
        <?php if (VmConfig::get ('jchosen')) { ?>
        <div>
				<?php echo $field['formcode'] ?>
        </div>
        <?php } else {
        	echo $field['formcode'];
        } ?>
      </div>
	<?php
	}

  if ($counter == $countfields && $task != 'addST') {
    echo '</div>';
  }

}

// At the end we have to close the current
// table and delimiter ?>


<?php // Output: Hidden Fields
echo $hiddenFields;
if ($task != 'addST') {
 	echo '</div>';
}
?>
