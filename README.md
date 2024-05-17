# System-Banking-Backend-Simulation-
BACKEND QUESTION (Buatlah desain arsitektur sistem banking untuk mencegah nasabah melakukan beberapa transaksi secara bersamaan (detik yang sama) dimana jumlah transaksi melebihi total saldo. )

Ekspektasi: 
1. Hanya ada 1 transaksi yang diproses dan 2 transaksi lainnya ditolak meskipun ketiga transaksi dilakukan pada detik yang sama
2. Tidak boleh ada exception/error terjadi saat pemrosesan transaksi Banking System

![ilustration](https://lh6.googleusercontent.com/hrviW8KFvcE60vYaSLRAWsMv6bsQkdk9_mpuFphMQFQRMUIURi4QPucXLamXEALrdTwEvT0zFQ1gX2oTPjMW472PLx4tXrh5wEch__gnTrtooIrdIT1MFVqRYwSVfjmhUA=w740)

Fixing : 
Untuk mencegah nasabah melakukan beberapa transaksi secara bersamaan yang melebihi total saldo dalam sistem perbankan, Anda bisa menggunakan konsep "optimistic locking" atau "pessimistic locking" dalam desain arsitektur sistem Anda. Dalam kasus ini, kita akan menggunakan "pessimistic locking" untuk memastikan bahwa hanya satu transaksi yang diproses pada satu waktu.  

Berikut adalah desain arsitektur sistem menggunakan PHP dengan konsep API untuk mencapai ekspektasi yang diinginkan:
Desain Arsitektur  
1. API Gateway: Menerima permintaan transaksi dari klien.
2. Transaction Controller: Mengelola logika transaksi.
3. Transaction Service: Mengimplementasikan logika bisnis transaksi.
4. Database: Menyimpan data saldo nasabah dan riwayat transaksi.  

### Skema Database  
```bash
# create database banking;
# use banking;
```
```sql
CREATE TABLE accounts (
    account_id INT PRIMARY KEY,
    balance DECIMAL(15, 2) NOT NULL
);

CREATE TABLE transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT,
    amount DECIMAL(15, 2),
    transaction_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(account_id)
);
```  
**Notes: Insert new one or two account data for testing**

#### API Endpoint :  
- Endpoint: /api/transaction
- Method: POST
- Request Body:
**Update balance**
```json
"account_id": 1,
"amount": 100
```
**Tranfer balance**
```json
"account_id": 1,
"amount": 100
```

### API Response Implementation  
**Use Curl**
```bash
#!/bin/bash

# Panggil endpoint transfer dengan curl dan simpan output-nya di variabel
response1=$(curl -X POST http://localhost/banking/index.php?transaction=transfer -H "Content-Type: application/json" -d '{"source_account_id": 1, "destination_account_id": 2, "amount": 100}')
response2=$(curl -X POST http://localhost/banking/index.php?transaction=transfer -H "Content-Type: application/json" -d '{"source_account_id": 2, "destination_account_id": 3, "amount": 100}')
response3=$(curl -X POST http://localhost/banking/index.php?transaction=transfer -H "Content-Type: application/json" -d '{"source_account_id": 3, "destination_account_id": 2, "amount": 100}')

# Tampilkan output dari setiap panggilan
echo "Response 1: $response1"
echo "Response 2: $response2"
echo "Response 3: $response3"

```
