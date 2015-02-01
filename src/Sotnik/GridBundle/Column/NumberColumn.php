<?php
namespace Sotnik\GridBundle\Column;

use Sotnik\GridBundle\ColumnFilter\Filter;

class NumberColumn extends CommonColumn implements ColumnInterface
{
    public function __construct($id, $resultGetter, $queryMapping)
    {
        parent::__construct($id, $resultGetter, $queryMapping);

        $this->setFilters();
    }

    protected function setFilters()
    {
        $this->addFilter(new Filter\Equals());
        $this->addFilter(new Filter\NotEqualTo());
        $this->addFilter(new Filter\LowerThan());
        $this->addFilter(new Filter\LowerThanOrEqualTo());
        $this->addFilter(new Filter\GreaterThan());
        $this->addFilter(new Filter\GreaterThanOrEqualTo());
        $this->addFilter(new Filter\BetweenExclusive());
        $this->addFilter(new Filter\BetweenInclusive());
        $this->addFilter(new Filter\DefinitionStatus());
    }
}
