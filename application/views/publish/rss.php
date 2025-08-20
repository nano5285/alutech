<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0">
	<channel>    
		<title><?php echo xml_convert($title); ?> RSS</title>
		<language><?php echo $language; ?></language> 
<?php if($content): ?>
		<description><?php echo xml_convert($content); ?></description>
<?php endif; ?>
		<link><?php echo $link; ?></link>
<?php foreach($query as $row): ?>
		<item>	
			<title><?php echo xml_convert($row->title); ?></title>
			<guid><?php echo $row->permalink; ?></guid>
			<?php $cnt = str_replace('="/acms/uploads/', '="' . base_url() . $this->config->item('upload_folder'), $row->content); ?>
			<description><![CDATA[<?php echo $cnt; ?>]]></description>
			<pubDate><?php echo date('r', strtotime($row->date)); ?></pubDate>
		</item>
<?php endforeach; ?>
	</channel>
</rss>