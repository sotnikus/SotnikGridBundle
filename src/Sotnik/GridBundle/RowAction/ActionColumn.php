<?php
namespace Sotnik\GridBundle\RowAction;

class ActionColumn implements ActionColumnInterface
{
    /**
     * @var Action[]
     */
    protected $actions = [];

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $width = '';

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param array Action[]
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[] =  $action;
    }

    /**
     * @return \Closure[]
     */
    public function getActions()
    {
        return $this->actions;
    }
}
