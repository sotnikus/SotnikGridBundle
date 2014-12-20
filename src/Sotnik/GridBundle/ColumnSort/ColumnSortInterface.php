<?php
namespace Sotnik\GridBundle\ColumnSort;

use Doctrine\ORM\QueryBuilder;

interface ColumnSortInterface
{
    const DIRECTION_DESC = 'desc';
    const DIRECTION_ASC = 'asc';

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function setDescQueryPart(QueryBuilder $queryBuilder);

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function setAscQueryPart(QueryBuilder $queryBuilder);
}
