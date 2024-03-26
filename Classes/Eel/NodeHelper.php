<?php
namespace NeosRulez\Blog\Eel;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Projection\Content\TraversableNodeInterface;
use Neos\Eel\ProtectedContextAwareInterface;

class NodeHelper implements ProtectedContextAwareInterface
{

    /**
     * @param Node|TraversableNodeInterface $node
     * @return Node|TraversableNodeInterface
     */
    public function findClosestDocumentNode(Node|TraversableNodeInterface $node): Node|TraversableNodeInterface
    {
        return $this->getClostestDocumentNode($node);
    }

    /**
     * @param Node|TraversableNodeInterface $node
     * @return Node|TraversableNodeInterface
     */
    private function getClostestDocumentNode(Node|TraversableNodeInterface $node): Node|TraversableNodeInterface
    {
        if($node->getNodeType()->isOfType('Neos.Neos:Document')) {
            return $node;
        }
        return $this->getClostestDocumentNode($node->findParentNode());
    }

    /**
     * All methods are considered safe, i.e. can be executed from within Eel
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }

}
