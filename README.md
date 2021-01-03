# Showcase Micro Framework
<p align="center">
  <img src="https://github.com/MohKamal/php-showcase-template/blob/master/icon.png?raw=true">
</p>
A micro mini php framework to make one page or no back-end web site, like a presentation with no models

# PLEASE THIS PROJECT IS NOT SAFE FOR PRODUCTION, ONE PRESENTATION PAGE WITH NON DATA IS FINE, BUT DATABASE AND AUTH, PLEASE USE A FRAMEWORK LIKE Laravel, Symfony, Slim...

## Routes
```php  
    $router->get('/path', function () {
        /* Code to execute */
        return self::response()->redirect('login');
        /* Another */
        return HomeController::Home();
    });

    $router->post('/path',  function ($request) {
        return HomeController::Contact($request);
    });
```

## Validator
To check if request body has a key, or any array has a key, use the validator.

```php  
    /**
     * Return the video single page
     */
    static function Play($request){
        if(Validator::validate($request->getBody(), ['id'])){
            $url = Search::searchVideoById($request->getBody()['id']);
            return self::response()->view('App/video', array([
                'url' => $url
                ]));
        }

        return self::response()->redirect('/errors/404');
    }
```

To get more validation and verification use the function validation, it has options : required, string, numeric, email, phone, min lenght, max lenght

```php  
        /**
         * Store new user
         */
        static function store($request){
            $errors = Validator::validation($request->getBody(), [
                'email' => 'required | email', 
                'password' => 'required | min:8', 
                'username' => 'required | min:3 | max:10 | string'
                ]);
            if (empty($errors)) {
                $user = new User();
                $user->bcrypt($request->getBody()['password']);
                $user->username = $request->getBody()['username'];
                $user->email = $request->getBody()['email'];
                $user->save();

                //Log the user
                Auth::loginWithEmail($user->email);
                return self::response()->redirect('/');
            }
            return self::response()->view('Auth/register', array('errors' => $errors));
        }
```
## Response
Response is an object used to make user responses more easier.
There is three response : view, redirect and json

### Response View
To return a view, use view response
```php  
    $router->get('/path', function () {
        return self::response()->view('App/welcome');
    });
```

### Response Redirect
To redirect a user, use a redirect response
```php  
    $router->get('/path', function () {
        return self::response()->redirect('/contact-us');
    });
```

### Response Json
To return any object as json response, use response json
```php  
    $router->get('/path', function () {
        $data = DB::model('User')->select()->where('active', 1)->get();

        return self::response()->json($data);
    });
```

### Json resource
When return a model as json, the hidden properties aslo are send out there, to prevent that from happening, you can use JsonResource object.
To create one, call the createJsonResource command : 

```bash
php creator createJsonResource resource_Name
```
And then, in the handle function, specify the data returned, or leave it blank, only the database columns will be returned.
```php
    use \Showcase\Framework\HTTP\Resources\JsonResource;
    class UserResource extends JsonResource{       

        /**
         * Init the resource with model
         * @return json
         */
        public function __construct($obj){
            JsonResource::__construct($obj);
        }

        /**
         * Set the properties to return
         * @return array
         */
        function handle(){
            return [
                'Identification' => $this->id,
                'user_email' => $this->email,
                'parent_id' => "er15cc52",
            ];
        }
    }
```

```php
    use \Showcase\JsonResources\UserResource;

    class HomeController extends BaseController{
        static function Index(){
            $user = DB::model('User')->select()->where('id', 5)->first();
            return self::response()->json(new UserResource($user));
        }
    }
```
will return :
```json
    {
        "Identification":"5",
        "user_email":"email@gmail.com",
        "parent_id":"er15cc52"
    }
```
#### Json Resource for array
To return an array of object as json, mapped datan you use the static function array of JsonResource.
```php
    use \Showcase\JsonResources\UserResource;

    class HomeController extends BaseController{
        static function Index(){
            $users = DB::model('User')->select()->limit(15)->get();
            return self::response()->json(UserResource::array($users));
        }
    }
```

