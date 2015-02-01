<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class NotContain extends BaseFilter implements ColumnFilterInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function transformQuery(QueryBuilder $queryBuilder)
    {
        $paramName = $this->getParamName();

        return $queryBuilder->andWhere($this->queryMapping . ' NOT LIKE :' . $paramName)
            ->setParameter($paramName, '%' . $this->getValue() . '%');
    }
}
