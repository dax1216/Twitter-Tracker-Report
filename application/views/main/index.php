<h1>Social App Tracking Report</h1>
<div class="form_div">
    <form method="POST" name="sform" action="<?=base_url()?>main/process" enctype="multipart/form-data">
        <input type="file" name="csv" />
        <input type="submit" name="submit" value="Process CSV" id="submit" />
    </form>
    <br />
    <br />
<?php
    if(count($reports) > 0) {
?>
    <table cellpadding="0" cellspacing="3">
        <thead>
            <tr>
                <th width="130"></th>
                <th width="200">Report</th>
                <th width="150">Report Uploaded</th>
                <th width="150">Status</th>
            </tr>
        </thead>
        <tbody>
<?php   foreach($reports as $row) { ?>
            <tr>
                <td style="font-size: 11px;"><a href="main/remove_report/<?=$row['id']?>">remove</a>&nbsp;&nbsp;&nbsp;<?=($row['status'] == 1) ? '<a href="main/export/' . $row['id'] . '">download report</a>' : ''?></td>
                <td><?=$row['report_name']?></td>
                <td align="center"><?=date('m-d-Y', strtotime($row['date']))?></td>
                <td align="center"><?=($row['status']) ? 'Complete' : 'In Progress'?></td>
            </tr>
<?php   } ?>
        </tbody>
    </table>
<?php
    }
?>
    <!--iframe name="my_frame" width="900" frameborder="0" height="400" src="#"></iframe-->
</div>

<script type="text/javascript">
    $(document).ready( function () {
        /*
        $('iframe').load( function () {
            $('.loader').remove();
            $('#submit').after('<div style="margin-top: 10px; font-size: 12px;" id="export_link"><a href="<?=base_url()?>main/export">Export result as csv</a></div>');
        });

        $('#submit').click( function () {
            $('#export_link').remove();
            $(this).after('<img src="<?=base_url()?>images/ajax_loader.gif" class="loader" />');
        });
        */
        $("#flashmessage").animate({top: "0px"}, 1000 ).show('fast').fadeIn(200).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
    });
</script>

