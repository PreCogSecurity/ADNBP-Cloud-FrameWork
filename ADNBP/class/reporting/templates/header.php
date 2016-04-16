<div class="row">
    <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
        <h1 class="page-title txt-color-blueDark"><i class="fa-fw fa fa-<?=(strlen($data->ico))?$data->ico:'home'?>"></i> <?=htmlentities($data->title)?> <span>> <?=htmlentities($data->subtitle)?> <a href='?reload'><i class="fa fa-refresh"></i></a></span></h1>
    </div>
    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        <ul id="sparks" class="">
            <?php if(is_array($data->sparks)) foreach ($data->sparks as $key => $value) {?>
            <li class="sparks-info">
                <h5> <?=htmlentities($value->title)?> 
                    <span class="txt-color-<?=($value->color)?htmlentities($value->color):'blue'?>">
                    <?=($value->ico)?'<i class="fa fa-'.$value->ico.'"></i>&nbsp;':''?>
                    <?=htmlentities($value->subtitle)?></span></h5>
                    <?php if(strlen($value->data)) {?>
                        <div class="sparkline txt-color-<?=($value->color)?htmlentities($value->color):'blue'?> hidden-mobile hidden-md hidden-sm">
                        <?=htmlentities($value->data)?>
                        </div>
                     <?php } ?>
            </li>    
            <?php } ?>
            
        </ul>
    </div>
</div>
