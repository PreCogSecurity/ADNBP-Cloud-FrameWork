<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <!-- Widget ID (each widget will need unique ID)-->
    <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-<?=$key?>" data-widget-editbutton="false">
        <header>
            <span class="widget-icon"> <i class="fa fa-table"></i> </span>
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
            <div class="widget-body ">
                <p><?=htmlentities($data->subtitle)?></p>
                <div id="bar-chart-<?=$key?>" class="chart"></div>
            </div>
            <!-- end widget content -->
    
        </div>
        <!-- end widget div -->
    
    </div>
    <!-- end widget -->


</article>
<!-- WIDGET END -->
<script>
    var idMorris = 'bar-chart-<?=$key?>';

</script>
