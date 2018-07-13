<?php
namespace NeosRulez\Blog\Domain\Repository;

/*
 * This file is part of the NeosRulez.Blog package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class CategoryRepository extends Repository
{

    public function getCategories() {

        $classname = '\NeosRulez\Blog\Domain\Model\Category';
        $query = $this->persistenceManager->createQueryForType($classname);
        $results = $query->execute();
        return $results;

    }

}