<?php
namespace Sotnik\GridBundle\ColumnFilter\Filter;

use Doctrine\ORM\QueryBuilder;

class DateTimeEquals extends BaseFilter implements ColumnFilterInterface
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
        $paramName = $this->getParamName();

        $input = $this->getValue();

        return $queryBuilder->andWhere($this->queryMapping . ' = :' . $paramName)
            ->setParameter($paramName, new \DateTime($input['eq']));
    }

    public function getRenderType()
    {
        return ColumnFilterInterface::INPUT_DATETIME;
    }
}
