<?php
namespace Sotnik\GridBundle\RowAction;

use Sotnik\GridBundle\Extend\Confirmable;

class Action extends Confirmable implements ActionInterface
{
    private $urlGetter;

    private $title;

    private $url;

    private $targetAttr = '_self';

    /**
     * @param string $title
     * @param callable $urlGetter
     */
    public function __construct($title, \Closure $urlGetter)
    {
        $this->title = $title;
        $this->urlGetter = $urlGetter;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function prependIcon($icon)
    {
        $space = '';
        if ($this->title != '') {
            $space = '&nbsp;';
        }

        $title = '<span class="glyphicon glyphicon-' . $icon . '" aria-hidden="true"></span>' .
            $space . $this->getTitle();
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $targetAttr
     * @return $this
     */
    public function setTargetAttr($targetAttr)
    {
        $this->targetAttr = $targetAttr;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetAttr()
    {
        return $this->targetAttr;
    }

    /**
     * @param $urlGetterArg
     * @return string
     */
    public function handleUrlGetter($urlGetterArg)
    {
        $urlGetter = $this->urlGetter;
        $this->url =  $urlGetter($urlGetterArg);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
