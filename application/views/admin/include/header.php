<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?= $sTitle ?></title>
        <link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/evending.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/sunny/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="<?= base_url(); ?>assets/js/evending.js"></script>
    </head>
    <body>
    <div class="page_wrapper">
        <div class="nav">
            <a class="nav_link" href="<?= base_url();?>member/homepage">Начало</a>
            <a class="nav_link" href="<?= base_url();?>member/actions">Действия</a>
            <a class="nav_link active" href="<?= base_url();?>admin/manage">Управление</a>
            <a class="nav_link logout" href="<?= base_url();?>member/logout">Изход</a>
        </div>
        <div class="content">

