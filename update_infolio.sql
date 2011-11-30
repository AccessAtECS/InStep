CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vassetswithcounts`
AS select
   `assets`.`id` AS `id`,
   `assets`.`title` AS `title`,
   `assets`.`description` AS `description`,
   `assets`.`href` AS `href`,
   `assets`.`width` AS `width`,
   `assets`.`height` AS `height`,
   `assets`.`type` AS `type`,
   `assets`.`public` AS `public`,
   `assets`.`enabled` AS `enabled`,
   `assets`.`updated_by` AS `updated_by`,
   `assets`.`updated_time` AS `updated_time`,
   `assets`.`created_by` AS `created_by`,
   `assets`.`created_time` AS `created_time`,ifnull(`vassetcounts`.`count`,0) AS `count`,
   `assets`.`instep_asset` AS `instep_asset`,
   `institution`.`id` AS `institution_id`,
   `institution`.`url` AS `institution_url`
from (((`assets` left join `vassetcounts` on((`assets`.`id` = `vassetcounts`.`asset_id`))) join `user` on((`user`.`ID` = `assets`.`created_by`))) join `institution` on((`user`.`institution_id` = `institution`.`id`)));


ALTER TABLE `assets` ADD `instep_asset` INT(1) NULL DEFAULT '0' AFTER `created_time`