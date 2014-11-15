<?php

namespace _64FF00\PurePerms\ppdata;

interface PPDataInterface
{
	public function getData();
	
	public function getNode($node);
	
	public function removeNode($node);
	
	public function setData(array $data);
	
	public function setNode($node, $value);
}