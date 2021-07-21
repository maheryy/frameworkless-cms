<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta_title ?></title>
    <?php if (!empty($meta_description)) : ?>
        <meta name="description" content="<?= $meta_description ?>">
    <?php endif; ?>
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
        <a class="logo" href="/">Munkee</a>
    </div>
    <?= !empty($header_menu) ? \App\Controllers\Website::getMenuHeader($header_menu) : '' ?>
</header>
<?= empty($display_hero) ? ''
    : \App\Controllers\Website::getHero(
        'Hero !!',
        'Etiam quis tristique lectus. Aliquam in arcu eget velit pulvinar dictum vel in
                justo.',
        'https://images.pexels.com/photos/2598638/pexels-photo-2598638.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260'
    )
?>
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
            <p class="copyright">Munkee © 2021</p>
        </div>
    </div>
</footer>
</body>
</html>