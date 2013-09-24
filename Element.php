<?php

/**
 * JSON Serializer
 */

namespace attitude\Elements;

/**
 * JSON Serializer Class
 *
 * Serializing engine with gzip compression.
 *
 * @author Martin Adamko <@martin_adamko>
 * @version v0.1.0
 * @licence MIT
 *
 */
class SerializerJSON_Element implements Serializer_Interface
{
    /**
     * Singleton instance
     *
     * @var JSONSerializer
     *
     */
    private static $instance = null;

    /**
     * Level of compression
     *
     * Can be given as FALSE or 0 for no compression up to 9 for maximum
     * compression. If set to -1 a default compression is used.
     *
     * @see http://php.net/manual/en/function.gzencode.php
     * @var bool|int
     *
     */
    public static $compress = false;

    /**
     * Class Constructor
     *
     * Protected visibility allows building singleton class.
     *
     * @param   void
     * @returns object
     *
     */
    protected function __construct()
    {
        if (function_exists('gzdecode')) {
            static::$compress = DependencyContainer::get(get_called_class().'::$compress');
        }

        return $this;
    }

    /**
     * Returns singleton instance of this class
     *
     * @param   void
     * @returns JSONSerializer  Instance of this class
     *
     */
    public static function instance()
    {
        return self::$instance===null ? new self : self::$instance;
    }

    /**
     * Returns the compression value
     *
     * @param   void
     * @returns int|false   Returns value in range of -1 to 9 or `FALSE`
     *
     */
    protected function compression()
    {
        if (!function_exists('gzdecode')) {
            return false;
        }

        // Return default for generally enabled
        if (static::$compress===true) {
            return -1;
        }

        return (-2 < (int)static::$compress && (int)static::$compress < 10) ? (int) static::$compress : false;
    }

    /**
     * Unserializes data
     *
     * Returns unserialized data
     *
     * @param   string  $data   Data to unserialize
     * @returns mixed           Unserialized data
     *
     */
    public function unserialize($data)
    {
        if ((bool) $this->compression()) {
            $data = gzdecode($data);
        }

        return json_decode($data, true);
    }

    /**
     * Serializes data
     *
     * Returns serialized data
     *
     * @param   mixed   $data   Data to serialize
     * @returns string          Serialized data
     *
     */
    public function serialize($data)
    {
        if ($data===null) {
            return '';
        }

        $compression = $this->compression();

        return (bool) $compression ? gzencode(json_encode($data), $compression) : json_encode($data);
    }
}
