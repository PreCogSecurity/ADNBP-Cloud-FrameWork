<?php
namespace CloudFramework\Tool;

final class RequestParser
{
    /**
     * Catch all request headers
     * @return array
     */
    public static function getRequestHeaders()
    {
        $headers = array();
        if(function_exists("getallheaders")) {
            foreach (getallheaders() as $key => $value) {
                $headers[strtolower($key)] = $value;
            }
        }
        return $headers;
    }

    /**
     * Catch all post data
     * @return array
     */
    public static function getPostParams()
    {
        $data = array();
        try {
            $rawData = file_get_contents('php://input');
            $data = json_decode($rawData, JSON_UNESCAPED_UNICODE);
            if (null === $data) {
                parse_str($rawData, $data);
            }
        } catch (\Exception $t) {
            syslog(LOG_ERR, $t->getMessage());
        }
        $data = array_merge($data, $_POST);
        return $data;
    }

    /**
     * Catch query string data
     * @return array
     */
    public static function getQueryString()
    {
        $query = array();
        try {
            parse_str($_SERVER['QUERY_STRING'], $query);
        } catch(\Exception $t) {
            syslog(LOG_ERR, $t->getMessage());
        }
        return $query;
    }

    /**
     * Gets post param
     * @param $key
     * @return mixed|null
     */
    public static function getPostParam($key)
    {
        $post = self::getPostParams();
        return array_key_exists($key, $post) ? $post[$key] : null;
    }

    /**
     * Get specific header
     * @param string $key
     * @return string|null
     */
    public static function getHeader($key)
    {
        $key = strtolower(trim($key));
        $headers = self::getRequestHeaders();
        return (array_key_exists($key, $headers)) ? $headers[$key] : null;
    }

    /**
     * Get query param
     * @param $key
     * @return array|null
     */
    public static function getQueryParam($key)
    {
        $query = self::getQueryString();
        return array_key_exists($key, $query) ? $query[$key] : null;
    }

    /**
     * Get $_SERVER value
     * @param string $key
     * @return null|mixed
     */
    public static function getServerKey($key)
    {
        $value = null;
        if (array_key_exists($key, $_SERVER)) {
            $value = $_SERVER[$key];
        }
        return $value;
    }

    /**
     * Get $_FILES value
     * @param string $key
     * @return array
     */
    public static function getUploadedFile($key)
    {
        $file = array();
        if (array_key_exists($key, $_FILES)) {
            $file = $_FILES[$key];
        }
        return $file;
    }
}