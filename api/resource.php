<?php

class Resource {

    public static function upload() {
        if (!isset($_FILES['resource'])) {
            throw new HTTPException("no resource present", 400);
        }

        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        // Check $_FILES['resource']['error'] value.
        if (!isset($_FILES['resource']['error']) || is_array($_FILES['resource']['error'])) {
            throw new HTTPException("invalid parameters", 400);
        }
        switch ($_FILES['resource']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new HTTPException("no resource present", 400);
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new HTTPException("resource exceeded max file size", 400);
            default:
                throw new HTTPException("unknown resource error occurred", 500);
        }

        // Extract the relevant variables from $_FILES.
        $file_size = $_FILES['resource']['size'];
        $file_tmp = $_FILES['resource']['tmp_name'];
        $file_type = $_FILES['resource']['type'];
        $file_name = $_FILES['resource']['name'];
        $_exploded = explode('.', $file_name);
        $file_ext = strtolower(end($_exploded));

        // Prevent uploads larger than 5MB.
        $MAX_SIZE = 5 * 1024 * 1024;
        if ($file_size > $MAX_SIZE) {
            throw new HTTPException("resource exceeded max file size", 400);
        }

        // Extract the MIME type of the file.
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fmime = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        // Prevent uploads that are not PDF files.
        // FIXME: Prevent just checking the file extension...
        $TYPES = ['application\/pdf', 'application\/jpeg', 'application\/png'];
        if (in_array($fmime, $TYPES)) {
            throw new HTTPException("resource is not PDF", 400);
        }

        // Execute the actual SQL query after confirming its formedness.
        try {
            $_user = Flight::get('user') ?: 'test';
            Flight::db()->query("INSERT INTO Resources (username, mimetype, data) VALUES(
                '$_user', '$fmime', ".Flight::db()->quote(file_get_contents($file_tmp))."
            );");
            log::transact(Flight::db()->last_query());

            // Get the last entered row's ID and return that.
            $id = Flight::db()->query("SELECT @@IDENTITY AS ID")->fetch();
            $entity = ["id" => $id["ID"],
                       "username" => $_user,
                       "mimetype" => $fmime,
                       "data" => null];
            Realtime::record(__CLASS__, Realtime::create, $entity);
            return $entity;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function download($id) {
        try {
            $res = Flight::db()->select("Resources", ["mimetype", "data"], ["id" => $id]);
            if (count($res) === 0) {
                throw new HTTPException('no such resource', 400);
            }

            $mime = $res[0]['mimetype'];
            $contents = $res[0]['data'];
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
        Flight::response()
            ->status(200)
            ->header("Content-Type", $mime)
            ->write($contents)
            ->send();
    }

    public static function list() {
        try {
            $_user = Flight::get('user');
            $result = Flight::db()->select("Resources", ["id", "mimetype"], ["username" => $_user]);
            return $result;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function exists($id) {
        try {
            $result = Flight::db()->has("Resources", ["id" => $id]);
            return $result;
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }

    public static function delete($id) {
        try {
            $res = Flight::db()->delete("Resources", ["id" => $id]);
            if (count($res) === 0) {
                throw new HTTPException('no such resource', 400);
            }

            return ["id" => $id];
        } catch(PDOException $e) {
            throw new HTTPException(log::err($e, Flight::db()->last_query()), 500);
        }
    }
}

// TODO: Add a GET method that returns meta: mimetype, size, etc.
Flight::route('GET /resource/@id', function($id) {
    Flight::check_token();
    return Resource::download($id); // no JSON! "IT'S RAWW!"
});
Flight::route('POST /resource', function() {
    Flight::check_token();
    return Flight::json(["result" => Resource::upload()]);
});
Flight::route('DELETE /resource/@id', function($id) {
    Flight::check_token();
    return Flight::json(["result" => Resource::delete($id)]);
});
Flight::route('GET /resource', function() {
    Flight::check_token();
    return Flight::json(["result" => Resource::list()]);
});
