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
        <a class="logo" href="#">Hollytel</a>
    </div>
    <nav>
        <ul class="links">
            <li><a href="#">Chambres</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Evenements</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
</header>
<!--<section class="hero-header">-->
<!--    <div id="hero-img"-->
<!--         data-url="https://images.pexels.com/photos/2598638/pexels-photo-2598638.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260"></div>-->
<!--    <article class="hero-content">-->
<!--        <h1>--><? //= $content_title ?><!--</h1>-->
<!--        <p>Etiam quis tristique lectus. Aliquam in arcu eget velit pulvinar dictum vel in-->
<!--            justo.</p>-->
<!--    </article>-->
<!--</section>-->
<main class="content">
    <?php include $this->view ?>
</main>
<footer>
    <div class="flex flex-col m-auto">
        <div class="flex justify-center h-full">
            <div class="footer-section link w-3/12">
                <h3>Services</h3>
                <ul>
                    <li><a href="#">Web design</a></li>
                    <li><a href="#">Development</a></li>
                    <li><a href="#">Hosting</a></li>
                    <li><a href="#">Company</a></li>
                    <li><a href="#">Team</a></li>
                    <li><a href="#">Careers</a></li>
                </ul>
            </div>
            <div class="footer-section text w-3/12">
                <h3>Hollytel</h3>
                <div class="section-content">
                    <p>Praesent sed lobortis mi. Suspendisse vel placerat ligula. Vivamus ac sem lacus. Ut vehicula
                        rhoncus
                        elementum. Etiam quis tristique lectus. Aliquam in arcu eget velit pulvinar dictum vel in
                        justo.</p>
                </div>
            </div>
            <div class="footer-section contact w-3/12">
                <h3>Contactez nous</h3>
                <div class="section-content">
                    <form method="POST" action="contact">
                        <div class="form-field">
                            <input type="email" class="form-control" name="email" placeholder="Adresse email">
                        </div>
                        <div class="form-field">
                            <textarea class="form-control" name="message" placeholder="Message" rows="5"></textarea>
                        </div>
                        <div class="form-field">
                            <input type="submit" class="form-action" value="Envoyer">
                        </div>
                        <div class="info-box">
                            <span class="info-description"></span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="footer-section newsletter w-3/12">
                <h3>Newsletter</h3>
                <div class="section-content">
                    <form method="POST" action="newsletter">
                        <div class="form-field">
                            <input type="email" class="form-control" name="email" placeholder="Adresse email">
                            <input type="submit" class="form-action" value="S'inscrire">
                        </div>
                        <div class="info-box">
                            <span class="info-description"></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer-section social">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-snapchat"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
        <div class="footer-section">
            <p class="copyright">Hollytel © 2021</p>
        </div>
    </div>
</footer>
</body>
</html>