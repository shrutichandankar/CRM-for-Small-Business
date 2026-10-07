USE crm_db;

-- ---------------------------------------------------------
-- Step 1: remove rows that point to customers that no longer exist
-- (needed, otherwise the foreign keys below cannot be created)
-- ---------------------------------------------------------
DELETE FROM sales_tracking        WHERE customer_id NOT IN (SELECT id FROM customers);
DELETE FROM support_tickets       WHERE customer_id NOT IN (SELECT id FROM customers);
DELETE FROM communication_history WHERE customer_id NOT IN (SELECT id FROM customers);

-- ---------------------------------------------------------
-- Step 2: add foreign keys so deleting a customer also deletes
-- their sales, tickets and communication history
-- ---------------------------------------------------------
ALTER TABLE sales_tracking
  ADD CONSTRAINT fk_sales_customer
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE;

ALTER TABLE support_tickets
  ADD CONSTRAINT fk_support_customer
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE;

ALTER TABLE communication_history
  ADD CONSTRAINT fk_history_customer
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE;

-- If you have not yet run crm_modules_update.sql, also run only its
-- ALTER TABLE part (adds 'Negotiation'); skip its sample INSERT lines,
-- because database.sql already inserted the same sample records.
