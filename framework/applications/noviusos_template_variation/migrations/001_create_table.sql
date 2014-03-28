/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

CREATE TABLE IF NOT EXISTS `nos_template_variation` (
  `tpvar_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tpvar_template` varchar(255) NOT NULL,
  `tpvar_title` varchar(255) NOT NULL,
  `tpvar_context` varchar(25) NOT NULL,
  `tpvar_default` tinyint(4) NOT NULL DEFAULT '0',
  `tpvar_data` text,
  `tpvar_created_at` datetime NOT NULL,
  `tpvar_updated_at` datetime NOT NULL,
  `tpvar_created_by_id` INT UNSIGNED NULL,
  `tpvar_updated_by_id` INT UNSIGNED NULL,
  PRIMARY KEY (`tpvar_id`),
  KEY `tpvar_template` (`tpvar_template`),
  KEY `tpvar_context` (`tpvar_context`)
) DEFAULT CHARSET=utf8;

DELETE FROM `nos_role_permission` WHERE `perm_category_key` = 'noviusos_template_variation';

INSERT INTO `nos_role_permission` (`perm_role_id`, `perm_name`, `perm_category_key`)
SELECT `perm_role_id`, `perm_name`, 'noviusos_template_variation'
FROM `nos_role_permission`
WHERE `perm_name` = 'nos::access'
AND  `perm_category_key` = 'noviusos_appmanager';