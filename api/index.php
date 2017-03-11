<?php
    require 'lib/meedo.php';
    require 'lib/jwt.php';
    require 'lib/flight/Flight.php';

    /**
     * @SWG\Info(title="BoilerBooks", version="2.0.0")
     *
     * @SWG\Tag(
     *   name="user",
     *   description="User",
     *   @SWG\ExternalDocumentation(
     *     description="Find out more",
     *     url="http://swagger.io"
     *   )
     * )
     */

    // By versioning the API, we ensure that clients are aware of the specific
    // calls and will be explicit in their intent.  By default use newest API
    // (this) if no version is specified, but return an error on older versions
    // that aren't supported.
    define("API_VERSION", 2);
    if (isset(getallheaders()['Version']) && getallheaders()["Version"] !== API_VERSION) {
        return http_return(400, ["error" => "incorrect API version number"]);
    }

    // The `server_info.php` file contains database and token secrets and will
    // not be versioned with the API. Establishing connection to the database
    // occurs first, before any API endpoints are established.
    require_once '../server_info.php';
    Flight::set('database', new database([
        'database_type' => 'mysql',
        'database_name' => DB_NAME,
        'server' => DB_HOST,
        'username' => DB_USER,
        'password' => DB_PASS,
        'charset' => 'utf8',
        'option' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
    ]));
    Flight::map("db", function() {
        return Flight::get('database');
    });

    // Load our dynamics and utilities and set up 404, 500 response.
    require_once 'utils.php';

    // Establish API endpoints and start!
    require_once 'rights.php';
    require_once 'authenticate.php';
    require_once 'budget.php';
    require_once 'income.php';
    require_once 'organization.php';
    require_once 'purchase.php';
    require_once 'resource.php';
    require_once 'user.php';
    Flight::start();
?>
