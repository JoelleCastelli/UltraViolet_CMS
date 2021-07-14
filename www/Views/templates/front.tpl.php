<?php
    use App\Core\Helpers;
    use App\Core\Request;
    $noTemplateUrl = [
        Helpers::callRoute('login'),
        Helpers::callRoute('register'),
        Helpers::callRoute('forget_password')
    ];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= isset($title) && $title  != "" ? APP_NAME.' - '.$title : META_TITLE ?></title>
    <meta name="description" content="<?= isset($description) && $description  != "" ? $description : META_DESC ?>">
    <link rel="shortcut icon" href="<?= PATH_TO_IMG ?>favicon.ico"/>

    <!--JS-->
    <script src="<?=PATH_TO_DIST.'main.js'?>"></script>
    <!--CSS-->
    <link rel="stylesheet" href="<?=PATH_TO_DIST.'main.css'?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />

    <?php
    if(isset($headScripts) && !empty($headScripts)) {
        foreach ($headScripts as $script) {
            echo "<script src='$script'></script>";
        }
    }
    ?>
</head>

<body>
    <div class="container">
        <?php
            if (!in_array(Request::getURI(), $noTemplateUrl)) {
                include 'Views/components/navbar-front.php';
            }
        ?>
        <main class="main main-front">
            <div class="main-content">
                <?php
                    if (isset($flash)) $this->displayFlash($flash);
                    include $this->view;
                ?>
            </div>
        </main>
    </div>

    <?php
        if(isset($bodyScripts) && !empty($bodyScripts)) {
            foreach ($bodyScripts as $script) {
                echo "<script src='$script'></script>";
            }
        }
    ?>
</body>

</html>