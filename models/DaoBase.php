<?php

abstract class DaoBase {
    protected $pdo;
    
    public function __construct($pdo){
        $this->pdo = $pdo;
    }
}