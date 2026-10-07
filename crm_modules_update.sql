USE crm_db;

-- Add Negotiation to the sales pipeline.
ALTER TABLE sales_tracking
MODIFY stage ENUM('Prospecting', 'Proposal', 'Negotiation', 'Won', 'Lost')
DEFAULT 'Prospecting';

-- Optional sample records. Run only if your tables do not already contain
-- these records and customer ID 1 exists.
INSERT INTO sales_tracking (customer_id, deal_name, amount, stage) VALUES
(1, 'Enterprise Software License', 5000.00, 'Prospecting'),
(1, 'Cloud Migration Service', 12500.00, 'Proposal'),
(1, 'Annual Support Package', 2500.00, 'Won');

INSERT INTO support_tickets (customer_id, subject, message, status, priority) VALUES
(1, 'Cannot reset password', 'User is locked out and needs a reset link.', 'Open', 'High'),
(1, 'Invoice clarification', 'Question about the tax breakdown on Invoice #402.', 'In Progress', 'Low');

INSERT INTO communication_history (customer_id, interaction_type, notes) VALUES
(1, 'Call', 'Discussed initial project requirements and baseline budget limits.'),
(1, 'Email', 'Sent over the formal platform proposal deck.'),
(1, 'Meeting', 'Met in person to finalize details. Closed the annual package deal.');