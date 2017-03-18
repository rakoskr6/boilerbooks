<?php
//require_once 'rights.php';
// FIXME: Need a way to get all authorities higher than the currently authed user.

class Rights {
    protected function __construct() {}
    protected function __clone() {}

    // Note: if `organization = '*' && budget = '*' && amount = -1 && year = 0`, this
    // is considered "root" privilege.
    public static function grant($username, $organization, $budget, $year, $amount) {
        $right = Dynamics::extract(__METHOD__, func_get_args());
        $right["granter"] = Flight::get('user');

        // Enforce the proper schema for a root privileged right.
        if($organization === "*" && ($budget !== "*" || $year != 0 || $amount != -1)) {
            throw new HTTPException("improperly formed root privileged right", 400);
        }

        // Ensure proper privilege cascade to grant new rights.
        if(!Rights::check_rights(Flight::get('user'), $organization, $budget, $year, $amount)[0]["result"]) {
            throw new HTTPException("insufficient privileges to grant privileges", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            Flight::db()->insert("Rights", $right);
            log::transact(Flight::db()->last_query());
            return $username;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function revoke($username, $organization, $budget, $year) {
        $right = Dynamics::extract(__METHOD__, func_get_args());

        // Ensure proper privileges to revoke rights.
        // FIXME: Confirm that -1 amount enforces [revoker > revokee].
        if(!Rights::check_rights(Flight::get('user'), $organization, $budget, $year, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to revoke rights", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $result = Flight::db()->delete("Rights", ["AND" => $right]);

            // Make sure 1 row was acted on, otherwise the income did not exist
            if ($result === 1) {
                log::transact(Flight::db()->last_query());
                return $updates;
            } else {
                throw new HTTPException("no such right existed", 404);
            }
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function check_rights($username, $organization, $budget, $year, $amount) {

        // Make sure we have rights to view the rights.
        if (!Flight::get('user')) {
            return [["error" => "insufficient privileges to check rights"], 401];
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $result = Flight::db()->select("Rights", "*", ["username" => $username]);
            if (count($result) < 1) {
                return [["error" => "no rights for that user"], 404];
            }

            // Check all rights for the user in the given organization & category.
            // Note: amount has to be <= the given amount for the right to be validated.
            // Note: this was intentionally not done as an SQL WHERE clause.
            foreach ($result as $r) {
                if (($r["organization"] === "*" || $r["organization"] === $organization) &&
                    ($r["budget"] === "*" || $r["budget"] === $budget) &&
                    ($r["year"] == 0 || $r["year"] == $year) &&
                    ($r["amount"] == -1 || $r["amount"] >= $amount)) {
                    return [["result" => true], 200];
                }
            }

            return [["result" => false], 200];
        } catch(PDOException $e) {
            return [["error" => log::err($e, Flight::db()->last_query())], 500];
        }
    }

    // Public-facing version of the _check() function.
    public static function check($username, $organization, $budget, $year, $amount) {
        list($json, $code) = Rights::check_rights($username, $organization, $budget, $year, $amount);
        return Flight::json($json, $code);
    }

    public static function view($username) {

        // Make sure we have rights to view the rights given (or all users).
        if (Flight::get('user') != $username &&
            !Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to view other users' rights", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $selector = Flight::fields(["username", "granter", "organization",
                                        "budget", "year", "amount"]);
            $result = Flight::db()->select("Rights", $selector, ["username" => $username]);
            if (count($result) < 1) {
                throw new HTTPException("no results", 404);
            }

            return $result;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function search() {

        // Make sure we have rights to view the rights given (or all users).
        if (!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to view all rights", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $selector = Flight::fields(["username", "granter", "organization",
                                        "budget", "year", "amount"]);
            $result = Flight::db()->select("Rights", $selector);
            if (count($result) < 1) {
                throw new HTTPException("no results", 404);
            }

            return $result;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }
}

// FIXME: check()?
Flight::dynamic_route('GET /check/@username', 'Rights::check');
Flight::dynamic_route('GET /rights/@username', 'Rights::view');
Flight::dynamic_route('POST /rights/@username', 'Rights::grant');
Flight::dynamic_route('DELETE /rights/@username', 'Rights::revoke');
Flight::dynamic_route('GET /rights', 'Rights::search');
