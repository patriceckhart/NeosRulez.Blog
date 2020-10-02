<?php
namespace NeosRulez\Blog\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class PaginationImplementation extends AbstractFusionObject {

    /**
     * @return array
     */
    public function evaluate() {
        $posts = $this->fusionValue('posts');
        $countposts = $this->fusionValue('countposts');
        $page = $this->fusionValue('page');

        if (empty($posts)) {
            $itemsPerPage = 6;
        } else {
            $itemsPerPage = $posts;
        }

        $resultsCount = $countposts;

        if (empty($page)) {
            $page = 1;
        }

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

        $queryOffset = $itemsPerPage * ($page - 1);
        $queryItems = $itemsPerPage * $page;

        $pagination['current'] = $page;

        $prev = false;
        $next = false;

        if($page - 1 != 0) {
            $prev = $page - 1;
        }

        if($pages != $page) {
            $next = $page + 1;
        }

        $result = ['pagination' => $pagination, 'queryOffset' => $queryOffset, 'queryItems'=> $queryItems, 'page' => $page, 'prev' => $prev, 'next' => $next];

        return $result;
    }

}