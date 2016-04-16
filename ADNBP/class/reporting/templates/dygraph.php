<?php
/**
 * User: adrianmm
 * Date: 04/12/15
 * Time: 07:06
 */
?>

<div id="dygraph_<?=$controlVars->reportNumber.'_'.md5($data->title)?>" style="width:100%; height:300px;"></div>

<!-- DYGRAPH -->
<?php if(!$controlVars->dygraphJS){?><script src="<?=$this->super->getConf('urlPrefix')?>/adnbp-public/static/v1/sa151/js/plugin/dygraphs/dygraph-combined.min.js"></script><?php $controlVars->dypgraphJS = true;} ?>
<script type="text/javascript">
    <?php if(is_array($data->rows)) {?>
    function dygraph_<?=$controlVars->reportNumber.'_'.md5($data->title)?>_data(){
        return"<?=$data->xType?>,<?=implode(",",$data->rows[1])?>\n<?php
            foreach ($data->rows[2]  as $j=>$col) {
                echo $col;
                foreach ($data->rows[1]  as $i=>$row) {
                    if(!strlen($data->rows[0][$i][$j]))
                        $data->rows[0][$i][$j]=0;
                    echo ','.$data->rows[0][$i][$j];
                }
                echo '\n';
            }?>"
    }
    <?php } ?>

    $(document).ready(function() {

        /*
         * PAGE RELATED SCRIPTS
         */

        g<?=$controlVars->reportNumber?> = new Dygraph(document.getElementById("dygraph_<?=$controlVars->reportNumber.'_'.md5($data->title)?>"), dygraph_<?=$controlVars->reportNumber.'_'.md5($data->title)?>_data, {
            customBars : false,
            title : '<?=$data->title?>',
            ylabel : '<?=$data->yLabel?>',
            legend : 'always',
            labelsDivStyles : {
                'textAlign' : 'right'
            },
            showRangeSelector : true
        });



    })
</script>