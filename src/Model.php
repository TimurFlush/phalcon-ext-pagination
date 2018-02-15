<?php

namespace TimurFlush\PhalconExtPagination;

use Kilte\Pagination\Pagination;

/**
 * Class Model
 * @package TimurFlush\PhalconExtPagination
 */
class Model extends \Phalcon\Paginator\Adapter\Model
{
    /**
     * @var array
     */
    private $cache = [
        'paginate' => null,
        'pages' => null,
    ];

    /**
     * @return \stdClass
     */
    public function getNavigator()
    {
        if ($this->cache['pages'] instanceof \stdClass)
            return $this->cache['pages'];

        $paginate = $this->getPaginate();

        $currentPage = $paginate->current;
        $totalItems = $paginate->total_items;
        $itemsPerPage = $paginate->limit;
        $totalPages = $paginate->total_pages;

        $navigator = new Pagination($totalItems, $currentPage, $itemsPerPage, 2);
        $navigator = $navigator->build();

        $countArray = array_count_values($navigator);

        $firstPrevious = $currentPage - ($countArray[Pagination::TAG_PREVIOUS] ?? 0);
        $firstNext = $currentPage + 1;

        $pages = [];
        foreach ($navigator as $key => $tag){
            switch ($tag){
                case Pagination::TAG_FIRST:
                    $pages[] = '1';
                    break;
                case Pagination::TAG_LAST:
                    $pages[] = $totalPages;
                    break;
                case Pagination::TAG_MORE:
                    $pages[] = '...';
                    break;
                case Pagination::TAG_LESS:
                    $pages[] = '...';
                    break;
                case Pagination::TAG_CURRENT:
                    $pages[] = $currentPage;
                    break;
                case Pagination::TAG_PREVIOUS:
                    $pages[] = $firstPrevious;
                    $firstPrevious++;
                    break;
                case Pagination::TAG_NEXT:
                    $pages[] = $firstNext;
                    $firstNext++;
                    break;
            }
        }

        $ret = new \stdClass();
        $ret->pages = $pages;
        $ret->active = $currentPage;

        return $this->cache['pages'] = $ret;
    }

    /**
     * @return \stdClass
     */
    public function getPaginate()
    {
        if ($this->cache['paginate'] instanceof \stdClass)
            return $this->cache['paginate'];

        return $this->cache['paginate'] = parent::getPaginate();
    }
}