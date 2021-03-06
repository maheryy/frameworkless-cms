<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta_title ?? $site_title ?></title>
    <meta name="description" content="<?= $meta_description ?? $site_description ?>">
    <?php if (!$is_indexable) : ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="/assets/css/default_theme.min.css">
    <script src="/assets/vendor/jquery/jquery-3.5.1.min.js"></script>
    <script src="/assets/js/default_theme.js"></script>
</head>
<body>

<header class="main-header">
    <div class="main-logo">
        <a class="logo" href="/"><?= $site_title ?></a>
    </div>
    <?= !empty($header_menu) ? \App\Controllers\Website::getMenuHeader($header_menu) : '' ?>
</header>
<?= !empty($display_hero) ? \App\Controllers\Website::getHero($hero_data['title'], $hero_data['description'], $hero_data['image']) : '' ?>
<main class="content">
    <?php include $this->view ?>
</main>
<footer>
    <div class="flex flex-col m-auto">
        <?php if (!empty($footer_sections)) : ?>
            <div class="flex justify-center h-full">
                <?php
                $size = 12 / count($footer_sections);
                foreach ($footer_sections as $item) {
                    switch ($item['type']) {
                        case \App\Core\Utils\Constants::LS_FOOTER_TEXT :
                            echo \App\Controllers\Website::getTextFooter($item['label'], nl2br($item['data']), $size);
                            break;
                        case \App\Core\Utils\Constants::LS_FOOTER_LINKS :
                            echo \App\Controllers\Website::getLinkFooter($item['label'], $item['data'], $size);
                            break;
                        case \App\Core\Utils\Constants::LS_FOOTER_CONTACT :
                            echo \App\Controllers\Website::getContactFooter($item['label'], $size);
                            break;
                        case \App\Core\Utils\Constants::LS_FOOTER_NEWSLETTER :
                            echo \App\Controllers\Website::getNewsletterFooter($item['label'], $size);
                            break;
                    }
                }
                ?>
            </div>
        <?php endif; ?>
        <?= !empty($footer_socials) ? \App\Controllers\Website::getSocialFooter($footer_socials) : '' ?>
        <div class="footer-section">
            <p class="copyright"><?= $site_title . ' ?? ' . date('Y') ?></p>
        </div>
    </div>
</footer>
</body>
</html>