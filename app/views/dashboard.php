<article class="content">
    <p>Pages : <?= $pages ?></p>
    <p>Users : <?= $users ?></p>
    <p>Roles : <?= $roles ?></p>
    <p>Navs : <?= $navs ?></p>

    <p>
        <?php
        echo '<pre>';
        print_r($debug);
        echo '</pre>';
        ?>
    </p>
</article>