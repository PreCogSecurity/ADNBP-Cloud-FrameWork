<?php if(!$simple) {?>
<article >
    <!-- Widget ID (each widget will need unique ID)-->
    <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-<?=$key?>-<?=md5($data->title)?>" data-widget-editbutton="false">
        <!-- widget options:
        usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
    
        data-widget-colorbutton="false"
        data-widget-editbutton="false"
        data-widget-togglebutton="false"
        data-widget-deletebutton="false"
        data-widget-fullscreenbutton="false"
        data-widget-custombutton="false"
        data-widget-collapsed="true"
        data-widget-sortable="false"
    
        -->

        <header>
            <span class="widget-icon"> <i class="fa fa-table"></i> </span>
<?php } ?>
            <h2><?=htmlentities($data->title)?></h2>
<?php if(!$simple) {?>
        </header>

        <!-- widget div-->
        <div>
    
            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->
    
            </div>
            <!-- end widget edit box -->
<?php } ?>
    
            <!-- widget content -->
            <div class="widget-body">
                <p><?=htmlentities($data->subtitle)?></p>
                <div class="table-responsive">
                
                    <table id="report-<?=$key?>" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <?php if(is_array($data->columns)) foreach ($data->columns as $key => $value) {
                                        if(is_string($value)) $value = array('title'=>$value);
                                    ?>
                                <th><?=($value['link'])?'<a href="'.$value['link'].'" target="'.$value['target'].'">':''?><?=htmlentities($value['title'])?><?=($value['link'])?'</a>':''?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($data->rows)) foreach ($data->rows as $key => $value) {?>
                            <tr>
                                <?php if(is_array($value)) foreach ($value as $key2 => $cell) {
                                        if(!is_array($cell)) $cell = array('value'=>$cell);

	                                	$align = $cell['align'];
                                        $colspan = $cell['colspan'];
                                        $rowspan = $cell['rowspan'];
                                        $nowrap = $cell['nowrap'];
										if(!$align && (isset($cell['currency']))) $align='right';
                                        if(is_array($cell['value'])) $cell['value'] = implode(', ',$cell['value']);
                                	?>
                                <td<?=($align)?' class="text-'.$align.'"':''?><?=($nowrap)?' nowrap':''?><?=($colspan)?' colspan="'.$colspan.'"':''?><?=($rowspan)?' rowspan="'.$rowspan.'"':''?><?=($cell['anchor'])?' id="'.htmlentities($cell['anchor']).'"':''?>><?php
                                	if(strlen($cell['currency']))
                                        $cell['value'] = (strlen($cell['value']))?htmlentities($adnbp->numberFormat($cell['value'],2)).'&nbsp;'.htmlentities($cell['currency']):'';
                                    else
									    $cell['value'] =htmlentities($cell['value']);
									if(isset($cell['bold'])) $cell['value'] = '<strong>'.$cell['value'].'</strong>';
									if(isset($cell['small'])) $cell['value'] = '<small>'.$cell['value'].'</small>';
                                    if(isset($cell['link'])) $cell['value'] = '<a href="'.htmlentities($cell['link']).'" target="'.$cell['target'].'">'.$cell['value'].'</a>';
									echo (strlen($cell['value']))?$cell['value']:'&nbsp';
									
                                ?></td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    
                </div>
                
            </div>
            <!-- end widget content -->
<?php if(!$simple) {?>
    
        </div>
        <!-- end widget div -->
    
    </div>
    <!-- end widget -->

</article>
<?php } ?>
<!-- WIDGET END -->
