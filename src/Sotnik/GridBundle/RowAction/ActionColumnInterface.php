<?php
namespace Sotnik\GridBundle\RowAction;

interface ActionColumnInterface
{
    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $width
     */
    public function setWidth($width);

    /**
     * @return string
     */
    public function getWidth();

    /**
     * @param Action $action
     */
    public function addAction(Action $action);

    /**
     * @return Action[]
     */
    public function getActions();
}