will return : 
```json
[
    {
        "Identification":"1",
        "user_email":"email@gmail.com",
        "parent_id":"er15cc52"
    },
    {
        "Identification":"2",
        "user_email":"another@gmail.com",
        "parent_id":"er15cc52"
    },
    {
        "Identification":"3",
        "user_email":"rtrtrtrt@email.com",
        "parent_id":"er15cc52"
    },
    {
        "Identification":"7",
        "user_email":"jao@gmail.com",
        "parent_id":"er15cc52"
    }
]
```

### Response codes
To return codes use :
* 404 : response()->notFound()
* 200 : response()->OK()
* 403 : response()->unauthorized()
* 500 : response()->internal()

## Views

Every view is in the Views folder, you can create a subfolders and add your views files in there. Example : 
* Views 
  - Home
    - Welcome.view.php
* Contact
  - Contact.view.php
    - About.view.php

### Attention

Your views files need to end with .view.php, so they can be found, if not, you will get a 404 status

### include

To include a view inside another, simply use @include tag
```html

<!-- Include footer to page -->
<body>
    @include("App/footer")
</body>
```

### Extend

Extend is used to call a layout page. for example, you have same nav and footer, so you create a page with nav and footer and html structure and you call it main.view.php

Every page you call gonna extend from the main view

### Attention

You have to put the @render() tag inside the main view in the position where you with the child view would display



```html
<!-- main.view.php -->
<body>
    <nav></nav>
    @render()
    <footer></footer>
</body>

```

```html
<!-- contact.view.php -->
@extend("App/main")
<body>
    <!-- You page Code -->
</body>
```

### Execute php inside a view

To execute a custom php insdie a view, you can use the php function

```html
<!-- contact.view.php -->
@extend("App/main")
<body>
    @php
        $var = 1;
        @display "this is a var $var" @enddisplay;
    @endphp
    <!-- You page Code -->
</body>
```

### Foreach and for Loops
To execute a loop without using the @php function, use the @foreach and @for loops.
```html
<!-- contact.view.php -->
@extend("App/main")
<body>
    <!-- Foreach loop -->
    @foreach(\Showcase\Models\User::toList() as $user){
        if($user->isAdmin)
            @display "<p>$user->name</p>" @enddisplay;
    }@endforeach

    <!-- For loop -->
    @for($i=0; $i < 5; $i++){
        @display "number $i" @enddisplay;
    }@endfor
    <!-- You page Code -->
</body>
```

#### Note
Use natice php code inside the loops.
Don't forget the brakets '{}' inside the @foreach and @endforeach or the @for and @endfor, also @if and @else or @endif

### Condition If
If you want to check a condition without the php function, use the @if function.
```html
<!-- contact.view.php -->
@extend("App/main")
<body>
    <!-- Simple if -->
    @if($show){
        @display "<p>Show it!</p>" @enddisplay;
    }@endif

    <!-- If with Else -->
    @if($show){
        @display "<p>Show it!</p>" @enddisplay;
    }@else{
        @display "<p>Not Showing it!</p>" @enddisplay;
    }@endif
    <!-- You page Code -->
</body>
```

### Display to view
To display a variable or a function result, use @display function, inside @php, @foreach, @for and @if statement or in plain html.
```html
<!-- contact.view.php -->
@extend("App/main")
<body>
    @if($show){
        <!-- Display -->
        @display '<p>Show it!</p>' @enddisplay
    }@endif
    <!-- Display -->
    @display '<p>Out Side!</p>' @enddisplay
    
        <!-- Display -->
    @display $number + 5 @enddisplay
</body>
```

To display a simple variable sent from controller, use only the variable name
```php
return self::response()->view('App/welcome', array(
                            'title' => 'post 1',
                            ));
```
```html
<!-- post.view.php -->
@extend("App/main")
<body>
    <p>$title</p>
</body>
```
## Send variables from Controller to view

To send a variable from controller to a view, add an array to the view method of the controller.

```php  
    /**
     * Return the video single page
     */
    static function Play($request){
        if(Validator::validate($request->getBody(), ['id'])){
            $url = Search::searchVideoById($request->getBody()['id']);
            return self::response()->view('App/video', array([
                'url' => $url
                ]));
        }

        return self::response()->redirect('/errors/404');
    }
```

## Styles & Javascript & other Files

To include files from the resources folder to your views, you need to use a tag :

Assets : Main folder for all the resources, even images.

Styles : CSS folder.

Scripts : Javascript folder.

