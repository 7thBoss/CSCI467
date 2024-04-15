CREATE TABLE orders ( 
    order_id INT(8) NOT NULL AUTO_INCREMENT,
    customer_id INT(32) NOT NULL,
    order_status VARCHAR(8) NOT NULL,
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
    onhand INT(32) NOT NULL,
    PRIMARY KEY (part_num)
);
