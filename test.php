<?php

class Major {
    public $id;
    public $name;
}


$major = new Major();
if($major->id === NULL) echo 'null<br>';
$major->id = 1;
if($major->id === NULL) echo 'null<br>';
$major->name = 'major name';

$reflection = new ReflectionClass(get_class($major));
$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

foreach($properties as $property) {
    echo $property->getName().' => '.$property->getValue($major).'<br>';
}


echo $major->propertyNames.'hehe';


$major->propertyNames = 'adsf';


echo $major->propertyNames.'hehe';