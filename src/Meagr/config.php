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
	* Routing route map defaults 
	*
	* @return array
	*/
	public static function routeMap() {
		return array(
				// '{custom_map_section}' => 'my_url_segment'
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
				'home' => array(
					'uri' => '__HOME__', 
					'pattern' => '\Meagr\Controller::GET_index'
				),
				'404' => array(
					'uri' => '__404__', 
					'pattern' => '\Meagr\Controller::GET_404'
				), 
				//mvc
				'mvc' => array(
					'uri' => '{domain}/{class}/{method}/', 
					'pattern' => '\{modules}\{controllers}\{class}::{method}'
				), 
				//hmvc
				'hmvc' => array(
					'uri' => '{domain}/{class}/{method}/', 
					'pattern' => '\{modules}\{class}\{controllers}\{class}::{method}'
				),
				//Sub_Controller::Method()
				'subcontroller' => array(
					'uri' => '{domain}/{class}/{method}/', 
					'pattern' => '\{modules}\{class}\{controllers}\{subclass}::{submethod}'
				),
				//sub.domain.com
				//to use the subcontroller you must have a controller within the subdomain folder
				// by the same name and a method of that name inside the controllers folder
				// so http://admin.yourdomain.com => \modules\admin\admin\controllers\admin::GET_index
				// this is a fallback for when no class method is found
				'subdomain' => array(
					'uri' => '{subdomain}.{domain}/{class}/{method}/', 
					'pattern' => '\{modules}\{subdomain}\{class}\{controllers}\{class}::{method}', 
					'filter' => function($object, $args = null){ 

				        // instantiate our router singleton
						$router = Router::init(); 
						
						//check if there is a {class} value set
				        if ($object->routeMapKeyExists('{class}') === false or $router->getRouteMap('{class}') == '/') {

				        	//get the subdomain thats been set in the url and the config
				        	$subdomain = $router->getRouteMap('{subdomain}'); 

				        	//if not, set it to the subdomain
				        	$router->setRouteMap('{class}', $subdomain);	

				        	//lets not double up
				        	$router->setRouteMap('{subdomain}', '');	

				        	//make the pattern namespace compatible
				        	$object = Router::namespaceRoutePattern($object);

				        	//update the pattern
					        $object = $router->translatePattern($object);
					        
					        //set the class back to empty
				        	$router->setRouteMap('{class}', '');				        	
				        	$object->uri_mapped = rtrim($object->uri_mapped, '/');
				        }

						//return our object
						return $object;						
					}
				),
				//catch all for pages so: http://prwhitehead.co.uk/photography => \Modules\Controllers\Page::GET_photography
				'pages' => array(
					'uri' => '{domain}/{args}', 
					'pattern' => '\{modules}\{controllers}\Page::{args}', 
					'filter' => function($object, $args = null) {

						//if we have no args, just return as we dont need to do anything
						if (! is_array($args) or empty($args)) {
							return $object; 
						}

				        // instantiate our router singleton
						$router = Router::init(); 						

						//if we have arguments, get the first
						$page = $args[0]; 

						//create the string
						$args_string = implode('/', $args);

						//unset the first
						unset($args[0]);

						//reorder the remaining
						sort($args);
			        
			        	//set the router route map arg to be our page
			        	$router->setRouteMap('{args}', $page);	

			        	//translate the pattern
				        $object = $router->translatePattern($object);
			        	
			        	//namespace the pattern
			        	$object = Router::namespaceRoutePattern($object);

						//trim the uri			        	
			        	$object->uri_mapped = rtrim($object->uri_mapped, '/');

			        	//put the args back on the url for matching
			        	$router->setRouteMap('{args}', $args_string);	

			        	//...and translate
				        $object = $router->translateUri($object);

						//set the new pattern, minus the argument string, instead use the first argument
						$object->setMappedPattern(str_replace($args_string, $page, $object->getMappedPattern()));
						$object->setMappedUri($object->uri_mapped);

						//return
						return $object; 
					}
				), 				
			);
	}



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
		$class = '\Modules\\'. (defined('SITE_SUBDOMAIN') ? SITE_SUBDOMAIN . '\\' : '') .'Config\\' . ucwords(ENVIRONMENT) . '\Config'; 
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