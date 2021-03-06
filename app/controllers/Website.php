<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Router;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\Mailer;
use App\Core\Utils\Repository;
use App\Core\Utils\Request;
use App\Core\Utils\Validator;
use App\Core\View;


class Website extends Controller
{
    private string $uri;

    public function __construct(array $options = [])
    {
        $this->router = Router::getInstance();
        $this->request = new Request();
        $this->repository = new Repository();
        $this->uri = $this->router->getUri();

        # Database check before taking any actions
        if (!Database::isReady()) {
            if (Request::isPost()) $this->sendError("Une installation est nécessaire : ${_SERVER['HTTP_HOST']}/admin/installer");

            $this->router->redirect('/admin/installer');
        }

        $this->setTemplate('website');
    }

    private function setNewVisitor(?string $ip, ?string $agent, string $uri, string $date)
    {
        if (!$this->repository->visitor->findUniqueVisitor($ip, $uri, $date)) {
            $this->repository->visitor->create([
                'ip' => $ip ?? '',
                'agent' => $agent ?? '',
                'uri' => $uri,
                'date' => $date,
            ]);
        }
    }

    # GET routes
    public function display()
    {
        switch ($this->uri) {
            case '/reviews' :
                $view = $this->getReviewsPage();
                break;
            case '/review' :
                $view = $this->getReviewFormPage();
                break;
            default :
                $view = $this->getUserDefinedPage();
                break;
        }
        # Unexpected case
        if (empty($view)) $this->handleUnexpectedError('Une erreur est survenue, la page demandée n\'est pas disponible');

        $layout = $this->getLayoutData();
        $view['context']['header_menu'] = $layout['header_menu'] ?? [];
        $view['context']['footer_sections'] = $layout['footer_data']['sections'] ?? [];
        $view['context']['footer_socials'] = $layout['footer_data']['socials'] ?? [];

        $this->setNewVisitor($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $this->uri, date('Y-m-d'));
        $this->render($view['view'], $view['context']);
    }

    # POST routes
    public function formAction()
    {
        $res = [];
        switch ($this->request->get('action')) {
            case 'contact':
                $res = $this->handleContactUs($this->request->post('email'), $this->request->post('message'));
                break;
            case 'newsletter':
                $res = $this->handleNewsletter($this->request->post('email'));
                break;
            case 'review':
                $res = $this->handleReview($this->request->post('rate'), $this->request->post('name'), $this->request->post('email'), $this->request->post('review'));
                break;
        }
        $this->sendJSON($res);
    }

    private function getUserDefinedPage()
    {
        $page = $this->repository->post->findPageBySlug($this->uri);
        if (!$page) $this->handle404NotFound();
        $hero_data = json_decode($this->getValue(Constants::STG_HERO_DATA), true);
        return [
            'view' => $page ? 'website_default' : 'error_404',
            'context' => [
                'site_title' => $this->getValue(Constants::STG_TITLE),
                'site_description' => $this->getValue(Constants::STG_DESCRIPTION),
                'meta_title' => $page['meta_title'] ?? null,
                'meta_description' => $page['meta_description'] ?? null,
                'is_indexable' => $page['meta_indexable'] ?? null,
                'content_title' => $page['title'] ?? null,
                'content' => $page['content'] ?? null,
                'display_hero' => !empty($hero_data) && ($hero_data['status'] == 1 && $this->uri === '/' || $hero_data['status'] == 2),
                'hero_data' => $hero_data ?? [],
            ]
        ];
    }

    private function handle404NotFound()
    {
        $layout = $this->getLayoutData();
        $this->render('error_404', [
            'site_title' => $this->getValue(Constants::STG_TITLE),
            'is_indexable' => false,
            'header_menu' => $layout['header_menu'] ?? [],
            'footer_sections' => $layout['footer_data']['sections'] ?? [],
            'footer_socials' => $layout['footer_data']['socials'] ?? [],
        ]);
    }

    private function handleUnexpectedError(string $error)
    {
        $layout = $this->getLayoutData();
        $this->render('error_default', [
            'error_message' => $error,
            'site_title' => $this->getValue(Constants::STG_TITLE),
            'is_indexable' => false,
            'header_menu' => $layout['header_menu'] ?? [],
            'footer_sections' => $layout['footer_data']['sections'] ?? [],
            'footer_socials' => $layout['footer_data']['socials'] ?? [],
        ]);
    }

    private function getReviewsPage()
    {
        if (!$this->getValue(Constants::STG_REVIEW_ACTIVE)) $this->handle404NotFound();
        return [
            'view' => 'website_review_list',
            'context' => [
                'site_title' => $this->getValue(Constants::STG_TITLE),
                'meta_title' => 'Les avis de nos clients',
                'meta_description' => 'Les avis de nos clients',
                'is_indexable' => false,
                'reviews' => $this->repository->review->findAllApproved((int) $this->getValue(Constants::STG_REVIEW_DISPLAY_MAX))
            ]
        ];
    }

