<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Instant Website Report</title>
        <script type="text/javascript" src="<?=site_url('js/jquery.js')?>"></script>
        <link href="<?=site_url('css/reset.css')?>" rel="stylesheet" type="text/css"  />
        <link href="<?=site_url('css/styles.css')?>" rel="stylesheet" type="text/css"  />
    </head>
    <body>
        <div id="wrapper">
            <div id="header">
                <div class="logo"><img src="<?=site_url('images/seo_logo.png')?>" alt="SEO Logo" /></div>
            </div>
            <div id="content">
            <?= @flash_message() ?>
            <?= $content ?>
            </div>
            <div id="footer">
                &copy; Copyright 2007-<?=date('Y')?>, SEO.com, LLC
            </div>
        </div>
    </body>
</html>