CREATE TABLE orders ( 
  customer_id INT(64) NOT NULL,
  order_id INT(64) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (customer_id, order_id));

CREATE TABLE order_parts ( 
  part_num INT(64) NOT NULL,
  order_id INT(64) NOT NULL AUTO_INCREMENT,
  quantity INT(64) NOT NULL,
  PRIMARY KEY (part_num)
  FOREIGN KEY (order_id) REFERENCES orders(order_id));

CREATE TABLE warehouses ( 
  warehouse_id INT(64) NOT NULL AUTO_INCREMENT, 
  part_num INT(64) NOT NULL,  
  PRIMARY KEY (warehouse_id, part_num));

CREATE TABLE warehouse_parts ( 
  part_num INT(64) NOT NULL, 
  warehouse_id INT(64) NOT NULL, 
  quantity INT(64) NOT NULL, 
  PRIMARY KEY (part_num)
  FOREIGN KEY (warehouse_id) REFERENCES warehouses(warehouse_id));