    private function getReviewFormPage()
    {
        if (!$this->getValue(Constants::STG_REVIEW_ACTIVE)) $this->handle404NotFound();
        return [
            'view' => 'website_review_form',
            'context' => [
                'site_title' => $this->getValue(Constants::STG_TITLE),
                'meta_title' => 'Votre avis',
                'meta_description' => 'Votre avis compte',
                'is_indexable' => false,
            ]
        ];
    }

    private function handleContactUs(?string $email, ?string $message)
    {
        if (!Validator::isValidEmail($email)) {
            return ['success' => false, 'message' => Validator::ERROR_EMAIL_DEFAULT];
        }
        if (!Validator::isValid($message)) {
            return ['success' => false, 'message' => 'le message ne peut pas être vide'];
        }

        # Send message to admin or contact email registered
        $mail_contact = Mailer::send([
            'to' => $this->getValue(Constants::STG_EMAIL_CONTACT) ?? $this->getValue(Constants::STG_EMAIL_ADMIN),
            'reply_to' => $email,
            'subject' => 'Message reçu',
            'content' => View::getHtml('email/contact_form', [
                'sender' => $email,
                'message' => nl2br($message),
                'date' => Formatter::getDateTime('now', 'd/m/Y - H:i:s')
            ]),
        ]);
        if (!$mail_contact['success']) {
            return ['success' => false, 'message' => $mail_contact['message']];
        }

        # Send confirmation email to the user
        $mail_confirmation = Mailer::send([
            'to' => $email,
            'subject' => 'Votre message a été envoyé',
            'content' => View::getHtml('email/contact_confirmation', [
                'message' => nl2br("Nous vous contacterons dès que possible"),
            ]),
        ]);
        if (!$mail_confirmation['success']) {
            return ['success' => false, 'message' => $mail_confirmation['message']];
        }

        return ['success' => true, 'message' => 'Message envoyé !'];
    }

    private function handleNewsletter(?string $email)
    {
        if (!$this->getValue('newsletter_active')) {
            return ['success' => false, 'message' => 'Le service est indisponible, veuillez réessayer plus tard'];
        }
        if (!Validator::isValidEmail($email)) {
            return ['success' => false, 'message' => Validator::ERROR_EMAIL_DEFAULT];
        }

        try {
            Database::beginTransaction();
            $found = $this->repository->subscriber->findSubscriber($email);

            if (empty($found)) {
                $subscriber_id = $this->repository->subscriber->subscribe($email);
            } elseif ($found['status'] == Constants::STATUS_INACTIVE) {
                $subscriber_id = $found['id'];
                $this->repository->subscriber->update((int)$subscriber_id, ['status' => Constants::STATUS_ACTIVE]);
            } else {
                return ['success' => false, 'message' => "l'adresse email est déjà utilisé"];
            }
            # Send confirmation email to the user
            $mail_confirmation = Mailer::send([
                'to' => $email,
                'subject' => 'Confirmation de votre inscription à notre newsletter',
                'content' => View::getHtml('email/newsletter_confirmation', [
                    'message' => nl2br("Votre inscription a bien été pris en compte."),
                    'unsubscribe_link' => 'http://' . $_SERVER['HTTP_HOST'] . '/admin/unsubscribe?subscriber=' . $subscriber_id,
                ]),
            ]);
            if (!$mail_confirmation['success']) {
                throw new \Exception($mail_confirmation['message']);
            }

            Database::commit();
        } catch (\Exception $e) {
            Database::rollback();
            return ['success' => false, 'message' => "Une erreur est survenue : " . $e->getMessage()];
        }

        return ['success' => true, 'message' => 'Inscription bien pris en compte'];
    }

    private function handleReview(int $rate, ?string $name, ?string $email, ?string $review)
    {
        if (!$name) {
            return ['success' => false, 'message' => 'Le nom est obligatoire'];
        }
        if (!Validator::isValidEmail($email)) {
            return ['success' => false, 'message' => Validator::ERROR_EMAIL_DEFAULT];
        }

        if (!empty($this->repository->review->findReviewByEmailAndDate($email, date('Y-m-d')))) {
            return ['success' => false, 'message' => 'Vous avez déjà soumis un avis'];
        }

        try {
            Database::beginTransaction();
            $this->repository->review->create([
                'rate' => $rate,
                'author' => $name,
                'email' => $email,
                'review' => $review,
                'status' => $this->getValue(Constants::STG_REVIEW_APPROVAL) ? Constants::REVIEW_VALID : Constants::REVIEW_PENDING,
                'date' => date('Y-m-d H:i:s'),
            ]);


            Database::commit();
        } catch (\Exception $e) {
            Database::rollback();
            return ['success' => false, 'message' => "Une erreur est survenue : " . $e->getMessage()];
        }

        return ['success' => true, 'message' => 'Merci pour votre avis !', 'url_next' => '/', 'url_next_delay' => 1];
    }

