<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo __("dbv.php - Database versioning, made easy"); ?></title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="en" />
    <meta name="robots" content="noindex,nofollow" />
    <meta name="author" content="Victor Stanciu - http://victorstanciu.ro" />

    <link rel="stylesheet" type="text/css" media="screen" href="public/stylesheets/dbv.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="public/stylesheets/codemirror.css" />

    <script type="text/javascript">
        var APP = {};
    </script>
    <!-- 加载JQuery 并做兼容处理 -->
    <script type="text/javascript" src="public/scripts/jquery.min.js"></script>
    <script type="text/javascript">
    var j$ = jQuery.noConflict();
    </script>
    <script type="text/javascript" src="public/scripts/bootstrap.min.js"></script>

    <script type="text/javascript" src="public/scripts/prototype.js"></script>
    <script type="text/javascript" src="public/scripts/builder.js"></script>
    <script type="text/javascript" src="public/scripts/effects.js"></script>

    <script type="text/javascript" src="public/scripts/codemirror/codemirror.js"></script>
    <script type="text/javascript" src="public/scripts/codemirror/mode/sql.js"></script>
    <script type="text/javascript" src="public/scripts/codemirror/mode/php.js"></script>

    <script type="text/javascript" src="public/scripts/dbv.js"></script>
</head>
<body>
    <div class="navbar navbar-static-top navbar-inverse">
        <div class="navbar-inner">
            <div class="container">
                <a href="index.php" class="brand">dbv<span>.php</span></a>
                <!--
                <ul class="nav pull-right">
                    <li><a href="http://dbv.vizuina.com"><?php echo __('Check for updates'); ?></a></li>
                </ul>
                -->
            </div>
        </div>
    </div>
    <div id="content" class="container">
        <div id="log" style="margin: 20px 0 -10px 0;">
            <?php $this->_view('log'); ?>
        </div>
        <div class="row-fluid">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-pane-schema" data-toggle="tab"><?php echo __('Database schema'); ?></a></li>
                <li><a href="#tab-pane-revisions" data-toggle="tab"><?php echo __('Revisions'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-pane-schema">
                    <div id="left">
                        <?php $this->_view('schema'); ?>
                    </div>
                </div>
                <div class="tab-pane" id="tab-pane-revisions">
                    <div id="right">
                        <?php $this->_view('revisions'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>