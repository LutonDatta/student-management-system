<!doctype html>
<html>
<head>    
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title><?= isset($error_title) ? $error_title : 'General Error!'; ?></title>
    <link rel="stylesheet" href="<?= cdn_url().'bootstrap-4.3.1-dist/css/bootstrap.min.css'; ?>">
</head>
<body>
    <div class="container text-center">
            <h1 class="headline mt-5"><?= isset($error_header) ? $error_header : 'General Error!'; ?></h1>
            <p class="lead"><?= isset($error_message) ? $error_message : 'Something error found.'; ?></p>
            <p class="lead"><button class="btn btn-secondary" id="goBackBtn"><?=lang('Student.go_back');?></button></p>
            
            <script {csp-script-nonce}>document.getElementById("goBackBtn").addEventListener("click",function(){window.history.go(-1);});</script>
    </div>
</body>
</html>
