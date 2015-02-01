<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class StartsWith extends BaseFilter implements ColumnFilterInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function transformQuery(QueryBuilder $queryBuilder)
    {
        $paramName = $this->getParamName();

        return $queryBuilder->andWhere($this->queryMapping . ' LIKE :' . $paramName)
            ->setParameter($paramName, $this->getValue() . '%');
    }
}
