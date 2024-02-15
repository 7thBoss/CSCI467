CREATE TABLE legacy ( part_num INT(64) NOT NULL, description CHAR(64) NOT NULL, weight INT(64) NOT NULL, picture CHAR(64), price FLOAT(64) NOT NULL, PRIMARY KEY (part_num));

CREATE TABLE warehouse ( warehouse_id INT(64) NOT NULL, part_num INT(64) NOT NULL, quantity INT(64), PRIMARY KEY (warehouse_id, part_num), FOREIGN KEY (part_num) REFERENCES legacy(part_num));

CREATE TABLE customer ( customer_id INT(64) NOT NULL, PRIMARY KEY (customer_id));

CREATE TABLE customer_order ( customer_id INT(64) NOT NULL, part_num INT(64) NOT NULL, quantity INT(64), PRIMARY KEY (customer_id, part_num), FOREIGN KEY (customer_id) REFERENCES customer(customer_id), FOREIGN KEY (part_num) REFERENCES legacy(part_num));
