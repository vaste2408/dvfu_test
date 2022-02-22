-- ----------------------------
-- Table structure for Employees
-- ----------------------------
DROP TABLE IF EXISTS `Employees`;
CREATE TABLE `Employees`  (
  `id` int(11) NOT NULL,
  `fio` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id`) USING BTREE
)