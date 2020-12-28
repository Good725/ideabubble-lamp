<p>&nbsp;</p>
<h1>
    Application logs
</h1>
<p>&nbsp;</p>
<form method="get">
    <div class="form-row gutters vertically_center dataTables_filter">
            <div class="col-xs-4">
                <input type="date" name="logs_from" id="filter_date_from" class="col-xs-12" />
            </div>
            <div class="col-xs-4">
                <input type="date" name="logs_to" id="filter_date_to" class="col-xs-12" />
            </div>
        <div class="col-xs-12 form-row form-actions form-action-group py-3" style="text-align: left;">
                <input type="text" name="logs_type" id="filter_type" />
                <button type="submit" class="btn btn-primary"> Submit </button>
            </div>
    </div>
</form>
<table id="logs_datatable" class="table table-striped dataTable">
    <thead>
    <tr>
        <th>Date</th>
        <th>Type</th>
        <th>Message</th>
    </tr>  
    </thead>
    <tbody> 
    
        <?php 
        $i=0;
        foreach($logs as $log): ?>
        
            <?php foreach(array_reverse($log['content']) as $content): ?>
        
                <tr>
                    
                    <td>
                        <a title="<?=$log['title']?>"><?=$content['date']; ?></a>
                    </td>
                    <td>
                        <?=$content['type']; ?>
                    </td>
                    <td>
                        
                        <?php if(isset($content['stacktrace'])): ?>
                        
                        <a class="stacktrace_trigger" data-id="<?=$i; ?>" href="#"><b><?=$content['message']; ?></b></a>

                            <div class="stacktrace" id="st_<?=$i;?>" style="display: none;" data-status="0" >
                                
                                <p style="line-height: 23px;"><?=nl2br($content['stacktrace']); ?></p>

                                
                            </div>
                        
                        <?php else: ?>
                        
                            <?=$content['message']; ?>
                        
                        <?php endif; ?>
                        
                    </td>
                    
                </tr>
        
            <?php 
            
            $i++;
            endforeach; 
            
            ?>
        
        
        <?php endforeach; ?>
        
    </tbody>
</table>

