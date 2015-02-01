<?php
namespace Sotnik\GridBundle\Batch;

use Symfony\Component\HttpFoundation\Request;

class BatchRequestHandler implements BatchRequestHandlerInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var BatchActionInterface[]
     */
    private $batchActions;

    public function __construct(Request $request, $formScopePrefix, array $batchActions)
    {
        $this->request = $request;
        $this->scope = $formScopePrefix . self::SCOPE;
        $this->batchActions = $batchActions;
    }

    public function handle()
    {
        $formData = $this->request->request->get($this->scope);

        if (isset($formData[self::ACTION_INDEX]) &&
            isset($this->batchActions[$formData[self::ACTION_INDEX]]) &&
            isset($formData[self::ACTION_ID]) &&
            is_array($formData[self::ACTION_ID]) &&
            !empty($formData[self::ACTION_ID])) {

            $batchAction = $this->batchActions[$formData[self::ACTION_INDEX]];
            $action = $batchAction->getAction();
            $action($formData[self::ACTION_ID]);
        }
    }
}
