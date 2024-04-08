CREATE TABLE orders ( 
  order_id INT(64) NOT NULL AUTO_INCREMENT,
  customer_id INT(64) NOT NULL,
  order_status CHAR(20) NOT NULL,
  PRIMARY KEY (order_id));

CREATE TABLE order_parts ( 
  part_num INT(64) NOT NULL,
  order_id INT(64) NOT NULL,
  quantity INT(64) NOT NULL,
  PRIMARY KEY (part_num),
  FOREIGN KEY (order_id) REFERENCES orders(order_id));

CREATE TABLE warehouse_parts ( 
  part_num INT(64) NOT NULL, 
  quantity INT(64) NOT NULL, 
  PRIMARY KEY (part_num));
