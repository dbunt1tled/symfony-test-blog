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
}