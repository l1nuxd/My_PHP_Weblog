-- CREATE TABLE users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     name VARCHAR(100) NOT NULL,
--     username VARCHAR(50) NOT NULL UNIQUE,
--     email VARCHAR(100) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- );

-- mysqldump -u dbuser -p dbpassword Webapp > backup.sql


-- CREATE TABLE login_logs (
--     id BIGINT AUTO_INCREMENT PRIMARY KEY,
--     ip_address VARCHAR(45) NOT NULL,
--     user_agent TEXT,
--     referrer TEXT,
--     username VARCHAR(50),
--     login_logs SMALLINT,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

/*CREATE TABLE login_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    referrer TEXT,
    username VARCHAR(50),
    login_logs SMALLINT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

*/


-- create table tweets (
--     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--     user_id BIGINT UNSIGNED NOT NULL,
--     content VARCHAR(280) NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- );