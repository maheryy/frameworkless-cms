<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($csrf_token)) : ?>
        <meta name="csrf-token" content="<?= $csrf_token ?>">
    <?php endif; ?>
    <title>Munkee</title>
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="/assets/css/main.min.css">
    <script src="/assets/vendor/jquery/jquery-3.5.1.min.js"></script>
    <script src="/assets/js/main.js"></script>
</head>
<body>
<?php include $this->view; ?>
</body>
</html>