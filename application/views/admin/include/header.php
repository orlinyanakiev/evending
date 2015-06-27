<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?=$sTitle?></title>

    <!-- Bootstrap -->
    <link href="<?= base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
<!--    <link rel="stylesheet" type="text/css" href="--><?//= base_url();?><!--assets/css/evending.css">-->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/sunny/jquery-ui.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container col-sm-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="container col-sm-3">
                <div class="well well-sm">
                    Здравейте, <b><?= $oUser->FirstName.' '.$oUser->LastName;?></b>!
                </div>
            </div>
<!--            <div class="container col-sm-1">-->
<!--                <a class="btn btn-info btn-lg" href=""><i class="glyphicon glyphicon-refresh"> Обнови</i></a>-->
<!--            </div>-->
            <div class="container col-sm-1 col-sm-offset-6">
                <a class="btn btn-primary btn-lg" href="<?= base_url();?>member/Logout/" role="button"><i class="glyphicon glyphicon-off"></i> Изход</a>
            </div>
        </div>
    </div>

    <div class="container col-sm-2">
