<!--
Copyright: Gianluca Costa & Andrea de Franceschi 2007-2010, http://www.xplico.org
 Version: MPL 1.1/GPL 2.0/LGPL 2.1
-->
<div class="generic">
<div class="search">



<center>
<?php echo $this->Form->create('Search',array( 'url' => array('controller' => 'ftps', 'action' => 'index')));
      echo $this->Form->input('label', array('type'=>'text','size' => '40', 'label' => __('Search:'), 'default' => $srchd));
     echo $this->Form->end(__('Go'));?>
</center>
</div>

<table id="messagelist" summary="Message list" cellspacing="0">
<tr>
	<th class="date"><?php echo $this->Paginator->sort('capture_date', __('Date')); ?></th>
	<th class="from"><?php echo $this->Paginator->sort('url', __('Url')); ?></th>
    <th class="username"><?php echo $this->Paginator->sort('username', __('User')); ?></th>
	<th class="number"><?php echo $this->Paginator->sort('download_num', __('Download')); ?></th>
	<th class="number"><?php echo $this->Paginator->sort('upload_num', __('Upload')); ?></th>
</tr>
<?php foreach ($ftps as $ftp): ?>
<?php if ($ftp['Ftp']['first_visualization_user_id']) : ?>
  <tr>
	<td><?php echo $ftp['Ftp']['capture_date']; ?></td>
	<td><?php echo $this->Html->link($ftp['Ftp']['url'],'/ftps/view/' . $ftp['Ftp']['id']); ?></td>
        <td><?php echo $ftp['Ftp']['username']; ?></td>
	<td><?php echo $ftp['Ftp']['download_num']; ?></td>
	<td><?php echo $ftp['Ftp']['upload_num']; ?></td>
  </tr>
<?php else : ?>
 <tr>
	<td><b><?php echo $ftp['Ftp']['capture_date']; ?></b></td>
	<td><b><?php echo $this->Html->link($ftp['Ftp']['url'],'/ftps/view/' . $ftp['Ftp']['id']); ?></b></td>
        <td><b><?php echo $ftp['Ftp']['username']; ?></b></td>
	<td><b><?php echo $ftp['Ftp']['download_num']; ?></b></td>
	<td><b><?php echo $ftp['Ftp']['upload_num']; ?></b></td>
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
