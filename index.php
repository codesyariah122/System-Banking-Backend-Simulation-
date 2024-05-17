<?php
/**
* @author : Puji Ermanto <pujiermanto@gmail.com>
**/

require 'DB.php';
require 'TransactionService.php';
require 'TransactionController.php';

$controller = new TransactionController();
$controller->handleTransactionRequest();