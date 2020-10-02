<?php
namespace NeosRulez\Blog\Controller;

/*
 * This file is part of the NeosRulez.Blog package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Eel\FlowQuery\Operations;

class CommentController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \Neos\ContentRepository\Domain\Service\ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @Flow\Inject
     * @var \Neos\ContentRepository\Domain\Service\NodeTypeManager
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
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }


    public function createAction() {

        $args = $this->request->getArguments()['args'];

        $context = $this->contextFactory->create();
        $newNode = new \Neos\ContentRepository\Domain\Model\NodeTemplate();
        $newNode->setNodeType($this->nodeTypeManager->getNodeType('NeosRulez.Blog:Content.BlogPostComment'));
        $parentNode = $context->getNodeByIdentifier($args['parentnode']);
        $childNodes = $parentNode->getChildNodes();
        $commentNode = end($childNodes);

        $now = new \DateTime();
        $newNode->setProperty('blogPostCommentdate', $now);
        $newNode->setProperty('blogPostCommentAuthorName',$args['name']);
        $newNode->setProperty('blogPostCommentAuthorEmail',$args['email']);
        $newNode->setProperty('blogPostCommentMessage',$args['message']);
        $newNode->setProperty('title',$args['name']);

        $commentNode->createNodeFromTemplate($newNode);

        if(!empty($this->settings['notificationMail'])) {
            $this->sendNotificationAction($_SERVER['HTTP_REFERER']);
        }

        $this->redirectToUri($_SERVER['HTTP_REFERER'].'#comments');

    }

    /**
     * @param string $url
     */
    public function sendNotificationAction($url) {

        $mail = new \Neos\SwiftMailer\Message();
        $mail
            ->setFrom(array($this->settings['notificationMail'] => $this->settings['notificationMail']))
            ->setTo(array($this->settings['notificationMail'] => $this->settings['notificationMail']))
            ->setSubject('Blog Notification');
        $mail->setBody('New comment on: '.$url, 'text/html');
        $mail->send();

    }

}
