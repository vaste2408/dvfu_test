-- ----------------------------
-- ТАБЛИЦА ДЛЯ ЗАПИСИ СОТРУДНИКОВ НА СОБРАНИЕ
-- ----------------------------
DROP TABLE IF EXISTS `Forums`;
CREATE TABLE `Forums`  (
  `id_employee` int(11) NOT NULL,
  `id_meeting` int(11) NOT NULL,
  `active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id_employee`, `id_meeting`) USING BTREE,
  INDEX `f_m`(`id_meeting`) USING BTREE,
  CONSTRAINT `f_e` FOREIGN KEY (`id_employee`) REFERENCES `Employees` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `f_m` FOREIGN KEY (`id_meeting`) REFERENCES `Meetings` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
)