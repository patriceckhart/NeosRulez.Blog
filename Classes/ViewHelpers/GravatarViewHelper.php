<?php
namespace NeosRulez\Blog\ViewHelpers;

class GravatarViewHelper extends \Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper {

    public function initializeArguments()
    {
        $this->registerArgument('authorMail', 'string', 'Authors email adress', true);
    }

    /**
     * @return string
     */
    public function render() {
        $md5AuthorMail = md5($this->arguments['authorMail']);
        return $md5AuthorMail;
    }

}