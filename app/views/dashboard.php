<div class="content flex transparent">
    <div class="col-1 flex flex-col w-1/2 mr-1">
        <section class="card rounded my-0.75">
            <h3 class="head"> Debug </h3>
            <p>Pages : <?= $pages ?></p>
            <p>Users : <?= $users ?></p>
            <p>Roles : <?= $roles ?></p>
            <p>Menus : <?= $menus ?></p>
            <p>
                <?php
                echo '<pre>';
                print_r($debug);
                echo '</pre>';
                ?>
            </p>
        </section>
        <section class="card rounded my-0.75">
            <h3 class="head"> Mes pages publiées </h3>
            <p>Pages : <?= $pages ?></p>
            <p>Users : <?= $users ?></p>
            <p>Roles : <?= $roles ?></p>
            <p>Menus : <?= $menus ?></p>
        </section>
        <section class="card rounded my-0.75">
            <h3 class="head"> Activités </h3>
            <p>Pages : <?= $pages ?></p>
            <p>Users : <?= $users ?></p>
            <p>Roles : <?= $roles ?></p>
            <p>Menus : <?= $menus ?></p>
        </section>
        <section class="card rounded my-0.75">
            <h3 class="head"> Actions rapides </h3>
            <article class="card-content">
                <ul class="w-11/12 flex flex-wrap justify-around">
                    <li class="py-0.5 px-1"><a href="#">Ajouter une page</a></li>
                    <li class="py-0.5 px-1"><a href="#">Ajouter un menu</a></li>
                    <li class="py-0.5 px-1"><a href="#">Personnaliser mon site</a></li>
                    <li class="py-0.5 px-1"><a href="#">Mon profil</a></li>
                </ul>
            </article>
        </section>
    </div>
    <div class="col-2 flex flex-col w-1/2">
        <section class="card rounded my-0.75">
            <h3 class="head"> Traffic du site </h3>
            <p>Count : <?= $visitors ?></p>
        </section>
        <section class="card rounded my-0.75">
            <h3 class="head"> Pages les plus populaires </h3>
        </section>
    </div>
</div>