<?php
namespace NeosRulez\Blog\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;

class BlogPostsImplementation extends AbstractFusionObject {

    /**
     * @return string
     */
    public function evaluate() {
        return 'foo';
    }

}