<?php
namespace NeosRulez\Blog\Eel\Helper;

use Neos\Flow\Annotations as Flow;

class DataHelper extends \Neos\Eel\Helper\ArrayHelper
{

    /**
     * @Flow\Inject
     * @var \NeosRulez\Blog\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param array $categoryIdentifiers
     * @return array<Category>
     */
    public function categories($categoryIdentifiers)
    {
        $categories = [];
        foreach ($categoryIdentifiers as $categoryIdentifier) {
            $category = $this->categoryRepository->findByIdentifier($categoryIdentifier);
            if ($category) {
                $category[] = $category;
            }
        }
        return implode(', ', array_map(function ($category) {
            return $category->getName();
        }, $categories));
    }

}