```html
<!-- Adding resources url to style -->
<link href="@{{Styles}}main.min.css">
```

```html
<!-- Adding resources url to image -->
<img src="@{{Assets}}images/logo.png" class="img-fluid" alt="logo"/>
<!-- Adding Base url to a link tag -->
<a href="@{{Base}}/Contact">Contact-Us</a>
```

## Controllers and Models

### Attention
Please use Good Frameworks for huge projects, for more security and easy project management

To create a new controller use php command line

```bash
php creator createController Controller_Name
```
Example

```bash
php creator createController ContactController
```

To create a new model use php command line

```bash
php creator createModel Model_Name
```
Example
```bash
php creator createModel ContactModel
```

### Save model to database

Showcase use SQLite/MySql database, you can set-up at appsettings.json.

Database will not be initialized if not set to 'true' at appsettings.json file (USE_DB parametre).

### Warning
MySql it not fully test, if you find any bugs or error, please repport them to be fixed.

```json
{
    "USE_DB": "true",
    "DB_HOST": "your_file_name.db",
    "DB_TYPE": "SQLite",
}

{
    "USE_DB": "true",
    "DB_HOST": "localhost",
    "DB_TYPE": "MySql",
    "DB_NAME": "showcase_db",
    "DB_USERNAME": "root",
    "DB_PASSWORD": "",
    "DB_PORT": "3306",
}
```

To create new object from model, simple : 

```php
$model = new Model();
$model->param = "value";
$model->param = 10;
$model->save();
```

When using the save function, the model data will be stored in the database.

### Update model to database

To update an exisitng model in the database, you need to get it first :

```php
use \Showcase\Framework\Database\DB;
$model = DB::model('Model')->select()->where('id', 5)->first();
$model->param = "new value";
$model->save();
```

When using the save function on exising model in database, the new data will be updated in the database

### Delete model from database

To delete model, use the delete function : 

```php
$model = DB::model('Model')->select()->where('id', 5)->first();
$model->delete();
```
If you are using the soft delete columns, the row will not be removed from the database, only deleted_at and active will be updated.

If you not using the soft delete columns, the row will removed for good.

## Get model/Array of models from database

### By any Column

To get one model from database by any columns/properties you need, use where function:

```php
$model = DB::model('Model')->select()->where('column', $value)->first();
Log::print($model->paramName);
```

### Get Array of objects

To get array of models, you gonna use the static function toList() : 

```php
$models = DB::model('Model')->select()->where('column', $value)->get();
Log::print($models[0]->paramName);

```
### Get Array of Objects with trash

To get array of models, with one, or more conditions, you gonna use the static function toList() : 

```php
$models = DB::model('Model')->select()->where('column', $value)->withTrash()->get();
Log::print($models[0]->paramName);
```

## Database object
For an easy search and query build, use the DB object

```php
use \Showcase\Framework\Database\DB;
use \Showcase\Framework\IO\Debug\Log;

$users = DB::model('User')->select()->where('email', '%@gmail%', 'LIKE')->get();
foreach($users as $user)
    Log::print($user->email . " | " . $user->username);

```

To select from a table, use table function, and give the table name
```php
$users = DB::table('users')->select()->where('email', '%@gmail%', 'LIKE')->get();
```
This will return an array of data.

To get an array of object for a model, use the model function, and give the model Name
```php
$users = DB::model('User')->select()->where('email', '%@gmail%', 'LIKE')->get();
```

*** To get an array of data use get() function, to get one object, use first() function

* functions
    * table($name) : table name to select from
    * model($name) : model to convert data to after fetching it
    * select($columns) : you can specify the columns to select in case you are using the table function
        ```php
        $users = DB::table('users')->select('username', 'email')->where('email', '%@gmail%', 'LIKE')->get();
        ```
        if you are using model function instead of table, the select columns with not be applied
    * where($column, $value, $condition) : add where condition to you query, the condition value is '=' by default
    * orWhere($column, $value, $condition) : add or condition to you query, the condition value is '=' by default
    * limit($number) : to limit the query result
    * distinct($column) : get distinct result for all columns by default, or to specific column
    * count($expression) : get all columns count by default, or an expression/column
    * first() : get the first result
    * get() : get an array of results
    * withTrash() : in case you are using soft delete, with this function, also the deleted records will be selected
    * insert($columns) : insert to model/table at database
        ```php
        DB::table('users')->insert(['name' => 'test', 'email' => 'test@email.com'])->run();
        ```
    * update($columns) : update a model/table columns
        ```php
        DB::table('users')->update(['name' => 'test1'])->where('id', 12)->run();
        ```
    * delete() : delete a record in the database, this function don't take in concidiration the soft delete, to use the soft delete, use the delete function of the models
        ```php
        DB::table('users')->delete()->where('id', 12)->run();
        ```
    * run() : call this function when using the insert, update and delete functions, it return the numbers of lines affected

