<h1>ADNBP Cloud FrameWork Portal Configuration</h1>
<?php if(is_array($_config)) foreach ($_config as $key => $value) {?>
	<h2><?=$value->title?></h2>
	<h3><?=$value->subtitle?></h3>
	<form method="post">
	<input type='hidden' name='command' value='<?=htmlentities($value->command)?>'>
	<blockquote>
	<b><?=$value->description?></b><br/>
	<?php if(is_array($value->vars)) foreach ($value->vars as $key => $value2) {?>
		<?php if($value2->var) {?>
		// <?=$value2->description?>
		<br/>
		<small>
		$this->setConf('<?=$value2->var?>',
			'<input type='text' name='<?=$value2->var?>' value='<?=$this->getConf($value2->var)?>'>'); 
			 // (Default if empty: <?=($value2->default)?$value2->default:"''"?>).</small>
			 
		<?php } elseif($value2->field) {?>
		// <?=$value2->description?>
		<br/>
		<small>
		<?=$value2->field?>: <input type='<?=($value2->type)?$value2->type:"text"?>' name='<?=$value2->field?>' value='<?=$_REQUEST[$value2->field]?>'> 
		<?php } elseif($value2->file) {?>
		// <?=$value2->description?>
		<br/>
		<textarea width=100% rows=10>Content</textarea>
		<?php } ?>
	<?php } ?>
	</blockquote>
	<input type='submit' value='save' onclick="if(!confirm('<?=addslashes($value->alert)?>')) return false;">
	</form>
<?php } ?>