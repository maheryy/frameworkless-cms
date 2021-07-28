<?php

namespace App\Core\Utils;

/**
 * Static class for database seeds
 * Seeders data methods must have the same name as the model name in order to call BaseRepository::runSeed()
 * Ex :
 *  - table name => my_table
 *  - model name => MyTable
 *  - seed data => Seeder::myTable()
 *
 */
class Seeder
{

    /**
     * List all of the seeders available in this class
     *
     * @return array
     */
    public static function getAvailableSeeders()
    {
        return [
            'role',
            'user',
            'permission',
            'rolePermission',
            'settings',
            'post',
            'pageExtra',
            'menu',
            'menuItem',
            'review',
            'visitor',
            'subscriber',
        ];
    }

    public static function role()
    {
        return [
            Constants::ROLE_SUPER_ADMIN => ['name' => 'Super Administrateur'],
            Constants::ROLE_ADMIN => ['name' => 'Administrateur'],
            Constants::ROLE_EDITOR => ['name' => 'Editeur'],
            Constants::ROLE_MODERATOR => ['name' => 'Modérateur'],
            Constants::ROLE_SUBSCRIBER => ['name' => 'Abonné'],
        ];
    }

    public static function permission()
    {
        return [
            Constants::PERM_READ_USER => [
                'name' => 'Visibilité d\'un utilisateur',
                'description' => ''
            ],
            Constants::PERM_CREATE_USER => [
                'name' => 'Création d\'un utilisateur',
                'description' => ''
            ],
            Constants::PERM_UPDATE_USER => [
                'name' => 'Modification d\'un utilisateur',
                'description' => ''
            ],
            Constants::PERM_DELETE_USER => [
                'name' => 'Suppression d\'un utilisateur',
                'description' => ''
            ],
            Constants::PERM_READ_PAGE => [
                'name' => 'Visibilité d\'une page',
                'description' => ''
            ],
            Constants::PERM_CREATE_PAGE => [
                'name' => 'Création d\'une page',
                'description' => ''
            ],
            Constants::PERM_UPDATE_PAGE => [
                'name' => 'Modification d\'une page',
                'description' => ''
            ],
            Constants::PERM_PUBLISH_PAGE => [
                'name' => 'Publication d\'une page',
                'description' => ''
            ],
            Constants::PERM_DELETE_PAGE => [
                'name' => 'Suppression d\'une page',
                'description' => ''
            ],
            Constants::PERM_READ_MENU => [
                'name' => 'Visibilité des menus',
                'description' => ''
            ],
            Constants::PERM_CREATE_MENU => [
                'name' => 'Création d\'un menu',
                'description' => ''
            ],
            Constants::PERM_UPDATE_MENU => [
                'name' => 'Modification d\'un menu',
                'description' => ''
            ],
            Constants::PERM_DELETE_MENU => [
                'name' => 'Suppression d\'un menu',
                'description' => ''
            ],
            Constants::PERM_READ_CUSTOMIZATION => [
                'name' => 'Visibilité des personnalisations',
                'description' => ''
            ],
            Constants::PERM_UPDATE_CUSTOMIZATION => [
                'name' => 'Modification des personnalisations',
                'description' => ''
            ],
            Constants::PERM_READ_SETTINGS => [
                'name' => 'Visibilité des paramètres',
                'description' => ''
            ],
            Constants::PERM_UPDATE_SETTINGS => [
                'name' => 'Modification des paramètres',
                'description' => ''
            ],
            Constants::PERM_READ_ROLE => [
                'name' => 'Visibilité des rôles',
                'description' => ''
            ],
            Constants::PERM_CREATE_ROLE => [
                'name' => 'Création d\'un rôle',
                'description' => ''
            ],
            Constants::PERM_UPDATE_ROLE => [
                'name' => 'Modification d\'un rôle',
                'description' => ''
            ],
            Constants::PERM_READ_REVIEW => [
                'name' => 'Visibilité des avis',
                'description' => ''
            ],
            Constants::PERM_MANAGE_REVIEW => [
                'name' => 'Approbation/désapprobation d\'un avis',
                'description' => ''
            ],
            Constants::PERM_DELETE_REVIEW => [
                'name' => 'Suppression d\'un avis',
                'description' => ''
            ],
            Constants::PERM_READ_NEWSLETTER => [
                'name' => 'Visibilité d\'une newsletter',
                'description' => ''
            ],
            Constants::PERM_CREATE_NEWSLETTER => [
                'name' => 'Création d\'une newsletter',
                'description' => ''
            ],
            Constants::PERM_UPDATE_NEWSLETTER => [
                'name' => 'Modification d\'une newsletter',
                'description' => ''
            ],
            Constants::PERM_DELETE_NEWSLETTER => [
                'name' => 'Suppression d\'une newsletter',
                'description' => ''
            ],
            Constants::PERM_SEND_NEWSLETTER => [
                'name' => 'Envoie d\'une newsletter',
                'description' => ''
            ],
        ];
    }

