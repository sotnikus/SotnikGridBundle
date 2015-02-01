<?php
namespace Sotnik\GridBundle\Column;

use Sotnik\GridBundle\ColumnFilter\Filter;

class BooleanColumn extends CommonColumn implements ColumnInterface
{
    private $trueLabel = 'true';

    private $falseLabel = 'false';

    public function __construct($id, $resultGetter, $queryMapping)
    {
        parent::__construct($id, $resultGetter, $queryMapping);

        $this->setFilters();
    }

    public function setLabels($trueLabel, $falseLabel)
    {
        $this->trueLabel = $trueLabel;
        $this->falseLabel = $falseLabel;

        $this->resetFilters();
        $this->setFilters();
    }

    protected function setFilters()
    {
        $this->addFilter(new Filter\Boolean($this->trueLabel, $this->falseLabel));
    }

    public function getValueOfResultRow($row)
    {
        $value = parent::getValueOfResultRow($row);

        if ($value == 1) {
            $value = $this->trueLabel;
        } else {
            $value = $this->falseLabel;
        }

        return $value;
    }
}
