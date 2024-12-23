<!--
Copyright: Gianluca Costa & Andrea de Franceschi 2007-2010, http://www.xplico.org
 Version: MPL 1.1/GPL 2.0/LGPL 2.1
-->
<script language="JavaScript">
    function popupVetrina(whatopen) {
      newWindow = window.open(whatopen, 'popup_vetrina', 'width=520,height=550,scrollbars=yes,toolbar=no,resizable=yes,menubar=no');
      return false;
    }
</script>

<div class="generic">
<div class="search">
  <div class="src_second">
        <br />
	<?php echo $this->Form->create('Search', array( 'url' => array('controller' => 'arps', 'action' => 'index')));
	      echo $this->Form->input('Search', array( 'type'=>'text','size' => '30', 'label'=>__('Search:'), 'default' => $srchd));      
	 echo $this->Form->end(__('Go'));?>
   </div>
  <div class="cline"> </div>
</div>
<br>
<!-- to-do : download these data in XLS format (or ODS) -->
<table id="messagelist" summary="Message list" cellspacing="0" table-layout: auto>
<tr>
	<th class="date"><?php echo $this->Paginator->sort('capture_date', __('Date')); ?></th>
	<th><?php echo $this->Paginator->sort('mac', __('MAC')); ?></th>
	<th><?php echo $this->Paginator->sort('ip', __('IP')); ?></th>
	<th class="info"><?php echo __('Info'); ?></th>
</tr>
<?php foreach ($arp_msgs as $arp_msg): ?>
<?php if ($arp_msg['Arp']['first_visualization_user_id']) : ?>
  <tr>
	<td><?php echo $arp_msg['Arp']['capture_date']; ?></td>
	<td><?php echo $arp_msg['Arp']['mac']; ?></td>
	<td><?php echo $arp_msg['Arp']['ip']; ?></td>
        <td class="pinfo"><a href="#" onclick="popupVetrina('/arps/info/<?php echo $arp_msg['Arp']['id']; ?>','scrollbar=auto'); return false"><?php echo __('info.xml'); ?></a></td>

  </tr>
<?php else : ?>
 <tr>
	<td><b><?php echo $arp_msg['Arp']['capture_date']; ?></b></td>
        <td><b><?php echo $arp_msg['Arp']['mac']; ?></b></td>
	<td><b><?php echo $arp_msg['Arp']['ip']; ?></b></td>
        <td class="pinfo"><b><a href="#" onclick="popupVetrina('/arps/info/<?php echo $arp_msg['Arp']['id']; ?>','scrollbar=auto'); return false"><?php echo __('info.xml'); ?></a></b></td>
  </tr>
<?php endif ?>
<?php endforeach; ?>
</table>

<table id="listpage" summary="Message list" cellspacing="0">
<tr>
	<th class="next"><?php echo $this->Paginator->prev(__('Previous'), array(), null, array('class'=>'disabled')); ?></th>
       	<th><?php echo $this->Paginator->numbers(); echo '<br/>'.$this->Paginator->counter(); ?></th>
	<th class="next"><?php echo $this->Paginator->next(__('Next'), array(), null, array('class' => 'disabled')); ?></th>
</tr>
</table>
</div>
