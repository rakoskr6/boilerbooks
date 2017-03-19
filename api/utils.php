<?php

// A special exception subclass to be used by API endpoints; when thrown, causes
// the API vendor (this code) to return the HTTP error code given by $e->getCode()
// with JSON, where "error" will be set to $e->getMessage().
class HTTPException extends Exception {}

// A static container class for logging functions.
// Remember! `chmod 662`!
class log {
    protected function __construct() {}
    protected function __clone() {}

    // The internal sys_log() function is for any non-database logs to be recorded
    // along with a timestamp of when it occurred. Should be used sparingly.
    public static function sys($log) {
        file_put_contents('./sys.log', "[{date(\"Y-m-d h:i:sa\")}] $log\n", FILE_APPEND);
    }

    // The internal error_log() function is for any database errors to be recorded
    // along with a timestamp of when it occurred. It should return a UUID of the
    // error, which will then be sent to the administrator and the user as a ticket.
    //
    // Note: if you pass a PDOException, it will automatically log the message and SQL.
    public static function err($exc, $log) {
        if ($exc instanceof Exception) {
            $exc = $exc->getMessage();
        }

        $uuid = uniqid('sql_');
        error_log($exc . ' => ' . $log);
        file_put_contents('./errors.log', "[{date(\"Y-m-d h:i:sa\")}] [$uuid] $exc => $log\n", FILE_APPEND);
        return $uuid;
    }

    // The internal transact_log() function is for any database transactions to be recorded
    // along with a timestamp of when it occurred.
    public static function transact($log) {
        file_put_contents('./transactions.log', "[{date(\"Y-m-d h:i:sa\")}] $log\n", FILE_APPEND);
    }
}

// Filters...
class Validators {
    protected function __construct() {}
    protected function __clone() {}

    private static function username($x) {
        return  $x === 'me' ||
                (is_string($x) && in_array(strlen($x), range(3, 16)) &&
                preg_match('/^([a-z0-9]+(?:[._][a-z0-9]+)*)\$/i', $x));
    }
    private static function date($x) {
        return is_string($x) && (preg_match('/^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}$/', $x) == true);
    }
    private static function alpha($x) {
        return is_string($x) && ctype_alpha($x);
    }
    private static function numeric($x) {
        return is_numeric($x);
    }
    private static function alphanumeric($x) {
        return is_string($x) && ctype_alnum($x);
    }
    private static function empty($x) {
        return is_string($x) && strlen($x) === 0;
    }
    private static function words($x) {
        return is_string($x) && (preg_match("/^[A-Za-z]+[A-Za-z \\s]*\$/", $x) == true);
    }
    private static function phone($x) {
        return is_string($x) && (preg_match("/^[0-9]{10,11}\$/", $x) == true);
    }

    // Apply a set of validation functors to an array of vars. Each var must
    // match a validator and each validator MUST exist in this class as a
    // static func. If any of the filters fails to apply correctly to a var,
    // an exception is thrown.
    public static function apply($vars, $defs) {
        foreach ($defs as $param_name => $filter_name) {
            if (!array_key_exists($param_name, $vars))
                throw new HTTPException("parameter $param_name does not exist", 400);

            // If the `not_` prefix exists, then flip the condition.
            $cond = (0 === strpos($filter_name, 'not_')) ? true : false;
            if ($cond) $filter_name = preg_replace('/not_/', '', $filter_name);
            if ($filter_name !== 'apply' && !method_exists('Validators', $filter_name))
                throw new HTTPException("filter $filter_name does not exist", 400);

            if (call_user_func(['Validators', $filter_name], $vars[$param_name]) === $cond)
                throw new HTTPException("$param_name does not match filter type '$filter_name'", 400);
        }
        return true;
    }
}

// Dynamics...
class Dynamics {
    protected function __construct() {}
    protected function __clone() {}

    // Dynamically invokes a method and maps the associative array of arguments
    // onto the method's parameter names. If any non-optional arguments are
    // missing, the $missing callback is invoked with the parameter name.
    // If the $arguments array contains more arguments than method parameters exist,
    // these leftover arguments will be automatically discarded.
    //
    // Usage:
    // $p = ["username" => "abc", "nonexistentparam" => 2];
    // Dynamics::invoke("User::add", $p, function($name) {
    //     die('Missing or invalid parameter $name!');
    // }
    //
    public static function invoke($method, $arguments, callable $missing) {
        $values = [];
        $all = (new \ReflectionMethod($method))->getParameters();
        foreach ($all as $p) {
            $name = $p->getName();
            $exists = array_key_exists($name, $arguments);
            if (!$exists && !$p->isDefaultValueAvailable()) {
                $missing($name);
                $arguments[$name] = null;
            }
            $values[$p->getPosition()] = $exists ? $arguments[$name] : $p->getDefaultValue();
        }
        return $method(...$values);
    }

    // Unpacks a function's arguments from their positions into an associative
    // array; the inverse of dynamically invoking a function. This MUST be used
    // with the current method name and arguments passed in. If it is not used
    // in this exact way, or a method's argument does not exist, it will fail.
    //
    // Usage: $res = Dynamics::extract(__METHOD__, func_get_args());
    //
    // TODO: Make `__METHOD__, func_get_args()` go away.
    public static function extract($method, $arguments) {
        $values = [];
        $all = (new \ReflectionMethod($method))->getParameters();
        foreach ($all as $p) {
            $values[$p->getName()] = $arguments[$p->getPosition()];
        }
        return $values;
    }
}

