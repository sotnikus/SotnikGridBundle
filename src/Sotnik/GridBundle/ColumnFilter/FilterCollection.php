<?php
namespace Sotnik\GridBundle\ColumnFilter;

use Sotnik\GridBundle\ColumnFilter\Filter\ColumnFilterInterface;

class FilterCollection implements FilterCollectionInterface
{
    /**
     * @var ColumnFilterInterface[];
     */
    private $filters = [];

    public function addFilter(ColumnFilterInterface $filter)
    {
        $this->filters[$filter->getName()] = $filter;
    }

    public function getFilter($filterName)
    {
        $result = null;

        if (isset($this->filters[$filterName])) {
            $result = $this->filters[$filterName];
        }

        return $result;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function count()
    {
        return count($this->filters);
    }
}
