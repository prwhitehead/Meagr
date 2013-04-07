<?

/**
* Config
*
*
* @package Meagr
* @version 1.0.0
* @author Paul Whitehead
*/

namespace Meagr; 

class Config {

	/**
	* The system language defaults 
	*
	* @return array
	*/
	public static function image() {
		return array(
				'thumb-width' => 100, 
				'thumb-height' => 80,
				'quality' => 75, 
				'cache-dir' => PUBLIC_PATH . '/cache/images', 
				'thumb-dir' => PUBLIC_PATH . '/cache/images/thumbs' 
			);
	}	


	/**
	* The system language defaults 
	*
	* @return array
	*/
	public static function language() {
		return array(
				'default' => 'EN'
			);
	}


	/**
	* Meta defaults 
	*
	* @return array
	*/
	public static function meta() {
		return array(
				'title' => __METHOD__, 
				'description' => 'Cheap, so you dont have to be',
				'keywords' => 'meagr, php, framework, one love, open source'
			);
	}


	/**
	* Routing route map defaults 
	*
	* @return array
	*/
	public static function routeMap() {
		return array(
				'{modules}' => 'modules',
				'{controllers}' => 'controllers', 
				'{subdomain}' => trim(SITE_SUBDOMAIN, '\\'), 
				'{domain}' => SITE_DOMAIN, 

				//everything that is passed after the domain is a possible argument
				'{args}' => trim(\Meagr\Input::server('REQUEST_URI'), '/'),
			);
	}


	/**
	* Routing defaults 
	*
	* app: Admin
	* class: Admin
	* method: user()
	* args: /my/extra/args
	* domain: meagr.com
	* subdomain: member.
	* 
	* @return array
	*/
	public static function routes() {
		return array(

				// //default system routes
				array('__HOME__' => '\Meagr\Controller::GET_index'),
				array('__404__' => '\Meagr\Controller::GET_404'), 

				//mvc
				array('{domain}/{class}/{method}/' => '\{modules}\{controllers}\{class}::{method}'), 

				//hmvc
				array('{domain}/{class}/{method}/' => '\{modules}\{class}\{controllers}\{class}::{method}'),

				//Sub_Controller::Method()
				array('{domain}/{class}/{method}/' => '\{modules}\{class}\{controllers}\{subclass}::{submethod}'),

				//sub.controller
				array('{subdomain}.{domain}/{class}/{method}/' => '\{modules}\{subdomain}\{class}\{controllers}\{class}::{method}'),

				//catch all for pages so: http://prwhitehead.co.uk/photography => \Modules\Controllers\Page::GET_photography
				array('{domain}/{args}' => '\{modules}\{controllers}\Page::{args}'), 				
			);
	}


	/**
	* FTP defaults and connections details
	*
	* @return array
	*/
	public static function ftp() {
		return array(
				//default connection
				'default' => array(
					'host' => 'ftp.prwhitehead.co.uk', 
					'username' => 'ftpprw@prwhitehead.co.uk', 
					'password' => Encrypt::decrypt('Fe0ldNJa7hVcRSCN2jrdeKoVZImE90KSpSynXqDkYJsB+5mcBf4ob135HC6J/s7YVkeD9fdK4c08Bu4UrPc85g=='),
					'port' => '21', 
					'passive' => true, 
					'exec' => false),
				//test	 
				'test' => array(
					'host' => 'ftp.prwhitehead.com', 
					'username' => 'ftpprw@prwhitehead.co.uk', 
					'password' => Encrypt::decrypt('Fe0ldNJa7hVcRSCN2jrdeKoVZImE90KSpSynXqDkYJsB+5mcBf4ob135HC6J/s7YVkeD9fdK4c08Bu4UrPc85g==')),
					'port' => '21'				
			);
	}


