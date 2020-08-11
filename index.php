<?php 
require_once __DIR__.'/vendor/autoload.php';
use BinaryManagment\BinaryTree\BinaryConstruct;
use BinaryManagment\BinaryTree\BinaryManager;

$binary1 = new BinaryConstruct();
$binary1->createNode(1,1);
$binary1->createNode(1,2);
$binary2 = new BinaryManager();
$binary2->generateFiveLevels();
$nodes = $binary2->getAllUpDownNodes(14);
if($nodes){
    foreach($nodes as $val){
            print_r($val);
    }
}