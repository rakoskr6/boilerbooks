<?php
    require_once 'rights.php';

    class Income {
        protected function __construct() {}
        protected function __clone() {}

        public static function add($organization, $year, $source, $type, $amount,
                                   $item, $status, $comments) {
            $income = Dynamics::extract(__METHOD__, func_get_args());
            $income["username"] = Flight::get('user');

            // Ensure proper privileges to create an income.
            if(!Rights::check_rights(Flight::get('user'), $organization, "*", $year, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to add an income", 401);
            }

            // Execute the actual SQL query after confirming its formedness.
            try {
                Flight::db()->insert("Income", $income);
                log::transact(Flight::db()->last_query());
                return $income;
            } catch(PDOException $e) {
                throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
            }
        }

        public static function update($incomeid, $year = null, $source = null,
                                      $type = null, $item = null, $status = null, $comments = null) {
            $income = Dynamics::extract(__METHOD__, func_get_args());

            // FIXME: Use $incomeid to get the params and check against those first.
            // Ensure proper privileges to update an income.
            if(!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to update an income", 401);
            }

            // Scrub the parameters into an updates array.
            $updates = array_filter($income, function($v, $k) { return !is_null($v); }, ARRAY_FILTER_USE_BOTH);
            unset($updates["incomeid"]);
            if (count($updates) == 0) {
                throw new HTTPException("no updates to commit", 400);
            }

            // Execute the actual SQL query after confirming its formedness.
            try {
                $result = Flight::db()->update("Income", $updates, ["incomeid" => $incomeid]);

                // Make sure 1 row was acted on, otherwise the income did not exist
                if ($result == 1) {
                    log::transact(Flight::db()->last_query());
                    return $updates;
                } else {
                    throw new HTTPException("no such income available", 404);
                }
            } catch(PDOException $e) {
                throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
            }
        }

        public static function view($incomeid) {

            // FIXME: Use $incomeid to get the params and check against those first.
            // Make sure we have rights to view the income.
            if (!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to view an income", 401);
            }

            // Execute the actual SQL query after confirming its formedness.
            try {
                $result = Flight::db()->select("Income", "*", ["incomeid" => $incomeid]);

                return $result;
            } catch(PDOException $e) {
                throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
            }
        }

        public static function search() {

            // Make sure we have rights to view the income.
            if (!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to view an income", 401);
            }

            // Execute the actual SQL query after confirming its formedness.
            try {
                $result = Flight::db()->select("Income", "*");

                return $result;
            } catch(PDOException $e) {
                throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
            }
        }
    }

    Flight::dynamic_route('GET /income/@incomeid', 'Income::view');
    Flight::dynamic_route('POST /income/@incomeid', 'Income::add');
    Flight::dynamic_route('PATCH /income/@incomeid', 'Income::update');
    Flight::dynamic_route('GET /incomes', 'Income::search');
?>
