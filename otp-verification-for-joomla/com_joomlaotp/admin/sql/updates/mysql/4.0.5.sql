CREATE TABLE IF NOT EXISTS `#__miniorange_otp_custom_message` (
`id` int(11) UNSIGNED NOT NULL,
`mo_custom_email_success_message` VARCHAR(1048) NOT NULL,
`mo_custom_email_error_message` VARCHAR(1048) NOT NULL,
`mo_custom_email_invalid_format_message` VARCHAR(1048) NOT NULL,
`mo_custom_email_blocked_message` VARCHAR(1048) NOT NULL,
`mo_custom_phone_success_message` VARCHAR(1048) NOT NULL,
`mo_custom_phone_error_message` VARCHAR(1048) NOT NULL,
`mo_custom_phone_invalid_format_message` VARCHAR(1048) NOT NULL,
`mo_custom_phone_blocked_message` VARCHAR(1048) NOT NULL,
`mo_custom_invalid_otp_message` VARCHAR(1048) NOT NULL,
`mo_block_country_code` VARCHAR (1048) NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

INSERT IGNORE INTO `#__miniorange_otp_custom_message`(`id`) values (1);

ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `rs_email_field` VARCHAR(255) ;
ALTER TABLE `#__miniorange_otp_customer` ADD COLUMN `rs_contact_field` VARCHAR(255) ;