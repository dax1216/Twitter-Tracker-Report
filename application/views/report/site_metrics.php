<table class="site_metrics_table">
    <tr>
        <td width="100">Sitemap</td>
        <td width="250" align="center"><?=($sitemap) ? 'Yes' : 'No'?></td>
        <td></td>
    </tr>
    <tr>
        <td width="100">Page Speed</td>
        <td width="250" align="center"><?=$loading_time ?> seconds</td>
        <td></td>
    </tr>
    <tr>
        <td width="100">Domain Age</td>
        <td width="250" align="center"><?=$domain_age?></td>
        <td></td>
    </tr>
    <tr>
        <td width="100">Domain Expiry</td>
        <td width="250" align="center"><?=$expiry_date?></td>
        <td></td>
    </tr>
</table>