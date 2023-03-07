<?php

class Paginator extends GeneralUtil
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_template = 'paginator'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '';

    protected $count = null;
    protected $page = 1;
    protected $pages = 1;
    protected $offset = 0;
    protected $limit = 10;
    protected $range = 4;
    protected $urlParams = array();

    protected $perPageLimits = array(10, 25, 50, 100);

    public function __construct($count = null, $page = null, $limit = null)
    {
        parent::__construct(true);
        $this->preset($count, $page, $limit);
        $this->tpl->assign('paginator', $this);
    }

    public function setTemplate($template, $path = null)
    {
        $this->_template = $template;
        $this->_template_path = !is_null($path) ? $path : $this->_template_path;
    }

    public function setUrlParams(array $params)
    {
        $this->urlParams = $params;
    }

    public function preset($count = null, $page = null, $limit = null)
    {
        $this->count = !is_null($count) ? $count : $this->count;
        $this->page = $page ? $page : RequestWrapper::request('page', $this->page);
        $this->limit = $limit ? $limit : $this->limit;
        $this->calc();
    }

    public function page($page = null)
    {
        if ($page) {
            $this->preset($this->count, $page, $this->limit);
        }

        return $this->page;
    }

    public function count($count = null)
    {
        if (!is_null($count)) {
            $this->preset($count, null, $this->limit);
        }

        return (int) $this->count;
    }

    public function pages()
    {
        return $this->pages;
    }

    public function perPageLimits()
    {
        return $this->perPageLimits;
    }

    public function calc()
    {
        if (is_null($this->count)) {
            return ;
        }

        $this->pages = ceil($this->count / $this->limit);

        if ($this->page > $this->pages) {
            $this->page = $this->pages;
        }

        if ($this->page < 1) {
            $this->page = 1;
        }

        if ($this->page > 1) {
            $this->offset = ($this->page - 1) * $this->limit;
        } else {
            $this->offset = 0;
        }
    }

    public function offset()
    {
        return $this->offset;
    }

    public function limit($limit = null)
    {
        if ($limit) {
            $this->preset(null, null, $limit);
        }

        return $this->limit;
    }

    public function getRange($range = null)
    {
        $this->range = $range ? $range : $this->range;

        if ($this->range >= $this->pages) {
            $this->range = $this->pages - 1;
        }

        if ($this->page + round($this->range / 2) >= $this->pages) {
            $start = $this->pages - $this->range;
        } else if ($this->page > $this->range / 2) {
            $start = round($this->page - ($this->range / 2));
        } else {
            $start = 1;
        }

        $end = $start + $this->range > $this->pages ? $this->pages : $start + $this->range;

        $pages = array();
        for ($i = $start; $i <= $end; $i++) {
            $pages[$i] = $i;
        }

        return (count($pages) == 1) ? array() : $pages;
    }

    public function range()
    {
        return (int)$this->range;
    }

    public function hasNext()
    {
        return $this->page < $this->pages;
    }

    public function hasPrevious()
    {
        return $this->page > 1;
    }

    public function prev()
    {
        return $this->hasPrevious() ? $this->page() - 1 : 1;
    }

    public function next()
    {
        return $this->hasNext() ? $this->page() + 1 : $this->pages();
    }

    public function query(array $params = array())
    {
        $url = parse_url(RequestWrapper::path(true));

        $query = array();
        if(array_key_exists('query', $url)) {
            parse_str($url['query'], $query);
        }

        return $url['path'] . '?' . http_build_query($this->urlParams + $params + $query);
    }

    public function display($display = true)
    {
        $this->_template = 'paginator';

        if ($display) {
            print $this->fetchTemplateWithoutHeaderAndFooter(false);
        } else {
            return $this->fetchTemplateWithoutHeaderAndFooter(false);
        }
    }

    public function displayPerpage($display = true)
    {
        $this->_template = 'perpage';

        if ($display) {
            print $this->fetchTemplateWithoutHeaderAndFooter(false);
        } else {
            return $this->fetchTemplateWithoutHeaderAndFooter(false);
        }
    }
}
