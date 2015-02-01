<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class DateTimeInterval extends BaseFilter implements ColumnFilterInterface
{

    private $locale;

    public function __construct($locale = 'en')
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

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
                ->setParameter($paramName, new \DateTime($this->getValue()['from']));

        } elseif (isset($this->getValue()['from']) && isset($this->getValue()['to'])) {
            $paramName1 = $this->getParamName() . '1';
            $paramName2 = $this->getParamName() . '2';

            $qb = $queryBuilder->andWhere($this->queryMapping . " BETWEEN :$paramName1 AND :$paramName2")
                ->setParameter($paramName1, new \DateTime($this->getValue()['from']))
                ->setParameter($paramName2, new \DateTime($this->getValue()['to']));

        } elseif (!isset($this->getValue()['from']) && isset($this->getValue()['to'])) {
            $paramName = $this->getParamName();
            $qb = $queryBuilder->andWhere($this->queryMapping . ' <= :' . $paramName)
                ->setParameter($paramName, new \DateTime($this->getValue()['to']));
        }

        return $qb;
    }

    public function getRenderType()
    {
        return ColumnFilterInterface::BETWEEN_DATETIME;
    }
}
