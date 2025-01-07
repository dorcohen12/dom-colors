<?php
	defined('INSITE') or die('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
    <head>
        <title><?php echo htmlspecialchars($Website->settings->web_title);?></title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="token" content="<?php echo ($_SESSION['csrf_token'] ?? '');?>" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css" integrity="sha384-JvExCACAZcHNJEc7156QaHXTnQL3hQBixvj5RV5buE7vgnNEzzskDtx9NQ4p6BJe" crossorigin="anonymous" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;600;700;800;900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="<?php echo $Website->settings->web_url;?>/assets/<?php echo TEMPLATE_NAME;?>/assets/css/style.css?v=<?php echo time();?>" rel="stylesheet" />
    </head>
    <body>
        <div id="notify"></div>
        <main class="page-content">
            <div class="container">

