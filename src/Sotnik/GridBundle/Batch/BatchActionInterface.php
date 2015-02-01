<?php
namespace Sotnik\GridBundle\Batch;

use Sotnik\GridBundle\Extend\ConfirmableInterface;

interface BatchActionInterface extends ConfirmableInterface
{
    /**
     * @return \Closure
     */
    public function getAction();

    /**
     * @return string
     */
    public function getLabel();
}
