<?php
namespace Sotnik\GridBundle\Pagination;

interface PaginationInterface
{
    /**
     * @param integer $currentPage
     * @return void
     */
    public function setCurrentPage($currentPage);

    /**
     * @return integer
     */
    public function getCurrentPage();

    /**
     * @param integer $maxPerPage
     * @return mixed
     */
    public function setMaxPerPage($maxPerPage);

    /**
     * @return integer
     */
    public function getMaxPerPage();

    /**
     * @param int $hydrationMode
     * @return integer
     * @throws \InvalidArgumentException
     */
    public function getResult($hydrationMode);

    /**
     * @return integer
     */
    public function getTotalCount();

    /**
     * @return integer
     */
    public function getTotalPages();

    /**
     * @return bool
     */
    public function getLeftJoinCollection();

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQuery();
}
