<?php
/**
* @author : Puji Ermanto <pujiermanto@gmail.com>
**/
class TransactionController {
	public function handleTransactionRequest() {
		$transactionType = $_GET['transaction'];
		$input = json_decode(file_get_contents('php://input'), true);
		$result = [];

		if($transactionType === "transfer") {
			if (!isset($input['source_account_id']) || !isset($input['destination_account_id']) || !isset($input['amount'])) {
				echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
				return;
			}
			$sourceAccountId = $input['source_account_id'];
			$destinationAccountId = $input['destination_account_id'];
			$amount = $input['amount'];

			$transactionService = new TransactionService();
			$result = $transactionService->processTransfer($sourceAccountId, $destinationAccountId, $amount);
		}

		if($transactionType === "update") {
			if(!isset($input['account_id']) || !isset($input['amount'])) {
				echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
				return;
			}

			$accountId = $input['account_id'];
			$amount = $input['amount'];

			$transactionService = new TransactionService();
			$result = $transactionService->processTransaction($accountId, $amount);
		}

		if($transactionType === "balance") {
			if(!isset($input['account_id'])) {
				echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
				return;
			}

			$accountId = $input['account_id'];

			$transactionService = new TransactionService();
			$result = $transactionService->checkBalance($accountId);
		}
		
		echo json_encode($result);
	}

	public function handleBalanceRequest() {
		$input = json_decode(file_get_contents('php://input'), true);

		if (!isset($input['account_id'])) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
			return;
		}

		$accountId = $input['account_id'];

		$transactionService = new TransactionService();
		$result = $transactionService->checkBalance($accountId);

		echo json_encode($result);
	}
}
