<div class="content flex transparent">
    <div class="col-1 flex flex-col w-1/2 mr-1">
        <article class="card rounded my-0.75">
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
        </article>

    </div>
    <div class="col-2 flex flex-col w-1/2">
        <article class="card rounded my-0.75">
            <h3 class="head"> Visiteurs </h3>
            <p>Count : <?= $visitors ?></p>
        </article>
    </div>

</div>