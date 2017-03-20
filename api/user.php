<?php
require_once 'rights.php';
require_once 'resource.php';
require_once 'realtime.php';

class User {
    protected function __construct() {}
    protected function __clone() {}

    public static function add($username, $password, $name, $email, $address) {

        $user = Dynamics::extract(__METHOD__, func_get_args());
        Validators::apply($user, [
            'username' => 'username', 'password' => 'not_empty',
            'name' => 'alpha', 'email' => 'not_empty', 'address' => 'not_empty'
        ]);
        $user["password"] = password_hash($password, PASSWORD_DEFAULT);

        // Execute the actual SQL query after confirming its formedness.
        try {
            Flight::db()->insert("Users", $user);
            log::transact(Flight::db()->last_query());
            Realtime::record(__CLASS__, Realtime::create, $user);
            return $user;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    // TODO: Perhaps deleting the cert is a bad idea: existing reimbursements
    // and pending ones will rely on the cert at the Treasurer side of things.
    public static function remove($username) {
        $user = Dynamics::extract(__METHOD__, func_get_args());
        Validators::apply($user, ['username' => 'username']);

        // Make sure we have rights to delete users.
        if(Flight::get('user') != $username &&
           !Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to delete users", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $result = Flight::db()->delete("Users", ["username" => $username]);

            // Make sure 1 row was acted on, otherwise the user did not exist
            if ($result == 1) {
                log::transact(Flight::db()->last_query());
                Realtime::record(__CLASS__, Realtime::delete, $username);
                return $username;
            } else {
                throw new HTTPException("user not found", 404);
            }
        } catch(PDOException $e) {
            log::transact(Flight::db()->last_query());
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function update($username, $password = null, $name = null,
                                  $email = null, $address = null, $cert = null) {
        $user = Dynamics::extract(__METHOD__, func_get_args());
        Validators::apply($user, [
            'username' => 'username', 'password' => 'not_empty',
            'name' => 'alpha', 'email' => 'not_empty', 'address' => 'not_empty'
        ]);

        // Make sure we have rights to update the user.
        if (Flight::get('user') != $username &&
            !Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to update other users", 401);
        }

        // Make sure any cert id is a valid Resource.
        if($cert !== null && !Resource::exists($cert)) {
            throw new HTTPException("certificate was not a valid resource", 400);
        }

        // Scrub the parameters into an updates array.
        $updates = array_filter($user, function($v, $k) { return !is_null($v); }, ARRAY_FILTER_USE_BOTH);
        unset($updates["username"]);
        if (count($updates) == 0) {
            throw new HTTPException("no updates to commit", 400);
        }

        // If the password is being modified (even to the same thing), you *must* increment the
        // revoke counter to invalidate old tokens dispersed for authentication.
        if (isset($updates["password"])) {
            $user["password"] = password_hash($password, PASSWORD_DEFAULT);
            $updates["revoke_counter[+]"] = "1";
        }

        // Execute the actual SQL query after confirming its formedness.
        try {

            // Make sure the user exists. This needs an extra query, because update will
            // return 0 when no changes are made, but the user still exists.
            if (Flight::db()->has("Users", ["username" => $username]) === false) {
                throw new HTTPException("user not found", 404);
            }

            $result = Flight::db()->update("Users", $updates, ["username" => $username]);
            log::transact(Flight::db()->last_query());
            unset($updates["revoke_counter[+]"]);
            Realtime::record(__CLASS__, Realtime::update, $updates);
            return $updates;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function view($username) {
        $user = Dynamics::extract(__METHOD__, func_get_args());
        Validators::apply($user, ['username' => 'username']);

        // Short-circuit a special username "me" to mean the authed user.
        if($username === 'me') {
            $username = Flight::get('user');
        }

        // Make sure we have rights to view the username given (or all users).
        if (Flight::get('user') != $username &&
            !Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
            throw new HTTPException("insufficient privileges to view other users", 401);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $queried = Flight::fields(["username", "name", "email", "address", "cert"]);
            $result = Flight::db()->select("Users", $queried['fields'], ["username" => $username]);
            if (count($result) == 0) {
                throw new HTTPException("no such user '$username'", 404);
            }

            return $result[0];
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function search() {

        // Execute the actual SQL query after confirming its formedness.
        try {
            $columns = ["username", "name", "email", "address", "cert"];
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
                        ["Users", $agg['field'], $selector]
                    );
                    $agg_res[$agg['op'].':'.$agg['field']] = $meta;
                }
                return $agg_res;
            }

            if(!Rights::check_rights(Flight::get('user'), "*", "*", 0, -1)[0]["result"]) {
                throw new HTTPException("insufficient privileges to view all users", 401);
            }

            $result = Flight::db()->select("Users", $queried['fields'], $selector);
            return $result;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }
}

/**
 * @SWG\Get(path="/users/{username}",
 *   tags={"users"},
 *   summary="",
 *   description="",
 *   operationId="User::view",
 *   produces={"application/json"},
 *   @SWG\Parameter(
 *     name="username",
 *     in="path",
 *     description="",
 *     required=true,
 *     type="string"
 *   ),
 *   @SWG\Response(
 *     response=200,
 *     description="successful operation",
 *     @SWG\Schema(
 *       type="object",
 *       additionalProperties={
 *         "type":"integer",
 *         "format":"int32"
 *       }
 *     )
 *   ),
 *   @SWG\Response(
 *     response=400,
 *     description="Invalid username supplied"
 *   ),
 *   security={{
 *     "api_key":{}
 *   }}
 * )
 */
Flight::dynamic_route('GET /user/@username', 'User::view');

/**
 * @SWG\Post(path="/users/{username}",
 *   tags={"users"},
 *   summary="",
 *   description="",
 *   operationId="User::add",
 *   produces={"application/json"},
 *   @SWG\Parameter(
 *     name="username",
 *     in="path",
 *     description="",
 *     required=true,
 *     type="string"
 *   ),
 *   @SWG\Response(
 *     response=200,
 *     description="successful operation",
 *     @SWG\Schema(
 *       type="object",
 *       additionalProperties={
 *         "type":"integer",
 *         "format":"int32"
 *       }
 *     )
 *   ),
 *   @SWG\Response(
 *     response=400,
 *     description="Invalid username supplied"
 *   ),
 *   security={{
 *     "api_key":{}
 *   }}
 * )
 */
Flight::dynamic_route('POST /user/@username', 'User::add', false);

/**
 * @SWG\Patch(path="/users/{username}",
 *   tags={"users"},
 *   summary="",
 *   description="",
 *   operationId="User::update",
 *   produces={"application/json"},
 *   @SWG\Parameter(
 *     name="username",
 *     in="path",
 *     description="",
 *     required=true,
 *     type="string"
 *   ),
 *   @SWG\Response(
 *     response=200,
 *     description="successful operation",
 *     @SWG\Schema(
 *       type="object",
 *       additionalProperties={
 *         "type":"integer",
 *         "format":"int32"
 *       }
 *     )
 *   ),
 *   @SWG\Response(
 *     response=400,
 *     description="Invalid username supplied"
 *   ),
 *   security={{
 *     "api_key":{}
 *   }}
 * )
 */
Flight::dynamic_route('PATCH /user/@username', 'User::update');

/**
 * @SWG\Delete(path="/users/{username}",
 *   tags={"users"},
 *   summary="",
 *   description="",
 *   operationId="User::remove",
 *   produces={"application/json"},
 *   @SWG\Parameter(
 *     name="username",
 *     in="path",
 *     description="",
 *     required=true,
 *     type="string"
 *   ),
 *   @SWG\Response(
 *     response=200,
 *     description="successful operation",
 *     @SWG\Schema(
 *       type="object",
 *       additionalProperties={
 *         "type":"integer",
 *         "format":"int32"
 *       }
 *     )
 *   ),
 *   @SWG\Response(
 *     response=400,
 *     description="Invalid username supplied"
 *   ),
 *   security={{
 *     "api_key":{}
 *   }}
 * )
 */
Flight::dynamic_route('DELETE /user/@username', 'User::remove');

/**
 * @SWG\Get(path="/users",
 *   tags={"users"},
 *   summary="",
 *   description="",
 *   operationId="User::search",
 *   produces={"application/json"},
 *   parameters={},
 *   @SWG\Response(
 *     response=200,
 *     description="successful operation",
 *     @SWG\Schema(
 *       type="object",
 *       additionalProperties={
 *         "type":"integer",
 *         "format":"int32"
 *       }
 *     )
 *   ),
 *   @SWG\Response(
 *     response=400,
 *     description="Invalid username supplied"
 *   ),
 *   security={{
 *     "api_key":{}
 *   }}
 * )
 */
Flight::dynamic_route('GET /user', 'User::search');
