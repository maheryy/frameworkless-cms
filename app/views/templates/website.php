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
    <?= \App\Controllers\Website::getMenuHeader([
        ['label' => 'Chambres', 'link' => '#'],
        ['label' => 'Services', 'link' => '#'],
        ['label' => 'Evenements', 'link' => '#'],
        ['label' => 'FAQ', 'link' => '#'],
        ['label' => 'Contact', 'link' => '#'],
    ]) ?>
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
        <div class="flex justify-center h-full">
            <?= \App\Controllers\Website::getLinkFooter('Our services', [['label' => 'reviews', 'link' => '/reviews'], ['label' => 'review', 'link' => '/review']], 3) ?>
            <?= \App\Controllers\Website::getTextFooter(
                'Munkee company',
                'Praesent sed lobortis mi. Suspendisse vel placerat ligula. 
                    Vivamus ac sem lacus. Ut vehicula rhoncus elementum. Etiam quis tristique lectus. 
                    Aliquam in arcu eget velit pulvinar dictum vel in justo.',
                3)
            ?>
            <?= \App\Controllers\Website::getContactFooter('Contact us', 3) ?>
            <?= \App\Controllers\Website::getNewsletterFooter('Newsletter', 3) ?>
        </div>
        <?= \App\Controllers\Website::getSocialFooter([
            ['icon' => 'facebook', 'link' => '#'],
            ['icon' => 'twitter', 'link' => '#'],
            ['icon' => 'snapchat', 'link' => '#'],
            ['icon' => 'instagram', 'link' => '#']
        ]) ?>
        <div class="footer-section">
            <p class="copyright">Munkee © 2021</p>
        </div>
    </div>
</footer>
</body>
</html>