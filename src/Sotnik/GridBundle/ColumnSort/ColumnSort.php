<?php
namespace Sotnik\GridBundle\ColumnSort;

use Doctrine\ORM\QueryBuilder;

class ColumnSort implements ColumnSortInterface
{
    private $queryMapping;

    public function __construct($queryMapping)
    {
        $this->queryMapping = $queryMapping;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function setDescQueryPart(QueryBuilder $queryBuilder)
    {
         return  $queryBuilder->OrderBy($this->queryMapping, 'DESC');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function setAscQueryPart(QueryBuilder $queryBuilder)
    {
        return  $queryBuilder->OrderBy($this->queryMapping, 'ASC');
    }
}
