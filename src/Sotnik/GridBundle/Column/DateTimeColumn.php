<?php
namespace Sotnik\GridBundle\Column;

use Sotnik\GridBundle\ColumnFilter\Filter;

class DateTimeColumn extends CommonColumn implements ColumnInterface
{

    private $locale;

    private $format = 'Y-m-d H:i:s';

    public function __construct($id, $resultGetter, $queryMapping, $locale = 'en')
    {
        parent::__construct($id, $resultGetter, $queryMapping);
        $this->locale = $locale;

        $this->setFilters();
    }

    protected function setFilters()
    {
        $this->addFilter(new Filter\DateTimeEquals($this->locale));
        $this->addFilter(new Filter\DateTimeInterval($this->locale));
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getValueOfResultRow($row)
    {
        /** @var \Datetime $value */
        $value = parent::getValueOfResultRow($row);

        return $value->format($this->format);
    }
}
