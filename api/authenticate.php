<?php

class Authenticate {
    protected function __construct() {}
    protected function __clone() {}

    public static function issue_token($username, $ip, $revoke_counter) {
        $issuedAt = time();
        $expiry = $issuedAt + (60*60*24*3); // 3 day expiry
        $jwt_data = [
            'iat'  => $issuedAt,
            'nbf'  => $issuedAt,
            'exp'  => $expiry,
            "data" => [
                "username" => $username,
                "ip" => Flight::request()->ip,
                "revoke_counter" => $revoke_counter
            ]
        ];

        // Encode a token and return it, but also set it as a cookie.
        $token = JWT::encode($jwt_data, TOKEN_SECRET, 'HS512');
        setcookie(TOKEN_COOKIE, $token, $expiry, "/", SERVER_HOST);

        return $token;
    }

    // This function is technically part of this class...
    public static function check_token() {
        return _check_token();
    }

    public static function login($username, $password) {
        // Execute the actual SQL query after confirming its formedness.
        try {
            $result = Flight::db()->select("Users", ["username", "password", "revoke_counter"], ["username" => $username]);

            if (count($result) !== 1) {
                throw new HTTPException("user does not exist", 404);
            }
            if (!password_verify($password, $result[0]["password"])) {
                throw new HTTPException("incorrect password", 401);
            }

            $token = Authenticate::issue_token($username, Flight::request()->ip, $result[0]['revoke_counter']);

            return $token;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function revoke() {
        $username = Flight::get('user');
        try {
            $result = Flight::db()->update("Users", ["revoke_counter[+]" => 1], ["username" => $username]);
            if (count($result) !== 1) {
                throw new HTTPException("user does not exist", 404);
            } else {
                log::transact(Flight::db()->last_query());
            }

            //Remove the data in the cookie and expire it
            setcookie(TOKEN_COOKIE, "", time()-3600, "/", SERVER_HOST);

            return 'success';
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function check() {
        return true;
    }

    public static function refresh() {
        $username = Flight::get('user');
        try {
            $result = Flight::db()->select("Users", ["revoke_counter"], ["username" => $username]);

            if (count($result) !== 1) {
                throw new HTTPException("user does not exist", 404);
            }

            $token = Authenticate::issue_token($username, Flight::request()->ip, $result[0]['revoke_counter']);

            return $token;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }
}

Flight::dynamic_route('GET /authenticate', 'Authenticate::check');
Flight::dynamic_route('POST /authenticate', 'Authenticate::login', false);
Flight::dynamic_route('PATCH /authenticate', 'Authenticate::refresh');
Flight::dynamic_route('DELETE /authenticate', 'Authenticate::revoke');
