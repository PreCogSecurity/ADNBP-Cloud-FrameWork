
                
                
                <p><?=htmlentities($data->subtitle)?></p>
                <div class="table-responsive">
                
                    <table id="report-<?=$key?>" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <?php if(is_array($data->columns)) foreach ($data->columns as $key => $value) {?>
                                <th><?=htmlentities($value['title'])?></th>    
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(is_array($data->rows)) foreach ($data->rows as $key => $value) {?>
                            <tr>
                                <?php if(is_array($value)) foreach ($value as $key2 => $cell) {
                                        $align = $cell['align'];
                                        if(!$align && (isset($cell['currency']))) $align='right';
                                    ?>
                                <td<?=($align)?' class="text-'.$align.'"':''?><?=($cell['anchor'])?' id="'.htmlentities($cell['anchor']).'"':''?>><?php
                                    if(isset($cell['currency'])) $cell['value'] = $adnbp->numberFormat($cell['value'],2).' '.$cell['currency'];
                                    // save echo
                                    $cell['value'] =htmlentities($cell['value']);
                                    if(isset($cell['bold'])) $cell['value'] = '<strong>'.$cell['value'].'</strong>';
                                    if(isset($cell['small'])) $cell['value'] = '<small>'.$cell['value'].'</small>';
                                    if(isset($cell['link'])) $cell['value'] = '<a href="'.htmlentities($cell['link']).'">'.$cell['value'].'</a>';
                                    echo $cell['value'];
                                    
                                ?></td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    
                </div>
                
            