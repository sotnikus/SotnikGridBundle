<?php
namespace Sotnik\GridBundle\RowAction;

use Sotnik\GridBundle\Extend\ConfirmableInterface;

interface ActionInterface extends ConfirmableInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $icon
     */
    public function prependIcon($icon);

    /**
     * @param string $targetAttr
     */
    public function setTargetAttr($targetAttr);

    /**
     * @return string
     */
    public function getTargetAttr();

    /**
     * @param $urlGetterArg
     * @return string
     */
    public function handleUrlGetter($urlGetterArg);

    /**
     * @return string
     */
    public function getUrl();
}
