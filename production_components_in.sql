/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 10.4.14-MariaDB : Database - production
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`production` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `production`;

/*Table structure for table `components_in` */

DROP TABLE IF EXISTS `components_in`;

CREATE TABLE `components_in` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `incoming_comp_po` varchar(200) DEFAULT NULL,
  `component_id` bigint(20) DEFAULT NULL,
  `component_price` decimal(9,2) DEFAULT NULL,
  `group` varchar(25) DEFAULT NULL,
  `qty` bigint(20) DEFAULT NULL,
  `tgl_in` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4;

/* Trigger structure for table `components_in` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `components_in_stok_masuk` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `components_in_stok_masuk` AFTER INSERT ON `components_in` FOR EACH ROW BEGIN
        UPDATE components SET stock=stock+new.qty
        WHERE id=new.component_id;
        UPDATE components SET 	   saldo_akhir=stock*price_beli
        WHERE id=new.component_id;        
END */$$


DELIMITER ;

/* Trigger structure for table `components_in` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `components_in_stok_masuk_dobel` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `components_in_stok_masuk_dobel` AFTER DELETE ON `components_in` FOR EACH ROW BEGIN
        UPDATE components SET stock=stock-old.qty
        WHERE id=old.component_id;
        UPDATE components SET saldo_akhir=stock*price_beli
        WHERE id=old.component_id;        
END */$$


DELIMITER ;

/* Trigger structure for table `components_out` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `components_out_stock_out` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `components_out_stock_out` AFTER INSERT ON `components_out` FOR EACH ROW BEGIN
	if(new.group='material') THEN
        UPDATE components SET stock=stock-new.qty
        WHERE id=new.component_id;
                UPDATE components SET saldo_akhir=stock*price_beli
        WHERE id=new.component_id;
    END IF;
END */$$


DELIMITER ;

/* Trigger structure for table `components_out` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `components_out_stock_out_delete` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `components_out_stock_out_delete` AFTER DELETE ON `components_out` FOR EACH ROW BEGIN
        UPDATE components SET stock=stock+old.qty
        WHERE id=old.component_id;
                UPDATE components SET saldo_akhir=stock*price_beli
        WHERE id=old.component_id;
END */$$


DELIMITER ;

/* Trigger structure for table `incoming_components` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `incoming_components_delete` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `incoming_components_delete` AFTER DELETE ON `incoming_components` FOR EACH ROW BEGIN
	DELETE FROM incoming_components_detail WHERE incoming_components_detail.incoming_components_po=old.no_po;
    	DELETE FROM components_in WHERE components_in.incoming_comp_po=old.no_po;
END */$$


DELIMITER ;

/* Trigger structure for table `incoming_data` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `incoming_data_delete` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `incoming_data_delete` AFTER DELETE ON `incoming_data` FOR EACH ROW BEGIN
	DELETE from production_data
    WHERE file_id=old.id;
END */$$


DELIMITER ;

/* Trigger structure for table `outgoing_components` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `outgoing_components_cancel` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `outgoing_components_cancel` AFTER DELETE ON `outgoing_components` FOR EACH ROW BEGIN
	DELETE from outgoing_components_detail
    WHERE outgoing_components_job=old.no_job;
END */$$


DELIMITER ;

/* Trigger structure for table `outgoing_components_detail` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `outgoing_components_detail_stokout` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `outgoing_components_detail_stokout` AFTER INSERT ON `outgoing_components_detail` FOR EACH ROW BEGIN
	INSERT INTO components_out
    (job_ticket,component_id,component_price,components_out.group,qty)
    VALUES (new.outgoing_components_job,new.components_id,new.components_price,'material',new.qty_out);
END */$$


DELIMITER ;

/* Trigger structure for table `outgoing_components_detail` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `outgoing_components_detail_delete` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `outgoing_components_detail_delete` AFTER DELETE ON `outgoing_components_detail` FOR EACH ROW BEGIN
	DELETE from components_out
    WHERE job_ticket=old.outgoing_components_job
    and component_id=old.components_id
    AND qty=old.qty_out
    ORDER by id DESC LIMIT 1;
END */$$


DELIMITER ;

/* Trigger structure for table `production_data` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `production_data_delete` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `production_data_delete` AFTER DELETE ON `production_data` FOR EACH ROW BEGIN
	DELETE from production_data_detail
    WHERE production_id=old.id;
	DELETE from components_out
    WHERE job_ticket=old.job_ticket; 
    DELETE from manifest WHERE production_id=old.id;
END */$$


DELIMITER ;

/* Trigger structure for table `production_data_detail` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `production_data_detail_delete` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `production_data_detail_delete` AFTER DELETE ON `production_data_detail` FOR EACH ROW BEGIN
	DELETE FROM production_data_detail_list
    WHERE production_data_detail_id=old.id;
END */$$


DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
