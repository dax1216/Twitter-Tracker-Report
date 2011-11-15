<h1>Website Analysis for: <?=$url ?></h1>

<table>
    <tr>
        <td colspan="2">
        <div class="div100">
            <div class="report_header">Overall Score</div>
            <div class="report_content">
                <img src="<?=site_url('images/loader.gif')?>" alt="Loading report..." class="loader" />
            </div>
        </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
        <div class="div100">
            <div class="report_header">Competition</div>
            <div class="report_content" id="competitors_div">
                <img src="<?=site_url('images/loader.gif')?>" alt="Loading report..." class="loader" />
            </div>
        </div>
        </td>
    </tr>
    <tr>
        <td valign="top">
        <div class="div50">
            <div class="report_header">Pages Indexed</div>
            <div class="report_content" id="pages_indexed_div">
                <img src="<?=site_url('images/loader.gif')?>" alt="Loading report..." class="loader" />
            </div>
        </div>
        </td>
        <td valign="top">
        <div class="div50">
            <div class="report_header">Google PageRank</div>
            <div class="report_content" id="googlepagerank_div">
                <img src="<?=site_url('images/loader.gif')?>" alt="Loading report..." class="loader" />
            </div>
        </div>
        </td>
    </tr>
    <tr>
        <td valign="top">
        <div class="div50">
            <div class="report_header">Keywords</div>
            <div class="report_content">
                <img src="<?=site_url('images/loader.gif')?>" alt="Loading report..." class="loader" />
            </div>
        </div>
        </td>
        <td valign="top">
        <div class="div50">
            <div class="report_header">Site Metrics</div>
            <div class="report_content" id="site_metrics_div">
                <img src="<?=site_url('images/loader.gif')?>" alt="Loading report..." class="loader" />
            </div>
        </div>
        </td>
    </tr>
</table>
<script type="text/javascript">
    var base_url = '<?=site_url()?>';
    
    $(document).ready( function () {
        getPagesIndexed('<?=$url?>');
        getGooglePageRank('<?=$url?>');
        getSiteMetrics('<?=$url?>');
        getCompetitors('<?=$url?>', '<?=$keyword?>');
    });
</script>