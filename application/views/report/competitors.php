<table class="competitors_table">
    <thead>
        <tr>
            <th width="250">Website</th>
            <th width="150">Alexa Rank</th>
            <th width="150">PageRank</th>
            <th width="175">Inbound Links</th>
            <th width="175">Pages Indexed</th>
        </tr>
    </thead>
    <tbody>
        <tr class="main_url">
            <td><?=$report['url']?></td>
            <td><?=$report['google_page_rank']?></td>
            <td><?=$report['alexa_rank']?></td>
            <td><?=$report['inbound_links']?></td>
            <td><?=$report['google_pages_indexed']?></td>
        </tr>
<?php   foreach($competitors as $url => $data) { ?>
        <tr>
            <td><?=$url?></td>
            <td><?=$data['google_pagerank']?></td>
            <td><?=$data['alexa_rank']?></td>
            <td><?=$data['yahoo_inlinks']?></td>
            <td><?=$data['google_pagesindexed']?></td>
        </tr>
<?php   } ?>
    </tbody>
</table>