<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEST</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />
    <link rel="stylesheet" href="../styles/main.css">
    <script src="../public/js/jquery-3.5.1.min.js"></script>
    <script src="../public/js/main.js"></script>
</head>

<body>
    <?php include $toolbar; ?>
    <?php include $sidebar; ?>
    <main class="content">
        <?php include $this->view; ?>
    </main>
</body>

</html>