<!--
Copyright: Gianluca Costa & Andrea de Franceschi 2007-2010, http://www.xplico.org
 Version: MPL 1.1/GPL 2.0/LGPL 2.1
-->
<div class="generic">
<div class="search">


<center>
<?php echo $this->Form->create('Telnets', array( 'url' => array('controller' => 'telnets', 'action' => 'index')));
      echo $this->Form->input('search',  array( 'type'=>'text','size' => '40', 'label' => __('Search'), 'default' => $srchd));
echo $this->Form->end(__('Go'));
?>
</center>
</div>
 <table id="messagelist" summary="Message list" cellspacing="0">
 <tr>
	<th class="date"><?php echo $this->Paginator->sort('capture_date', __('Date')); ?></th>
	<th class="subject"><?php echo $this->Paginator->sort('hostname', __('Host')); ?></th>
	<th class="from"><?php echo $this->Paginator->sort('username', __('Username')); ?></th>
	<th class="size"><?php echo $this->Paginator->sort('cmd_size', __('Size')); ?></th>
 </tr>
 <?php foreach ($telnets as $telnet): ?>
 <?php if ($telnet['Telnet']['first_visualization_user_id']) : ?>
  <tr>
	<td><?php echo $telnet['Telnet']['capture_date']; ?></td>
	<td><?php echo $this->Html->link($telnet['Telnet']['hostname'],'/telnets/view/' . $telnet['Telnet']['id']); ?></td>
        <td><?php echo $telnet['Telnet']['username']; ?></td>
	<td><?php echo $telnet['Telnet']['cmd_size']; ?></td>
  </tr>
 <?php else : ?>
  <tr>
	<td><b><?php echo $telnet['Telnet']['capture_date']; ?></b></td>
	<td><b><?php echo $this->Html->link($telnet['Telnet']['hostname'],'/telnets/view/' . $telnet['Telnet']['id']); ?></b></td>
        <td><b><?php echo $telnet['Telnet']['username']; ?></b></td>
	<td><b><?php echo $telnet['Telnet']['cmd_size']; ?></b></td>
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
