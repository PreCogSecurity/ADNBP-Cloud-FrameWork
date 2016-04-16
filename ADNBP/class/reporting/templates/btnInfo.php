<a href="<?=$data->link?>" target="<?=$data->target?>" class="btn btn-<?=($data->type)?$data->type:"default"?> btn-<?=($data->size)?$data->size:"mg"?>">
<?php if($data->glyphicon) { ?>
    <span class="btn-label">
<i class="glyphicon glyphicon-<?=$data->glyphicon?>"></i>
</span>
<?php } ?>
    <strong><?=$data->title?></strong> <i><?=$data->subtitle?></i>
</a>
