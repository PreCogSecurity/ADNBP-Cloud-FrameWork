<div class="well well-sm well-light">
    <h4 class="txt-color-blueLight"><?=htmlentities($data->title)?> <span class="semi-bold"><?=htmlentities($data->subtitle)?></span> </h4>
    <br>
    <div class="sparkline txt-color-<?=($data->color)?:'blueLight'?> text-center"
         data-sparkline-type="bar"
         data-sparkline-width="96%"
         data-sparkline-barwidth="<?=($data->barwidth)?:'11'?>"
         data-sparkline-barspacing = "<?=($data->barspacing)?:'5'?>"
         data-sparkline-height="<?=($data->height)?:'80'?>px">
        <?=htmlentities($data->values)?>
    </div>
</div>