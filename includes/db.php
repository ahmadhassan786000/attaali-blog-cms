<?php
/*
 * Simple procedural MySQLi helpers (PHP 7.2 compatible)
 * Placeholders "?" are replaced with safely escaped values.
 */

function db()
{
    static $link = null;
    if ($link === null) {
        mysqli_report(MYSQLI_REPORT_OFF);
        $link = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$link) {
            http_response_code(500);
            die('Database connection failed. Please check includes/config.php');
        }
        mysqli_set_charset($link, 'utf8mb4');
    }
    return $link;
}

function db_quote($v)
{
    if ($v === null) {
        return 'NULL';
    }
    if (is_int($v)) {
        return (string)$v;
    }
    if (is_float($v)) {
        return str_replace(',', '.', (string)$v);
    }
    if (is_bool($v)) {
        return $v ? '1' : '0';
    }
    return "'" . mysqli_real_escape_string(db(), (string)$v) . "'";
}

function db_prepare($sql, $params)
{
    if (empty($params)) {
        return $sql;
    }
    $i = 0;
    return preg_replace_callback('/\?/', function ($m) use (&$i, $params) {
        $v = array_key_exists($i, $params) ? $params[$i] : null;
        $i++;
        return db_quote($v);
    }, $sql);
}

function db_query($sql, $params = array())
{
    $res = mysqli_query(db(), db_prepare($sql, $params));
    if ($res === false) {
        $msg = mysqli_error(db());
        error_log('DB error: ' . $msg . ' | SQL: ' . $sql);
        if (defined('DEBUG') && DEBUG) {
            die('<pre>DB error: ' . htmlspecialchars($msg) . "\nSQL: " . htmlspecialchars($sql) . '</pre>');
        }
        return false;
    }
    return $res;
}

function db_all($sql, $params = array())
{
    $rows = array();
    $res = db_query($sql, $params);
    if ($res && $res !== true) {
        while ($row = mysqli_fetch_assoc($res)) {
            $rows[] = $row;
        }
        mysqli_free_result($res);
    }
    return $rows;
}

function db_row($sql, $params = array())
{
    $res = db_query($sql, $params);
    if ($res && $res !== true) {
        $row = mysqli_fetch_assoc($res);
        mysqli_free_result($res);
        return $row ? $row : null;
    }
    return null;
}

function db_val($sql, $params = array(), $default = null)
{
    $row = db_row($sql, $params);
    if ($row) {
        $vals = array_values($row);
        return $vals[0];
    }
    return $default;
}

function db_exec($sql, $params = array())
{
    $res = db_query($sql, $params);
    if ($res === false) {
        return false;
    }
    return mysqli_affected_rows(db());
}

function db_insert_id()
{
    return mysqli_insert_id(db());
}
