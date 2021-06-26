<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta_title ?></title>
    <?php if (!empty($meta_description)) : ?>
        <meta name="description" content="<?= $meta_description ?>">
    <?php endif; ?>
    <?php if ($is_indexable) : ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="/assets/css/theme_default.css">
    <script src="/assets/vendor/jquery/jquery-3.5.1.min.js"></script>
    <script src="/assets/js/theme_default.js"></script>
</head>
<body>

<header class="main_h">
    <div class="row">
        <a class="logo" href="#">P/F</a>

        <div class="mobile-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav>
            <ul>
                <li><a href="#sec01">Section 01</a></li>
                <li><a href="#sec02">Section 02</a></li>
                <li><a href="#sec03">Section 03</a></li>
                <li><a href="#sec04">Section 04</a></li>
            </ul>
        </nav>

    </div> <!-- / row -->
</header>

<div class="hero">

    <h1><span>Hey</span><br><?= $content_title ?></h1>

</div>

<?php include $this->view ?>

</body>
</html>