<?php

namespace _64FF00\PurePerms\ppdata;

interface PPDataInterface
{
    public function getData();
    
    public function getName();
    
    public function getNode($node);
    
    public function getWorldData($levelName);
    
    public function removeNode($node);
    
    public function setData(array $data);
    
    public function setNode($node, $value);
    
    public function setWorldData($levelName, array $worldData);
}