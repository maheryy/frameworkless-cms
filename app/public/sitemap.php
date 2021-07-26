<?php

namespace App;

use App\Core\Utils\ConstantManager;
use App\Core\Utils\Formatter;
use App\Core\Utils\Repository;

require __DIR__ . '/../core/utils/Autoloader.php';

Autoloader::register();
ConstantManager::loadConstants();

$pages = (new Repository)->post->findPublishedPages();
$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
header("Content-Type: application/xml; charset=utf-8");
?>
<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($pages as $page) : ?>
        <url>
            <loc><?= "{$protocol}://{$_SERVER['HTTP_HOST']}{$page['slug']}" ?></loc>
            <lastmod><?= Formatter::getDateTime($page['updated_at'], 'Y-m-d') ?></lastmod>
        </url>
    <?php endforeach; ?>
</urlset>