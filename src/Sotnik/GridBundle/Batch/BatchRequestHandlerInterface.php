<?php
namespace Sotnik\GridBundle\Batch;

interface BatchRequestHandlerInterface
{
    const SCOPE = 'batch_action';

    const ACTION_ID = 'id';

    const ACTION_INDEX = 'index';

    public function handle();
}
