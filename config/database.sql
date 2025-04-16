-- -- Hapus database jika sudah ada (HATI-HATI! Ini akan menghapus semua data)
-- DROP DATABASE IF EXISTS debts_management;

-- -- Buat database baru
-- CREATE DATABASE debts_management 
--     CHARACTER SET utf8mb4 
--     COLLATE utf8mb4_unicode_ci;

-- -- Gunakan database yang baru dibuat
-- USE debts_management;

-- -- 1. Tabel Users
-- CREATE TABLE users (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     username VARCHAR(100) NOT NULL UNIQUE,
--     nama VARCHAR(200) NOT NULL,
--     email VARCHAR(255) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL,
--     role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
--     status TINYINT(1) NOT NULL DEFAULT 1,
--     reset_token VARCHAR(255) NULL,
--     reset_expiry DATETIME NULL,
--     created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
--     updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB;

-- -- 2. Tabel Agents
-- CREATE TABLE agents (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     kode_agen VARCHAR(20) NOT NULL UNIQUE,
--     nama_agen VARCHAR(200) NOT NULL,
--     kontak TEXT NULL,
--     created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
--     updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB;

-- -- 3. Tabel Payment Methods
-- CREATE TABLE payment_methods (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     kode_metode VARCHAR(20) NOT NULL UNIQUE,
--     nama_metode VARCHAR(100) NOT NULL,
--     created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
--     updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB;

-- -- 4. Tabel Debts (Hutang)
-- CREATE TABLE debts (
--     id INT PRIMARY KEY AUTO_INCREMENT,
--     user_id INT NOT NULL,
--     agent_id INT NOT NULL,
--     payment_method_id INT NOT NULL,
--     tanggal_hutang DATE NOT NULL,
--     tanggal_jatuh_tempo DATE NOT NULL,
--     sisa_hutang DECIMAL(15,2) NOT NULL,
--     created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
--     updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
--     -- Foreign Keys
--     FOREIGN KEY (user_id) 
--         REFERENCES users(id)
--         ON DELETE CASCADE,
        
--     FOREIGN KEY (agent_id) 
--         REFERENCES agents(id)
--         ON DELETE RESTRICT,
        
--     FOREIGN KEY (payment_method_id) 
--         REFERENCES payment_methods(id)
--         ON DELETE RESTRICT
-- ) ENGINE=InnoDB;

-- -- Tambahan Index untuk Optimasi
-- CREATE INDEX idx_users_username ON users(username);
-- CREATE INDEX idx_agents_kode ON agents(kode_agen);
-- CREATE INDEX idx_payment_methods_kode ON payment_methods(kode_metode);
-- CREATE INDEX idx_debt_dates ON debts(tanggal_hutang, tanggal_jatuh_tempo);
-- CREATE INDEX idx_user_debts ON debts(user_id);
-- CREATE INDEX idx_agent_debts ON debts(agent_id);

-- new

-- Hapus database jika sudah ada (HATI-HATI! Ini akan menghapus semua data)
DROP DATABASE IF EXISTS debts_management;

-- Buat database baru
CREATE DATABASE debts_management 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Gunakan database yang baru dibuat
USE debts_management;

-- 1. Tabel Users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    nama VARCHAR(200) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    status TINYINT(1) NOT NULL DEFAULT 1,
    reset_token VARCHAR(255) NULL,
    reset_expiry DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabel Sessions
CREATE TABLE sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    login_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_activity DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expiry_time DATETIME NOT NULL,
    
    FOREIGN KEY (user_id) 
        REFERENCES users(id)
        ON DELETE CASCADE,
        
    INDEX idx_session_token (session_token),
    INDEX idx_user_sessions (user_id)
) ENGINE=InnoDB;

-- 3. Tabel Agents
CREATE TABLE agents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_agen VARCHAR(20) NOT NULL UNIQUE,
    nama_agen VARCHAR(200) NOT NULL,
    kontak TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Tabel Payment Methods
CREATE TABLE payment_methods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_metode VARCHAR(20) NOT NULL UNIQUE,
    nama_metode VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5. Tabel Debts (Hutang)
CREATE TABLE debts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    debt_id VARCHAR(200) NOT NULL,
    user_id INT NOT NULL,
    agent_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    tanggal_hutang DATE NOT NULL,
    tanggal_jatuh_tempo DATE NOT NULL,
    sisa_hutang DECIMAL(15,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (user_id) 
        REFERENCES users(id)
        ON DELETE CASCADE,
        
    FOREIGN KEY (agent_id) 
        REFERENCES agents(id)
        ON DELETE RESTRICT,
        
    FOREIGN KEY (payment_method_id) 
        REFERENCES payment_methods(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tambahan Index untuk Optimasi
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_agents_kode ON agents(kode_agen);
CREATE INDEX idx_payment_methods_kode ON payment_methods(kode_metode);
CREATE INDEX idx_debt_dates ON debts(tanggal_hutang, tanggal_jatuh_tempo);
CREATE INDEX idx_user_debts ON debts(user_id);
CREATE INDEX idx_agent_debts ON debts(agent_id);