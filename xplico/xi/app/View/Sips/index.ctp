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

<center>
<?php echo $this->Form->create('Search',array( 'url' => array('controller' => 'sips', 'action' => 'index')));
      echo $this->Form->input('label', array('type'=>'text','size' => '40', 'label'=> __('Search:'), 'default' => $srchd));
      echo $this->Form->end(__('Go'));?>
</center>
</div>

<table id="messagelist" summary="Message list" cellspacing="0">
<tr>
	<th class="date"><?php echo $this->Paginator->sort('capture_date', __('Date')); ?></th>
	<th class="from"><?php echo $this->Paginator->sort('from_addr', __('From')); ?></th>
    <th class="to"><?php echo $this->Paginator->sort('to_addr', __('To')); ?></th>
	<th class="number"><?php echo $this->Paginator->sort('duration', __('Duration')); ?></th>
    <th class="date"><?php echo __('Info'); ?></th>
</tr>
<?php foreach ($sips as $sip): ?>
<?php
 /* time in HH:MM:SS */
 $h = (int)($sip['Sip']['duration']/3600);
 $m = (int)(($sip['Sip']['duration']-3600*$h)/60);
 $s = $sip['Sip']['duration'] - 3600*$h - 60*$m;
 $hms=''.$h.':'.$m.':'.$s;
?>
<?php if ($sip['Sip']['first_visualization_user_id']) : ?>
  <tr>
	<td><?php echo $sip['Sip']['capture_date']; ?></td>
        <td><?php echo str_replace('>', '&gt;', str_replace('<', '&lt;', $sip['Sip']['from_addr'])); ?> </td>
        <td><?php echo str_replace('>', '&gt;', str_replace('<', '&lt;', $sip['Sip']['to_addr'])); ?></td>
	<td><?php echo $this->Html->link($hms,'/sips/view/' . $sip['Sip']['id']); ?></td>
        <td class="pinfo"><a href="#" onclick="popupVetrina('/sips/info/<?php echo $sip['Sip']['id']; ?>','scrollbar=auto'); return false"><?php echo __('info.xml'); ?></a><div class="ipcap"><?php echo $this->Html->link('pcap', 'pcap/' . $sip['Sip']['id']); ?></div></td>
  </tr>
<?php else : ?>
 <tr>
	<td><b><?php echo $sip['Sip']['capture_date']; ?></b></td>
        <td><b><?php echo str_replace('>', '&gt;', str_replace('<', '&lt;', $sip['Sip']['from_addr'])); ?></b></td>
        <td><b><?php echo str_replace('>', '&gt;', str_replace('<', '&lt;', $sip['Sip']['to_addr'])); ?></b></td>
	<td><b><?php echo $this->Html->link($hms,'/sips/view/' . $sip['Sip']['id']); ?></b></td>
        <td class="pinfo"><b><a href="#" onclick="popupVetrina('/sips/info/<?php echo $sip['Sip']['id']; ?>','scrollbar=auto'); return false"><?php echo __('info.xml'); ?></a></b><div class="ipcap"><b><?php echo $this->Html->link('pcap', 'pcap/' . $sip['Sip']['id']); ?></b></div></td>
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
