<?php

/**
 * Template Loader for WeRePack
 *
 * @link       https://WeRePack.org
 * @since      1.0.0
 *
 * @package    Repack
 * @subpackage Repack/includes
 */

/**
 * Register Template Loader for WeRePack. This is a fork from Gary Jones's Template Loader.
 *
 * @author    Gary Jones
 * @link      http://github.com/GaryJones/Gamajo-Template-Loader
 *
 * @package    Repack
 * @subpackage Repack/includes
 * @author     Philipp Wellmer <philipp@ouun.io>
 */
class Repack_Template_Loader {
	/**
	 * Prefix for filter names.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $filter_prefix = 'repack';

	/**
	 * Directory name where custom templates for this plugin should be found in the theme.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $theme_template_directory = 'repack';

	/**
	 * Reference to the root directory path of this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_directory = REPACK_PLUGIN_DIR;

	/**
	 * Directory name where templates are found in this plugin.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $plugin_template_directory = 'templates';

	/**
	 * Internal use only: Store located template paths.
	 *
	 * @var array
	 */
	private $template_path_cache = array();

	/**
	 * Internal use only: Store variable names used for template data.
	 *
	 * Means unset_template_data() can remove all custom references from $wp_query.
	 *
	 * Initialized to contain the default 'data'.
	 *
	 * @var array
	 */
	private $template_data_var_names = array( 'data' );

	/**
	 * Clean up template data.
	 *
	 * @since 1.2.0
	 */
	public function __destruct() {
		$this->unset_template_data();
	}

	/**
	 * Retrieve a template part.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Template slug.
	 * @param string $name Optional. Template variation name. Default null.
	 * @param bool   $load Optional. Whether to load template. Default true.
	 * @return string
	 */
	public function get_template_part( $slug, $name = null, $load = true ) {
		// Execute code for this part.
		do_action( 'get_template_part_' . $slug, $slug, $name );
		do_action( $this->filter_prefix . '_get_template_part_' . $slug, $slug, $name );

		// Get files names of templates, for given slug and name.
		$templates = $this->get_template_file_names( $slug, $name );

		// Return the part that is found.
		return $this->locate_template( $templates, $load, false );
	}

	/**
	 * Make custom data available to template.
	 *
	 * Data is available to the template as properties under the `$data` variable.
	 * i.e. A value provided here under `$data['foo']` is available as `$data->foo`.
	 *
	 * When an input key has a hyphen, you can use `$data->{foo-bar}` in the template.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed  $data     Custom data for the template.
	 * @param string $var_name Optional. Variable under which the custom data is available in the template.
	 *                         Default is 'data'.
	 * @return Repack_Template_Loader
	 */
	public function set_template_data( $data, $var_name = 'data' ) {
		global $wp_query;

		$wp_query->query_vars[ $var_name ] = (object) $data;

		// Add $var_name to custom variable store if not default value.
		if ( 'data' !== $var_name ) {
			$this->template_data_var_names[] = $var_name;
		}

		return $this;
	}

	/**
	 * Remove access to custom data in template.
	 *
	 * Good to use once the final template part has been requested.
	 *
	 * @since 1.2.0
	 *
	 * @return Repack_Template_Loader
	 */
	public function unset_template_data() {
		global $wp_query;

		// Remove any duplicates from the custom variable store.
		$custom_var_names = array_unique( $this->template_data_var_names );

		// Remove each custom data reference from $wp_query.
		foreach ( $custom_var_names as $var ) {
			if ( isset( $wp_query->query_vars[ $var ] ) ) {
				unset( $wp_query->query_vars[ $var ] );
			}
		}

		return $this;
	}

	/**
	 * Given a slug and optional name, create the file names of templates.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Template slug.
	 * @param string $name Template variation name.
	 * @return array
	 */
	protected function get_template_file_names( $slug, $name ) {
		$templates = array();
		if ( isset( $name ) ) {
			$templates[] = $slug . '-' . $name . '.php';
		}
		$templates[] = $slug . '.php';

		/**
		 * Allow template choices to be filtered.
		 *
		 * The resulting array should be in the order of most specific first, to least specific last.
		 * e.g. 0 => recipe-instructions.php, 1 => recipe.php
		 *
		 * @since 1.0.0
		 *
		 * @param array  $templates Names of template files that should be looked for, for given slug and name.
		 * @param string $slug      Template slug.
		 * @param string $name      Template variation name.
		 */
		return apply_filters( $this->filter_prefix . '_get_template_part', $templates, $slug, $name );
	}

	/**
	 * Retrieve the name of the highest priority template file that exists.
	 *
	 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
	 * inherit from a parent theme can just overload one file. If the template is
	 * not found in either of those, it looks in the theme-compat folder last.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $template_names Template file(s) to search for, in order.
	 * @param bool         $load           If true the template file will be loaded if it is found.
	 * @param bool         $require_once   Whether to require_once or require. Default true.
	 *                                     Has no effect if $load is false.
	 * @return string The template filename if one is located.
	 */
	public function locate_template( $template_names, $load = false, $require_once = true ) {

		// Use $template_names as a cache key - either first element of array or the variable itself if it's a string.
		$cache_key = is_array( $template_names ) ? $template_names[0] : $template_names;

		// If the key is in the cache array, we've already located this file.
		if ( isset( $this->template_path_cache[ $cache_key ] ) ) {
			$located = $this->template_path_cache[ $cache_key ];
		} else {

			// No file found yet.
			$located = false;

			// Remove empty entries.
			$template_names = array_filter( (array) $template_names );
			$template_paths = $this->get_template_paths();

			// Try to find a template file.
			foreach ( $template_names as $template_name ) {
				// Trim off any slashes from the template name.
				$template_name = ltrim( $template_name, '/' );

				// Try locating this template file by looping through the template paths.
				foreach ( $template_paths as $template_path ) {
					if ( file_exists( $template_path . $template_name ) ) {
						$located = $template_path . $template_name;
						// Store the template path in the cache.
						$this->template_path_cache[ $cache_key ] = $located;
						break 2;
					}
				}
			}
		}

		if ( $load && $located ) {
			load_template( $located, $require_once );
		}

		return $located;
	}

	/**
	 * Return a list of paths to check for template locations.
	 *
	 * Default is to check in a child theme (if relevant) before a parent theme, so that themes which inherit from a
	 * parent theme can just overload one file. If the template is not found in either of those, it looks in the
	 * theme-compat folder last.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	protected function get_template_paths() {
		$theme_directory = trailingslashit( $this->theme_template_directory );

		$file_paths = array(
			10  => trailingslashit( get_template_directory() ) . $theme_directory,
			100 => $this->get_templates_dir(),
		);

		// Only add this conditionally, so non-child themes don't redundantly check active theme twice.
		if ( get_stylesheet_directory() !== get_template_directory() ) {
			$file_paths[1] = trailingslashit( get_stylesheet_directory() ) . $theme_directory;
		}

		/**
		 * Allow ordered list of template paths to be amended.
		 *
		 * @since 1.0.0
		 *
		 * @param array $var Default is directory in child theme at index 1, parent theme at 10, and plugin at 100.
		 */
		$file_paths = apply_filters( $this->filter_prefix . '_template_paths', $file_paths );

		// Sort the file paths based on priority.
		ksort( $file_paths, SORT_NUMERIC );

		return array_map( 'trailingslashit', $file_paths );
	}

	/**
	 * Return the path to the templates directory in this plugin.
	 *
	 * May be overridden in subclass.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_templates_dir() {
		return trailingslashit( $this->plugin_directory ) . $this->plugin_template_directory;
	}
}
