<?php

namespace SistemaTCC\Model\Serializer;

use ReflectionClass;

trait ObjectToJson
{
	public function toJson()
	{
		$reflect = new ReflectionClass($this);
		$data = [];

		foreach ($reflect->getProperties() as $var) {
			if(isset($this->{$var->name})){
				if(is_object($this->{$var->name}) && method_exists($this->{$var->name},'toJson')){
					$data[$var->name] = $this->{$var->name}->toJson();
				}else{
					$data[$var->name] = $this->{$var->name};
				}
			}
		}

		return $data;
	}
}
