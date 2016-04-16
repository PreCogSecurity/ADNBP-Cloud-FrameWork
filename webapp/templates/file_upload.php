<?php
	$this->loadClass("io/Bucket");
	$_bucketPath = 'adnbp-cloud-framwork-public/upload';
	$bucket = new Bucket($_bucketPath);	
	
	if(!isset($_REQUEST['direct']))
		$_url = $bucket->getUploadUrl();
	$bucket->manageUploadFiles();	
?>
<html>
<body>
URL to upload: <?=$_url;?>
<li>move_uploaded_file: <?=(function_exists('move_uploaded_file'))?'ok':'false'?></li>
<pre>
	Buckets vars:
	<?=print_r($bucket->vars,true)?>
</pre>
<form action="<?=$_url;?>" method="post" enctype="multipart/form-data">
  <?php if(!isset($_REQUEST['direct'])){ ?>
  <input type='hidden' name='direct' value=''>
  <?php } ?>
  Send these files:<p/>
  <input name="uploaded_files[]" type="file" multiple="multiple"/><p/>
  <input type="submit" value="Send files" />
</form>
<pre>
	Files in Bucket:
	<?php
	 //$bucket->deleAllFiles();
	 //$bucket->rmdir('/');
	 
	 ?>
	<?=print_r($bucket->fastScan(''),true);?>
</pre>
<pre>
    <?=print_r($bucket->uploadedFiles ,true)?>
    <?=print_r($bucket->errorMsg,true)?>
</pre>
</body>
</html>	