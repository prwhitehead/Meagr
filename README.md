Meagr
=====

A small, focused framework for small-ish projects that require an aspect of H/MVC and a lightweight MySQL ORM/Model. With a bootstrap Form class, Environment specific config, CSS/JS/HTML Caching, S3 interface, Encryption, Validation, automatic db schema creation, Auth, Timer, Nonce and helpers for Inputand  Arrays. 

Folder structure
----------------

Meagr allows you some flexibility when setting up your application. The main set of system classes can be found in the composer package which is added to your vendor folder with the following require command in your composer.json file

    
    "require": {
        "prwhitehead/meagr": "dev-master"
    }

Once the files have been installed and ready to be used, you must setup your modules / application folder. Because of the way the non-composer autoloader works, whatever folder you place your files will define your namespacing.     

The files are required for the routing, config etc. The 'modules' folder if where your application will sit. It was called 'modules' and not 'app', or 'application' as each module could be a seperate micro-application, performing a certain set of tasks. 

Modules modules should be namespaced as follows

      
    /Modules/YourModulesName

This namespace matches the folder name and structure of your controllers/models/views folders

      
    /Modules/YourModulesName/Controllers/Classname

Sub controllers can also be used by using the underscore in the filename. 

      
    /admin/user/create

Would first look to find an Admin controller class, which contained a GET_user method and pass in 'create' as a variable. The next check would look to find the Admin_User controller which is in admin_user.php within the admin app folder

      
    /modules/admin/controllers/admin_user.php

For the method:

      
    Admin_User::GET_create();

Routing
-------

By default a website can use either an MVC or HMVC structure, which uses:

     
    http://www.example.com/news/latest

Which would route to either: 

     
    modules/news/controllers/news.php

or: 

     
    modules/controllers/news.php

The HMVC structure takes presidence and over MVC which is the default. 

If we were using the HMVC structure for our app modules, the system will look for the following class and method

     
    <?  
    Modules\News\Controllers\News::GET_latest();

If there is an even number of arguments the system will attempt to assign them to key => value pairs. So a URL as follows: 

      
    <your domain>/admin/user/add/this/that/and/again/hi

Should be parsed into an array as such after the method Admin_User::GET_add() has been found: 

      
    <?
    Array
    (
        [add] => this
        [that] => and
        [again] => hi
    )

The original URI segments are always passed as an array called $segments and if the segment count is even, then $pairs will hold the parsed key => value pairs. So the following URI: 

      
    <your domain>/admin/user/something/else/that/is/passed/as/segments/again/

Would translate to accessing Admin_user class and if no GET_something() method could be found, the system will check for a default controller (usually GET_index()) and then pass the following array to the method, if found: 

      
    <?
    Array
    (
        [segments] => Array
            (
                [0] => admin
                [1] => user
                [2] => something
                [3] => else
                [4] => that
                [5] => is
                [6] => passed
                [7] => as
                [8] => segments
                [9] => again
            )

        [pairs] => Array
            (
                [something] => else
                [that] => is
                [passed] => as
                [segments] => again
            )
    )

Using views
-----------

To include a view in one of your Controllers use the following: 

      
    <?
    echo \Meagr\View::view('ModulesName::view-filename');

To pass data to the a simple view file which is located in the sites default app/view folder: 

      
    <?
    \Meagr\View::view('test', compact('data'));

Using the Model
---------------

      
    <?
    $news = Modules\Models\News::init()->tableAlias('n')
    	->distinct()
    	->where('n.title', 'tes%', 'LIKE')
    	->where('n.created_at', '0000%')
    	->orderDesc('n.id')
    	->limit(2)
    	->offset(3)
    	->go();

Returns: 									

      
    <?
    Array
    (
        [0] => Modules\News\Models\News Object
            (
                [id] => 4
                [title] => testing
                [slug] => testing
                [content] => This is the content of the first testing news article
                [parent] => 9
                [created_at] => 0000-00-00 00:00:00
                [updated_at] => 0000-00-00 00:00:00
            )
       [1] => Modules\News\Models\News Object
            (
                [id] => 3
                [title] => testing
                [slug] => testing
                [content] => This is the content of the first testing news article
                [parent] => 9
                [created_at] => 0000-00-00 00:00:00
                [updated_at] => 0000-00-00 00:00:00
            )
    )	

