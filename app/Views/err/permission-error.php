<!doctype html>
<html>
<head>    
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>Invalid Permission Error!</title>
    <link rel="stylesheet" href="<?= cdn_url().'bootstrap-4.3.1-dist/css/bootstrap.min.css'; ?>">
</head>
<body>
    <div class="container text-center">
            <h1 class="headline mt-5">Not Enough Permission!</h1>
            <p class="lead"><?= isset($permission_error_message) ? $permission_error_message : 'You have no permission to do this.'; ?></p>
            <p class="lead">
                <?php 
                    $permissions = get_role_actions();
                    $p_r = (isset($pr) AND isset($permissions[$pr])) ? $permissions[$pr] : 'User Authorization (Allow this person to use this site)';
                    echo 'Permission required: ' . $p_r;
                ?>
            </p>
            
            <h3 class="headline mt-5">
                <?= esc(getSchool()->sch_name);?> 
                [<?=esc(esc(getSchool()->sch_eiin));?>]
            </h3>
                    
            <p class="lead">
                <button class="btn btn-secondary" id="goBackBtn"><?=lang('Student.go_back');?></button>
                <?=anchor('/','View this Institution',['class'=>'btn btn-secondary']);?>
            </p>
            
            <script {csp-script-nonce}>document.getElementById("goBackBtn").addEventListener("click",function(){window.history.go(-1);});</script>
            
    </div>
    
    

    <!-- Bootstrap & jQuery are needed to display modal -->
    <?php $cdn_url = (isset($cdn_url) AND strlen($cdn_url) > 0) ? $cdn_url : cdn_url(); ?>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script {csp-script-nonce}>window.jQuery || document.write('<script src="<?=$cdn_url.'jquery/3.4.1/jquery-3.4.1.min.js';?>"><\/script>');</script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script {csp-script-nonce}>$.fn.modal || document.write('<script src="<?=$cdn_url.'bootstrap-4.3.1-dist/js/bootstrap.min.js';?>"><\/script>');</script>    
    
    <style {csp-style-nonce}>
            @font-face {font-family: 'nikoshf';src: url('<?=cdn_url('fonts/SolaimanLipi_20-04-07.ttf');?>') format('truetype');}
            .font-nikosh,h1,h2,h3,h4,h5,h6,p,a,li,span:not(.fa),strong,td,.card-header,div,.post-title{ font-family: nikoshf !important; }
    </style>
    
</body>
</html>
