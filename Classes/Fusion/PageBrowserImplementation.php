<?php
namespace NeosRulez\Blog\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class PageBrowserImplementation extends AbstractFusionObject {

    /**
     * @return string
     */
    public function evaluate() {
        $posts = $this->fusionValue('posts');
        $countposts = $this->fusionValue('countposts');

        if (empty($posts)) {
            $itemsPerPage = 6;
        } else {
            $itemsPerPage = $posts;
        }

        $resultsCount = $countposts;
        if ($resultsCount <= $itemsPerPage) {
            $items = $articles;
        } else {
            $queryOffset = $itemsPerPage * ($page - 1);
            $queryItems = $itemsPerPage * $page;
            // $this->view->assign('queryOffset', $queryOffset);
            // $filterString = '[blogcategories *= "';
            // foreach ($category as $cat) {
                // $filterString .= $cat->getIdentifier().',';
            // }
            // $filterString = trim($filterString, ',');
            // $filterString .= '"]';
            // $items = (new FlowQuery(array($node)))->children('[instanceof NeosRulez.Blog:BlogPost]')->context(array('workspaceName' => 'live'))->sort('_index', $sorting)->slice($queryOffset, $queryItems)->filter($filterString)->get();
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
        // $this->view->assign('pagination', $pagination);

        $result[] = ['pagination' => $pagination, 'queryOffset' => $queryOffset];

        return $result;
    }

}