// Modified version of Flight::json(...) from Flight\Engine to allow setting
// CORS headers for client consumption. Use this function ALWAYS!
Flight::map('json', function($data, $code = 200, $encode = true, $charset = 'utf-8', $option = 0) {
    $json = ($encode) ? json_encode($data, $option) : $data;
    $origin_header = isset(getallheaders()['Origin']) ? getallheaders()['Origin'] : '*';
    Flight::response()
        ->status($code)
        ->header('Content-Type', 'application/json; charset='.$charset)
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Allow-Origin', $origin_header)
        ->header('Access-Control-Allow-Headers', "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With")
        ->write($json)
        ->send();
});
Flight::route('OPTIONS *', function() {
    return Flight::json('');
});

// Using this is very convenient; we always receive an exception result.
// If an HTTPException is thrown, we know to return that to the user normally.
// Otherwise, encode the error in a log and notify the administrator about
// the fatal error, and return the reference number to the user invoking it.
//
// TODO: Return a log reference number on non-HTTPException thrown.
Flight::map('error', function($e) {
    try {
        if ($e instanceof HTTPException) {
            return Flight::json(["error" => $e->getMessage()], $e->getCode());
        } else {
            error_log($e->getMessage() . '\n' . $e->getTraceAsString());
            return Flight::json(["fatal" => [
                "message" => $e->getMessage(),
                "trace" => $e->getTraceAsString()
            ]], 500);
        }
    } catch(Throwable $t) {
        http_response_code(500);
        die("unhandled internal fatal exception: {$t->getMessage()}\n{$t->getTraceAsString()}");
    }
});

//
Flight::map('notFound', function() {
    throw new HTTPException("api endpoint does not exist", 404);
});

// Validate the token before invoking any API endpoints if it's requested.
// This is secretly a function of Authenticate::check_token() but it needs to be here.
function _check_token() {

    // Look for token in cookies first; if not in cookies, check in HTTP
    // headers, following the `Authorization: Bearer <token>` schema.
    // If we can't find a token, we error out.
    if (isset(Flight::request()->cookies[TOKEN_COOKIE])) {
        $encoded_token = Flight::request()->cookies[TOKEN_COOKIE];
    } else if (isset(getallheaders()['Authorization'])
             && preg_match('/Bearer\s(\S+)/', getallheaders()["Authorization"], $matches)) {
        $encoded_token = $matches[1];
    }
    if (isset($encoded_token) === false) {
        throw new HTTPException("no token supplied", 401);
    }

    // Decode the token and ensure it hasn't expired or that the IP address
    // it was issued to is the one sending it right now. If these security
    // checks fail, error out.
    try {
        $token = JWT::decode($encoded_token, TOKEN_SECRET, ['HS512']);
    } catch(ExpiredException $e) {
        throw new HTTPException("token has expired", 401);
    } catch(Exception $e) {
        throw new HTTPException("error decoding token", 400);
    }
    if (Flight::request()->ip !== $token->data->ip) {
        throw new HTTPException("ip does not match token", 400);
    }

    // Obtain the user and verify the revoke_counter to match the token's.
    // If the revoke_counter is not exactly equal, the token is revoked.
    try {
        $user = Flight::db()->get("Users", ["username", "revoke_counter"],
                                ["username" => $token->data->username]);
        if (!isset($user)) {
            throw new HTTPException("user not found", 404);
        }
        if ($user['revoke_counter'] > $token->data->revoke_counter) {
            throw new HTTPException("token has been revoked", 404);
        } else if ($user['revoke_counter'] < $token->data->revoke_counter) {
            throw new HTTPException("token is from the future", 404);
        }
    } catch(PDOException $e) {
        throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
    }

    // Phew, we made it this far, we've passed the gates of Hell!
    return $token;
}
Flight::map("check_token", function() {
    Flight::set('user', _check_token()->data->username);
});

// Dynamically routes an HTTP endpoint to a global or static function,
// by using the Dynamics::invoke() method above and matching parameters.
Flight::map('dynamic_route', function($match, $to, $require_auth = true) {
    Flight::route($match, function(...$args) use (&$to, &$require_auth) {
        if ($require_auth) Flight::check_token();
        $json_params = json_decode(Flight::request()->getBody(), true) ?: [];
        $url_params = array_pop($args)->params; // prioritized in merge()
        $all_params = array_merge($json_params, $url_params);

        $res = Dynamics::invoke($to, $all_params, function($name) {
            throw new HTTPException("missing paramter $name", 400);
        });
        return Flight::json(["result" => $res]);
    }, true);
});

// If the query contains any fields specified, only return those.
// The URL should contain `?fields=<field>,<field>,...` to match.
// If any aggregates (i.e. functions applied to fields) are used,
// They are returned as well after being validated.
//
// Return value: ["aggregates" => [...], "fields" => [...]]
Flight::map('fields', function($default_fields, $operators = ['min', 'max', 'avg', 'sum', 'count']) {
    $fields = explode(',', Flight::request()->query['fields'] ?: '');
    $fields = array_filter($fields, function($v) { return $v !== ''; });

    if (count($fields) === 0) {
        return ["aggregates" => [],
                "fields" => $default_fields];
    } else {
        $aggregates = [];
        $valid_fields = [];
        foreach ($fields as $field) {

            // Support for prefixed operators like min:x, max:y, and so on.
            $parts = explode(':', $field);
            if (count($parts) === 1) {
                $field = $parts[0];
                array_push($valid_fields, $field);
            } else {
                $op = $parts[0];
                $field = $parts[1];
                array_push($aggregates, ["op" => $op, "field" => $field]);
            }

            if(!in_array($field, $default_fields)) {
                throw new HTTPException("invalid field '$field'", 400);
            }
        }
        return ["aggregates" => $aggregates,
                "fields" => $valid_fields];
    }
});
