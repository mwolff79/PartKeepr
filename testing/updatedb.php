<?php
namespace PartKeepr\Testing;

declare(encoding = 'UTF-8');

use PartKeepr\PartKeepr,
    PartKeepr\Setup\Migration\PartDB\PartDBMigration,
    PartKeepr\Util\Configuration,
    PartKeepr\PartCategory\PartCategoryManager;


use PartKeepr\Setup\Setup;

declare(encoding = 'UTF-8');

include("../src/backend/PartKeepr/PartKeepr.php");

PartKeepr::initialize();

echo "mwolff79/PartKeepr DB Update Script\n\n";

if(Configuration::getOption("partkeepr.database.driver") !== pdo_mysql)
{
	echo "Error, not using MySQL. Please update manually.\n";
	exit;
}

if(Configuration::getOption("partkeepr.database.host", false) === false ||
	Configuration::getOption("partkeepr.database.username", false) === false ||
	Configuration::getOption("partkeepr.database.password", false) === false ||
	Configuration::getOption("partkeepr.database.dbname", false) === false)
	{
		echo "Error getting database settings. Check configuration and try again.\n";
		exit;
	}

echo "If you are sure you want to do this, type YES and hit return.\n";

$fp = fopen('php://stdin', 'r');
$data = fgets($fp, 1024);

if ($data !== "YES\n") {
	echo "Aborting.\n";
	die();
}	
	
$db_host = Configuration::getOption("partkeepr.database.host");
$db_user = Configuration::getOption("partkeepr.database.username");
$db_pass = Configuration::getOption("partkeepr.database.password");
$db_dbname = Configuration::getOption("partkeepr.database.dbname");

$link = mysql_connect($db_host, $db_user, $db_pass);
//mysql_select_db($db_dbname);

/* partcondition changes */
mysql_query("ALTER TABLE ".$db_dbname.".`Part` ADD COLUMN `partCondition` VARCHAR(255) NULL DEFAULT NULL AFTER `needsReview`;");

/* txtValue changes */
mysql_query("ALTER TABLE ".$db_dbname.".`PartParameter` ADD COLUMN `txtValue` VARCHAR(255) NULL DEFAULT NULL AFTER `rawValue`;");
mysql_query("ALTER TABLE ".$db_dbname.".`PartParameter` CHANGE COLUMN `value` `value` DOUBLE NULL;");
mysql_query("ALTER TABLE ".$db_dbname.".`PartParameter` CHANGE COLUMN `rawValue` `rawValue` DOUBLE NULL;");
mysql_query("ALTER TABLE ".$db_dbname.".`PartParameter` DROP FOREIGN KEY `FK_A28A985919187357`, DROP FOREIGN KEY `FK_A28A9859F8BD700D`;");
mysql_query("ALTER TABLE ".$db_dbname.".`PartParameter` ADD CONSTRAINT `FK_A28A985919187357` FOREIGN KEY (`siPrefix_id` ) REFERENCES ".$db_dbname.".`SiPrefix` (`id` ) ON DELETE CASCADE ON UPDATE CASCADE, ADD CONSTRAINT `FK_A28A9859F8BD700D` FOREIGN KEY (`unit_id` ) REFERENCES ".$db_dbname.".`Unit` (`id` ) ON DELETE CASCADE ON UPDATE CASCADE;");
mysql_query("ALTER TABLE ".$db_dbname.".`UnitSiPrefixes` DROP FOREIGN KEY `FK_723567409BE9F1F4`, DROP FOREIGN KEY `FK_72356740F8BD700D`;");
mysql_query("ALTER TABLE ".$db_dbname.".`UnitSiPrefixes` ADD CONSTRAINT `FK_723567409BE9F1F4` FOREIGN KEY (`siprefix_id` ) REFERENCES ".$db_dbname.".`SiPrefix` (`id` ) ON DELETE CASCADE ON UPDATE CASCADE, ADD CONSTRAINT `FK_72356740F8BD700D` FOREIGN KEY (`unit_id` ) REFERENCES ".$db_dbname.".`Unit` (`id` ) ON DELETE CASCADE ON UPDATE CASCADE;");
mysql_query("UPDATE ".$db_dbname.".`Unit` SET `id` = `id` + 1 ORDER BY `id` DESC;");
mysql_query("INSERT INTO ".$db_dbname.".`Unit` (`id`, `name`) VALUES (1, \"\");");
mysql_query("ALTER TABLE ".$db_dbname.".`UnitSiPrefixes` DROP FOREIGN KEY `FK_723567409BE9F1F4`, DROP FOREIGN KEY `FK_72356740F8BD700D`;");
mysql_query("ALTER TABLE ".$db_dbname.".`UnitSiPrefixes` ADD CONSTRAINT `FK_723567409BE9F1F4` FOREIGN KEY (`siprefix_id` ) REFERENCES ".$db_dbname.".`SiPrefix` (`id` ) ON DELETE RESTRICT ON UPDATE RESTRICT, ADD CONSTRAINT `FK_72356740F8BD700D` FOREIGN KEY (`unit_id` ) REFERENCES ".$db_dbname.".`Unit` (`id` ) ON DELETE RESTRICT ON UPDATE RESTRICT;");
mysql_query("ALTER TABLE ".$db_dbname.".`PartParameter` DROP FOREIGN KEY `FK_A28A985919187357`, DROP FOREIGN KEY `FK_A28A9859F8BD700D`;");
mysql_query("ALTER TABLE ".$db_dbname.".`PartParameter` ADD CONSTRAINT `FK_A28A985919187357` FOREIGN KEY (`siPrefix_id` ) REFERENCES ".$db_dbname.".`SiPrefix` (`id` ) ON DELETE RESTRICT ON UPDATE RESTRICT, ADD CONSTRAINT `FK_A28A9859F8BD700D` FOREIGN KEY (`unit_id` ) REFERENCES ".$db_dbname.".`Unit` (`id` ) ON DELETE RESTRICT ON UPDATE RESTRICT;");

mysql_close($link);

echo "All done.\n\n";
?>
