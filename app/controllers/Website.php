<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Exceptions\HttpNotFoundException;
use App\Core\Utils\Constants;
use App\Core\Utils\Formatter;
use App\Core\Utils\Mailer;
use App\Core\Utils\Validator;
use App\Core\View;


class Website extends Controller
{
    private string $uri;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->uri = $this->router->getUri();
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
        if (empty($view)) throw new \Exception("Une erreur est survenue, la page demandée n'est pas disponible");

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
        if (!$page) throw new HttpNotFoundException($this->uri);

        return [
            'view' => 'website_default',
            'context' => [
                'meta_title' => $page['meta_title'],
                'meta_description' => $page['meta_description'],
                'is_indexable' => $page['meta_indexable'],
                'content_title' => $page['title'],
                'content' => $page['content'],
                'display_hero' => $this->uri === '/',
            ]
        ];
    }

    private function getReviewsPage()
    {
        return [
            'view' => 'website_review_list',
            'context' => [
                'meta_title' => 'Les avis de nos clients',
                'meta_description' => 'Les avis de nos clients',
                'is_indexable' => false,
                'reviews' => $this->repository->review->findAll()
            ]
        ];
    }

    private function getReviewFormPage()
    {
        return [
            'view' => 'website_review_form',
            'context' => [
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
            'to' => $this->settings['email_contact'] ?? $this->settings['email_admin'],
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
                'status' => Constants::REVIEW_VALID,
                'date' => date('Y-m-d'),
            ]);

            # Send confirmation email to the reviewer
//            $mail = Mailer::send([
//                'to' => $email,
//                'subject' => 'Confirmation de votre inscription à notre newsletter',
//                'content' => View::getHtml('email/newsletter_confirmation', [
//                    'message' => nl2br("Votre inscription a bien été pris en compte."),
//                    'unsubscribe_link' => 'http://' . $_SERVER['HTTP_HOST'] . '/admin/unsubscribe?subscriber=' . $subscriber_id,
//                ]),
//            ]);
//
//            if (!$mail['success']) {
//                throw new \Exception($mail['message']);
//            }

            Database::commit();
        } catch (\Exception $e) {
            Database::rollback();
            return ['success' => false, 'message' => "Une erreur est survenue : " . $e->getMessage()];
        }

        return ['success' => true, 'message' => 'Votre avis a bien été pris en compte'];
    }
}
