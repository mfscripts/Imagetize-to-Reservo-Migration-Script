<?php
/*
 * Imagetize v1.1 => Reservo Migration Script.
 * 
 * Mirgration script for converting users, images and stats data
 * from Imagetize v1.1. Set the config values below, upload this
 * script to the base of your Reservo install and load it within
 * a browser.
 * 
 * REQUIREMENTS:
 * MySQL PDO
 * Reservo installed
 *
 * This has been tested with Imagetize v1.1 although it may also
 * work with other versions.
 */

// Imagetize v1.1 - database settings
define('IMAGETIZE_DB_HOST', 'localhost');
define('IMAGETIZE_DB_NAME', '');
define('IMAGETIZE_DB_USER', '');
define('IMAGETIZE_DB_PASS', '');

// Imagetize v1.1 - database table prefix
define('IMAGETIZE_DB_TABLE_PREFIX', '');

// Reservo - config file path
define('RESERVO_CONFIG_FILE_PATH', '_config.inc.php');

/*
 * ******************************************************************
 * END OF CONFIG SECTION, YOU SHOULDN'T NEED TO CHANGE ANYTHING ELSE
 * ******************************************************************
 */
 
// allow up to 24 hours for it to run
set_time_limit(60*60*24);

// make sure we are in the root and can find the config file
if (!file_exists(RESERVO_CONFIG_FILE_PATH))
{
    die('ERROR: Could not load Reservo config file. Ensure you\'re running this script from the root of your Reservo install.');
}

// include Reservo config
require_once(RESERVO_CONFIG_FILE_PATH);

// test database connectivity, Reservo
try
{
    $ysDBH = new PDO("mysql:host=" . _CONFIG_DB_HOST . ";dbname=" . _CONFIG_DB_NAME, _CONFIG_DB_USER, _CONFIG_DB_PASS);
    $ysDBH->exec("set names utf8");
}
catch (PDOException $e)
{
    die('ERROR: Could not connect to Reservo database. ' . $e->getMessage());
}

// test database connectivity, Imagetize
try
{
    $imagetizeDBH = new PDO("mysql:host=" . IMAGETIZE_DB_HOST . ";dbname=" . IMAGETIZE_DB_NAME, IMAGETIZE_DB_USER, IMAGETIZE_DB_PASS);
    $imagetizeDBH->exec("set names utf8");
}
catch (PDOException $e)
{
    die('ERROR: Could not connect to Imagetize database. ' . $e->getMessage());
}

// initial checks passed, load stats for converting and get user confirmation
$chevStats = array();

// files
$getFiles               = $imagetizeDBH->query('SELECT COUNT(ph_id) AS total FROM '.IMAGETIZE_DB_TABLE_PREFIX.'photos_639');
$row                    = $getFiles->fetchObject();
$chevStats['totalFiles'] = (int) $row->total;

// users
$getUsers               = $imagetizeDBH->query('SELECT COUNT(u_id) AS total FROM '.IMAGETIZE_DB_TABLE_PREFIX.'users WHERE u_banned = 0 AND u_removed = 0 AND u_id > 0');
$row                    = $getUsers->fetchObject();
$chevStats['totalUsers'] = (int) $row->total;

// folders
$getFolders               = $imagetizeDBH->query('SELECT COUNT(a_id) AS total FROM '.IMAGETIZE_DB_TABLE_PREFIX.'albums_527 WHERE a_removed = 0 AND a_id > 0');
$row                      = $getFolders->fetchObject();
$chevStats['totalFolders'] = (int) $row->total;

// banned ips
$getIpBans               = $imagetizeDBH->query('SELECT COUNT(ib_id) AS total FROM '.IMAGETIZE_DB_TABLE_PREFIX.'ip_bans_783');
$row                     = $getIpBans->fetchObject();
$chevStats['totalIpBans'] = (int) $row->total;

