CHANGELOG for 0.0.43x

Scripts
-------------------------------------------------------------------------------
app/app-header.php  used to decouple user specific changes from appfuel. This
                    script is always required and must be used.

www/index.php       refactored to use the app-header and is more adaptable to 
                    change

Modules
-------------------------------------------------------------------------------

Kernel:
    * New       Task module holding task handler and all kernel startup tasks
    * Removed   AppHandler now in Appfuel\App\AppHandler

Kernel\Mvc:
    * Removed    MvcContext now in App\AppContext
    * Removed    AppInput   refactored to Http and Console Modules
    * Removed    RequestUri  replaced by routing system
    * Removed    Route* all routing moved to Appfuel\Kernel\Route
    * Refactor   MvcDispatcher to Dispatcher and made static, also uses
                 Route Specification objects 
    * Refactor   MvcFront to FrontController
    * New        MvcController improved base controller
    * Deprecated MvcAction  now using MvcController
    * Refactor   InterceptFilter using Appfuel\App\AppFactory to create new 
                 Context objects
Routing:
    *   Routing namespace moved from Appfuel\Kernel\Mvc to Appfuel\Kernel\Route
    *   MvcRouteDetail Facade removed and replaced by strategy pattern where
        route spec objects are created by RouteFactory and stored in the
        RouteRegistry
    *   Regex matching now implemented with Router and RouteCollector
    *   bin/build-routes added to build routes

App:
    * New Namespace Appfuel\App used to hold application specific objects like:
    * AppContext AppHandler [WebHandler, ConsoleHandler], etc..
    * New AppContext refactored from Appfuel\Kernel\Mvc\MvcContext, still
      implements Appfuel\Kernel\Mvc\MvcContextInterface, but no longer requires
      an AppInputInterface, this is now used defined but defaults to 
      Appfuel\Http\HttpInputInterface for http requests and 
      Appfuel\Console\ConsoleInputInterface for console requests
    * New AppPath used to decouple the directories and files from the appfuel
    * New AppView used to hold view data, refactored out of AppContext
    * New AppAcl used to hold acl codes, refactored out of AppContext
    * New ConfigHandler, refactored from Appfuel\Kernel\ConfigLoader
    * Refactored AppFactory for bettern creation support
    
Http:
    * New HttpInput refactored from AppInput which can from 
      Appfuel\Kernel\AppInput

Console:
    * New ArgParser used parse commandline options/args into a known data
      structure
    * New ConsoleInput refactored from App\AppInput which came from 
      Appfuel\Kernel\AppInput

Regex:
    * New RegexPatternInterface which filters, validates and converts raw
      raw regex patterns to properly escaped regexs for forward slash 
      delimiters. This is used by the Appfuel\Kernel\Route\RouteCollector

Validate:
    * Refactor validation module better support for the ValidationHandler
    * Validation objects are now mapped and stored in a ValidationFactory
    * The following filters are available:
        Name        Class
        -----       ---------------------------------------------
        int         Appfuel\Validate\Filter\IntFilter
        bool        Appfuel\Validate\Filter\BoolFilter
        string      Appfuel\Validate\Filter\StringFilter
        email       Appfuel\Validate\Filter\EmailFilter
        ip          Appfuel\Validate\Filter\IpFilter
        float       Appfuel\Validate\Filter\FloatFilter

DataStructure:
    * New  ArrayData a better object wrapper for array data
    * Deprecated    DictionaryInterface
