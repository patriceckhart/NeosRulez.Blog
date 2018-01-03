<?php
namespace NeosRulez\Blog\DataSource;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Utility\TypeHandling;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\Neos\Domain\Service\UserService;
use Neos\ContentRepository\Domain\Model\NodeInterface;

class AuthorsDataSource extends AbstractDataSource {

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;


    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @var string
     */
    protected static $identifier = 'neosrulez-blog-authors';

    /**
     * @inheritDoc
     */
    public function getData(NodeInterface $node = null, array $arguments)
    {
        $options = [];

        $authors = $this->userService->getUsers();

        foreach ($authors as $author) {
            $options[] = [
                'label' => $author->getLabel(),
                'value' => $author->getLabel()
                /*'value' => json_encode([
                    '__identity' => $this->persistenceManager->getIdentifierByObject($author),
                    '__type' => TypeHandling::getTypeForValue($author)
                ])*/
            ];
        }

        return $options;
    }

}
