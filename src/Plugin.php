<?php


namespace WP_Wrapper\Plugin;

/**
  * Main plugin wrapper for setting up and booting a plugin.
 *
 * @since   1.0.0
 * @package WP_Wrapper\Plugin
 * @author  Sven Wagener <support@awesome.ug>
 */
class Plugin {
    /**
     * Plugin name.
     *
     * @since 1.0.0
     *
     * @var string Plugin name.
     */
    protected $name = null;

    /**
     * Plugin version.
     *
     * @since 1.0.0
     *
     * @var string Plugin name.
     */
    protected $version = null;

    /**
     * Services
     *
     * @since 1.0.0
     *
     * @var array $services Registered services
     */
    protected $services = [];


    /**
     * Textdomain
     *
     * @since 1.0.0
     *
     * @var string $textdomain Textdomain
     */
    protected $textdomain = '';


    /**
     * Path to translation folder
     *
     * @since 1.0.0
     *
     * @var string $translationPath Path to translations
     */
    protected $translation_path = '';

    /**
     * Running the plugin object.
     *
     * @since 1.0.0
     *
     * @return Plugin Plugin object.
     */
    public function __construct() {
        return $this;
    }

    /**
     * Register the plugin with the WordPress system.
     *
     * @since 1.0.0
     *
     * @return Plugin Plugin object.
     */
    public function boot() {
        $this->load();

        return $this;
    }

    /**
     * Get plugin name.
     *
     * @since 1.0.0
     *
     * @param string $name Name of the plugin.
     *
     * @return Plugin Plugin object.
     */
    public function setName( $name ) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get plugin name.
     *
     * @since 1.0.0
     *
     * @return string Plugin name.
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Set plugin version.
     *
     * @since 1.0.0
     *
     * @param string Plugin version.
     *
     * @return Plugin Plugin object.
     */
    public function setVersion( $version ) {
        $this->version = $version;

        return $this;
    }

    /**
     * Get plugin version.
     *
     * @since 1.0.0
     *
     * @return string Plugin version.
     */
    public function getVersion() : string {
        return $this->version;
    }

    /**
     * Add service.
     *
     * @since 1.0.0
     *
     * @param string $class Class name.
     * @param array  $params Parameters to put in constructor.
     *
     * @return Plugin Plugin object.
     **/
    public function addService( $class, ...$params ) : Plugin {
        $this->services[] = array( $class, $params );

        return $this;
    }


    /**
     * Get the list of services to register.
     *
     * @since 1.0.0
     *
     * @return Service[] Array of fully qualified class names.
     */
    public function getServices() {
        return $this->services;
    }


    /**
     * Register the individual services of this plugin.
     *
     * @since 1.0.0
     */
    public function registerServices() {
        array_walk($this->services, function ( $service ) {
            if ( ! class_exists( $service[0] ) ) {
                throw new Exception( sprintf( 'Service class \'%s\' does not exist', $service[0] ));
            }

            $class = new \ReflectionClass( $service[0] );

            if ( ! $class->implementsInterface( 'Service' ) ) {
                throw new Exception( sprintf( 'Service class \'%s\' does not implement Service interface', $service[0] ));
            }

            $class->newInstance( ...$service[1] );
        });
    }


    /**
     * Set a textdomain.
     *
     * @since 1.0.0
     *
     * @param string $textdomain Textdomain.
     * @param string $translation_path Path to translation folder.
     *
     * @return Plugin Plugin object.
     **/
    public function addTranslation( $textdomain, $translation_path ): Plugin {
        $this->textdomain = $textdomain;
        $this->translation_path = $translation_path;

        return $this;
    }


    /**
     * Load translation
     *
     * @since 1.0.0
     *
     * @return bool If translation is loaded
     **/
    public function loadTranslation() {
        return load_plugin_textdomain($this->textdomain, false, $this->translation_path);
    }
}