<?php

class NewsBlock extends GenerateBlock
{
    protected $_template = 'newsblock';
    protected $_template_path = '/main/';

    public function __construct()
    {
        parent::__construct(true);
    }

    protected function setVars()
    {
        if (! CMS::IsFeatureEnabled('News')) {
            return;
        }
        $status = $this->cms->Check();
        if ($status) {
            $allNews = $this->cms->GetAllNews();
            $count = General::getConfigValue('news_count_print', 3);
            $this->tpl->assign('newsBlock', @array_slice($allNews, 0, $count));
        }
    }
}

