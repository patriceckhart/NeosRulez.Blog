<?php
namespace NeosRulez\Blog\Controller;

/*
 * This file is part of the NeosRulez.Blog package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use NeosRulez\Blog\Domain\Model\Category;

class CategoryController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \NeosRulez\Blog\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('categories', $this->categoryRepository->findAll());
    }

    /**
     * @param \NeosRulez\Blog\Domain\Model\Category $category
     * @return void
     */
    public function showAction(Category $category)
    {
        $this->view->assign('category', $category);
    }

    /**
     * @return void
     */
    public function newAction()
    {
    }

    /**
     * @param \NeosRulez\Blog\Domain\Model\Category $newCategory
     * @return void
     */
    public function createAction(Category $newCategory)
    {
        $this->categoryRepository->add($newCategory);
        $this->redirect('index');
    }

    /**
     * @param \NeosRulez\Blog\Domain\Model\Category $category
     * @return void
     */
    public function editAction(Category $category)
    {
        $this->view->assign('category', $category);
    }

    /**
     * @param \NeosRulez\Blog\Domain\Model\Category $category
     * @return void
     */
    public function updateAction(Category $category)
    {
        $this->categoryRepository->update($category);
        $this->redirect('index');
    }

    /**
     * @param \NeosRulez\Blog\Domain\Model\Category $category
     * @return void
     */
    public function deleteAction(Category $category)
    {
        $this->categoryRepository->remove($category);
        $this->redirect('index');
    }
}
