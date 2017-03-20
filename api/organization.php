<?php
require_once 'rights.php';

class Organization {
    protected function __construct() {}
    protected function __clone() {}

    public static function add($name, $parent = null) {
        $org = Dynamics::extract(__METHOD__, func_get_args());

        // Ensure proper privileges to create a(n) (sub-)organization.
        if(!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to create organizations", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            Flight::db()->insert("Organizations", ["name" => $name, "parent" => $parent]);
            log::transact(Flight::db()->last_query());
            Realtime::record(__CLASS__, Realtime::create, $org);
            return $org;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function remove($name) {

        // Ensure proper privileges to create a(n) (sub-)organization.
        if(!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to delete organizations", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $result = Flight::db()->delete("Organizations", ["name" => $name]);

            // Make sure 1 row was acted on, otherwise the user did not exist
            if ($result == 1) {
                log::transact(Flight::db()->last_query());
                Realtime::record(__CLASS__, Realtime::delete, $name);
                return $name;
            } else {
                throw new HTTPException("no such organization", 404);
            }
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function search() {

        // Make sure we have rights to view the organizations.
        if (!Flight::get('user')) {
            throw new HTTPException("insufficient privileges to view organizations", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $columns = ["name", "parent"];
            $queried = Flight::fields($columns);
            $selector = Flight::filters($columns);

            // Short circuit if we find any aggregates!
            if (count($queried['aggregates']) > 0) {
                $agg_res = [];
                foreach ($queried['aggregates'] as $agg) {
                    $meta = call_user_func_array(
                        [Flight::db(), $agg['op']],
                        ["Organizations", $agg['field'], $selector]
                    );
                    $agg_res[$agg['op'].':'.$agg['field']] = $meta;
                }
                return $agg_res;
            }

            $result = Flight::db()->select("Organizations", $queried['fields'], $selector);
            return $result;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }
}

Flight::dynamic_route('POST /organization/@name', 'Organization::add');
Flight::dynamic_route('DELETE /organization/@name', 'Organization::remove');
Flight::dynamic_route('GET /organization', 'Organization::search');
