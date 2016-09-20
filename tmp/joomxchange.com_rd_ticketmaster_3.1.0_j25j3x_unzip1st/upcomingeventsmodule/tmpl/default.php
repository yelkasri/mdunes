<?php 

defined( '_JEXEC' ) or die( 'Restricted access' ); 

## Adding the AJAX part to the dropdowns
$document = JFactory::getDocument();
$document->addStyleSheet( 'modules/mod_ticketmasterupcoming/assets/mod_ticketmasterupcoming.css' );

?>



<div class="tm-module<?php echo $moduleclass_sfx ?>">
  
<ul class="tm-module<?php echo $ul_sfx ?>">

<?php 
 	for ($i = 0, $n = count($list); $i < $n; $i++ ){

	
	## Give give $row the this->item[$i]
	$row        = $list[$i];
	$alias 		= JFilterOutput::stringURLSafe($row->upcomingeventname);
	$link 		= JRoute::_('index.php?option=com_ticketmaster&view=event&id='.$row->ticketid.':'.$alias);

	?>

    <li>
      
      <p class="h3">
			<?php if ($date_position == 1) { echo date ($date_format, strtotime($row->ticketdate)); } ?>
            
			<?php if ($title == 1){ 
                        echo '<a href="'. $link.'">'.$row->upcomingeventname.'</a>'; 
                   }elseif ($title == 2) { 
                        echo '<a href="'. $link.'">'.$row->eventname.'</a>';
                   }else{
                        echo '<a href="'. $link.'">'.$row->ticketname.'</a>';
            }?>
            
            <?php if ($date_position == 2) { echo date ($date_format, strtotime($row->ticketdate)); } ?>       
      </p>
      
      <p>
		  <?php if ($date_position == 3) { echo date ($date_format, strtotime($row->ticketdate)); } ?>
          <?php echo $row->venue; ?>
          <?php if ($date_position == 4) { echo date ($date_format, strtotime($row->ticketdate)); } ?>
      </p>
      
    </li>

<?php } ?>    
    
</ul>
  
</div>