<div class="floating-menu">
<span class="accordionButton1"><b>go to section</b></span>
<div class="accordionContent1">
<?php
$provurl = base_url().'providers/provider_detail/idp/'.$idpid;
?>
<a href="<?php echo $provurl ; ?>#basic">Basic</a>
<a href="<?php echo $provurl ; ?>#federation">Federations</a>
<a href="<?php echo $provurl ; ?>#technical">Technical Information</a>
<a href="<?php echo $provurl ; ?>#metadata">Metadata</a>
<a href="<?php echo $provurl ; ?>#arp">ARP</a>
<?php
echo '<a href="'.base_url().'reports/idp_matrix/show/'.$idpid.'/idp">Attributes overview</a>';
?>
<a href="<?php echo $provurl ; ?>#attrs">Supported Attributes</a>
<?php
echo '<a href="'.base_url().'geolocation/show/'.$idpid.'/idp">Geolocation</a>';
echo '<a href="'.base_url().'manage/logos/provider/idp/'.$idpid.'">Logos</a>';
?>
</div>

</div>
