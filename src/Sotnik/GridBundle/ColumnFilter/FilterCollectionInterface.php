<?php
namespace Sotnik\GridBundle\ColumnFilter;

use Sotnik\GridBundle\ColumnFilter\Filter\ColumnFilterInterface;

interface FilterCollectionInterface
{
    /**
     * @param ColumnFilterInterface $filter
     * @return void
     */
    public function addFilter(ColumnFilterInterface $filter);

    /**
     * @param string $filterName
     * @return ColumnFilterInterface
     */
    public function getFilter($filterName);

    /**
     * @return ColumnFilterInterface[]
     */
    public function getFilters();

    /**
     * @return void
     */
    public function reset();

    /**
     * @return integer
     */
    public function count();
}
