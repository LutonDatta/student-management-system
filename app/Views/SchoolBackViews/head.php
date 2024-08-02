<!DOCTYPE html>
<html translate="no" lang="<?=myLang('en','bn');?>">
<head>    
    <meta charset="utf-8">
    <title><?=isset($title) ? $title : 'Welcome'; ?></title>
    <!--<base href="<?=base_url('/');?>">--><!--Generates error in circular add form submit, it submit form to root admin dashboard. -->
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="<?php echo base_url('favicon.ico');?>">
    
    <link href="<?=cdn_url().'bootstrap-4.3.1-dist/css/bootstrap.min.css';?>" rel="stylesheet">
    <link href="<?=cdn_url().'style-inst-admin.css';?>" rel="stylesheet">
    <link href="<?=cdn_url().'animate.css';?>" rel="stylesheet">
    <link href="<?=cdn_url().'fontawesome-free-5.9.0-web/css/all.min.css';?>" rel="stylesheet">
    <link href="<?=cdn_url().'toastr.min.css';?>" rel="stylesheet">
    <link href="<?=cdn_url().'jquery.gritter.css';?>" rel="stylesheet">
    <link href="<?=cdn_url().'checkbox.css';?>" rel="stylesheet">
</head>
<body class="<?=(service('request')->getUserAgent())->isMobile() ? 'mt-4' : '';?>">    
    <!-- For better user experience, on mobile devices, show school name at the top -->
    <?php 
        if((service('request')->getUserAgent())->isMobile()){
            echo "<h3 class='navbar-minimalize fixed-top text-center font-weight-bold pt-1 pb-1 border-bottom border-light mt-0 m-0 bg-info'>SSMS - School Admin</h3>";
        } 
    ?>
    
    
<div id="wrapper">
    