CREATE DATABASE IF NOT EXISTS spk_ac CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spk_ac;

CREATE TABLE kriteria (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    kode  VARCHAR(5)  NOT NULL UNIQUE,
    nama  VARCHAR(60) NOT NULL,
    bobot DECIMAL(4,2) NOT NULL,
    tipe  ENUM('benefit','cost') NOT NULL
);

CREATE TABLE alternatif (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    kode      VARCHAR(5)  NOT NULL UNIQUE,
    nama_pk   VARCHAR(30) NOT NULL,
    btu       INT NOT NULL,
    daya_watt INT NOT NULL,
    harga     INT NOT NULL
);

CREATE TABLE konsultasi (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    luas          DECIMAL(6,2) NOT NULL,
    matahari      VARCHAR(10)  NOT NULL,
    kebutuhan_btu INT NOT NULL,
    rekomendasi   VARCHAR(30)  NOT NULL,
    skor          DECIMAL(5,3) NOT NULL,
    tanggal       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO kriteria (kode, nama, bobot, tipe) VALUES
('C1', 'Kecocokan kapasitas', 0.50, 'benefit'),
('C2', 'Daya listrik (watt)', 0.30, 'cost'),
('C3', 'Harga (Rp)',          0.20, 'cost');

INSERT INTO alternatif (kode, nama_pk, btu, daya_watt, harga) VALUES
('A1', 'AC 0,5 PK',  5000,  485, 2800000),
('A2', 'AC 0,75 PK', 7000,  650, 3300000),
('A3', 'AC 1 PK',    9000,  880, 3800000),
('A4', 'AC 1,5 PK', 12000, 1200, 5200000),
('A5', 'AC 2 PK',   18000, 1700, 7500000);
