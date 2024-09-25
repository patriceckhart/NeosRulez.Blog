<?php
namespace NeosRulez\Blog\Controller;

/*
 * This file is part of the NeosRulez.Blog package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeTemplate;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\SwiftMailer\Message;

class CommentController extends ActionController
{

    /**
     * @Flow\Inject
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @Flow\Inject
     * @var NodeTypeManager
     */
    protected $nodeTypeManager;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @param array $args
     * @return void
     */
    public function createAction(array $args): void
    {
        $context = $this->contextFactory->create();
        $newNode = new NodeTemplate();
        $newNode->setNodeType($this->nodeTypeManager->getNodeType('NeosRulez.Blog:Content.BlogPostComment'));
        $parentNode = $context->getNodeByIdentifier($args['parentnode']);
        if($parentNode !== null) {
            $childNodes = $parentNode->getChildNodes();
            $commentNode = end($childNodes);

            $now = new \DateTime();
            $newNode->setProperty('blogPostCommentdate', $now);
            $newNode->setProperty('blogPostCommentAuthorName', $args['name']);
            $newNode->setProperty('blogPostCommentAuthorEmail', $args['email']);
            $newNode->setProperty('blogPostCommentMessage', $args['message']);
            $newNode->setProperty('title', $args['name']);

            $commentNode->createNodeFromTemplate($newNode);

            if(!empty($this->settings['notificationMail'])) {
                $this->sendNotificationAction($_SERVER['HTTP_REFERER']);
            }

            $this->redirectToUri($_SERVER['HTTP_REFERER'] . '#comments');
        }
    }

    /**
     * @param string $url
     */
    public function sendNotificationAction(string $url): void
    {
        $mail = new Message();
        $mail
            ->setFrom(array($this->settings['notificationMail'] => $this->settings['notificationMail']))
            ->setTo(array($this->settings['notificationMail'] => $this->settings['notificationMail']))
            ->setSubject('Blog Notification');
        $mail->setBody('New comment on: '.$url, 'text/html');
        $mail->send();
    }

}
