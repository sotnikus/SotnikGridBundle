<?php
namespace Sotnik\GridBundle\Grid;

interface GridResultInterface
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQuery();

    /**
     * @return string
     */
    public function getHtml();
}
