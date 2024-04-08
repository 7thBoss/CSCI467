CREATE TABLE order_parts ( 
  order_id INT(64) NOT NULL,
  part_num INT(64) NOT NULL,
  quantity INT(64) NOT NULL,
  PRIMARY KEY (customer_id, part_num));

CREATE TABLE orders ( 
  customer_id INT(64) NOT NULL,
  order_id INT(64) NOT NULL,
  PRIMARY KEY (customer_id, order_id)
  FOREIGN KEY (order_id) REFERENCES order_parts(order_id));

CREATE TABLE warehouses ( 
  warehouse_id INT(64) NOT NULL, 
  part_num INT(64) NOT NULL, 
  quantity INT(64), PRIMARY KEY (warehouse_id, part_num));