    private function getLayoutData()
    {
        $data = json_decode($this->getValue(Constants::STG_SITE_LAYOUT), true);
        if (empty($data)) return ['header_menu' => [], 'footer_data' => []];

        $res = [];
        foreach ($data as $item) {
            switch ($item['type']) {
                case Constants::LS_HEADER_MENU :
                    $res['header_menu'] = $this->repository->menuItem->findMenuItems((int)$item['menu_id']);
                    break;
                case Constants::LS_FOOTER_SOCIALS :
                    $res['footer_data']['socials'] = $this->repository->menuItem->findMenuItems((int)$item['menu_id']);
                    break;
                case Constants::LS_FOOTER_TEXT :
                    $res['footer_data']['sections'][] = [
                        'type' => $item['type'],
                        'label' => $item['label'],
                        'data' => $item['data'],
                    ];
                    break;
                case Constants::LS_FOOTER_LINKS :
                    $res['footer_data']['sections'][] = [
                        'type' => $item['type'],
                        'label' => $item['label'],
                        'data' => $this->repository->menuItem->findMenuItems((int)$item['menu_id']),
                    ];
                    break;
                case Constants::LS_FOOTER_CONTACT :
                    $res['footer_data']['sections'][] = [
                        'type' => $item['type'],
                        'label' => $item['label']
                    ];
                    break;
                case Constants::LS_FOOTER_NEWSLETTER :
                    $res['footer_data']['sections'][] = [
                        'type' => $item['type'],
                        'label' => $item['label']
                    ];
                    break;
            }
        }
        return $res;
    }


    /* ----------------- Utility functions to display website components ----------------- */

    public static function getMenuHeader(array $data)
    {
        $items = null;
        foreach ($data as $item) {
            $items .= "<li class='h-link'><a href='" . ($item['page_link'] ?? $item['url']) . "'>{$item['label']}</a></li>" . PHP_EOL;
        }
        return
            '<nav class="header-nav">
                <ul class="links">' . $items . '</ul>
            </nav>' . PHP_EOL;
    }


    public static function getHero(?string $title, ?string $content, ?string $bg_image)
    {
        return
            '<section class="hero-header">
                <div id="hero-img" data-url="' . $bg_image . '"></div>
                <article class="hero-content">
                    <h1>' . $title . '</h1>
                    <p>' . nl2br($content) . '</p>
                </article>
            </section>' . PHP_EOL;
    }

    public static function getContactFooter(?string $title, int $size)
    {
        return
            '<div class="footer-section contact w-' . $size . '/12">
                <h3>' . $title . '</h3>
                <div class="section-content">
                    <form method="POST" action="contact">
                        <div class="form-field">
                            <input type="email" class="form-control" name="email" placeholder="Adresse email">
                        </div>
                        <div class="form-field">
                            <textarea class="form-control" name="message" placeholder="Message" rows="5"></textarea>
                        </div>
                        <div class="form-field">
                            <button class="form-action" value="Envoyer">Envoyer</button>
                        </div>
                        <div class="info-box">
                            <span class="info-description"></span>
                        </div>
                    </form>
                </div>
            </div>' . PHP_EOL;
    }

    public static function getNewsletterFooter(?string $title, int $size)
    {
        return
            '<div class="footer-section newsletter w-' . $size . '/12">
                <h3>' . $title . '</h3>
                <div class="section-content">
                    <form method="POST" action="newsletter">
                        <div class="form-field">
                            <input type="email" class="form-control" name="email" placeholder="Adresse email">
                            <button type="submit" class="form-action">S\'inscrire</button>
                        </div>
                        <div class="info-box">
                            <span class="info-description"></span>
                        </div>
                    </form>
                </div>
            </div>' . PHP_EOL;
    }

    public static function getTextFooter(?string $title, ?string $text, int $size)
    {
        return
            '<div class="footer-section text w-' . $size . '/12">
                <h3>' . $title . '</h3>
                <div class="section-content">
                    <p>' . $text . '</p>
                </div>
            </div>' . PHP_EOL;
    }

    public static function getLinkFooter(?string $title, array $data, int $size)
    {
        $items = null;
        foreach ($data as $item) {
            $items .= "<li><a href='" . ($item['page_link'] ?? $item['url']) . "'>{$item['label']}</a></li>" . PHP_EOL;
        }

        return
            '<nav class="footer-section link w-' . $size . '/12">
                <h3>' . $title . '</h3>
                <ul>' . $items . '</ul>
            </nav>' . PHP_EOL;
    }

    public static function getSocialFooter(array $data)
    {
        $items = null;
        foreach ($data as $item) {
            $items .= "<li class='social-item'><a href='{$item['url']}'><i class='{$item['icon']}'></i></a></li>" . PHP_EOL;
        }

        return
            '<nav class="footer-section social">
                <ul>' . $items . '</ul>
            </nav>' . PHP_EOL;
    }

}