## Migration

To create a migration you need to use the commande line on the root folder.

```bash
php creator createMigration migration_name
```

A file will be created at Database\Migrations.

To edit the columns, you open the migration file and edit it.

Column Type :  int(), string(), double(), blob(), bool(), datetime()
Column conditions : nullable(), autoIncrement(), primary(), notnull(), default($value)

```php
    /**
     * Migration details
     */
    function handle(){
        $this->name = 'MigrationName';
        $this->column(
            Column::factory()->name('id')->int()
        );
        $this->column(
            Column::factory()->name('name')->string()
        );
        $this->column(
            Column::factory()->name('phone')->string()->nullable()
        );
        $this->timespan(); //created_at and updated_at columns
    }
```
updated_at columns will be updated everytime you update your model.
### Soft delete

To add soft delete columns, add the function softDelete().

```php
    /**
     * Migration details
     */
    function handle(){
        $this->name = 'MigrationName';
        $this->column(
            Column::factory()->name('id')->int()
        );
        $this->column(
            Column::factory()->name('name')->string()
        );
        $this->column(
            Column::factory()->name('phone')->string()->nullable()
        );
        $this->timespan();
        //Soft delete columns will be added
        $this->softDelete();
    }
```

To create those migration, you need to execute another command line.

```bash
php creator migrate
```

## Session

To display a message using $_SESSION, or to save a variable in the $_SESSION, to use it in different Controllers, you can use the session object

```php
    use \Showcase\Framework\Session\Session;
    //Store value
    Session::store('filter', 'drinks');

    //Get the value of filter
    echo Session::retrieve('filter'); //If filter dosen't exist, a null will be returned
```

## Session Alert

To display a message using $_SESSION, you can use the sessionAlert object

```php
    use \Showcase\Framework\Session\SessionAlert;
    //Store value
    SessionAlert::create('Email not found in the database', 'error');

    // There is four stats to the message : info, error, waring and success
    // info is the default

    //To remove the message
    SessionAlert::clear();

```
```html
<!-- To show the SessionAlert  -->
@sessionAlert()
<a href="@{{Base}}/Contact">Contact-Us</a>
```
## Authentication
To use Authentication, there is one simple mecanisme in showcase to use a simple user with password hashing and saving data in session.
To create the model and controller with the views run the command : 

```bash
php creator auth
```
After, you have to run the migrate command to create the user table in the database.

```bash
php creator migrate
```
And finaly, add the Auth routes to the web file.

```php
namespace Showcase {

    //Other includes here
    use \Showcase\Framework\HTTP\Gards\Auth;

    $router  = new Router(new Request);

    //Your routes

    //Auth routes
    Auth::routes($router);
}
```

Now you have, login and register controllers, with login and register view at Views/Auth.

You can use Auth object any where, to check if user is logged, or to get the current logged user.
```php
    //Get the user object
    Auth::user(); //return \Showcase\Models\User object

    //Get the user username
    Auth::username(); //return string

    //you can change the property name to return from username() function
    Auth::username('lastname'); //return string


    //Check if the user is logged
    if(Auth::check())
        Log::console("User logged " . Auth:: username());
    else
        Log::console("Please login!!");

    //Or
    if(Auth::guest())
        Log::console("Please login!!");
    else
        Log::console("User logged " . Auth:: username());

```
## Debug

To print out data to a log file, use the Log Class.

```bash
use \Showcase\Framework\IO\Debug\Log;

Log::print("Message to print in log file");
Log::console("Message to print in the console");
```

## Run it

go to the public folder with the command line

```bash
cd /public
```

And run the php server

```bash
php -S localhost:8000
```