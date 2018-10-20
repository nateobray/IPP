<?php
namespace obray\transport;

class IPP extends \obray\HTTP
{
    public function addRequest($url, $data, $headers=[])
    {
        parent::addRequest($url, 'GET', $data, $headers=[]);
    }
}