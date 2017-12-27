<?php
namespace NeosRulez\Blog\Controller;

/*
 * This file is part of the NeosRulez.Blog package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Eel\FlowQuery\Operations;

class BlogController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \Neos\ContentRepository\Domain\Service\ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @return void
     */
    public function indexPostsAction() {
        $posts = $this->request->getInternalArgument('__posts');
        $showdate = $this->request->getInternalArgument('__showdate');
        $nodeidentifier = $this->request->getInternalArgument('__nodeidentifier');
        $pagebrowser = $this->request->getInternalArgument('__pagebrowser');
        $sorting = $this->request->getInternalArgument('__sorting');
        $categories = $this->request->getInternalArgument('__blogcategories');
        $this->view->assign('categories', $categories);
        $this->view->assign('pagebrowser', $pagebrowser);
        $this->view->assign('showdate', $showdate);
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
            $articles = (new FlowQuery(array($node)))->children('[instanceof NeosRulez.Blog:BlogPost]')->context(array('workspaceName' => 'live'))->sort('_index', $sorting)->get();
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
                $items = (new FlowQuery(array($node)))->children('[instanceof NeosRulez.Blog:BlogPost]')->context(array('workspaceName' => 'live'))->sort('_index', $sorting)->slice($queryOffset, $queryItems)->get();
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

}
