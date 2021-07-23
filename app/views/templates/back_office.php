<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Munkee - admin</title>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="/assets/vendor/datatables/datatables.min.css">
    <link rel="stylesheet" href="/assets/css/main.min.css">
    <script src="/assets/vendor/jquery/jquery-3.5.1.min.js"></script>
    <script src="/assets/vendor/datatables/datatables.min.js"></script>
    <script src="/assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <?php if (isset($csrf_token)) : ?>
        <meta name="csrf-token" content="<?= $csrf_token ?>">
    <?php endif; ?>
</head>
<body>
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
</body>
</html>