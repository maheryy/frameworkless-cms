<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta_title ?? 'PAGE' ?></title>
    <link rel="stylesheet" href="/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="/css/datatables.min.css">
    <link rel="stylesheet" href="/css/main.min.css">
    <script src="/js/jquery-3.5.1.min.js"></script>
    <script src="/js/datatables.min.js"></script>
    <script src="/js/main.js"></script>
    <?php if (isset($csrf_token)) : ?>
        <meta name="csrf-token" content="<?= $csrf_token ?>>">
    <?php endif; ?>
</head>
<body>
    <?php if (isset($toolbar, $sidebar)) : ?>
        <?php include $toolbar; ?>
        <?php include $sidebar; ?>
        <main class="content">
            <?php if (isset($content_title)) : ?>
                <section class="content-header">
                    <h2><?= $content_title ?></h2>
                </section>
            <?php endif; ?>
            <section class="content-body">
                <?php include $this->view; ?>
            </section>
        </main>
    <?php else : ?>
        <?php include $this->view; ?>
    <?php endif; ?>
</body>
</html>