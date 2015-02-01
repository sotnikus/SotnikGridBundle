<?php
namespace Sotnik\GridBundle\Column;

use Sotnik\GridBundle\ColumnFilter\Filter;

class TextColumn extends CommonColumn implements ColumnInterface
{
    public function __construct($id, $resultGetter, $queryMapping)
    {
        parent::__construct($id, $resultGetter, $queryMapping);

        $this->setFilters();
    }

    protected function setFilters()
    {
        $this->addFilter(new Filter\Equals());
        $this->addFilter(new Filter\Contains());
        $this->addFilter(new Filter\NotContain());
        $this->addFilter(new Filter\StartsWith());
        $this->addFilter(new Filter\EndsWith());
    }
}