Because the Database class impliments the PHP SPL ArrayAccess we can also do this: 

      
    <?    
    //get member
    $member = Modules\Models\Member::init()->where('id', $id);

    //the count function will trigger the go() method and generate our results (lazy loading)
    if (count($member)) {

        //now we have access to member
        p($member);
    }

Config
------

In order to use different config's for your development / production / staging servers etc, add a 'config' folder to your app folder with the environment as a sub folder and a config.php file which is properly namespaced within that. The envionrment to be used is set with the "ENVIRONMENT" constant, so:

    
    <? 
    define("ENVIRONMENT", 'production');

Would mean the system looks for the following class:    

    ::php  
    <? 
    namespace Modules\Config\Production\Config(); 

Which would match: 

      
    modules/config/production/config.php

As a fail back, if you wish to just use a single config file in your app, include it within the base config directory and it will be included. 	

Input
-----

Use to get and set $_GET, $_POST, $_SESSION and $_COOKIE values. 

      
    <?
    echo \Meagr\Input::get('param_name'); 	

To set a value: 

      
    <?
    \Meagr\Input::get('param_name', 'value');	

Bcrypt
------

Instantiate Bcrypt(); 

      
    <?
    $b = new \Meagr\Bcrypt();

Hash your password / input data

      
    <?
    $password = $b->hash('mypassword01');

Get the salt for this instance of the Bcrypt class

     
    <? 
    $salt = $b->getSalt(); 

Now you can store the salt next to the users password and validate that next time they login via: 

      
    <?
    $new = new Bcrpyt(); 
    $new->setSalt($user_salt); 
    if ($new->verify('mypassword01', $users_password_from_db)) {
        return true;
    }		

Timer
-----

The timer class is for seeing how long events are taking and works on the 'multiton' pattern

Init the timer with your name, in this case 'db'

      
    <?
    \Meagr\Timer::init('db');

Echo the difference in the time since the timer was started

      
    <?
    echo \Meagr\Timer::init('db')->diff(); 

alternatively you can use: 

      
    <?
    \Meagr\Timer::init('db')->stop();

... sometime later

      
    <?
    echo \Meagr\Timer::init('db')->diff();

Member
------

Add users to the member table as follows. First instantiate the Bcrypt class, and generate the users unique salt, then you can hash their password.  

         
    <? 
    $b = new \Meagr\Bcrypt(); 
    $salt = $b->getSalt();
    $password = $b->hash('123');

Now we can create a new instance of the member object and save it to the database.

          
    <?
    $test = new Modules\Models\Member(); 
    $test->email = 'users@emailadderss.com';
    $test->password = $password; 
    $test->salt = $salt; 
    $test->first_name = 'first-name';
    $test->last_name = 'lastname';
    $id = $test->save();

Thats half the work done. Now we can check the user is valid from within functions and methods around the site by doing the following: 

      
    <?    
    $member = Modules\Models\Member::init()->where('email', $email);

    //check the user exists first
    if (count($member)) {
    
        //get the first index in results
        $member = $member[0]; 
    
        //init the Bcrypt class and set the instance's salt to the members db salt
        $crypt = new \Meagr\Bcrypt(); 
        $crypt->setSalt($member->salt);
    
        //now we can check to see if the users password matches our hash with the users salt
        if ($crypt->hash($password) == $member->password) {
    
            //the password matches, so create the session. 
            //we can get user data with \Meagr\Auth::current() which returns a Model object of member data
            \Meagr\Auth::create($member->id);
            // \Meagr\Router::redirect('/member/dashboard');
    
        //if the passwords dont match, force logout 
        } else {
            \Meagr\Auth::destroy();
            \Meagr\Router::redirect('/member');
        }
    
    //the memeber couldnt be found
    } else {
        throw new \Meagr\MeagrException('User not found');
    }

Logging a user out of their account / unsetting their session is alot easier: 

      
    <?
    \Meagr\Auth::destroy(); 

The form Class
--------------

Create a new form with something similar to this: 

      
    <?
    $form = Meagr\Form::init(array(
        'id' => 'form_id', 
        'class' => 'standard-form', 
        'method' => 'get', 
        'action' => '/admin/user/new', 
    )); 

