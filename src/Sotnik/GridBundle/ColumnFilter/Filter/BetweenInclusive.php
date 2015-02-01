<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class BetweenInclusive extends BaseFilter implements ColumnFilterInterface
{

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function transformQuery(QueryBuilder $queryBuilder)
    {
        $qb = $queryBuilder;

        if (isset($this->getValue()['from']) && !isset($this->getValue()['to'])) {
            $paramName = $this->getParamName();
            $qb = $queryBuilder->andWhere($this->queryMapping . ' >= :' . $paramName)
                ->setParameter($paramName, $this->getValue()['from']);

        } elseif (isset($this->getValue()['from']) && isset($this->getValue()['to'])) {
            $paramName1 = $this->getParamName() . '1';
            $paramName2 = $this->getParamName() . '2';

            $qb = $queryBuilder->andWhere($this->queryMapping . " BETWEEN :$paramName1 AND :$paramName2")
                ->setParameter($paramName1, $this->getValue()['from'])
                ->setParameter($paramName2, $this->getValue()['to']);

        } elseif (!isset($this->getValue()['from']) && isset($this->getValue()['to'])) {
            $paramName = $this->getParamName();
            $qb = $queryBuilder->andWhere($this->queryMapping . ' <= :' . $paramName)
                ->setParameter($paramName, $this->getValue()['to']);
        }

        return $qb;
    }

    public function getRenderType()
    {
        return ColumnFilterInterface::BETWEEN_INPUT;
    }
}
