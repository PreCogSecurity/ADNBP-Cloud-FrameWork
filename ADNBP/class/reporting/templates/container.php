<?php 
// Open Container
if(!$container) {?>                
<article>
    <!-- Widget ID (each widget will need unique ID)-->
    <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-<?=$key?>" 
        data-widget-editbutton="false" 
        data-widget-deletebutton="false">
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
            <span class="widget-icon"> <i class="fa fa-<?=(strlen($data->ico))?$data->ico:'table'?>"></i> </span>
            <h2><?=htmlentities($data->title)?></h2>
    
        </header>
    
        <!-- widget div-->
        <div>
    
            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->
    
            </div>
            <!-- end widget edit box -->
    
            <!-- widget content -->
            <div class="widget-body">
<?php } 

// Close container
elseif($container) {?>                
             </div>
            <!-- end widget content -->
    
        </div>
        <!-- end widget div -->
    
    </div>
    <!-- end widget -->

</article>
<!-- WIDGET END -->
<?php } ?>
          