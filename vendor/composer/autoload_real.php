<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInite6edfdf2708e6b3ac5c00f21425c990a
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInite6edfdf2708e6b3ac5c00f21425c990a', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInite6edfdf2708e6b3ac5c00f21425c990a', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInite6edfdf2708e6b3ac5c00f21425c990a::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}