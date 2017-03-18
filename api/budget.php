<?php
    require_once 'rights.php';

    class Budget {
        protected function __construct() {}
        protected function __clone() {}

        public static function add($organization, $name, $year, $amount) {
            $budget = Dynamics::extract(__METHOD__, func_get_args());

            // Ensure proper privileges to create a budget.
            if(!Rights::check_rights(Flight::get('user'), $organization, "*", $year, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to add a budget item", 401);
            }

            // Execute the actual SQL query after confirming its formedness.
            try {
                Flight::db()->insert("Budgets", $budget);
                log::transact(Flight::db()->last_query());
                return $budget;
            } catch(PDOException $e) {
                throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
            }
        }

        public static function remove($organization, $name, $year) {
            $budget = Dynamics::extract(__METHOD__, func_get_args());

            // Ensure proper privileges to delete a budget.
            if(!Rights::check_rights(Flight::get('user'), $organization, "*", $year, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to delete a budget item", 401);
            }

            // Execute the actual SQL query after confirming its formedness.
            try {
                $result = Flight::db()->delete("Budgets", ["AND" => $budget]);

                // Make sure 1 row was acted on, otherwise the user did not exist
                if ($result == 1) {
                    log::transact(Flight::db()->last_query());
                    return $budget;
                } else {
                    throw new HTTPException("no such budget item", 404);
                }
            } catch(PDOException $e) {
                throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
            }
        }

        public static function update($organization, $name, $year, $amount) {
            $budget = Dynamics::extract(__METHOD__, func_get_args());
            unset($budget["amount"]);

            // Ensure proper privileges to update a budget.
            if(!Rights::check_rights(Flight::get('user'), $organization, "*", $year, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to update a budget item", 401);
            }

            // Execute the actual SQL query after confirming its formedness.
            try {
                $result = Flight::db()->update("Budgets", ["amount" => $amount], ["AND" => $budget]);

                // Make sure 1 row was acted on, otherwise the budget did not exist.
                if ($result == 1) {
                    log::transact(Flight::db()->last_query());
                    return $budget;
                } else {
                    throw new HTTPException("no such budget item", 404);
                }
            } catch(PDOException $e) {
                throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
            }
        }

        public static function search() {

            /*
            // Build the selector so we return the right scope of budget items.
            $selector = [];
            if (isset($organization)) {
                $selector["organization"] = $organization;
            }
            if (isset($name)) {
                $selector["name"] = $name;
            }
            if (isset($year)) {
                $selector["year"] = $year;
            }
            if (count($selector) > 1) {
                $selector = ["AND" => $selector];
            }
            */

            // Ensure proper privileges to view all budgets.
            if(!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to view all budgets", 401);
            }

            // Execute the actual SQL query after confirming its formedness.
            try {
                $selector = Flight::fields(["organization", "name", "year", "amount"]);
                $result = Flight::db()->select("Budgets", $selector);

                return $result;
            } catch(PDOException $e) {
                throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
            }
        }
    }

    Flight::dynamic_route('POST /budget/@name', 'Budget::add');
    Flight::dynamic_route('PATCH /budget/@name', 'Budget::update');
    Flight::dynamic_route('DELETE /budget/@name', 'Budget::remove');
    Flight::dynamic_route('GET /budget', 'Budget::search');
?>
