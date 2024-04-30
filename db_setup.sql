CREATE TABLE orders ( 
    order_id INT(8) NOT NULL AUTO_INCREMENT,
    customer_id INT(32) NOT NULL,
    order_status VARCHAR(10),
    filled_date DATETIME,
    ordered_date DATETIME,
    weight_total FLOAT(6,2),
    price_total  FLOAT(8,2),
	
    PRIMARY KEY (order_id)	
);

CREATE TABLE order_parts ( 
    part_num INT(11) NOT NULL,
    order_id INT(11) NOT NULL,
    quantity INT(32) NOT NULL,
    PRIMARY KEY (part_num, order_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

CREATE TABLE warehouse_parts ( 
    part_num INT(11) NOT NULL,
    quantity INT(32) NOT NULL,
    onhand INT(32) NOT NULL,
    PRIMARY KEY (part_num)
);

CREATE TABLE shipping_cost (
	bracket_id INT NOT NULL AUTO_INCREMENT,
	price FLOAT(8,2) NOT NULL,
	min_weight FLOAT(6,2) NOT NULL,
	max_weight FLOAT(6,2) NOT NULL,

	PRIMARY KEY	(bracket_id)
);

	
