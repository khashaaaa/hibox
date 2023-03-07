<?php

class UrlWrapper
{
    private $url = '';

    public function Set($url){
        $this->url = $url;
        return $this;
    }

    public function Get(){
        return $this->url;
    }

    public function GetKey($key){
        $urlData = parse_url($this->url);
        $queryData = $this->parseQuery($urlData);
        return isset($queryData[$key]) ? $queryData[$key] : false;
    }

    public function DeleteKey($key){
        $urlData = parse_url($this->url);
        $queryData = $this->parseQuery($urlData);     
        if (is_array($key)) {
            if (count($key) == 2) {
                unset($queryData[$key[0]][$key[1]]);
            } else if (count($key) == 3) {
                unset($queryData[$key[0]][$key[1]][$key[2]]);
            } else if (count($key) == 4) {
                unset($queryData[$key[0]][$key[1]][$key[2]][$key[3]]);
            }
        } else {
            unset($queryData[$key]);
        }
        $newQuery = http_build_query($queryData);

        $port = isset($urlData['port']) ?  ($urlData['port'] == 80 ? '' : ':'.$urlData['port']) : '';
        $this->url = $urlData['scheme'] . '://' . $urlData['host'] . $port . $urlData['path'];
        if($newQuery)
            $this->url .= '?' . $newQuery;
        return $this;
    }
    

    public function DeleteQuery(){
        $urlData = parse_url($this->url);
        $port = isset($urlData['port']) ?  ($urlData['port'] == 80 ? '' : ':'.$urlData['port']) : '';
        $this->url = $urlData['scheme'] . '://' . $urlData['host'] . $port . $urlData['path'];
        return $this;
    }

    public function GetAction(){
        if(CMS::IsFeatureEnabled('Seo2')){
            $urlData = parse_url($this->url);
            $path = explode('/', $urlData['path']);

            if(count($path) > 1 && in_array($path[count($path) - 2], array('category', 'subcategory'))){
                $path = array_slice($path, 0, count($path)-1);
            }
            return $path[count($path) - 1];
        }
        else{
            return $this->GetKey('p');
        }
    }

    public function ReplaceAction($newAction){
        if(CMS::IsFeatureEnabled('Seo2')){
            $this->ReplaceActionWithAlias($newAction);
        }
        else{
            $this->ReplaceActionWithoutAlias($newAction);
        }

        return $this;
    }

    public function ReplaceActionWithoutAlias($newAction){
        $urlData = parse_url($this->url);

        $queryData = $this->parseQuery($urlData);
        $queryData['p'] = $newAction;
        $newQuery = http_build_query($queryData);

        $port = isset($urlData['port']) ?  ($urlData['port'] == 80 ? '' : ':'.$urlData['port']) : '';
        $this->url = $urlData['scheme'] . '://' . $urlData['host'] . $port . $urlData['path'];
        if($newQuery)
            $this->url .= '?' . $newQuery;

        return $this;
    }

    public function ReplaceActionWithAlias($newAction){
        $urlData = parse_url($this->url);
        $path = explode('/', $urlData['path']);

        if(in_array($path[count($path) - 2], array('category', 'subcategory', 'pristroy'))){
            $path = array_slice($path, 0, count($path)-1);
        }
        $path[count($path) - 1] = $newAction;

        $port = isset($urlData['port']) ?  ($urlData['port'] == 80 ? '' : ':'.$urlData['port']) : '';
        $this->url = $urlData['scheme'] . '://' . $urlData['host'] . $port . implode('/', $path);

        return $this;
    }
    
    public function prepareToSearchAjax(){
        $this->url = str_replace("/ajaxSearch/", "/search/", $this->url);
        return $this;
    }

    public function Add($key, $value){
        $urlData = parse_url($this->url);
        $queryData = $this->parseQuery($urlData);
        $queryData[$key] = $value;
        $newQuery = http_build_query($queryData);

        $port = isset($urlData['port']) ?  ($urlData['port'] == 80 ? '' : ':'.$urlData['port']) : '';
        $this->url = $urlData['scheme'] . '://' . $urlData['host'] . $port . $urlData['path'];
        if($newQuery)
            $this->url .= '?' . $newQuery;
        return $this;
    }

    public function AddArray($newData){
        $urlData = parse_url($this->url);
        $queryData = $this->parseQuery($urlData);
        $queryData = array_merge($queryData, $newData);
        $newQuery = http_build_query($queryData);

        $port = isset($urlData['port']) ?  ($urlData['port'] == 80 ? '' : ':'.$urlData['port']) : '';
        $this->url = $urlData['scheme'] . '://' . $urlData['host'] . $port . $urlData['path'];
        if($newQuery)
            $this->url .= '?' . $newQuery;
        return $this;
    }

    public function SetValue($key, $value){
        $this->DeleteKey($key)->Add($key, $value);
        return $this;
    }

    public function SetValueReadOnly($key, $value){
        $urlData = parse_url($this->url);
        $queryData = $this->parseQuery($urlData);
        $queryData[$key] = $value;
        $newQuery = http_build_query($queryData);

        $port = isset($urlData['port']) ?  ($urlData['port'] == 80 ? '' : ':'.$urlData['port']) : '';
        $url = $urlData['scheme'] . '://' . $urlData['host'] . $port . $urlData['path'];
        if($newQuery)
            $url .= '?' . $newQuery;
        return $url;
    }

    private function parseQuery($urlData){
        $queryData = array();
        if(isset($urlData['query']))
            parse_str($urlData['query'], $queryData);
        return $queryData;
    }
}
