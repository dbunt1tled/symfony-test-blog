<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 11.08.18
 * Time: 21:38
 */

namespace App\Utils;

class Globals
{
    protected static $categoryImagesDir;
    protected static $blogImagesDir;
    protected static $authorImagesDir;
    protected static $categoryImagesUrl;
    protected static $blogImagesUrl;
    protected static $authorImagesUrl;
    protected static $paginatorPageSize;
    /**
     * @return string
     */
    public static function getCategoryImagesDir()
    {
        return self::$categoryImagesDir;
    }

    /**
     * @param string $categoryImagesDir
     */
    public static function setCategoryImagesDir(string $categoryImagesDir): void
    {
        self::$categoryImagesDir = $categoryImagesDir;
    }

    /**
     * @return string
     */
    public static function getBlogImagesDir()
    {
        return self::$blogImagesDir;
    }

    /**
     * @param string $blogImagesDir
     */
    public static function setBlogImagesDir(string $blogImagesDir): void
    {
        self::$blogImagesDir = $blogImagesDir;
    }

    /**
     * @return string
     */
    public static function getCategoryImagesUrl()
    {
        return self::$categoryImagesUrl;
    }

    /**
     * @param string $categoryImagesUrl
     */
    public static function setCategoryImagesUrl(string $categoryImagesUrl): void
    {
        self::$categoryImagesUrl = $categoryImagesUrl;
    }

    /**
     * @return string
     */
    public static function getBlogImagesUrl()
    {
        return self::$blogImagesUrl;
    }

    /**
     * @param string $blogImagesUrl
     */
    public static function setBlogImagesUrl(string $blogImagesUrl): void
    {
        self::$blogImagesUrl = $blogImagesUrl;
    }
    /**
     * @return int
     */
    public static function getPaginatorPageSize()
    {
        return self::$paginatorPageSize;
    }

    /**
     * @param int $paginatorPageSize
     */
    public static function setPaginatorPageSize(int $paginatorPageSize): void
    {
        self::$paginatorPageSize = $paginatorPageSize;
    }

    /**
     * @return string
     */
    public static function getAuthorImagesDir()
    {
        return self::$authorImagesDir;
    }

    /**
     * @param string $authorImagesDir
     */
    public static function setAuthorImagesDir($authorImagesDir): void
    {
        self::$authorImagesDir = $authorImagesDir;
    }

    /**
     * @return string
     */
    public static function getAuthorImagesUrl()
    {
        return self::$authorImagesUrl;
    }

    /**
     * @param string $authorImagesUrl
     */
    public static function setAuthorImagesUrl($authorImagesUrl): void
    {
        self::$authorImagesUrl = $authorImagesUrl;
    }
}