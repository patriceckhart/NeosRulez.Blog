<?php
namespace NeosRulez\Blog\Controller;

/*
 * This file is part of the NeosRulez.Blog package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Eel\FlowQuery\Operations;
use NeosRulez\Blog\FlowQueryOperations\FilterOperation;

class BlogController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \Neos\ContentRepository\Domain\Service\ContextFactoryInterface
     */
    protected $contextFactory;

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

    /**
     * @return void
     */
    public function indexPostsAction() {
        $posts = $this->request->getInternalArgument('__posts');
        $showdate = $this->request->getInternalArgument('__showdate');
        $nodeidentifier = $this->request->getInternalArgument('__nodeidentifier');
        $pagebrowser = $this->request->getInternalArgument('__pagebrowser');
        $sorting = $this->request->getInternalArgument('__sorting');
        $category = $this->request->getInternalArgument('__blogcategories');
        $showsubtitle = $this->request->getInternalArgument('__showsubtitle');
        $showaslist = $this->request->getInternalArgument('__showaslist');
        $this->view->assign('pagebrowser', $pagebrowser);
        $this->view->assign('showdate', $showdate);
        $this->view->assign('showsubtitle', $showsubtitle);
        $this->view->assign('showaslist', $showaslist);
        $filterString = '';
        $workspaceName = "live";
        if (empty($posts)) {
            $itemsPerPage = 6;
        } else {
            $itemsPerPage = $posts;
        }
        if($sorting=="ascending") {
            $sorting = "ASC";
        } else {
            $sorting = "DESC";
        }
        $context = $this->contextFactory->create(array('workspaceName' => $workspaceName));
        if ($nodeidentifier==null) {
            $this->view->assign('noIdent', '1');
        } else {
            $node = $context->getNodeByIdentifier($nodeidentifier->getIdentifier());
            $filterString = '[blogcategories *= "';
            foreach ($category as $cat) {
                $filterString .= $cat->getIdentifier().',';
            }
            $filterString .= '"]';
            $articles = (new FlowQuery(array($node)))->children('[instanceof NeosRulez.Blog:BlogPost]')->context(array('workspaceName' => 'live'))->sort('_index', $sorting)->filter($filterString)->get();
            $this->view->assign('pathsegment', $node->getProperty('uriPathSegment'));
            if ($this->request->hasArgument('page')) {
                $page = $this->request->getArgument('page');
            } else {
                $page = 1;
            }
            $resultsCount = count($articles);
            if ($resultsCount <= $itemsPerPage) {
                $items = $articles;
            } else {
                $queryOffset = $itemsPerPage * ($page - 1);
                $queryItems = $itemsPerPage * $page;
                $this->view->assign('queryOffset', $queryOffset);
                $filterString .= '[blogcategories *= "' . $category . '"]';
                $items = (new FlowQuery(array($node)))->children('[instanceof NeosRulez.Blog:BlogPost]')->context(array('workspaceName' => 'live'))->sort('_index', $sorting)->slice($queryOffset, $queryItems)->filter($filterString)->get();
                $pages = ceil($resultsCount / $itemsPerPage);
                if ($pages <= 10) {
                    $minPagination = 1;
                    $maxPagination = $pages;
                }
                if ($pages > 10) {
                    $maxPagination = $page + 5;
                    if ($maxPagination > $pages) {
                        $maxPagination = $pages;
                    }
                    $minPagination = $maxPagination - 11;
                    if ($minPagination < 1) {
                        $minPagination = 1;
                        $maxPagination = 11;
                    }
                }
                for ($i = $minPagination; $i <= $maxPagination; $i++) {
                    $pagination['pages'][$i]['text'] = $i;
                }
                if ($page > 1) {
                    $pagination['prev'] = $page - 1;
                }
                if ($page < $pages) {
                    $pagination['next'] = $page + 1;
                }
                if ($minPagination > 1) {
                    $pagination['first'] = 1;
                }
                if ($maxPagination < $pages) {
                    $pagination['last'] = $pages;
                }
            }
            $pagination['current'] = $page;
            $this->view->assign('pagination', $pagination);
            $this->view->assign('items', $items);
        }
    }

    /**
     * @return void
     */
    public function showAction() {
        $this->view->assign('blogtitle', $this->request->getInternalArgument('__blogtitle'));
        $this->view->assign('blogsubtitle', $this->request->getInternalArgument('__blogsubtitle'));
        $this->view->assign('blogimage', $this->request->getInternalArgument('__blogimage'));
        $this->view->assign('blogpostcontent', $this->request->getInternalArgument('__blogpostcontent'));
        $this->view->assign('blogauthor', $this->request->getInternalArgument('__blogauthor'));
        $this->view->assign('blogdate', $this->request->getInternalArgument('__blogdate'));
        $this->view->assign('node', $this->request->getInternalArgument('__node'));

        $blogcategories = $this->request->getInternalArgument('__blogcategories');

        $this->view->assign('blogcategories', $blogcategories);

        $this->view->assign('showAuthor', $this->settings['showAuthor']);
        $this->view->assign('showDate', $this->settings['showDate']);
        $this->view->assign('showSocialShare', $this->settings['showSocialShare']);
        $this->view->assign('showCategory', $this->settings['showCategory']);

        $this->view->assign('twitterAccountName', $this->settings['twitterAccountName']);

        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $this->view->assign('postUri', $url);
    }

}
