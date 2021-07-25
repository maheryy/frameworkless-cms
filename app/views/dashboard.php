<div class="content flex transparent">
    <div class="col-1 flex flex-col w-1/2 mr-1">
        <section class="card rounded my-0.75">
            <h3 class="head"> Statut </h3>
            <article class="card-content">
                <p class="text-base lh-2.5">Bonjour <?= $username ?> !</p>
                <p class="text-base lh-1.5">Votre site contient actuellement <span class="font-semibold"><?= $count_pages['total'] ?> pages</span>,
                    dont <span class="font-semibold"><?= $count_pages['published'] ?> publiées</span> et <span class="font-semibold"><?= $count_pages['draft'] ?> brouillons.</span></p>
                <p class="text-base lh-1.5">Il y a eu <span class="font-semibold"><?= $count_visitors ?> visiteurs</span> au cours de ce mois.</p>
            </article>
        </section>
        <section class="card rounded my-0.75">
            <h3 class="head">Mes pages publiées</h3>
            <article class="card-content">
                <table class="m-auto w-3/5 form-collapse mb-1">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($user_pages as $item) : ?>
                        <tr>
                            <td class="py-0.75 px-1"><?= $item['title'] ?></td>
                            <?php if($can_edit_page) : ?>
                                <td class="w-1/12 text-center"><a class="link-default" href="<?= $item['link_edit'] ?>"><i class="fas fa-edit"></i></i></a></td>
                            <?php endif; ?>
                            <td class="w-1/12 text-center"><a class="link-default" href="<?= $item['link'] ?>"><i class="far fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pt-1 w-full text-right"><a href="<?= $link_all_pages ?>" class="font-light style-italic link-default">Voir toutes les pages</a></div>
            </article>
        </section>
        <section class="card rounded my-0.75">
            <h3 class="head">Dernières pages publiées</h3>
            <article class="card-content">
                <table class="m-auto w-3/5 form-collapse mb-1">
                    <thead>
                    <tr>
                        <th class="py-0.75 px-1 text-left border-bottom-default">Auteur</th>
                        <th class="px-1 text-left border-bottom-default">Page</th>
                        <th class="border-bottom-default"></th>
                        <th class="border-bottom-default"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($latest_pages as $item) : ?>
                        <tr>
                            <td class="py-0.75 px-1"><?= $item['author'] ?></td>
                            <td class="py-0.75 px-1"><?= $item['title'] ?></td>
                            <?php if($can_edit_page) : ?>
                                <td class="w-1/12 text-center"><a class="link-default" href="<?= $item['link_edit'] ?>"><i class="fas fa-edit"></i></i></a></td>
                            <?php endif; ?>
                            <td class="w-1/12 text-center"><a class="link-default" href="<?= $item['link'] ?>"><i class="far fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pt-1 w-full text-right"><a href="<?= $link_all_pages ?>" class="font-light style-italic link-default">Voir toutes les pages</a></div>
            </article>
        </section>
    </div>
    <div class="col-2 flex flex-col w-1/2">
        <section class="card rounded my-0.75 pb-1">
            <h3 class="head">Accès rapides</h3>
            <article class="card-content">
                <ul class="w-11/12 flex flex-wrap justify-around m-auto">
                    <?php foreach ($quick_links as $item) : ?>
                        <li class="py-0.5 px-1"><a class="link-default" href="<?= $item['link'] ?>"><?= $item['label'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </section>
        <section class="card rounded my-0.75">
            <h3 class="head">Traffic</h3>
            <article class="card-content" data-role="initVisitorsChart" data-options='<?= json_encode($traffic) ?>'>
                <canvas id="visitor-chart" width="400" height="200"></canvas>
            </article>
        </section>
        <section class="card rounded my-0.75">
            <h3 class="head">Pages les plus populaires</h3>
            <article class="card-content">
                <table class="m-auto w-4/5">
                    <thead>
                    <tr>
                        <th class="py-0.75 border-bottom-default">#</th>
                        <th class="py-0.75 border-bottom-default">Page</th>
                        <th class="py-0.75 border-bottom-default">Visiteurs</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($popular_pages as $key => $item) : ?>
                        <tr>
                            <td class="py-0.5 px-1 text-center"><?= $key + 1 ?></td>
                            <td class="py-0.5 px-1 text-center"><a class="link-default" href="<?= $item['uri'] ?>"><?= $item['page_title'] ?></a></td>
                            <td class="py-0.5 px-1 text-center"><?= $item['count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </article>
        </section>

    </div>
</div>