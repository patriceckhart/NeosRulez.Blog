<?php
namespace NeosRulez\Blog\DataSource;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;

class CategoriesDataSource extends AbstractDataSource
{

    /**
     * @var string
     */
    static protected $identifier = 'neosrulez-blog-categories';

    /**
     * @Flow\Inject
     * @var \NeosRulez\Blog\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @param NodeInterface $node The node that is currently edited (optional)
     * @param array $arguments Additional arguments (key / value)
     * @return array
     */
    public function getData(NodeInterface $node = null, array $arguments)
    {
        $options = [];
        foreach ($this->categoryRepository->getCategories() as $category) {
            $options[$this->persistenceManager->getIdentifierByObject($category)] = ['label' => $category->getName()];
        }
        return $options;
    }
}
