<?php

namespace Sotnik\GridBundle\Grid;

use Doctrine\ORM\QueryBuilder;

class GridResult implements GridResultInterface
{
    /**
     * @var QueryBuilder
     */
    private $query;

    /**
     * @var string
     */
    private $html;

    public function __construct(QueryBuilder $query, $html)
    {
        $this->query = $query;
        $this->html = $html;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }
}
