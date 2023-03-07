<?php

class EntitiesList {
    /**
     * @var Mapper
     */
    private $mapper;

    private $defaultPerPage = 10;

    /**
     * @param Mapper $mapper
     */
    public function __construct($mapper)
    {
        $this->mapper = $mapper;
    }

    public function getPaginatedList($page, $perPage, $filter = array())
    {
        $total = $this->mapper->getCount($filter);

        $page = intval($page) ? intval($page) : 1;
        $perPage = $perPage ? $perPage : $this->defaultPerPage;

        $list = $this->mapper->findLimited(($page-1)*$perPage, $perPage, $filter);

        return array(
            'content' => $list,
            'totalCount' => $total,
            'page' => $page,
            'perPage' => $perPage == 'all' ? $total : $perPage
        );
    }
}