	/**
	* email defaults and SMTP connections details
	*
	* @return array
	*/
	public static function email() {
		return array(
				'header' => MODULE_PATH . '/views/partials/email-header.php', 
				'footer' => MODULE_PATH . '/views/partials/email-footer.php', 
				'from-address' => 'prwhitehead@gmail.com', 
				'from-name' => 'Test Testingsom', 
				'smtp' => false,
				'smtp-port' => '465', 
				'smtp-username' => 'prwhitehead@gmail.com', 
				'smtp-password' => Encrypt::decrypt('LQScrjgIlXBPd85JJWOJgoK5iH+1jYrgEiVd/Jl7BWErBKAKQoq6mHwfWXgCuHcenRbGmv2drbncgqvrlORFiA=='), 
				'smtp-host' =>'ssl://smtp.gmail.com'
			);
	}


	/**
	* member table details
	*
	* @return array
	*/
	public static function member() {
		return array(
				'table' => 'member', 
				'password_col' => 'password', 
				'salt_col' => 'salt'
			);
	}


	/**
	* encryption key and IV salt
	*
	* @return array
	*/
	public static function encrypt() {
		return array(
				'key' => "UBeChsDHZOYdbITKz/LXceb3XMPVFOAP",
    			'iv' => "vsrpN/tmchipHza9jldMLVNCowLpkse5WOU8mVTnSOo="
			);
	}


	/**
	* database connection details
	*
	* @return array
	*/
	public static function database() {
		return array(
				'host' => 'localhost', 
				'username' => 'test', 
				'password' => 'test', 
				'dbname' => 'testing'
			);
	}	


	/**
	* AWS S3 connection details
	*
	* @return array
	*/
	public static function s3() {
		return array(
				'bucket_name' => 'test_bucket', 
				'key' => '', 
				'secret' => '',

			);
	}


	/**
	* cache details
	*
	* @return array
	*/
	public static function cache() {
		return array(
			
				//our cache dir
				'dir' => '/cache', 

				//one hour, 
				'duration' => 60*60, 

				//the extension of our cache files - without the dot
				'file_ext' => 'html', 

				//our css cache filename
				'css' => 'cache.css',

				//our js cache filename
				'js' => 'cache.js'				
			);
	}	



	//helper functions

	/**
	* method is the name of the config class method we want to merge with the app config
	*
	* @param method string The name of the method, whose settings we want to retrieve
	*
	* @return array
	*/
	public static function settings($method = null) {

		$module_array = $core_array = array();

		//make sure we have a method
		if (is_null($method)) {
			throw new MeagrException('No Config method name supplied to ' . __METHOD__);
		}

		//get our configs ... 
		//check for base app config
		$class = new self; 
		$reflection = new \ReflectionClass($class);		
		if ($reflection->hasMethod($method)) {
			$core_array = call_user_func_array(array($class, $method), array()); 
		}

		//check for base app config
		$class = '\Modules\Config';			
		if (class_exists($class)) {
			$reflection = new \ReflectionClass($class);		

			if ($reflection->hasMethod($method)) {
				$module_array = call_user_func_array(array($class, $method), array()); 
			}
		}

		//check for environment dependant config (which takes prescident)
		$class = '\modules\\'. SITE_SUBDOMAIN .'Config\\' . ucwords(ENVIRONMENT) . '\Config';
		if (class_exists($class)) {
			$reflection = new \ReflectionClass($class);		

			if ($reflection->hasMethod($method)) {
				$module_array = call_user_func_array(array($class, $method), array()); 
			}
		}			

		return self::parseArgs($module_array, $core_array);
	}


	/**
	* get a specific value, with $key dot notation
	*
	* @param method string The name of the method, whose settings we want to retrieve
	* @param key string the specfic key we want from our method array
	*
	* @return mixed[array|string]
	*/
	public static function get($method = null, $key = null) {
		if (is_null($method)) {
			throw new MeagrException('The config method "' . $method . '" could now be found');
		}

		$settings = self::settings($method);

		if (! is_null($key)) {
			return Arr::get($settings, $key);
		}

		return $settings;
	}


	/**
	 * Combine two objects / arrays and return them
	 *
	 * @author Paul Whitehead via wordpress
	 * @return string
	 **/
	static function parseArgs($args, $defaults) {
		if (is_object($args)) {
			$r = get_object_vars( $args );
		} elseif (is_array($args)) {
			$r =& $args;
		}

		if (is_array($defaults)) {
			return array_merge($defaults, $r);
		}
		return $r;
	}
}