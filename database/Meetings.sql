-- ----------------------------
-- Table structure for Meetings
-- ----------------------------
DROP TABLE IF EXISTS `Meetings`;
CREATE TABLE `Meetings`  (
  `id` int(11) NOT NULL,
  `starts_at` datetime(0) NOT NULL,
  `ends_at` datetime(0) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`id`) USING BTREE
)