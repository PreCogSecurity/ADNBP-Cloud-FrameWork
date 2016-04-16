<?=(strlen($data->title))?"{$data->title}</br></br>":''?>
<?php if(is_array($data->fields)) {?>
    <table id="report-<?=$key?>" class="table table-striped table-bordered">
        <tbody>
    <?php    foreach ($data->fields as $i=>$fieldData) {?>
        <tr>
            <th><?=htmlentities($fieldData['title'])?></th>
            <td>
                <?=$this->formFieldHtml($fieldData);?>
            </td>
        </tr>
    <?php     } ?>
        </tbody>
    </table>
<?php } ?>
