<?php

interface IDatabase{
    public static function getInstance():IDatabase;
    public function getConnection();
}