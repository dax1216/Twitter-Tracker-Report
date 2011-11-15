<?php
    $pages_indexed = array($google, $yahoo, $bing);
    
    sort($pages_indexed) ;
    $max_value = array_pop($pages_indexed);

    $top = $max_value + 10000;
?>
<table height="auto">
    <tr>
        <td width="140">
            <div class="max_bar">
            <?php
                if($google !== FALSE) {
                    $google_bar = ceil(($google / $top) * 100);

                    $google_bar = $google_bar > 100 ? 100 : $google_bar;
            ?>
                <div class="blue_bar" style="height: <?=$google_bar?>%">
                    <?=$google?>
                </div>
            <?php
                } else {
            ?>
                <p>Unable to pull data for Google.</p>
            <?php
                }
            ?>
            </div>
        </td>
        <td width="140">
            <div class="max_bar">
            <?php
                if($yahoo !== FALSE) {
                    $yahoo_bar = ceil(($yahoo / $top) * 100);

                    $yahoo_bar = $yahoo_bar > 100 ? 100 : $yahoo_bar;
            ?>
                <div class="green_bar" style="height: <?=$yahoo_bar?>%">
                    <?=$yahoo?>
                </div>
            <?php
                } else {
            ?>
                <p>Unable to pull data for Yahoo.</p>
            <?php
                }
            ?>
            </div>
        </td>
        <td width="140">
            <div class="max_bar">
            <?php
                if($bing !== FALSE) {
                    $bing_bar = ceil(($bing / $top) * 100);

                    $bing_bar = $bing_bar > 100 ? 100 : $bing_bar;
            ?>
                <div class="orange_bar" style="height: <?=$bing_bar?>%">
                    <?=$bing?>
                </div>
             <?php
                } else {
            ?>
                <p>Unable to pull data for Bing.</p>
            <?php
                }
            ?>
            </div>
        </td>
    </tr>
    <tr>
        <td valign="top" align="center"><img src="<?=site_url('images/google.png')?>" alt="Google" /></td>
        <td valign="top" align="center"><img src="<?=site_url('images/yahoo.jpg')?>" alt="Yahoo" /></td>
        <td valign="top" align="center"><img src="<?=site_url('images/bing.jpg')?>" alt="Bing" /></td>
    </tr>
</table>