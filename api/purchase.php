<?php
require_once 'rights.php';

class Purchase {
    protected function __construct() {}
    protected function __clone() {}

    public static function add($username, $organization, $budget, $item, $reason,
                               $vendor, $cost, $comments) {
        $purchase = Dynamics::extract(__METHOD__, func_get_args());

        // Make sure we have rights to update the purchase.
        if (Flight::get('user') != $username &&
            !Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to add other users' purchases", 401);
        }
        $income["username"] = Flight::get('user');

        // Execute the actual SQL query after confirming its formedness.
        try {
            Flight::db()->insert("Purchases", $purchase);
            log::transact(Flight::db()->last_query());

            // Get the last entered row's ID and return that.
            $pid = Flight::db()->query("SELECT @@IDENTITY AS PID")->fetch();
            $purchase['purchaseid'] = $pid["PID"];
            Realtime::record(__CLASS__, Realtime::create, $purchase);
            return $purchase;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    // Get the info of the people to approve the purchase and return it.
    /*
    SELECT CONCAT(U.first, ' ', U.last) name, U.email, a.committee
    FROM approval a
    INNER JOIN Users U
        ON U.username = a.username
    WHERE a.committee = (
        SELECT P.committee
        FROM Purchases P
        WHERE P.purchaseID = '$currentitemid'
    )
        AND a.ammount >= (
            SELECT P.cost
            FROM Purchases P
            WHERE P.purchaseID = '$currentitemid'
        )
        AND a.category = (
            SELECT P.category
            FROM Purchases P
            WHERE P.purchaseID = '$currentitemid'
        )
        OR a.category = '*'
    );
    */

    public static function update($purchaseid, $username, $approvedby = null, $item = null,
                                  $reason = null, $vendor = null, $cost = null, $comments = null,
                                  $status = null, $fundsource = null, $purchasedate = null,
                                  $receipt = null) {
        $purchase = Dynamics::extract(__METHOD__, func_get_args());

        // Make sure we have rights to update the purchase.
        if (Flight::get('user') != $username &&
            !Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to edit other users' purchases", 401);
        }

        // Scrub the parameters into an updates array.
        $updates = array_filter($purchase, function($v, $k) { return !is_null($v); }, ARRAY_FILTER_USE_BOTH);
        unset($updates["purchaseid"]);
        unset($updates["username"]);
        if (count($updates) == 0) {
            throw new HTTPException("no updates to commit", 400);
        }
        $updates["#modify"] = "NOW()";

        // Execute the actual SQL query after confirming its formedness.
        try {
            $result = Flight::db()->update("Purchases", $updates, ["AND" =>
                                           ["purchaseid" => $purchaseid, "username" => $username]
                                           ]);

            // Make sure 1 row was acted on, otherwise the income did not exist
            if ($result == 1) {
                log::transact(Flight::db()->last_query());
                Realtime::record(__CLASS__, Realtime::update, $purchase);
                return $purchase;
            } else {
                throw new HTTPException("no such purchase", 404);
            }
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function view($purchaseid) {

        // Make sure we have rights to view the purchase.
        if (!Flight::get('user')) {
            throw new HTTPException("insufficient privileges to view a purchase", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $queried = Flight::fields(["purchaseid", "username", "approvedby",
                                        "item", "purchasereason", "vendor", "cost",
                                        "comments", "status", "fundsource",
                                        "purchasedate", "receipt", "organization",
                                        "budget", "year"]);//, "modify"]);
            $result = Flight::db()->select("Purchases", $queried['fields'], ["purchaseid" => $purchaseid]);

            return $result;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function search($offset = 0, $limit = 250) {

        // Execute the actual SQL query after confirming its formedness.
        try {
            $columns = ["purchaseid", "username", "approvedby",
                        "item", "purchasereason", "vendor", "cost",
                        "comments", "status", "fundsource",
                        "purchasedate", "receipt", "organization",
                        "budget", "year"];//, "modify"];
            $queried = Flight::fields($columns);
            $selector = Flight::filters($columns);

            // Short circuit if we find any aggregates!
            if (count($queried['aggregates']) > 0) {
                if (!Flight::get('user')) {
                    throw new HTTPException("insufficient privileges to view aggregate data", 401);
                }

                $agg_res = [];
                foreach ($queried['aggregates'] as $agg) {
                    $meta = call_user_func_array(
                        [Flight::db(), $agg['op']],
                        ["Purchases", $agg['field'], $selector]
                    );
                    $agg_res[$agg['op'].':'.$agg['field']] = $meta;
                }
                return $agg_res;
            }

            // Make sure we have rights to view the purchase.
            if (!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to view all purchases", 401);
            }

            $result = Flight::db()->select("Purchases", $queried['fields'], $selector);
            return $result;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }
}

Flight::dynamic_route('GET /purchase/@purchaseid', 'Purchase::view');
Flight::dynamic_route('POST /purchase/@purchaseid', 'Purchase::add');
Flight::dynamic_route('PATCH /purchase/@purchaseid', 'Purchase::update');
Flight::dynamic_route('GET /purchase', 'Purchase::search');
