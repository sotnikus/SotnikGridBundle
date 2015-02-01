<?php
namespace Sotnik\GridBundle\Batch;

use Sotnik\GridBundle\Extend\Confirmable;

class BatchAction extends Confirmable implements BatchActionInterface
{
    /**
     * @var \Closure
     */
    private $action;

    /**
     * @var string
     */
    private $label;


    public function __construct($label, \Closure $action)
    {
        $this->action = $action;
        $this->label = $label;
    }

    /**
     * @return \Closure
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
