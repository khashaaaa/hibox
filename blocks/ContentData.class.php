<?php

class ContentData extends GenerateBlock
{
    protected $_cache = false; //- кэшируем или нет.
    protected $_life_time = 3600; //- время на которое будем кешировать
    protected $_template = 'content'; //- шаблон, на основе которого будем собирать блок
    protected $_template_path = '/main/';

    /**
     * @var cms
     */
    protected $cms;

    public function __construct()
    {
        parent::__construct(true);
        $this->cms = new CMS();
    }

    protected function setVars()
    {
        $cRep = new ContentRepository($this->cms);
        try {
            $alias = (General::getConfigValue('site_temporary_unavailable') && !OTBase::isAdmin()) ? 'site_unavailable' : SCRIPT_NAME;
            if ($this->request->valueExists('pid')) {
                $page = $cRep->GetFullPageById((int)$this->request->getValue('pid'));
            } else {
                $cRep->checkIsServicePage($alias);
                $page = $cRep->GetPageByAlias($alias);
                if (! $page) {
                    $page = $cRep->GetPageByAlias('404');
                }
            }            
            $this->tpl->assign('page', $page);
            $text = $page['text'];
            $this->tpl->assign('text', $text);
            $this->tpl->assign('status', $this->cms->Check());
        } catch (DBException $e) {
            Session::setError($e->getMessage(), 'DBError');
        } catch(ServiceException $e){
            Session::setError($e->getMessage(), $e->getErrorCode());
        }
    }

}
