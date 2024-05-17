<?php
/**
* @author : Puji Ermanto <pujiermanto@gmail.com>
**/
class TransactionService {
	public function processTransaction($accountId, $amount) {
		$db = DB::getInstance()->getConnection();

		try {
            // Start a transaction
			$db->beginTransaction();

            // Lock the account row for update
			$stmt = $db->prepare("SELECT balance FROM accounts WHERE account_id = :account_id FOR UPDATE");
			$stmt->execute(['account_id' => $accountId]);
			$account = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$account) {
				throw new Exception('Account not found');
			}

			$balance = $account['balance'];

            // Check if the balance is sufficient
			if ($balance < $amount) {
				$db->rollBack();
				return ['status' => 'error', 'message' => 'Insufficient balance'];
			}

            // Update the balance
			$newBalance = $balance + $amount;
			$stmt = $db->prepare("UPDATE accounts SET balance = :new_balance WHERE account_id = :account_id");
			$stmt->execute(['new_balance' => $newBalance, 'account_id' => $accountId]);

            // Insert the transaction
			$stmt = $db->prepare("INSERT INTO transactions (account_id, amount) VALUES (:account_id, :amount)");
			$stmt->execute(['account_id' => $accountId, 'amount' => $amount]);

            // Commit the transaction
			$db->commit();

			return ['status' => 'success', 'message' => 'Transaction completed successfully'];
		} catch (Exception $e) {
            // Rollback the transaction if something failed
			$db->rollBack();
			return ['status' => 'error', 'message' => $e->getMessage()];
		}
	}

	public function processTransfer($sourceAccountId, $destinationAccountId, $amount) {
		$db = DB::getInstance()->getConnection();

		try {
        // Start a transaction
			$db->beginTransaction();

        // Lock the source account row for update
			$stmt = $db->prepare("SELECT balance FROM accounts WHERE account_id = :account_id FOR UPDATE");
			$stmt->execute(['account_id' => $sourceAccountId]);
			$sourceAccount = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$sourceAccount) {
				throw new Exception('Source account not found');
			}

        // Lock the destination account row for update
			$stmt = $db->prepare("SELECT balance FROM accounts WHERE account_id = :account_id FOR UPDATE");
			$stmt->execute(['account_id' => $destinationAccountId]);
			$destinationAccount = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$destinationAccount) {
				throw new Exception('Destination account not found');
			}

			$sourceBalance = $sourceAccount['balance'];
			$destinationBalance = $destinationAccount['balance'];

        // Check if the source account balance is sufficient
			if ($sourceBalance < $amount) {
				$db->rollBack();
				return ['status' => 'error', 'message' => 'Insufficient balance'];
			}

        	// Update the source account balance
			$newSourceBalance = $sourceBalance - $amount;
			$stmt = $db->prepare("UPDATE accounts SET balance = :new_balance WHERE account_id = :account_id");
			$stmt->execute(['new_balance' => $newSourceBalance, 'account_id' => $sourceAccountId]);

        	// Update the destination account balance
			$newDestinationBalance = $destinationBalance + $amount;
			$stmt = $db->prepare("UPDATE accounts SET balance = :new_balance WHERE account_id = :account_id");
			$stmt->execute(['new_balance' => $newDestinationBalance, 'account_id' => $destinationAccountId]);

        	// Insert the transaction for source account
			$stmt = $db->prepare("INSERT INTO transactions (account_id, amount) VALUES (:account_id, :amount)");
			$stmt->execute(['account_id' => $sourceAccountId, 'amount' => -$amount]);

        	// Insert the transaction for destination account
			$stmt = $db->prepare("INSERT INTO transactions (account_id, amount) VALUES (:account_id, :amount)");
			$stmt->execute(['account_id' => $destinationAccountId, 'amount' => $amount]);

        	// Commit the transaction
			$db->commit();

        	// Ambil balance terbaru kedua akun
			$updatedSourceBalance = $newSourceBalance;
			$updatedDestinationBalance = $newDestinationBalance;

			return [
				'status' => 'success',
				'message' => 'Transfer completed successfully',
				'source_balance' => $updatedSourceBalance,
			];
		} catch (Exception $e) {
        // Rollback the transaction if something failed
			$db->rollBack();
			return ['status' => 'error', 'message' => $e->getMessage()];
		}
	}

	public function checkBalance($accountId) {
		$db = DB::getInstance()->getConnection();

		try {
            // Query to get the account balance
			$stmt = $db->prepare("SELECT balance FROM accounts WHERE account_id = :account_id");
			$stmt->execute(['account_id' => $accountId]);
			$account = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$account) {
				throw new Exception('Account not found');
			}

			return ['status' => 'success', 'balance' => $account['balance']];
		} catch (Exception $e) {
			return ['status' => 'error', 'message' => $e->getMessage()];
		}
	}
}
