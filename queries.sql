SELECT * FROM `wp_clients_contracts` where client_id = 47 AND datetime <= '2025-10-31 00:00:00' ORDER BY datetime DESC LIMIT 1;

SELECT * FROM wp_contract_headcounts WHERE client_contract_id = 320 AND datetime <= '2025-10-31 00:00:00'ORDER BY datetime DESC LIMIT 1;

SELECT 
    client_contracts.*, 
    headcounts.*
FROM wp_clients_contracts AS client_contracts
LEFT JOIN wp_contract_headcounts AS headcounts
       ON headcounts.client_contract_id = client_contracts.client_contract_id
       AND headcounts.datetime <= '2025-10-31 00:00:00'
WHERE client_contracts.client_id = 47
  AND client_contracts.datetime <= '2025-10-31 00:00:00'
ORDER BY client_contracts.datetime DESC, headcounts.datetime DESC
LIMIT 1;

combined Query   