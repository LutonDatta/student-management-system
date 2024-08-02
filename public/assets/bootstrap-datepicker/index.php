<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>CDN of Ultra-School.com</title>
    <link href="https://cdn.ultra-school.com/style-inst-admin.css" media="all" rel="stylesheet" type="text/css" />
    <link href="https://cdn.ultra-school.com/bootstrap-4.4.1-dist/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container text-center">
        <img class="img-fluid img-thumbnail" src="https://cdn.ultra-school.com/img/Ultra-School-Cover.jpg"/>
        <h3>CDN of Ultra-School.com</h3></td></tr>
        <h3>We Provide Website that can DIGITALIZE your School</h3>
        <p class="content-block">Some optional files are stored here for convenience.</p>
        <p class="content-block">You can use any file from this server without any special permission.</p>
        <p class="content-block">
            <a class="btn btn-success" target="_console" href="https://ultra-school.com/">Ultra-School.com</a>
            <a class="btn btn-success" target="_console" href="https://yourtaka.com/">YourTaka.com</a>
            <a class="btn btn-success" target="_console" href="https://pay.ultra-school.com/">pay.Ultra-School.com</a>
            <a class="btn btn-success" target="_console" href="https://blog.ultra-school.com/">Blog</a>
            <a class="btn btn-success" href="tel:+8801738166336">+88 01738 166 336</a>
        </p>
    </div>
    
    <div class="text-center">
        <hr></hr>
        <p>Follow us on <a target="_console" href="https://web.facebook.com/ultra.school.website">Facebook</a> or Check videos on <a target="_console" href="https://youtube.com/channel/UCj1AEja9CZpaZhBrxxWVYag/">YouTube</a>.</p>
        <hr></hr>
    </div>
    
    <div class="text-center">
        <?php 
        $dir    = __DIR__;
        $files = scandir($dir); // scandir($dir, 1)

        foreach( $files as $svg){
            if(in_array($svg,['nbproject','index.php','app.yaml']) || (strpos($svg, '.') === 0) ) continue;
            echo "<a href='".(is_dir(__DIR__.DIRECTORY_SEPARATOR.$svg) ? $svg .'/index.php' : $svg)."' style='font-size:20px;'>{$svg}</a><br>";
        }
        ?>
    </div>
    
</body>
</html>