Here we pass in our array of form set up data, including all the normal form tag attributes. 

You can now add input field data view addFields() or addField(). The later only expects a single array of input data, whereas the former will only accept a multidimensional array of more than one input field data, as the example below shows: 

     
    <?         
    $form->addFields(
        array(
            'email' => array(
                'id' => 'email', 
                'name' => 'email', 
                'class' => 'form-element ', 
                'label' => 'Email Address', 
                'help' => 'Please enter a valid email address',
                'placeholder' => 'john@smith.com', 
                'type' => 'text',
                'validate' => array(
                        'not' => array(
                            //key is the type of check
                            //value is the message upon failure
                            'empty' => 'An email address must be provided'
                            ), 
                        'is' => array(
                                'string' => 'A string is required'
                            ), 
                        'valid' => array(
                                'email' => 'A valid email is required', 
                                'gmail' => 'A gmail email address must be provided'
                            )
                    ), 
                'append' => array(
                        'type' => 'span', 
                        'value' => 'Options',
                        'options' => array(
                                'action' => array(
                                        'id' => 'action',
                                        'title' => 'Action', 
                                        'icon' => '',
                                        'href' => \Meagr\Router::redirect('/admin/login', false)
                                    ), 
                                'second_action' => array(
                                        'id' => 'second_action',
                                        'title' => 'Second Action', 
                                        'href' => \Meagr\Router::redirect('/admin/login', false)
                                    ),  
                                'divider' => true, 
                                'third_action' => array(
                                        'id' => 'third_action',
                                        'title' => 'third Action', 
                                        'href' => \Meagr\Router::redirect('/admin/login', false)
                                    ),  
                            )
                    ),
                'value' => \Meagr\Input::post('email'),     
            'password' => array(
                'id' => 'password', 
                'name' => 'password', 
                'class' => 'form-element', 
                'label' => 'Password', 
                'type' => 'password',
                'validate' => array(
                    'not_empty', 
                    'is_string'
                ), 
                'value' => ''
            ), 
            'submit' => array(
                'id' => 'submit', 
                'name' => 'submit', 
                'class' => 'form-element', 
                'label' => '', 
                'type' => 'submit',
                'validate' => array(), 
                'value' => 'Submit'
            ), 
        )
    ); 

The 'append' arrays create a drop down menu. 

All items within the array which are not used specifically by the form classs are turned into attributes and values, so:

      
    <?
    'email' => array(
        'id' => 'email', 
        'name' => 'email', 
        'class' => 'form-element ', 
        'label' => 'Email Address', 
        'help' => 'Please enter a valid email address',
        'placeholder' => 'john@smith.com', 

Would automatically create:

      
    <input placeholder="john@smith.com" help="Please enter a valid email address" class="form-element" id="email" name="email" />              

Validation required as follows:  

      
    <?
    'validate' => array(
        'not' => array(
            //key is the type of check
            //value is the message upon failure
            'empty' => 'An email address must be provided'
            )
    )

This passes the value for the input to Validate class, which uses the Validate::not() method to check if the value is not 'empty' and if the value is empty, passes back the message provided.      

HTML can be added to the form via the use of: 

      
    <?
    $form->addHTML('<div class="container">'); 

The later on, you may close the div: 

      
    <?
    $form->addHTML('</div>');   

Once you have setup your form just the way you like it, use the build() method to echo our the form to the screen: 

      
    <?
    $form->build();     

Nonce class
-----------

The Nonce class is used to validate requests made by the system, for form inputs and url actions. 

First create your nonce form input field as follows: 

     
    <? 
    \Meagr\Nonce::input('nonce-name');

Now, to check our request is coming from an authorised user within the site:

      
    <?
    if (\Meagr\Nonce::valid($_POST['_nonce'], 'nonce-name')){
        //do stuff as request is valid 
    }

We can also create GET requests for our requests:

      
    <a href="index.php?<?=\Meagr\Nonce::string('nonce-name'); ?>">Link One</a>

Which is validated the same way: 

      
    <?
    if (\Meagr\Nonce::valid($_GET['_nonce'], 'nonce-name')){
        //do stuff as request is valid 
    }

Arr (Array class)
-----------------

Use . to seperate array level

     
    <? 
    $array = array(
        'one' => array('oneone' => 'hello', 'onetwo' => 'sup'), 'two' => 'goodbye', 'three' => 'one', 'four' => 2, 'five' => '3'
    );
    
      
    <?
    p(\Meagr\Arr::get($array, 'one.onetwo'));
    \Meagr\Arr::set($array, 'one.onetwo', 'init');
    p(\Meagr\Arr::get($array, 'one.onetwo'));    

Amazon AWS S3
-------------

Bucket names, keys and secret keys are entered within the Config::s3() array. To then create a bucket you can use: 

      
    <?
    $s3 = \Meagr\s3::init(); 
    $s3->createBucket('testing_bucket_name');

To list your buckets, from the details that have been entered into the Config::s3() method

      
    <?
    $buckets = $s3->listBuckets();
    if (! empty($buckets)) {
        foreach($buckets as $bucket) {
            //do stuff
        }
    }

To test if a bucket exists prior to creating it: 

      
    <?
        $b = 'newish';
        if (! $s3->bucketExists($b)) {
            $s3->createBucket($b);
        }

Check where a bucket is currently stored: 

      
    <?
    echo $s3->getBucketLocation('testing_bucket');

Test if a bucket exists and whether a file has been uploaded before continuing: 

    
    <? 
    $file = PUBLIC_PATH . '/css/bootstrap.css';
    $bucket = 'newish';

    if ($s3->bucketExists($b) and $s3->addFile($file, $bucket)) {
        //do stuff
    }

    //now we can get a list of the bucket contents
    $contents = $s3->getBucket($bucket);

$contents will contain something like the following when printed to the screen: 

     
    <? 
    p($contents);

    //produces
    Array
    (
        [README.text] => Array
            (
                [name] => README.text
                [time] => 1358636456
                [size] => 22845
                [hash] => 05316005760d05b7358214fa4d569aa3
            )

        [bootstrap.css] => Array
            (
                [name] => bootstrap.css
                [time] => 1358680587
                [size] => 120125
                [hash] => 444737d256d564959f9b9abb84aa94cc
            )

        [debug.css] => Array
            (
                [name] => debug.css
                [time] => 1358637424
                [size] => 0
                [hash] => d41d8cd98f00b204e9800998ecf8427e
            )

    )    

To get the contents of a file which is stored on an s3 bucket: 

    
    <? 
    //request the file by its filename and the bucket name
    $file = $s3->getFile('bootstrap.css', $bucket);

To delete a file from a bucket: 

    
    <?
    $s3->deleteFile('bootstrap.css', $bucket);

To copy a file from one bucket to another: 

    
    <?
    $copy = $s3->copyFile($new_filename, $new_bucket, $current_filename, $current_bucket)); 

If the copy was successful then copy will return bool true. 


Caching PHP -> HTML
-------------------

The caching system is very simple. Folders are created automattically if they do not exist and all values are initally set within the site/app config Cache() method. Here, two array keys are set, 'dir' and 'duration'. The Directory will be created from the site path and the 'dir' value. The duration in seconds will control how often a file is recreated. All files are URL specific, so you can cache home, about etc for however long you specify within the 'duration' index. 

Additionally to output caching, there is also css and js contatination to save http requests.

The following is a snippet taken from the Response class and used to check if the current URI has an HTML cache file associated with it, if none is found, or the time since the file was created has passed our time limit, a new file is created and returned to the user. 

      
    <? 
    //init with the default key which is then turned into our current URI
    $cache = Cache::init(); 

    //if cache is available and within the time limit, which we are over ridding to be 1 minute
    if ($cache->setDuration(60)->exists()) { 
        
        //get the cache and assign to a variable which can be echo'ed or manipulated
        $body = $cache->get();

    //if the cache doesnt already exist, or the time limit has expired
    } else {

        //now can set the body and the time limit
        $cache->set($body, 60);
    }    

Concatination of CSS / JS
----------------

The system allows for css files to be added to an array which is passed into the cache class and then combined together. As with the HTML a cache timeout is added and checked each time. The last modified time of each time in the array is also checked to make sure that notne of the files have been updated since the cache was created.

This would go in the head of your template:

      
    <? /*incude the css files in the cache file */ 
    $array = array(
        PUBLIC_PATH . "/css/normalize.css", 
        PUBLIC_PATH . "/css/bootstrap.css", 
        PUBLIC_PATH . "/css/main.css"
    ); ?>
    <link rel="stylesheet" href="<?=\Meagr\Cache::init('css')->concat($array)->file ;?>">

Alternatively, by changing the init('css') to init('js'), the javaScript filename is take from the Config::cache() methods array of values and used. 

    
    <? /*incude the javascript files in the cache file */ 
    $array = array(
        PUBLIC_PATH . "/js/vendor/modernizr-2.6.2.min.js",
        PUBLIC_PATH . "/js/vendor/jquery-1.8.2.js", 
        PUBLIC_PATH . "/js/bootstrap-dropdown.js", 
        PUBLIC_PATH . "/js/bootstrap-tab.js", 
    ); ?>
    <script src="<?=\Meagr\Cache::init('js')->concat($array)->file; ?>"></script>  


Hooks and Hooking
-----------------

Meagr allows you to bind and trigger actions with a simple hooking system. To bind an aciton to be later triggered you must provide the name of the event and then an array containing either the name of the class and method as strings, or an instance of the class and the method name as a string. 

First example using two strings, or the class and method names: 

     
    <?
    \Meagr\Hook::bind('header', array('Home', 'additionalCss'));

Second example providing as array containing an instance of an object and a method name: 

     
    <?
    \Meagr\Hook::bind('header', array(new Home, 'additionalCss'));

The hook can then be triggered within your code:

    
    <?
    $css = Meagr\Hook::trigger('additionalCss');
    foreach($css as $file){
        //do stuff
    }

The result of the trigger will assign an array of data to the variable so must be looped though etc inorder to access the data. Hooks can be bound within the __before() methods of the class and that way given to all methods within the class.


Emails + SMTP
-------------

Meagr has an Email class that allows for SMTP mailing with a simple to use wrapper for the PHPMailer class. Each Email class method returns $this which allows for method chaining

    
    <?
    //initialise the class
    \Meagr\Email::init()

        //add from address, if not provided, the default from the site / environment config will be used
        ->addFrom('paul@prwhitehead.co.uk', 'Paul Whitehead')
    
        //add our subject
        ->addSubject('This is the subject of our email') 
    
        //add an address name => $name, email => $email
        ->addAddress('youraddress@domain.com', 'Paul Whitehead')
    
        //add a multidimentional array of addresses
        // ->addAddresses($array)
    
        //add a bcc'd address
        ->addBCC('prwhi@prw.com', 'Paul Whitehead')
    
        //add a cc's address
        ->addCC('prwhi@prw.com', 'Paul Whitehead')
    
        //add the header html, if this is not provided, the site config is used
        // ->addHeader('/the/file/path/location/of/the/file.php')
    
        //add the footer html, if this is not provided, the site config is used
        // ->addFooter('/the/file/path/location/of/the/file.php')
    
        //add an attachment
        ->addAttachment(PUBLIC_PATH . '/index.php')
    
        //add the content via a callback class/method (methods must echo content)
        ->addContent(array(__NAMESPACE__ . '\Home', 'email'), array('one' => 'hello', 'two' => 'goodbye'))
    
        //dont send but output to the screen, remove this to send live emails
        ->debug(true)
    
        //send and return self
        ->go();

Content can be added through a class method which echo's out content, whcih is then buffered and added to the email content to be sent. Above the addContent() method passes in an array containing a class name with a fully qualified namespace and a method named 'email', a second optional array of values can be passed in which is extracted within the email content. The email() method can look as follows: 

    
    <?
    namespace Modules\Controllers; 

    class Home { 
        public static function email() {
            //if a partial is used via the View class, it must be echoed
            echo \Meagr\View::partial('email-content', func_get_args());
        }        

Some values are set through the site / environment config which is then used to configure defaults as well as SMTP auth values: 

     
    <?
    class Config { 
        public static function email() {
            return array(
                    'header' => MODULE_PATH . '/views/partials/email-header.php', 
                    'footer' => MODULE_PATH . '/views/partials/email-footer.php', 
                    'from-address' => 'prwhitehead@gmail.com', 
                    'from-name' => 'Paul Whithed', 
                    'smtp' => false,
                    'smtp-port' => '465', 
                    'smtp-username' => 'prwhitehead@gmail.com', 
                    'smtp-password' => 'your-password', 
                    'smtp-host' =>'ssl://smtp.gmail.com'
                );
        }

FTP
---

Meagr has a simple FTP interface which allows files to be up and downloaded, deleted, copied and moved, directories to be created and deleted. It is not designed to be all encompassing, but instead to allow the majority of tasks to be performed quickly and easily. 

The FTP class can be used to connect to multiple ftp servers similantiously by adding their details to the Config class in an ftp() method as follows: 

     
    <? 
    class Config {

        static function ftp($connection_name = 'default') {
            return array(
                    'default' => array(
                            //details
                        ), 
                    'backup' => array(
                            //details
                        )    
                )
        }

When the FTP class is instantiated, the $connection_name variable is used to determine which set of connection details to be used. This is the primary method of providing these details, however other details may be added post instantiation. 

To instantiate the FTP class and connect to the server with the default connection details: 

     
    <? 
    $ftp = \Meagr\FTP::init('default');
    $ftp->connect();

Method chaining is also available: 

     
    <?
    $ftp = \Meagr\FTP::init('default')->connect();

To move around the FTP server folder structure the cd() method is available. Pass in the dirctory you wish to navigate to. Each time you change directory the FTP class keeps a train of the present working directory which is accessible through pwd(). So to move from the root server level to a subfolder you can use: 

     
    <? 
    //change to a subfolder called 'wp-content'
    $ftp->cd('/wp-content');

    //return the directory contents as an array
    $contents = $ftp->lsPwd(); 

    //move to another subdirectory called 'db-backup'
    $ftp->cd($ftp->pwd() . '/backup-db');

The FTP class will throw exceptions when it cannot perform an action, so wrap your commands within a try/catch: 

     
    <? 
    try{
        $ftp->mkdir('test');

    } catch(MeagrException $e){
        echo $e->getMessage(); 
    }

...or you could simple do this and use the inDir() method which performs a check on the current directory listing before returning a booleon result: 

    
    <?
    //check for a directory within the present working directory
    if (! $ftp->exists('test')) {

        //and if not found, create it
        $ftp->mkdir('test');
    }

In order to up or download a file to the current connection you can use the following:

    
    <? 
    //push a file to the current working directory
    $ftp->putFile(PUBLIC_PATH . '/css/bootstrap.css');

    //get a file called 'bootstrap.css' from the current working directory and put it in the PUBLIC_PATH
    $ftp->getFile('bootstrap.css', PUBLIC_PATH);

To delete a file from a connection: 

     
    <? 
    //if no /directory/structure is passed, the class assumes that 
    //you wish to delete the file from the present working directory
    $ftp->rmFile('bootstrap.css');

To change the access mode of a file (chmod) you can do the following 

    
    <?
    //a string is required, which will be padded to the required 4 characters
    $ftp->chmod('bootstrap.css', '777'); //equates to 0777

To rename a file, you can simple add the file and the new name and the class will assume the present working directory needs to be added: 

     
    <? 
    $ftp->rename('bootstrap.css', 'b.css');

However rename can be used to move files and folders by passing in a directory to the second parameter.

     
    <? 
    $ftp->rename('bootstrap.css', '/home/bootstrap.css');  

Debug
-----

To allow you to debug apps and log their behaviour Meagr has a flexible Debug class built in and in use. 

     
    <? 
    //init the class and pass in the the collection name, which in this case is 'log'
    \Meagr\Debug::init('log')->add(
                                    //pass in an array of key => value pairs
                                    array(
                                        //our message
                                        'message' => 'Load Controller Before',
                                        //the class that is calling plus the method
                                        'class' => __METHOD__, 
                                        //our status is used to group logs
                                        'status' => 'success', 
                                        //use this to prevent using tons of memory when not in debug mode
                                        'backtrace' => Debug::backtrace())
                                    );    

You can pass in any set of key to value pairs for your logging, which is why the debug class is so flexible. The Debug::output() method will take the keys from the first entry and use them from then on. 

     
    <?
    //to print our log
    echo \Meagr\Debug::init('log')

    //and request only items with a 'success' status, an array of status's can also be passed
    ->output('success');


Languages
---------
By the use of seperate language files, Meagr supports the swapping of language strings via the traditional __() function found in the 'core/helpers.php' file.  

     
    <? 
    echo __('my short sentence');

This function is a wrapper to the OOP method:

    
    <? 
    //pass in our LANG_CODE, which should match the name of the file in our language folder
    \Meagr\Language::init(LAMG_CODE)->get($language_string, $default);

Language files much match the LANG_CODE with an prefixed '.php'. So if LANG_FILE is set to be 'EN', then Meagr will check for an array called $lanaguage within the following locations: 

    
    /* our default location */
    <SITE_PATH>/meagr/modules/config/en.php

    /* also checking for environmental locations as well as subdomain apps */
    <SITE_PATH>/meagr/modules/config/development/language/en.php    

Within our 'en.php' file we need to simply create an array of key => value pairs, the keys match the the $language_string from above. 

    
    <?
    $language = array(
            'hello' => 'hello, welcome to my site', 
            'description' => 'description of my site'
        );    


Feedback
--------
The feedback class is available to store session data about form feedback or anything that needs to be recalled when a page is refreshed or submitted elsewhere. 

    
    <? 
    //initialise the type of feedback we want to give
    $feedback = \Meagr\Feedback::init('errors');

    //set the feedback message for the group 'form' this would be general feedback 
    $feedback->set('Sorry the form was invalid', 'form'); 

    //add an email input error message
    $feedback->set('The email address was invalid', 'email_address'); 

    //check to see if we have feedback for the 'form' group
    if ($feedback->exists('form')) {

        //show() loops through the 'form' group array and outputs a string of div's
        echo $feedback->show('form');
    }

    //check to see if we have feedback for the 'email_address' group
    if ($feedback->exists('email_address')) {

        //show() loops through the 'email_address' group array and outputs a string of div's
        echo $feedback->show('email_address');
    }    


Images
------

Create a simple thumbnail 100px width by 80px height: 

    
    <?
    \Meagr\Image::init()
                ->open(PUBLIC_URL . '/test.jpg')
                //create a new image, passing width / height
                ->thumb(100, 80)    
                //save to disk and return the locaiton of the new image
                ->save();   

    //we can how echo out our image
    echo '<img src="' . $t . '" />';
        
Most of the available options are listed below: 

    
    <?
    $t = \Meagr\Image::init()

        //open a file to be read
        ->open(PUBLIC_PATH . '/test.jpg')
    
        //set the cache limit or false
        ->cache(60)
    
        //set width / height
        ->resize(500, 400)
    
        //set the width and height as well as the crop coordinates
        ->crop(100, 100, 80, 80)
    
        //set angle
        ->rotate(180)
    
        //set flatten to false for gif/png
        ->flatten(false)        
    
        //set quality
        ->quality(10)
    
        //override resolution for X and Y - NOT supported in GD Library
        ->resolutionX(100)
        ->resolutionY(100)          
    
        //write the file, return the saved location
        ->save();

    //we can how echo out our image
    echo '<img src="' . $t . '" />';

To Turn the contents of a directory into a collage, the following can be used: 

    
    <?
    //loop through our directory and record the images
    foreach (glob(PUBLIC_PATH . '/images/*.png') as $path) {
        $array[] = $path;
    }

    $t = \Meagr\Image::init()
    
        //we have to set the name before the rest of the functions run
        ->setSaveFileName(PUBLIC_PATH . '/collage.jpg')
    
        // cache the image for ever, if this was an int (ie 600) the image would be rebuild after an hour
        ->cache(true)
    
        //create a new image, passing width / height
        ->create(1140, 150) 
    
        //add filepaths to be added to the collage and each ones size
        ->collage($array, 95, 75)
    
        //save to disk
        ->save();

        //we can echo our the class and it gives us either the saved image or if 
        //that hasnt been created, the original image back
        echo '<img src="' . $t . '" />';
          
