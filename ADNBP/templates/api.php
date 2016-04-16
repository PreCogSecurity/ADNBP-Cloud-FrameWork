<?php 
$gIndex=0;
foreach ($api as $service => $serviceContent) 
{
?><div class="jumbotron">
    <h1><?=htmlentities($serviceContent['service'][$service]['title'])?></h1>
    <small><?=htmlentities($host)?></small>
    <p><?=$serviceContent['service'][$service]['description']?></p>
</div>
    <?php
        foreach ($api[$service][$service.'_group'] as $group => $groupContent) {
            $gIndex++;
            
    ?>
     <ul class="nav nav-tabs" role="tablist">
      <li class="active"><a href="#<?=$service.'_'.$group?>" role="tab" data-toggle="tab"><big><?=$groupContent['title']?></big></a></li>
      <?php if(is_array($api[$service][$service.'_'.$group.'_subgroup'])) foreach ($api[$service][$service.'_'.$group.'_subgroup'] as $subgroup => $subgroupContent) { ?>
      <li><a href="#<?=$service.'_'.$group.'_'.$subgroup?>" role="tab" data-toggle="tab"><small><?=$subgroupContent['title']?></small></a></li>
      <?php } ?>
    </ul>
   <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane active" id="<?=$service.'_'.$group?>">  
          <div class="panel panel-default">
          <div class="panel-body">
          <?=$groupContent['description']?>
          </div>
          </div>
      </div>
      <?php if(is_array($api[$service][$service.'_'.$group.'_subgroup'])) 
              foreach ($api[$service][$service.'_'.$group.'_subgroup'] as $subgroup => $subgroupContent) { ?>
      
      <div class="tab-pane" id="<?=$service.'_'.$group.'_'.$subgroup?>">
      <div class="tab-pane active" id="<?=$service.'_'.$group.'_'.$subgroup?>">  
          <div class="panel panel-default">
          <div class="panel-body">
          <?php 
          if(is_array($api[$service][$service.'_'.$group.'_'.$subgroup.'_methods'])) 
             foreach ($api[$service][$service.'_'.$group.'_'.$subgroup.'_methods'] as $method => $methodContent) { ?>
                 
            
            <b></b>
            <table class="table table-bordered  table-striped">
            <tr>
                <td width='20%' nowrap="yes">&nbsp;<?=$methodContent['method']?>:&nbsp;<a href='<?=$host.$methodContent['url']?>' target='blank'><?=$subgroupContent['call']?><span class="glyphicon glyphicon-link"></span></a></td>
                <td valign="bottom"><?=$methodContent[title]?>
                </td>
                <!-- onclick='window.location="/CloudFrameWorkService<?=(strlen($value2['example']))?$value2['example']:$value2['api']?>";'  -->
                </tr>
                <tr>
                    <td colspan="3">
                     <pre><?=htmlentities($methodContent['description'])?></pre>
                        
                    </td>
            </tr>
            </table>
          <?php } ?>
          </div>
          </div>
      </div>          
      </div>
      <?php } ?>

    </div>    
    <?php } ?>
    <?php } ?>



<?php  if(false) {?>
    

     
    <?php if(true) {?>
    <div class="panel-group" id="accordion">
    <?php if(is_array($docServices)) foreach ($docServices as $key => $value) {?>
        <div class="panel panel-default">
        <div class="panel-heading">
        <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$key?>"><?=(isset($value['title']))?$value['title']:''?></a>
        </h4>
        <p><?=(isset($value['description']))?$value['description']:''?></p>
        </div>
        <div id="collapse<?=$key?>" class="panel-collapse collapse ">
        <div class="panel-body">
            
            
            
            <?php if(isset($value['api']))  foreach ($value['api'] as $index => $value2) {?>
            <b><?=$value2['title']?></b>
            <table class="table table-bordered  table-striped">
            <tr>
                <?php if(isset($value2['GET'])) {?>
                <td width='10%'><button onclick='window.location="/CloudFrameWorkService<?=(strlen($value2['example']))?$value2['example']:$value2['api']?>";' type="button" class="btn btn-primary" data-toggle="collapse" data-target="#<?=$key?>_api_<?=$index?>">GET: <?=$value2['api']?></button></td>
                <td valign="bottom"><?=$value2['GET']?>
                <span class="glyphicon glyphicon-link"></span></td>
                <!-- onclick='window.location="/CloudFrameWorkService<?=(strlen($value2['example']))?$value2['example']:$value2['api']?>";'  -->
                </tr>
                <tr>
                    <td colspan="3">
                        <div  class="collapse in" id='<?=$key?>_api_<?=$index?>'>
                        <table class="table table-bordered ">
                        <?php if(isset($value2['params'])) {?>
                            <tr>
                                <th> Name</th><th> Description</th><th> Details</th>
                            </tr>
                            <?php if(is_array($value2['params'])) foreach ($value2['params'] as $index2 => $value3) {?>
                            <tr>
                                <td><?=$value3['paramName']?></td><td><?=$value3['paramDescription']?></td><td><?=$value3['paramDetails']?></td>
                            </tr>
                            <?php }?>
                        <?php }?>
                            <tr>
                                <td colspan="3"><span class="label label-default">Response:</span>
                                    <pre><?=$value2['GET-response-header']?></pre>
                                    <pre><?=$value2['GET-response-content']?></pre>
                                </td>
                            </tr>
                        </table>
                        </div>
                    </td>
                <?php }?>
            </tr>
            </table>
            <?php }?>
       
       
       
       </div>
       </div>
    </div>   
    <?php } ?>
    </div>   
    <?php } ?>
    
<?php } ?>