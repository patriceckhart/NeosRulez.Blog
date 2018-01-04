<?php
namespace NeosRulez\Blog\Eel\FlowQueryOperations;

/*
 * This file is part of the NeosRulez.Blog package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
/**
 * Extended EEL filter() operation for NeosRulez.Blog
 */
class FilterOperation extends \Neos\ContentRepository\Eel\FlowQueryOperations\FilterOperation {
    /**
     * {@inheritdoc}
     *
     * @var integer
     */
    static protected $priority = 200;
    /**
     * @var string
     */
    protected $dateTimeFormat = '';
    /**
     * {@inheritdoc}
     *
     * @param array (or array-like object) $context onto which this operation should be applied
     * @return boolean TRUE if the operation can be applied onto the $context, FALSE otherwise
     */
    public function canEvaluate($context) {
        return (isset($context[0]) && ($context[0] instanceof NodeInterface) && $context[0]->getNodeType()->isOfType('NeosRulez.Blog:BlogPost'));
    }
    /**
     * {@inheritdoc}
     *
     * @param \Neos\Eel\FlowQuery\FlowQuery $flowQuery
     * @param array $arguments
     * @return void
     */
    public function evaluate(\Neos\Eel\FlowQuery\FlowQuery $flowQuery, array $arguments) {
        if (isset($arguments[1]) && is_string($arguments[1]) && !empty($arguments[1])) {
            $this->dateTimeFormat = $arguments[1];
        }
        parent::evaluate($flowQuery, $arguments);
    }
    /**
     * {@inheritdoc}
     *
     * @param object $element
     * @param string $propertyPath
     * @return mixed
     */
    protected function getPropertyPath($element, $propertyPath) {
        switch($propertyPath) {
            case 'blogcategories':
                return $element->getProperty($propertyPath, TRUE);
                break;
        }
        return parent::getPropertyPath($element, $propertyPath);
    }
    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     * @param string $operator
     * @param mixed $operand
     * @return boolean
     */
    protected function evaluateOperator($value, $operator, $operand) {
        if ($operator === '*=') {
            if (is_array($value)) {
                if (is_string($operand)) {
                    $operandValues = explode(',', $operand);
                    return count(array_intersect($value, $operandValues)) > 0;
                }
                return FALSE;
            }
        }
        return parent::evaluateOperator($value, $operator, $operand);
    }
}