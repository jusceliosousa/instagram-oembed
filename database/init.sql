CREATE DATABASE db_integracao;

USE db_integracao;

CREATE TABLE `instagram` (
  `id` int(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `face_client_id` VARCHAR(30) NOT NULL,
  `face_secret_id` VARCHAR(50) NOT NULL,
  `redirect_uri` VARCHAR(255) NOT NULL,
  `instagram_client_id` VARCHAR(30) NOT NULL,
  `instagram_secret_id` VARCHAR(50) NOT NULL,
  `accessToken` VARCHAR(255) NULL,
  `expiresAt` TIMESTAMP NULL,
  `user_id` VARCHAR(50) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