// page setup
define('PAGE_TITLE', 'Imagetize 1.1 => Reservo Migration Tool');
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title><?php echo PAGE_TITLE; ?></title>
        <meta name="distribution" content="global" />
        <style>
            body
            {
                margin: 0px;
                padding: 0;
                font: 100%/1.0 helvetica, arial, sans-serif;
                color: #444;
                background: #ccc;
            }

            h1, h2, h3, h4, h5, h6
            {
                margin: 0 0 1em;
                line-height: 1.1;
            }

            h2, h3 { color: #003d5d; }
            h2 { font-size: 218.75%; }
            h3 { font-size: 137.5%; }
            h4 { font-size: 118.75%; }
            h5 { font-size: 112.5%; }
            p { margin: 0 0 1em; }
            img { border: none; }
            a:link { color: #035389; }
            a:visited { color: #09619C; }

            a:focus
            {
                color: #fff;
                background: #000;
            }

            a:hover { color: #000; }

            a:active
            {
                color: #cc0000;
                background: #fff;
            }

            table
            {
                margin: 1em 0;
                border-collapse: collapse;
                width: 100%;
            }

            table caption
            {
                text-align: left;
                font-weight: bold;
                padding: 0 0 5px;
                text-transform: uppercase;
                color: #236271;
            }

            table td, table th
            {
                text-align: left;
                border: 1px solid #b1d2e4;
                padding: 5px 10px;
                vertical-align: top;
            }

            table th { background: #ecf7fd; }

            blockquote
            {
                background: #ecf7fd;
                margin: 1em 0;
                padding: 1.5em;
            }

            code
            {
                background: #ecf7fd;
                font: 115% courier, monaco, monospace;
                margin: 0 .3em;
            }

            abbr, acronym
            {
                border-bottom: .1em dotted;
                cursor: help;
            }
            #container
            {
                margin: 0 0px;
                background: #fff;
            }

            #header
            {
                background: #ccc;
                padding: 20px;
            }

            #header h1 { margin: 0; }

            #navigation
            {
                float: left;
                width: 100%;
                background: #333;
            }

            #navigation ul
            {
                margin: 0;
                padding: 0;
            }

            #navigation ul li
            {
                list-style-type: none;
                display: inline;
            }

            #navigation li a
            {
                display: block;
                float: left;
                padding: 5px 10px;
                color: #fff;
                text-decoration: none;
                border-right: 1px solid #fff;
            }

            #navigation li a:hover { background: #383; }

            #content
            {
                clear: left;
                padding: 20px;
            }

            #content h2
            {
                color: #000;
                font-size: 160%;
                margin: 0 0 .5em;
            }

            #footer
            {
                background: #ccc;
                text-align: right;
                padding: 20px;
                height: 1%;
                font-size: 12px;
            }

            .important, .error
            {
                color: red;
                font-weight: bold;
            }

            .success
            {
                color: green;
                font-weight: bold;
            }

            .button
            {
                border:1px solid #4b546a;-webkit-box-shadow: #B7B8B8 0px 1px 0px inset;-moz-box-shadow: #B7B8B8 0px 1px 0px inset; box-shadow: #B7B8B8 0px 1px 0px inset;-webkit-border-radius: br_rightpx br_leftpx -1px -1px;-moz-border-radius: br_rightpx br_leftpx -1px -1px;border-radius: br_rightpx br_leftpx -1px -1px; padding: 10px 10px 10px 10px; text-decoration:none; display:inline-block;text-shadow: -1px -1px 0 rgba(0,0,0,0.3);font-weight:bold; color: #FFFFFF;
                background-color: #606c88; background-image: -webkit-gradient(linear, left top, left bottom, from(#606c88), to(#3f4c6b));
                background-image: -webkit-linear-gradient(top, #606c88, #3f4c6b);
                background-image: -moz-linear-gradient(top, #606c88, #3f4c6b);
                background-image: -ms-linear-gradient(top, #606c88, #3f4c6b);
                background-image: -o-linear-gradient(top, #606c88, #3f4c6b);
                background-image: linear-gradient(to bottom, #606c88, #3f4c6b);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#606c88, endColorstr=#3f4c6b);
                cursor: pointer;
            }

            .button:hover
            {
                border:1px solid #4b546a;
                background-color: #4b546a; background-image: -webkit-gradient(linear, left top, left bottom, from(#4b546a), to(#2c354b));
                background-image: -webkit-linear-gradient(top, #4b546a, #2c354b);
                background-image: -moz-linear-gradient(top, #4b546a, #2c354b);
                background-image: -ms-linear-gradient(top, #4b546a, #2c354b);
                background-image: -o-linear-gradient(top, #4b546a, #2c354b);
                background-image: linear-gradient(to bottom, #4b546a, #2c354b);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#4b546a, endColorstr=#2c354b);
            }
			
			label
			{
				margin-bottom: 2px;
				font-weight: bold;
			}
			
			label, input
			{
				clear: both;
				display: block;
			}
			
			input[type=password]
			{
				padding: 5px;
				border:1px solid #4b546a;-webkit-box-shadow: #B7B8B8 0px 1px 0px inset;-moz-box-shadow: #B7B8B8 0px 1px 0px inset; box-shadow: #B7B8B8 0px 1px 0px inset;-webkit-border-radius: br_rightpx br_leftpx -1px -1px;-moz-border-radius: br_rightpx br_leftpx -1px -1px;border-radius: br_rightpx br_leftpx -1px -1px; padding: 10px 10px 10px 10px; text-decoration:none;
				margin-bottom: 18px;
				width: 300px;
			}
        </style>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <h1>
                    <?php echo PAGE_TITLE; ?>
                </h1>
            </div>
            <div id="content">
                <?php if (!isset($_REQUEST['submitted'])): ?>
                    <h2>
                        Confirm Migration
                    </h2>
                    <p>
                        Use this tool to migrate your users, images, albums and other data from Imagetize into a Reservo install.
                    </p>
                    <p>
                        To start, upload this file to the root of your Reservo install, ensure you've set your configuration at the top of this php script, then click 'start migration' below. To confirm, we've loaded your existing Imagetize table sizes below.
                    </p>
                    <p style='padding-top: 4px; padding-bottom: 4px;'>
                        <table style='width: auto;'>
                            <tr>
                                <th style='width: 150px;'>Imagetize Table:</th>
                                <th style='width: 150px;'>Total Rows:</th>
                            </tr>
                            <tr>
                                <td>photos_639:</td>
                                <td><?php echo $chevStats['totalFiles']; ?></td>
                            </tr>
                            <tr>
                                <td>users:</td>
                                <td><?php echo $chevStats['totalUsers']; ?></td>
                            </tr>
                            <tr>
                                <td>albums_527:</td>
                                <td><?php echo $chevStats['totalFolders']; ?></td>
                            </tr>
                            <tr>
                                <td>ip_bans_783:</td>
                                <td><?php echo $chevStats['totalIpBans']; ?></td>
                            </tr>
                        </table>
                    </p>
                    <p class="important">
                        IMPORTANT: When you start this process, any existing data in your Reservo database will be cleared. Please ensure you've backed up both databases beforehand so you can easily revert if you need to.
                    </p>
					<p class="important">
                        This process wont actually migrate your images, it converts all the data in your Imagetize database for Reservo. Although it does keep the same file names for your stored images. So after this is completed you should move all your images into the Reservo /files/ folder on each server. More details supplied after the data conversion.
                    </p>
                    <p style="padding-top: 4px;">
                        <form method="POST" action="migrate.php">
                            <input type="hidden" name="submitted" value="1"/>
                            <input type="submit" value="Start Migration" name="submit" class="button" onClick="return confirm('Are you sure you want to delete all the data from your Reservo database and import from the Imagetize database?\n\nYour users will need to request a password reset via Reservo once their data is migrated.');"/>
                        </form>
                    </p>
                <?php else: ?>
                    <h2>
                        Importing Data
                    </h2>
                    <p>
                        Clearing existing Reservo data... 
                        <?php
                        // delete Reservo data
                        $ysDBH->query('DELETE FROM download_tracker');
                        $ysDBH->query('DELETE FROM file');
                        $ysDBH->query('DELETE FROM file_folder');
                        $ysDBH->query('DELETE FROM payment_log');
                        $ysDBH->query('DELETE FROM sessions');
                        $ysDBH->query('DELETE FROM session_transfer');
                        $ysDBH->query('DELETE FROM stats');
                        $ysDBH->query('DELETE FROM users');
						$ysDBH->query('DELETE FROM banned_ips');
						$ysDBH->query('DELETE FROM plugin_imageviewer_category');
						$ysDBH->query('DELETE FROM plugin_imageviewer_category_file');
						$ysDBH->query('DELETE FROM plugin_imageviewer_meta');
						$ysDBH->query('DELETE FROM file_server');
						
						// create local server entry
						$sql   = "INSERT INTO `file_server` (`id`, `serverLabel`, `serverType`, `ipAddress`, `ftpPort`, `ftpUsername`, `ftpPassword`, `statusId`, `storagePath`, `fileServerDomainName`, `scriptPath`, `totalSpaceUsed`, `maximumStorageBytes`, `priority`, `routeViaMainSite`, `lastFileActionQueueProcess`, `serverConfig`) VALUES (1, 'Local Default', 'local', '', 0, '', NULL, 2, NULL, NULL, NULL, 0, 0, 0, 0, '0000-00-00 00:00:00', NULL);";
						$q     = $ysDBH->prepare($sql);
						$count = $q->execute();

                        echo 'done.';
                        ?>
                        <?php updateScreen(); ?>
                    </p>
                    <p style='padding-top: 4px; padding-bottom: 4px;'>
                        <table style='width: auto;'>
                            <tr>
                                <th style='width: 150px;'>Imagetize Table:</th>
                                <th style='width: 150px;'>Total Rows:</th>
                                <th style='width: 150px;'>Reservo Table:</th>
                                <th style='width: 150px;'>Successful Rows:</th>
                                <th style='width: 150px;'>Failed Rows:</th>
                            </tr>

                            <?php
                            // do images
                            $getFiles       = $imagetizeDBH->query('SELECT ph_id, ph_a_id, ph_u_id, ph_filename, ph_views, ph_filesize, ph_upload_dt, ph_private, ph_user_ip FROM '.IMAGETIZE_DB_TABLE_PREFIX.'photos_639');
                            $success        = 0;
                            $error          = 0;
							while($row = $getFiles->fetch())
                            {
								$filePrefix = 'photos/';
								$localFilePath = $filePrefix.$row['ph_filename'];

                                // insert into Reservo db
                                $sql   = "INSERT INTO file (id, originalFilename, shortUrl, fileType, extension, fileSize, localFilePath, userId, totalDownload, uploadedIP, uploadedDate, statusId, visits, lastAccessed, deleteHash, folderId, serverId, accessPassword) VALUES (:id, :originalFilename, :shortUrl, :fileType, :extension, :fileSize, :localFilePath, :userId, :totalDownload, :uploadedIP, :uploadedDate, :statusId, :visits, :lastAccessed, :deleteHash, :folderId, :serverId, :accessPassword)";
                                $q     = $ysDBH->prepare($sql);
                                $count = $q->execute(array(
                                    ':id'               => $row['ph_id'],
                                    ':originalFilename' => $row['ph_filename'],
                                    ':shortUrl'         => $row['id'],
                                    ':fileType'         => guess_mime_type($row['ph_filename']),
                                    ':extension'        => get_file_extension(strtolower($row['ph_filename'])),
                                    ':fileSize'         => $row['ph_filesize'],
                                    ':localFilePath'    => $localFilePath,
                                    ':userId'           => ((int)$row['ph_u_id']==0?null:$row['ph_u_id']),
                                    ':totalDownload'    => $row['ph_views'],
                                    ':uploadedIP'       => $row['ph_user_ip'],
                                    ':uploadedDate'     => $row['ph_upload_dt'],
                                    ':statusId'         => 1,
                                    ':visits'           => $row['ph_views'],
                                    ':lastAccessed'     => null,
                                    ':deleteHash'       => MD5($row['ph_filename'].$row['ph_id'].rand(1000000,9999999)),
                                    ':folderId'         => ((int)$row['ph_a_id']==0?null:$row['ph_a_id']),
                                    ':serverId'         => 1,
                                    ':accessPassword'   => null,
                                ));

                                if ($count)
                                {
                                    $success++;
                                }
                                else
                                {
                                    $error++;
                                }
                            }
                            ?>
                            <tr>
                                <td>photos_639:</td>
                                <td><?php echo $chevStats['totalFiles']; ?></td>
                                <td>file:</td>
                                <td><?php echo $success; ?></td>
                                <td><?php echo $error; ?></td>
                            </tr>
                            <?php updateScreen(); ?>

                            <?php
                            // do users
                            $getUsers = $imagetizeDBH->query('SELECT u_id, u_login, u_password, u_email, u_registered_dt FROM '.IMAGETIZE_DB_TABLE_PREFIX.'users WHERE u_banned = 0 AND u_removed = 0 AND u_id > 0 ORDER BY u_id DESC');
                            $success  = 0;
                            $error    = 0;
                            while($row = $getUsers->fetch())
                            {
                                // insert into Reservo db
                                $sql       = "INSERT INTO users (id, username, password, level_id, email, lastlogindate, lastloginip, status, datecreated, createdip, identifier, paidExpiryDate) VALUES (:id, :username, :password, :level_id, :email, :lastlogindate, :lastloginip, :status, :datecreated, :createdip, :identifier, :paidExpiryDate)";
                                $q         = $ysDBH->prepare($sql);

                                $count = $q->execute(array(
                                    ':id'             => $row['u_id'],
                                    ':username'       => $row['u_login'],
                                    ':password'       => $row['u_password'],
                                    ':level_id'       => 1,
                                    ':email'          => $row['u_email'],
                                    ':lastlogindate'  => null,
                                    ':lastloginip'    => null,
                                    ':status'         => 'active',
                                    ':datecreated'    => $row['u_registered_dt'],
                                    ':createdip'      => null,
                                    ':identifier'     => MD5(microtime() . $row['u_id'] . microtime()),
									':paidExpiryDate' => null,
                                ));

								if($q->errorCode() == 0)
								{
                                    $success++;
                                }
                                else
                                {
									if($error < 100)
									{
										$errorLocal = $q->errorInfo();
										echo 'Skipped Row: '.$errorLocal[2]."<br/>";
									}
									if($error == 100)
									{
										echo "<strong>... [truncated insert errors to first 100]</strong><br/>";
									}
                                    $error++;
                                }
                            }
                            ?>
                            <tr>
                                <td>users:</td>
                                <td><?php echo $chevStats['totalUsers']; ?></td>
                                <td>users:</td>
                                <td><?php echo $success; ?></td>
                                <td><?php echo $error; ?></td>
                            </tr>
                            <?php updateScreen(); ?>

                            <?php
                            // do albums
                            $getFolders = $imagetizeDBH->query('SELECT a_id, a_name, a_u_id FROM '.IMAGETIZE_DB_TABLE_PREFIX.'albums_527 WHERE a_removed = 0 AND a_id > 0');
                            $success    = 0;
                            $error      = 0;
                            while($row = $getFolders->fetch())
                            {
								$isPublic = 2;
								
                                // insert into Reservo db
                                $sql   = "INSERT INTO file_folder (id, userId, folderName, isPublic, date_added) VALUES (:id, :userId, :folderName, :isPublic, :date_added)";
                                $q     = $ysDBH->prepare($sql);
                                $count = $q->execute(array(
                                    ':id'         => $row['a_id'],
                                    ':userId'     => $row['a_u_id'],
                                    ':folderName' => $row['a_name'],
                                    ':isPublic'   => (int)$row['a_private']==1?0:2,
									':date_added' => date('Y-m-d H:i:s'),
                                ));

                                if ($count)
                                {
                                    $success++;
                                }
                                else
                                {
                                    $error++;
                                }
                            }
                            ?>
                            <tr>
                                <td>albums_527:</td>
                                <td><?php echo $chevStats['totalFolders']; ?></td>
                                <td>file_folder:</td>
                                <td><?php echo $success; ?></td>
                                <td><?php echo $error; ?></td>
                            </tr>
                            <?php updateScreen();?>

                            <?php
                            // do ip bans
                            $getIpBans = $imagetizeDBH->query('SELECT ib_id, ib_ip, ib_ban_dt FROM '.IMAGETIZE_DB_TABLE_PREFIX.'ip_bans_783');
                            $success     = 0;
                            $error       = 0;
                            while($row = $getIpBans->fetch())
                            {
								// insert row
								$sql   = "INSERT INTO banned_ips (id, ipAddress, dateBanned, banType, banNotes, banExpiry) VALUES (:id, :ipAddress, :dateBanned, :banType, :banNotes, :banExpiry)";
                                $q     = $ysDBH->prepare($sql);
                                $count = $q->execute(array(
                                    ':id'            => $row['ib_id'],
                                    ':ipAddress'       => $row['ib_ip'],
                                    ':dateBanned'  => date('Y-m-d H:i:s'),
                                    ':banType'        => 'Whole Site',
									':banNotes' => $row['ib_ban_dt'],
									':banExpiry' => null,
                                ));

                                if ($count)
                                {
                                    $success++;
                                }
                                else
                                {
                                    $error++;
                                }
                            }
                            ?>
                            <tr>
                                <td>ip_bans_783:</td>
                                <td><?php echo $chevStats['totalIpBans']; ?></td>
                                <td>banned_ips:</td>
                                <td><?php echo $success; ?></td>
                                <td><?php echo $error; ?></td>
                            </tr>
                            <?php updateScreen(); ?>
							
							<?php
							// create local admin entry
							$sql   = "INSERT INTO `users` (`id`, `username`, `password`, `level_id`, `email`, `lastlogindate`, `lastloginip`, `status`, `title`, `firstname`, `lastname`, `languageId`, `datecreated`, `createdip`, `lastPayment`, `paidExpiryDate`, `paymentTracker`, `passwordResetHash`, `identifier`, `apikey`, `storageLimitOverride`, `privateFileStatistics`, `uploadServerOverride`, `userGroupId`, `profile`, `isPublic`, `accountLockStatus`, `accountLockHash`) VALUES
	(null, 'admin', '0192023a7bbd73250516f069df18b500', 20, 'admin@yoursite.com', '2015-11-13 11:32:49', '192.168.1.1', 'active', '', '', '', NULL, '2010-05-04 00:00:00', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL, '42175a2aqd5b91bea5ab24c9630c4a20', NULL, NULL, 0, NULL, NULL, NULL, 1, 0, '');";
							$q     = $ysDBH->prepare($sql);
							$count = $q->execute();
							?>

                        </table>
                    </p>

                    <p>
                        <strong>Import finished.</strong> Now copy your Imagetize images on your server (in /photos) into the /files/photos/ folder within Reservo. Note that your admin login to Reservo will be updated to the one you set at the start of this process.
                    </p>
					<p>
                        <strong>Admin User.</strong> We have setup a new admin user for you. To access the admin account use the username "admin" &amp; "admin123" as the password. Please change this password via "manage users" on first login.
                    </p>
					<p class="important">
                        IMPORTANT: When you are finished, ensure you remove this file from your server.
                    </p>
                    <p style="padding-top: 4px;">
                        <form method="POST" action="migrate.php">
                            <input type="submit" value="Restart" name="submit" class="button"/>
                        </form>
                    </p>
                <?php endif; ?>
            </div>
            <div id="footer">
                Copyright &copy; <?php echo date('Y'); ?> <a href="https://reservo.co" target="_blank">Reservo.co</a>
            </div>
        </div>
    </body>
</html>

<?php

// local functions
function updateScreen()
{
    flush();
    ob_flush();
}

function get_file_extension($file_name)
{
    return substr(strrchr($file_name, '.'), 1);
}

function long2Ip32bit($ip)
{
    return long2ip((float) $ip);
}

function guess_mime_type($filename)
{
    $mime_types = array(
        'txt'  => 'text/plain',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'php'  => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'swf'  => 'application/x-shockwave-flash',
        'flv'  => 'video/x-flv',
        // images
        'png'  => 'image/png',
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'ico'  => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif'  => 'image/tiff',
        'svg'  => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip'  => 'application/zip',
        'rar'  => 'application/x-rar-compressed',
        'exe'  => 'application/x-msdownload',
        'msi'  => 'application/x-msdownload',
        'cab'  => 'application/vnd.ms-cab-compressed',
        // audio/video
        'mp3'  => 'audio/mpeg',
        'qt'   => 'video/quicktime',
        'mov'  => 'video/quicktime',
        // adobe
        'pdf'  => 'application/pdf',
        'psd'  => 'image/vnd.adobe.photoshop',
        'ai'   => 'application/postscript',
        'eps'  => 'application/postscript',
        'ps'   => 'application/postscript',
        // ms office
        'doc'  => 'application/msword',
        'rtf'  => 'application/rtf',
        'xls'  => 'application/vnd.ms-excel',
        'ppt'  => 'application/vnd.ms-powerpoint',
        // open office
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
        'avi'  => 'video/avi',
    );

	$exp = explode('.', $filename);
    $ext = strtolower(array_pop($exp));
    if (array_key_exists($ext, $mime_types))
    {
        return $mime_types[$ext];
    }
    else
    {
        return 'application/octet-stream';
    }
}
?>