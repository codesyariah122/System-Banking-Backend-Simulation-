#!/bin/bash

# Panggil endpoint transfer dengan curl dan simpan output-nya di variabel
response1=$(curl -X POST http://localhost/banking/index.php?transaction=transfer -H "Content-Type: application/json" -d '{"source_account_id": 1, "destination_account_id": 2, "amount": 100}')
response2=$(curl -X POST http://localhost/banking/index.php?transaction=transfer -H "Content-Type: application/json" -d '{"source_account_id": 2, "destination_account_id": 3, "amount": 100}')
response3=$(curl -X POST http://localhost/banking/index.php?transaction=transfer -H "Content-Type: application/json" -d '{"source_account_id": 3, "destination_account_id": 2, "amount": 100}')

# Tampilkan output dari setiap panggilan
echo "Response 1: $response1"
echo "Response 2: $response2"
echo "Response 3: $response3"
