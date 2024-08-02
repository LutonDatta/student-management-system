<!doctype html>
<html>
<head>    
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>Invalid SOP User!</title>
    <link rel="stylesheet" href="<?= cdn_url().'bootstrap-4.3.1-dist/css/bootstrap.min.css'; ?>">
</head>
<body>
    <div class="container text-center">
            <h1 class="headline mt-5">You are not a support officer!</h1>
            <p class="lead">Support officers can contact with teachers and help them providing information. </p>

            <p class="lead">
                <a class="btn btn-primary" href="<?=base_url();?>">Go Home</a>
                <button class="btn btn-secondary" id="goBackBtn"><?=lang('Student.go_back');?></button>
            </p>
            
            <script {csp-script-nonce}>document.getElementById("goBackBtn").addEventListener("click",function(){window.history.go(-1);});</script>
            
    </div>
    
    

    <!-- Bootstrap & jQuery are needed to display modal -->
    <?php $cdn_url = (isset($cdn_url) AND strlen($cdn_url) > 0) ? $cdn_url : cdn_url(); ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script {csp-script-nonce}>window.jQuery || document.write('<script src="<?=$cdn_url.'jquery/3.4.1/jquery-3.4.1.min.js';?>"><\/script>');</script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script {csp-script-nonce}>$.fn.modal || document.write('<script src="<?=$cdn_url.'bootstrap-4.3.1-dist/js/bootstrap.min.js';?>"><\/script>');</script>    
    
</body>
</html>
