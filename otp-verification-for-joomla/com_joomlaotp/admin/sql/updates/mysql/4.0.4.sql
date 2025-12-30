ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `registration_otp_type` int(1) NOT NULL;
ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `login_otp_type` int(1) NOT NULL;
ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `enable_during_registration` int(1) NOT NULL;
ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `mo_otp_allowed_email_domains` VARCHAR(255) NOT NULL;
ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `reg_restriction` VARCHAR(255) NOT NULL;
ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `white_or_black` VARCHAR(255) NOT NULL;
--ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `redirect_after_login` VARCHAR(255) NOT NULL;
--ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `redirect_after_logout` VARCHAR(255) NOT NULL;