SELECT 
upload_transaction.ID AS TRANSACTION_ID, 
upload_transaction.TXN_STATUS, 
file_upload.PROJECT_TYPE, 
customers.CUSTOMER_NAME, 
customers.EMAIL, 
SUBSTRING(uploaded_files.FILE_URL,48) AS FILE_URL, 
upload_transaction.DATE_CREATED
FROM file_upload
INNER JOIN upload_transaction ON file_upload.TXN_ID = upload_transaction.ID
INNER JOIN customers ON file_upload.CUST_ID = customers.ID
INNER JOIN uploaded_files ON uploaded_files.FILE_UPLOAD_ID = file_upload.ID
;
