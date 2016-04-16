    <form name='CloudServiceReportingFilters' method='get' >
    <article>
    <!-- Widget ID (each widget will need unique ID)-->
    <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-CloudServiceReportingFilters"
    data-widget-editbutton="false"
    data-widget-deletebutton="false"
    data-widget-fullscreenbutton="false"
    >
    <!-- widget options:
    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

    data-widget-colorbutton="false"
    data-widget-editbutton="false"
    data-widget-togglebutton="false"

    data-widget-custombutton="false"
    data-widget-collapsed="true"
    data-widget-sortable="false"

    -->
    <header>
        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
        <h2>Filters</h2>
    </header>

    <!-- widget div-->
    <div>


    <!-- widget content -->
    <div class="widget-body">

        <div class="form-inline input-group-sm" role='form'>
        <?php foreach ($this->filters as $filter=>$filteroptions) {
            if( isset($filteroptions['breakline'])) echo "<br/>";
            if( $filteroptions['type']=="select" ) {
                ?><select  class="form-control  input-xs" name='filter_<?=htmlentities($filter)?>' onchange='this.form.submit();'>
                <?php foreach ($filteroptions['data'] as $key=>$value) {?>
                    <option value="<?=htmlentities($key)?>" <?=(( $key==$filteroptions['value'])?"selected":"")?>><?=htmlentities($value,ENT_SUBSTITUTE)?></option>
                <?php } ?>
                </select><?php
            } elseif( $filteroptions['type']=="checkbox" ) {
                ?><select  class="form-control  input-xs" name='filter_<?=htmlentities($filter)?>' onchange='this.form.submit();'>
                    <?php foreach ($filteroptions['data'] as $key=>$value) {?>
                        <option value="<?=htmlentities($key)?>" <?=(( $key==$filteroptions['value'])?"selected":"")?>><?=htmlentities($value,ENT_SUBSTITUTE)?></option>
                    <?php } ?>
                </select><?php
            } elseif( $filteroptions['type']=="input" ) {
                ?><input type="text"  class="form-control  input-xs"
                   name='filter_<?=htmlentities($filter)?>'
                   placeholder="<?=htmlentities($filteroptions['placeholder'])?>"
                   value="<?=htmlentities($filteroptions['value'])?>"
                   <?php if(isset($filteroptions['size'])) echo 'size="'.$filteroptions['size'].'"';?>
                /><?php
            }
        } ?>
                <button class="btn btn-default btn-primary" type="submit">
                    <i class="fa fa-search"></i> Filter
                </button>
        </div>

    </div>
    <!-- end widget content -->

    </div>
    <!-- end widget div -->

    </div>
    <!-- end widget -->

    </article>
    <!-- WIDGET END -->
    </form>