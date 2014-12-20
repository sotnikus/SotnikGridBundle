<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

interface ColumnFilterInterface
{
    const INPUT = 'input';

    const SELECT = 'select';

    const MULTI_SELECT = 'multi-select';

    const BETWEEN_INPUT = 'between-input';

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function transformQuery(QueryBuilder $queryBuilder);

    /**
     * @param $queryMapping
     * @return void
     */
    public function setQueryMapping($queryMapping);

    /**
     * @return QueryBuilder
     */
    public function getQueryMapping();

    /**
     * @param $value mixed
     * @return void
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getRenderType();
}
