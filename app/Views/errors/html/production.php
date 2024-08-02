<!doctype html>
<html>
<head>    
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title><?=lang('whoops');?></title>
    <style type="text/css">
            <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>
</head>
<body>
	<div class="container text-center">

		<h1 class="headline"><?=lang('Errors.whoops');?></h1>
		<p class="lead"><?=lang('Errors.whoopsText');?></p>
		<p class="lead">
                    <?=anchor('admin/global/support',lang('Errors.supportTicket'),['class'=>'btn btn-info']);?>
                    <button class="btn btn-secondary" id="goBackBtn"><?=lang('Student.go_back');?></button>
                    <script {csp-script-nonce}>document.getElementById("goBackBtn").addEventListener("click",function(){window.history.go(-1);});</script>
                </p>
                <p class="lead"><?=lang('Errors.showAd');?></p>
	</div>

        <?php // if(service('request')->getLocale() === 'bn'): ?>
            <style {csp-style-nonce}>
                    @font-face {font-family: 'nikoshf';src: url('<?=cdn_url('fonts/SolaimanLipi_20-04-07.ttf');?>') format('truetype');}
                    .font-nikosh,h1,h2,h3,h4,h5,h6,p,a,li,strong,td,.card-header,div,.post-title{ font-family: nikoshf !important;}
            </style>
        <?php // endif; ?>
            
        <link rel="stylesheet" href="<?=cdn_url('bootstrap-4.3.1-dist/css/bootstrap.min.css');?>">
        
</body>

</html>