    public static function rolePermission()
    {
        return [
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_USER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_USER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_USER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_USER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_PAGE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_MENU],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_MENU],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_MENU],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_MENU],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_SETTINGS],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_SETTINGS],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_ROLE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_ROLE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_ROLE],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_REVIEW],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_MANAGE_REVIEW],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_REVIEW],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_READ_NEWSLETTER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_CREATE_NEWSLETTER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_UPDATE_NEWSLETTER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_DELETE_NEWSLETTER],
            ['role_id' => Constants::ROLE_SUPER_ADMIN, 'permission_id' => Constants::PERM_SEND_NEWSLETTER],

            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_USER],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_USER],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_DELETE_PAGE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_MENU],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_MENU],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_MENU],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_DELETE_MENU],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_SETTINGS],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_SETTINGS],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_ROLE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_ROLE],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_REVIEW],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_MANAGE_REVIEW],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_DELETE_REVIEW],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_READ_NEWSLETTER],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_CREATE_NEWSLETTER],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_UPDATE_NEWSLETTER],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_DELETE_NEWSLETTER],
            ['role_id' => Constants::ROLE_ADMIN, 'permission_id' => Constants::PERM_SEND_NEWSLETTER],

            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_CREATE_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_UPDATE_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_PUBLISH_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_DELETE_PAGE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_MENU],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_CREATE_MENU],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_UPDATE_MENU],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_UPDATE_CUSTOMIZATION],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_ROLE],
            ['role_id' => Constants::ROLE_EDITOR, 'permission_id' => Constants::PERM_READ_REVIEW],

            ['role_id' => Constants::ROLE_MODERATOR, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_MODERATOR, 'permission_id' => Constants::PERM_READ_REVIEW],
            ['role_id' => Constants::ROLE_MODERATOR, 'permission_id' => Constants::PERM_MANAGE_REVIEW],
            ['role_id' => Constants::ROLE_MODERATOR, 'permission_id' => Constants::PERM_DELETE_REVIEW],
            ['role_id' => Constants::ROLE_MODERATOR, 'permission_id' => Constants::PERM_READ_NEWSLETTER],

            ['role_id' => Constants::ROLE_SUBSCRIBER, 'permission_id' => Constants::PERM_READ_PAGE],
            ['role_id' => Constants::ROLE_SUBSCRIBER, 'permission_id' => Constants::PERM_CREATE_PAGE],
        ];
    }

    public static function settings()
    {
        return [
            ['name' => Constants::STG_TITLE, 'value' => null],
            ['name' => Constants::STG_DESCRIPTION, 'value' => null],
            ['name' => Constants::STG_EMAIL_ADMIN, 'value' => null],
            ['name' => Constants::STG_EMAIL_CONTACT, 'value' => null],
            ['name' => Constants::STG_ROLE, 'value' => Constants::ROLE_EDITOR],
            ['name' => Constants::STG_PUBLIC_SIGNUP, 'value' => 0],
            [
                'name' => Constants::STG_SITE_LAYOUT,
                'value' => json_encode([
                    [
                        'type' => Constants::LS_HEADER_MENU,
                        'menu_id' => 1,
                        'label' => 'Liens en-tete',
                        'data' => null,

                    ],
                    [
                        'type' => Constants::LS_FOOTER_LINKS,
                        'menu_id' => 1,
                        'label' => 'Liens utiles',
                        'data' => null,

                    ],
                    [
                        'type' => Constants::LS_FOOTER_TEXT,
                        'menu_id' => null,
                        'label' => 'A propos',
                        'data' => 'Aliquam quis urna tincidunt, pretium neque eget, accumsan felis.
Sed dictum lorem vel lacinia placerat. Donec maximus feugiat scelerisque.',

                    ],
                    [
                        'type' => Constants::LS_FOOTER_CONTACT,
                        'menu_id' => null,
                        'label' => 'Contactez-nous',
                        'data' => null,

                    ],
                    [
                        'type' => Constants::LS_FOOTER_NEWSLETTER,
                        'menu_id' => null,
                        'label' => 'Newsletter',
                        'data' => null,

                    ],
                    [
                        'type' => Constants::LS_FOOTER_SOCIALS,
                        'menu_id' => 2,
                        'label' => 'Reseaux sociaux',
                        'data' => null,
                    ],
                ])
            ],
            [
                'name' => Constants::STG_HERO_DATA,
                'value' => json_encode([
                    'status' => 1,
                    'title' => 'Bienvenue !',
                    'description' => 'Phasellus volutpat ex at purus blandit, et rhoncus sem pellentesque. Integer blandit purus tortor, quis lacinia massa porttitor.',
                    'image' => 'https://images.pexels.com/photos/2598638/pexels-photo-2598638.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
                ])
            ],
            ['name' => Constants::STG_REVIEW_ACTIVE, 'value' => 1],
            ['name' => Constants::STG_REVIEW_APPROVAL, 'value' => 1],
            ['name' => Constants::STG_REVIEW_DISPLAY_MAX, 'value' => 10],
            ['name' => Constants::STG_NEWSLETTER_ACTIVE, 'value' => 1],
        ];
    }

    public static function user()
    {
        return [
            [
                'username' => 'temp_user',
                'email' => 'mail@mail.com',
                'password' => null,
                'role' => Constants::ROLE_SUPER_ADMIN,
                'status' => Constants::STATUS_ACTIVE,
            ],
            [
                'username' => 'tester',
                'email' => 'tester@esgix.com',
                'password' => password_hash('tester123', PASSWORD_DEFAULT),
                'role' => Constants::ROLE_ADMIN,
                'status' => Constants::STATUS_ACTIVE,
            ]
        ];
    }

    public static function post()
    {
        return [
            [
                'author_id' => 1,
                'title' => 'Bienvenue sur mon site !',
                'content' => '<h1>Section principale</h1>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam eu sapien diam. Suspendisse ut mauris at mi accumsan lacinia. In dapibus in risus eu pellentesque. Vestibulum venenatis metus nunc, sit amet finibus nulla posuere eu. Pellentesque venenatis felis lacinia purus laoreet, ac porttitor justo malesuada. Proin sed purus molestie, pharetra ex quis, feugiat dolor. Nulla magna orci, interdum a tempor id, laoreet in ligula. Mauris tempor purus leo, ac facilisis ipsum porta at. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Sed vel ligula nunc.</p>
<h2>&nbsp;</h2>
<h2>Une autre section</h2>
<p>Maecenas faucibus mollis interdum. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Curabitur blandit tempus porttitor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum id ligula porta felis euismod semper.</p>
<h3>&nbsp;</h3>
<h3>Encore une autre...</h3>
<p>Vestibulum id ligula porta felis euismod semper. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus. Donec ullamcorper nulla non metus auctor fringilla.</p>
<p>&nbsp;</p>',
                'type' => Constants::POST_TYPE_PAGE,
                'status' => Constants::STATUS_PUBLISHED,
                'published_at' => date('Y-m-d H:i:s'),
            ],
            [
                'author_id' => 1,
                'title' => 'Exemple - Politique de confidentialité',
                'content' => '<h3 id="introduction">Introduction</h3>
<p>Dans le cadre de son activit&eacute;, la soci&eacute;t&eacute; [Soci&eacute;t&eacute;], dont le si&egrave;ge social est situ&eacute; au [Adresse du si&egrave;ge social], est amen&eacute;e &agrave; collecter et &agrave; traiter des informations dont certaines sont qualifi&eacute;es de "donn&eacute;es personnelles". [Soci&eacute;t&eacute;] attache une grande importance au respect de la vie priv&eacute;e, et n&rsquo;utilise que des donnes de mani&egrave;re responsable et confidentielle et dans une finalit&eacute; pr&eacute;cise.</p>
<h3 id="donn-es-personnelles">Donn&eacute;es personnelles</h3>
<p>Sur le site web [Adresse du site], il y a 2 types de donn&eacute;es susceptibles d&rsquo;&ecirc;tre recueillies :</p>
<ul>
<li><strong>Les donn&eacute;es transmises directement</strong><br />Ces donn&eacute;es sont celles que vous nous transmettez directement, via un formulaire de contact ou bien par contact direct par email. Sont obligatoires dans le formulaire de contact le champs &laquo; pr&eacute;nom et nom &raquo;, &laquo; entreprise ou organisation &raquo; et &laquo; email &raquo;.</li>
<li><strong>Les donn&eacute;es collect&eacute;es automatiquement</strong><br />Lors de vos visites, une fois votre consentement donn&eacute;, nous pouvons recueillir des informations de type &laquo; web analytics &raquo; relatives &agrave; votre navigation, la dur&eacute;e de votre consultation, votre adresse IP, votre type et version de navigateur. La technologie utilis&eacute;e est le cookie.</li>
</ul>
<h3 id="utilisation-des-donn-es">Utilisation des donn&eacute;es</h3>
<p>Les donn&eacute;es que vous nous transmettez directement sont utilis&eacute;es dans le but de vous re-contacter et/ou dans le cadre de la demande que vous nous faites. Les donn&eacute;es &laquo; web analytics &raquo; sont collect&eacute;es de forme anonyme (en enregistrant des adresses IP anonymes) par Google Analytics, et nous permettent de mesurer l&rsquo;audience de notre site web, les consultations et les &eacute;ventuelles erreurs afin d&rsquo;am&eacute;liorer constamment l&rsquo;exp&eacute;rience des utilisateurs. Ces donn&eacute;es sont utilis&eacute;es par [Soci&eacute;t&eacute;], responsable du traitement des donn&eacute;es, et ne seront jamais c&eacute;d&eacute;es &agrave; un tiers ni utilis&eacute;es &agrave; d&rsquo;autres fins que celles d&eacute;taill&eacute;es ci-dessus.</p>
<h3 id="base-l-gale">Base l&eacute;gale</h3>
<p>Les donn&eacute;es personnelles ne sont collect&eacute;es qu&rsquo;apr&egrave;s consentement obligatoire de l&rsquo;utilisateur. Ce consentement est valablement recueilli (boutons et cases &agrave; cocher), libre, clair et sans &eacute;quivoque.</p>
<h3 id="dur-e-de-conservation">Dur&eacute;e de conservation</h3>
<p>Les donn&eacute;es seront sauvegard&eacute;es durant une dur&eacute;e maximale de 3 ans.</p>
<h3 id="cookies">Cookies</h3>
<p>Voici la liste des cookies utilis&eacute;es et leur objectif :</p>
<ul>
<li>Cookies Google Analytics (<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/cookie-usage">liste exhaustive</a>) : Web analytics</li>
<li>[Nom du cookie] : Permet de garder en m&eacute;moire le fait que vous acceptez les cookies afin de ne plus vous importuner lors de votre prochaine visite.</li>
</ul>',
                'type' => Constants::POST_TYPE_PAGE,
                'status' => Constants::STATUS_DRAFT,
                'published_at' => null,
            ],
            [
                'author_id' => 1,
                'title' => 'Evenement surprise du 29/07 !',
                'content' => '<h1 style="text-align: center;">A ne surtout pas manquer !</h1>
<p>&nbsp;</p>
<h2>Au programme :</h2>
<p>- Jeux</p>
<p>- Activit&eacute;s</p>
<p>- Animation</p>
<p>- Soir&eacute;e</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p style="text-align: center;">Pellentesque venenatis sapien vitae elementum vestibulum. Integer vitae odio vitae mi faucibus facilisis sed id est. Fusce maximus turpis ante, vel consectetur orci euismod ut. Duis tincidunt facilisis maximus. Mauris ligula diam, sodales quis ante vel, dignissim gravida neque. Maecenas a ex interdum, eleifend ex et, sollicitudin mi. Nulla nec rutrum justo, a ornare sem. Ut iaculis tristique elit, mollis malesuada eros pretium aliquam. Mauris placerat in sapien vel consequat. Mauris finibus fermentum nibh, a malesuada diam rutrum quis. Donec eu mattis odio. Proin sem turpis, bibendum non arcu vitae, varius faucibus orci. Nullam eleifend odio iaculis magna vulputate aliquam. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nullam aliquam fringilla felis ac condimentum.</p>',
                'type' => Constants::POST_TYPE_NEWSLETTER,
                'status' => Constants::STATUS_DRAFT,
                'published_at' => null,
            ],
        ];
    }

    public static function pageExtra()
    {
        return [
            [
                'post_id' => 1,
                'slug' => '/',
                'meta_title' => 'Bienvenue sur mon site !',
                'meta_description' => 'Vestibulum id ligula porta felis euismod semper. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus. Donec ullamcorper nulla non metus auctor fringilla.',
                'meta_indexable' => 0,
            ],
            [
                'post_id' => 2,
                'slug' => '/politique-de-confidentialite',
                'meta_title' => 'Politique de confidentialité',
                'meta_description' => 'Vestibulum id ligula porta felis euismod semper. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec id elit non mi porta gravida at eget metus. Donec ullamcorper nulla non metus auctor fringilla.',
                'meta_indexable' => 0,
            ],
        ];
    }

    public static function menuItem()
    {
        return [
            [
                'menu_id' => 1,
                'post_id' => 1,
                'label' => 'Accueil',
                'icon' => null,
                'url' => null,
            ],
            [
                'menu_id' => 1,
                'post_id' => null,
                'label' => 'Donner mon avis',
                'icon' => null,
                'url' => '/review',
            ],
            [
                'menu_id' => 1,
                'post_id' => null,
                'label' => 'Nos avis',
                'icon' => null,
                'url' => '/reviews',
            ],
            [
                'menu_id' => 2,
                'post_id' => null,
                'label' => 'facebook',
                'icon' => 'fab fa-facebook',
                'url' => '#',
            ],
            [
                'menu_id' => 2,
                'post_id' => null,
                'label' => 'instagram',
                'icon' => 'fab fa-instagram',
                'url' => '#',
            ],
            [
                'menu_id' => 2,
                'post_id' => null,
                'label' => 'twitter',
                'icon' => 'fab fa-twitter',
                'url' => '#',
            ],
            [
                'menu_id' => 2,
                'post_id' => null,
                'label' => 'youtube',
                'icon' => 'fab fa-youtube',
                'url' => '#',
            ],
        ];
    }

    public static function menu()
    {
        return [
            [
                'title' => 'Liens en-tete',
                'type' => Constants::MENU_LINKS,
                'status' => Constants::STATUS_ACTIVE
            ],
            [
                'title' => 'Réseaux sociaux',
                'type' => Constants::MENU_SOCIALS,
                'status' => Constants::STATUS_ACTIVE
            ],
        ];
    }

    public static function review()
    {
        return [
            [
                'rate' => 4,
                'author' => 'David',
                'email' => 'david.kaliz@mail.com',
                'review' => 'Incroyable expérience !!',
                'status' => Constants::REVIEW_VALID,
                'date' => date('Y-m-d', strtotime('-3 days')),
            ],
            [
                'rate' => 2,
                'author' => 'Bernard',
                'email' => 'bernard.muzko@mail.com',
                'review' => 'C\'est pas super !
                Service indisponible tous les jours...',
                'status' => Constants::REVIEW_VALID,
                'date' => date('Y-m-d', strtotime('-2 days')),
            ],
            [
                'rate' => 2,
                'author' => 'Logan',
                'email' => 'logan.sartelle@mail.com',
                'review' => 'Bof bof, il y a mieux quand même.',
                'status' => Constants::REVIEW_VALID,
                'date' => date('Y-m-d', strtotime('-1 days')),
            ],
        ];
    }

    public static function visitor()
    {
        return [
            [
                'ip' => '172.18.0.26',
                'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) XXXXX',
                'uri' => '/',
                'date' => date('Y-m-d', strtotime('-2 days')),
            ],
            [
                'ip' => '172.18.0.25',
                'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) XXXXX',
                'uri' => '/',
                'date' => date('Y-m-d', strtotime('-2 days')),
            ],
            [
                'ip' => '172.18.0.24',
                'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) XXXXX',
                'uri' => '/',
                'date' => date('Y-m-d', strtotime('-1 days')),
            ],
            [
                'ip' => '172.18.0.23',
                'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) XXXXX',
                'uri' => '/',
                'date' => date('Y-m-d', strtotime('-1 days')),
            ],
            [
                'ip' => '172.18.0.26',
                'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) XXXXX',
                'uri' => '/',
                'date' => date('Y-m-d', strtotime('-1 days')),
            ],
            [
                'ip' => '172.18.0.25',
                'agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) XXXXX',
                'uri' => '/',
                'date' => date('Y-m-d', strtotime('-1 days')),
            ],
        ];
    }

    public static function subscriber()
    {
        return [
            [
                'email' => 'mahery.rsh@gmail.com',
                'status' => Constants::STATUS_ACTIVE
            ],
            [
                'email' => 'mahery.rsh@gmail.com',
                'status' => Constants::STATUS_ACTIVE
            ],
            [
                'email' => 'mahery.rsh@gmail.com',
                'status' => Constants::STATUS_ACTIVE
            ],
            [
                'email' => 'mahery.rsh@gmail.com',
                'status' => Constants::STATUS_ACTIVE
            ],
            [
                'email' => 'mahery.rsh@gmail.com',
                'status' => Constants::STATUS_ACTIVE
            ],
            [
                'email' => 'mahery.rsh@gmail.com',
                'status' => Constants::STATUS_ACTIVE
            ],
        ];
    }
}
