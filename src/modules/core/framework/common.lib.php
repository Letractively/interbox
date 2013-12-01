<?php

/**
 * The main library for InterBox Framework.
 *
 * It provides a set of functions for resource management that enables multiple
 * themes and languages, and a Page-Process-Box model that separates logics from
 * views, with the cache module in the Core to help increase performance.
 * Note that the library is dependent on InterBox Core.
 * 
 * @version 0.9.20130721
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @license MIT License
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.framework
 */
//-----------------------------------------------------------
// paths
//-----------------------------------------------------------
/**
 * gets a relative path to a resource file in the system level
 *
 * @param string $resname   a resource file name in the specified path
 * @param string $path      a path relative to the configured system root
 * For example,
 * if /var/www is the configured system root
 * and the resource file is /var/www/scripts/calendar.js,
 * the path is "scripts".
 * @return string   gives a path to the resource file relative to the
 * configured system root, so it can be used in html; may give an empty string
 * indicating that the resource file does not exist
 */
function GetSysResPath($resname, $path) {
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = constant('IBC1_SYSTEM_ROOT');
    if (!empty($path))
        $path .= '/';
    $sysrespath = $path . $resname;
    if (is_file($syspath . $sysrespath))
        return $sysrespath;
//    return '';
    throw new Exception('path was not found: ' . $syspath . $sysrespath);
}

/**
 * gets a relative path to a resource file in the theme level
 *
 * @param string $resname   a resource file name
 * @param string $restype   images, stylesheets, scripts
 * @param int $themeid  optional
 * @return string
 */
function GetThemeResPath($resname, $restype, $themeid = NULL) {
    //set default theme id
    if (empty($themeid))
        $themeid = GetThemeID();

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = constant('IBC1_SYSTEM_ROOT');

    //get relative path
    $sysrespath = $restype . '/' . $resname;
    $themerespath = 'themes/' . $themeid . '/' . $sysrespath;

    //validate
    if (is_file($syspath . $themerespath))
        return $themerespath;
    if (is_file($syspath . $sysrespath))
        return $sysrespath;

    //not found
    throw new Exception('path was not found: ' . $syspath . $sysrespath);
//    return '';
}

/**
 * gets an absolute path to a directory where cache files are stored
 *
 * @param bool $makedir  optional
 * @param int $themeid  optional
 * @param string $lang   optional
 */
function GetCachePath($makedir = FALSE, $themeid = NULL, $lang = NULL) {
    //set default values
    if (empty($lang))
        $lang = GetLang();
    $lang = strtolower($lang);
    if (empty($themeid))
        $themeid = GetThemeID();
    $themeid = intval($themeid);

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = constant('IBC1_SYSTEM_ROOT');

    //get relative path
    $cachepath = "cache/$themeid/$lang";

    //validate
    if (is_dir($syspath . $cachepath)) {
        return $syspath . $cachepath;
    } else if ($makedir) {
//        if (!is_dir($syspath . 'cache/' . $themeid))
//            mkdir($syspath . 'cache/' . $themeid);
        if (mkdir($syspath . $cachepath, 0777, TRUE))
            return $syspath . $cachepath;
    }

    //not found
    throw new Exception('path was not ' . ($makedir ? 'created' : 'found') . ': ' . $syspath . $cachepath);
//    return '';
}

/**
 * gets an absolute path to a directory where template files are stored
 *
 * @param string $filename
 * @param string $classname
 * @param int $themeid
 * @param string $lang
 * @return string
 */
function GetTplPath($filename, $classname = NULL, $themeid = NULL, $lang = NULL) {

    if (empty($themeid))
        $themeid = GetThemeID();
    $themeid = intval($themeid);

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = constant('IBC1_SYSTEM_ROOT');

    if (!empty($classname))
        $classname.='/';
    else
        $classname = '';

    // get language code
    if ($lang == NULL)
        $lang = GetLang();
    else
        $lang = strtolower($lang);

    $theme = strval($themeid);
    while (TRUE) {
        if ($theme == '0') {
            // system-level
            $sysrespath = "{$syspath}templates/{$classname}";
            $tplpath = $sysrespath . $lang . '/' . $filename;
            if (is_file($tplpath))
                return $tplpath;

            // a language-neutral file
            $tplpath = $sysrespath . $filename;
            if (is_file($tplpath))
                return $tplpath;

            // not fully supported, needs aid from the default language
            $dlang = strtolower(constant('IBC1_DEFAULT_LANGUAGE'));
            if ($lang != $dlang) {
                if ($theme != '0') {
                    // theme-level
                    $themerespath = "{$syspath}themes/{$theme}/templates/{$classname}";
                    $tplpath = $themerespath . $dlang . '/' . $filename;
                    if (is_file($tplpath))
                        return $tplpath;
                }
                // system-level
                $tplpath = $sysrespath . $dlang . '/' . $filename;
                if (is_file($tplpath))
                    return $tplpath;
            }
            return '';
        } else {
            // theme-level
            $themerespath = "{$syspath}themes/{$theme}/templates/{$classname}";
            $tplpath = $themerespath . $lang . '/' . $filename;
            if (is_file($tplpath))
                return $tplpath;

            // a language-neutral file
            $tplpath = $themerespath . $filename;
            if (is_file($tplpath))
                return $tplpath;

            $theme = '0';
        }
    }
}

//-----------------------------------------------------------
// themes
//-----------------------------------------------------------
/**
 * gets theme id.
 *
 * It can be user preferred one or the default one.
 * @return int
 */
function GetThemeID() {
    $key = 'IBC1_ThemeID';

    if (isset($GLOBALS[$key]))
        return $GLOBALS[$key];

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = constant('IBC1_SYSTEM_ROOT');

    $id = intval(strCookie('ThemeID'));

    if ($id == 0 || is_dir("{$syspath}themes/{$id}"))
        $GLOBALS[$key] = $id;
    else // non-existent
        $GLOBALS[$key] = 0; //default & preserved theme id

    return $GLOBALS[$key];
}

/**
 * sets an user preferred theme.
 * 
 * @param int $id 
 */
function SetThemeID($id) {
    $id = intval($id);
    $GLOBALS['IBC1_ThemeID'] = $id;
    setcookie(IBC1_PREFIX . '_ThemeID', $id, time() + 7 * 24 * 60 * 60);
}

/**
 * gets all themes.
 * 
 * Theme information is stored in "themes.conf.php" in the directory 
 * of the web system named "themes".
 * @return array
 * <code>
 * array(
 *      1=>'theme name',// the default theme
 *      2=>'theme 2',// another theme
 *      //...
 * )
 * </code> 
 */
function GetThemes() {
    return include GetSysResPath('themes.conf.php', 'themes');
}

//-----------------------------------------------------------
// languages
//-----------------------------------------------------------
/**
 * gets language code according to http request and the system default setting.
 *
 * @return string
 */
function GetLang() {
    $key = 'IBC1_Language';

    //not first time
    if (isset($GLOBALS[$key]))
        return $GLOBALS[$key];

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = constant('IBC1_SYSTEM_ROOT');

    //by preference
    $lang = strtolower(strCookie('Language'));
    if ($lang != '' && is_dir("{$syspath}lang/{$lang}")) {
        $GLOBALS[$key] = $lang;
        return $lang;
    }

    //by browser
    $accepted = &$_SERVER['HTTP_ACCEPT_LANGUAGE'];
    if (isset($accepted)) {
        $l = explode(';', $accepted);
        $l = explode(',', $l[0]);
        foreach ($l as $lang) {
            $lang = strtolower($lang);
            if (is_dir("{$syspath}lang/{$lang}")) {
                $GLOBALS[$key] = $lang;
                return $lang;
            }
            $pos = strpos($lang, '-');
            if ($pos > 0) {
                //e.g. zh-cn
                $lang = substr($lang, 0, $pos);
                if (is_dir("{$syspath}lang/{$lang}")) {
                    $GLOBALS[$key] = $lang;
                    return $lang;
                }
            }
        }
    }

    //default
    $GLOBALS[$key] = strtolower(constant('IBC1_DEFAULT_LANGUAGE'));
    return $GLOBALS[$key];
}

/**
 * sets an user preferred language.
 * 
 * @param string $lang e.g. en, or zh-cn
 */
function SetLang($lang) {

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = constant('IBC1_SYSTEM_ROOT');

    if (is_dir("{$syspath}lang/{$lang}")) {
        $GLOBALS['IBC1_Language'] = $lang;
        setcookie(IBC1_PREFIX . '_Language', $lang, time() + 7 * 24 * 60 * 60);
    }
}

/**
 * gets all languages.
 * 
 * Language information is stored in "languages.conf.php" in the directory 
 * of the web system named "lang".
 * @return array
 * <code>
 * array(
 *      'en'=>'English',
 *      'zh-cn'=>'Simplified Chinese',
 *      //...
 * )
 * </code> 
 */
function GetLanguages() {
    $key = 'IBC1_Languages';
    if (isset($GLOBALS[$key]))
        return $GLOBALS[$key];
    $GLOBALS[$key] = include GetSysResPath('languages.conf.php', 'lang');
    return $GLOBALS[$key];
}

function GetLangName() {
    $l = GetLanguages();
    $code = GetLang();
    return $l[$code];
}

/**
 * reads words in a certain language with a given key
 *
 * @param string $key   a short word that identifies a longer expression
 * in some language
 * @param string $group optional, designed to reduce loading unused data
 * @return string
 */
function GetLangData($key, $group = NULL) {

    // read language code
    $lang = &$GLOBALS['IBC1_Language'];
    if (!isset($lang))
        $lang = GetLang();

    // data group
    if (empty($group))
        $group = $lang;
    else
        $group = $lang . '_' . $group;

    $data = &$GLOBALS['IBC1_LangData'];
    if (!isset($data) || !isset($data[$group]))
        $data[$group] = include GetSysResPath("$group.lang.php", "lang/$lang");

    if (isset($data[$group][$key]))
        return $data[$group][$key];

    return $key;
}

//-----------------------------------------------------------
// configurations
//-----------------------------------------------------------

LoadIBC1Class('ICacheReader', 'cache');
LoadIBC1Class('PHPCacheReader', 'cache.phpcache');

/**
 * reads a value associated with the given key in the config file
 *
 * @param string $key
 * @param string $group optional, designed to reduce loading unused data
 * @return mixed
 */
function GetConfigValue($key, $group = NULL) {
    if (empty($group))
        $group = 'conf_main'; //default group
    else
        $group = 'conf_' . $group;

    $reader = &$GLOBALS['IBC1_ConfigReader'];
    if (!isset($reader) || !isset($reader[$group])) {
        $reader[$group] = new PHPCacheReader("conf/$group.conf.php", $group);
    }
    return $reader[$group]->GetValue($key);
}

//-----------------------------------------------------------
//template processing
//-----------------------------------------------------------
/**
 * mappings from formatting types defined in template fields to 
 * formatting functions
 * 
 * @global array $GLOBALS['IBC1_TEMPLATE_FORMATTING']
 * @name $IBC1_TEMPLATE_FORMATTING 
 */
$GLOBALS['IBC1_TEMPLATE_FORMATTING'] = array(
    'html' => 'filterhtml',
    'text' => 'text2html',
    'jsstr' => 'toScriptString',
    'urlparam' => 'urlencode',
    'int' => 'intval',
    'float' => 'floatval',
    'date' => 'FormatDate',
    'time' => 'FormatTime',
    'datetime' => 'FormatDateTime'
);

function FormatTplVar($datatype, $value) {
    $funclist = &$GLOBALS['IBC1_TEMPLATE_FORMATTING'];
    if (isset($funclist[$datatype])) {
        return call_user_func($funclist[$datatype], $value);
    }
    return $value;
}

/**
 * format fields for a template
 * 
 * @see $GLOBALS['IBC1_TEMPLATE_FORMATTING']
 * @param array $vars 
 */
function FormatTplFields(&$vars) {
    foreach ($vars as $varname => &$varvalue) {
        $pos = strpos($varname, '_');
        if ($pos) {
            $func = substr($varname, 0, $pos);
            $funclist = &$GLOBALS['IBC1_TEMPLATE_FORMATTING'];
            if (isset($funclist[$func])) {
                $varvalue = call_user_func($funclist[$func], $varvalue);
            }
        }
    }
}

/**
 * reads a template file and gets content of it
 *
 * @param string $tplname
 * @param string $classname
 * @param int $themeid
 * @param string $lang
 * @return string
 */
function GetTemplate($tplname, $classname = NULL, $themeid = NULL, $lang = NULL) {
    $path = GetTplPath($tplname . '.tpl', $classname, $themeid, $lang);
    if ($path == '')
        return '';
    return file_get_contents($path);
}

//-----------------------------------------------------------
// caching related
//-----------------------------------------------------------


LoadIBC1Class('ICacheProvider', 'cache');

class DefaultBoxCacheProvider implements ICacheProvider {

    private $writer;
    private $reader;

    /**
     *
     * @param string $group
     * @return ICacheWriter 
     */
    public function GetWriter($group) {
        if (!isset($this->writer[$group])) {
            $filepath = GetCachePath(TRUE) . "/{$group}.cache.php";
            LoadIBC1Class('ICacheWriter', 'cache');
            LoadIBC1Class('PHPCacheWriter', 'cache.phpcache');
            $this->writer[$group] = new PHPCacheWriter($filepath, $group);
        }
        return $this->writer[$group];
    }

    /**
     *
     * @param string $group
     * @return ICacheReader 
     */
    public function GetReader($group) {
        if (!isset($this->reader[$group])) {
            $filepath = GetCachePath(TRUE) . "/{$group}.cache.php";
            LoadIBC1Class('ICacheReader', 'cache');
            LoadIBC1Class('PHPCacheReader', 'cache.phpcache');
            $this->reader[$group] = new PHPCacheReader($filepath, $group);
        }
        return $this->reader[$group];
    }

}

/**
 * checks data version with the current up-to-date version stored somewhere
 * in the system for the necessity to refresh the data to be cached
 *
 * @param int $dataversion
 * @param int $current
 * @return boolean
 */
function IsCachedDataOld($dataversion, $current) {
    if ($dataversion < 1)
        return FALSE;
    return $current > $dataversion;
}

/**
 * designed for <code>$cacheVersion</code> in Box and BoxGroup
 *
 * It is useful when there are a couple of versions that affect the views
 * created by a Box.
 * For example, in a box view, there is a list of titles whose data source
 * has a version A, whereas a setting data item with a version of B controls
 * the number of the titles in the list. Therefore, the
 * <code>$cacheVersion</code> for this box view should be
 * <code>AddVersions(A,B)</code>.
 * @param int $version1
 * @param int $version2
 * @return int
 */
function AddVersions($version1, $version2) {
    if ($version1 > $version2)
        return $version1;
    return $version2;
}

/**
 * designed for <code>$cacheGroup</code> and <code>$cacheKey</code>
 * in Box and BoxGroup
 *
 * @param string $name      a name for cached data group or key and simply
 * for basic identification
 * @param array $factors    optional, for further identification, meaning
 * factors that affect the content, which should be cached differently
 * For example, in a paged list, pages should be cached separately and one of
 * the so-called factors is its page number.
 * Note that in this version, the sequence of factors matters. So, be sure
 * that it is consistent.
 * @return string
 */
function GenerateCacheId($name, $factors = NULL) {
    //TODO sequence of factors
    if (!empty($factors)) {
        $name.=implode('', $factors);
    }
    //SOLUTION 1:
    //    $name = str_replace('/', '_', $name);
    //    $name = str_replace('\', '_', $name);
    //    $name = str_replace(':', '_', $name);
    //    return $name;
    //SOLUTION 2:
    //    return md5($name);
    //SOLUTION 3:
    return urlencode($name);
}

/**
 * remove all cached files 
 */
function ClearCache() {

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = constant('IBC1_SYSTEM_ROOT');

    $themes = array_keys(GetThemes());
    foreach ($themes as $id) {
        if (is_dir("{$syspath}cache/{$id}")) {
            $languages = dir("{$syspath}cache/{$id}");
            while (FALSE != ($lang = $languages->read())) {
                if (substr($lang, 0, 1) == '.') // omit '.','..','.xxx'
                    continue;
                $files = dir("{$syspath}cache/{$id}/{$lang}");
                while (FALSE != ($file = $files->read())) {
                    if (substr($file, 0, 1) == '.') // omit '.','..','.xxx'
                        continue;
                    unlink("{$syspath}cache/{$id}/{$lang}/{$file}");
                }
                $files->close();
                rmdir("{$syspath}cache/{$id}/{$lang}");
            }
            $languages->close();
            rmdir("{$syspath}cache/{$id}");
        }
    }
}

//-----------------------------------------------------------
//the Page-Process-Box model
//-----------------------------------------------------------

/**
 * a generic page model
 *
 * @version 0.14.20130719
 */
abstract class PageModel extends BoxModel {

    private $_title = '';
    private $_keywords = '';
    private $_description = '';
    private $_meta = '';
    private $_css = '';
    private $_cssfile = '';
    private $_js = '';
    private $_jsfile = '';
    private $_icon = '';
    private $_boxes = array();

    /**
     * constructor
     *
     * @param string $pagetpl   name of page template
     */
    function __construct($pagetpl, $classname = NULL) {
        parent::__construct(empty($classname) ? __CLASS__ : $classname);
        $this->containerTplName = $pagetpl;
        $this->parentClassName = __CLASS__;
        $this->Before(NULL);
    }

    /**
     * invoke functions requested by the client and registered in the
     *  config parameter; add box views specified as default views in
     *  the config parameter or as output views from processes into
     *  the page
     *
     * manually called before outputing HTML
     * @param array $config
     * <code>
     * array(
     *      'definitions'=>array(
     *          'module'=>'',
     *          'function'=>''
     *      ),
     *      'box'=>array([box name],[params]),
     *      'boxes'=>array(
     *          array([box name],[params]),
     *          ...
     *      ),
     *      'functions'=>array(
     *          [function name]=>array([process name],[params]),
     *          ...
     *      ),
     *      'modules'=>array(
     *          [module name]=>array(
     *              'box'=>array([box name],[params]),
     *              'boxes'=>array(
     *                  array([box name],[params]),
     *                  ...
     *              ),
     *              'functions'=>array(
     *                  [function name]=>array([process name],[params]),
     *                  ...
     *              )
     *          ),
     *          ...
     *      )
     * )
     * </code>
     * @return bool     whether something found to display or run
     */
    final public function Route(array $config) {

        //default parameter names for module & function
        $module = 'module';
        $function = 'function';

        //custom parameter names for module & function
        if (isset($config['definitions'])) {
            $defconf = &$config['definitions'];
            if (isset($defconf['module']))
                $module = $defconf['module'];
            if (isset($defconf['function']))
                $function = $defconf['function'];
        }

        //dynamically load and invoke functions
        // either in or out of specified modules
        $module_name = strGet($module);
        $function_name = strGet($function);
        if (!empty($module_name) && isset($config['modules'])) {

            //locate a module with a recursive approach
            //?module=[module name]
            $modconf = &$config['modules'];
            if (isset($modconf[$module_name])) {
                return $this->Route($modconf[$module_name]);
            }
        } else if (!empty($function_name) && isset($config['functions'])) {

            //?function=[function name]
            //?module=[module name]&function=[function name]
            $funconf = &$config['functions'];
            if (isset($funconf[$function_name])) {

                //a process should only run once
                $this->CallProcess(
                        $this->ConstructProcess($funconf[$function_name]), // construct an object of the Process
                        $function_name, $module_name
                );

                return TRUE;
            }
        }

        //a default view if there's no function invoked
        if (isset($config['box'])) {

            //a single box view
            $this->AddBox($this->ConstructBox($config['box']), NULL, $module_name);

            return TRUE;
        } else if (isset($config['boxes'])) {

            //an array of boxes
            foreach ($config['boxes'] as $f => $b)
                $this->AddBox($this->ConstructBox($b), is_string($f) ? $f : NULL, $module_name);

            return TRUE;
        }

        // nothing found
        return FALSE;
    }

    private function ConstructBox(array $box) {
        require_once GetSysResPath($box[0] . '.class.php', 'modules/boxes');
        return new $box[0]($box[1]);
    }

    private function ConstructProcess(array $proc) {
        require GetSysResPath($proc[0] . '.class.php', 'modules/processes');
        return new $proc[0]($proc[1]);
    }

    /**
     * add a box to a field of its parent
     *
     * @param BoxModel $box
     * @param string $field
     * @param string $module
     */
    final public function AddBox(BoxModel $box, $field = '', $module = '') {
        if (empty($field))
            $field = $this->contentFieldName;
        $box->module = $module;
        // before rendering
        $box->Before($this);
        // check status
        if ($box->status == BoxModel::STATUS_FORWARD) {
            $this->AddBox($this->ConstructBox($box->forwardbox), $field, $module);
        } else {
            // render box
            $html = $box->GetHTML();
            if (isset($this->_boxes[$field]))
                $this->_boxes[$field].= $html;
            else
                $this->_boxes[$field] = $html;
            // after rendering
            if ($box->status != BoxModel::STATUS_HIDDEN) {
                $box->After($this);
                // check status
                if ($box->status == BoxModel::STATUS_FORWARD)
                    $this->AddBox($this->ConstructBox($box->forwardbox), $field, $module);
            }
        }
    }

    final public function CallProcess(ProcessModel $proc, $function, $module = '') {
        $proc->module = $module;
        $proc->function = $function;
        if ($proc->Auth($this)) {
            $output = $proc->Process();
            switch ($proc->mode) {
                case ProcessModel::MODE_BOX:
                    $this->AddBox($this->ConstructBox($output), NULL, $module);
                    break;
                case ProcessModel::MODE_JSON:
                    header('Content-Type: text/plain');
                    echo json_encode($output);
                case ProcessModel::MODE_RESOURCE:
                    exit;
                case ProcessModel::MODE_NOTHING:
                    break; //silent & no output
            }
        } // TODO else?
    }

    /**
     * set title for the page
     *
     * @param string $title
     */
    public function SetTitle($title) {
        $this->_title = htmlspecialchars($title);
    }

    /**
     * add keywords
     *
     * @param string $keywords
     */
    public function SetKeywords($keywords) {
        $this->_keywords = htmlspecialchars($keywords);
    }

    /**
     * append a string to description
     *
     * @param string $desc
     */
    public function SetDescription($desc) {
        $this->_description = htmlspecialchars($desc);
    }

    /**
     * add extra &lt;meta&gt; information
     *
     * @param string $name
     * @param string $content
     */
    public function AddMeta($name, $content) {
        $name = htmlspecialchars($name);
        $content = htmlspecialchars($content);
        $this->_meta.="<meta content=\"$content\" name=\"$name\" />\n";
    }

    /**
     * append CSS code fragment to &lt;head&gt;
     *
     * @param string $css
     */
    public function AppendCSS($css) {
        $this->_css.=$css;
    }

    /**
     * add a CSS file to &lt;head&gt;
     *
     * @param string $cssfile
     * @param int $mode
     * <ul>
     * <li>mode=0, external => module=URL that locates the js file</li>
     * <li>mode=1, system level => module=name of the js file</li>
     * <li>mode=2, theme level => module=name of the js file</li>
     * </ul>
     */
    public function AddCSSFile($cssfile, $mode = 0) {
        //TODO prevent repetition
        switch ($mode) {
            case 1:
                $cssfile = GetSysResPath($cssfile, 'stylesheets');
                break;
            case 2:
                $cssfile = GetThemeResPath($cssfile, 'stylesheets');
                break;
        }
        if (!empty($cssfile)) {
            $this->_cssfile .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$cssfile}\" />\n";
        }
    }

    /**
     * add an array of CSS files to &lt;head&gt;
     *
     * @param array $cssfiles
     * array(
     *      array([cssfile],[mode]),
     *      ...
     * )
     * @see AddCSSFile()
     */
    public function AddCSSFiles(array $cssfiles) {
        if (!empty($cssfiles)) {
            foreach ($cssfiles as $css) {
                $this->AddCSSFile($css[0], $css[1]);
            }
        }
    }

    /**
     * append JavaScript code fragment to &lt;head&gt;
     *
     * @param string $js
     */
    public function AppendJS($js) {
        $this->_js.=$js;
    }

    /**
     * add a JavaScript file to &lt;head&gt
     *
     * @param string $jsfile
     * @param int $mode
     * @see AddCSSFile()
     */
    public function AddJSFile($jsfile, $mode = 0) {
        //TODO prevent repetition
        switch ($mode) {
            case 1:
                $jsfile = GetSysResPath($jsfile, 'scripts');
                break;
            case 2:
                $jsfile = GetThemeResPath($jsfile, 'scripts');
                break;
        }
        if (!empty($jsfile)) {
            $this->_jsfile .= "<script language=\"javascript\" type=\"text/javascript\" src=\"{$jsfile}\"></script>\n";
        }
    }

    /**
     * add an array of JavaScript files to &lt;head&gt
     *
     * @param array $jsfiles
     * array(
     *      array([jsfile],[mode]),
     *      ...
     * )
     * @see AddJSFile()
     */
    public function AddJSFiles(array $jsfiles) {
        if (!empty($jsfiles)) {
            foreach ($jsfiles as $js) {
                $this->AddJSFile($js[0], $js[1]);
            }
        }
    }

    /**
     * set an image as an icon
     * @param stirng $iconfile
     */
    public function SetIcon($iconfile, $type = '') {
        if ($type == '')
            $type = GetFileExt($iconfile);
        switch (strtolower($type)) {
            case 'ico':
                $mime = 'image/x-icon';
                break;
            case 'jpeg':
            case 'jpg':
                $mime = 'image/jpeg';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'bmp':
                $mime = 'image/bmp';
                break;
            default:
                $mime = $type;
        }
        $this->_icon = "<link rel=\"shortcut icon\" type=\"{$mime}\" href=\"{$iconfile}\" />\n";
    }

    final protected function LoadContent() {
        $content = '';
        foreach ($this->_boxes as $f => $c) {
            if ($f == $this->contentFieldName)
                $content = $c;
            else
                $this->SetField($f, $c);
        }
        return $content;
    }

    final public function GetHTML() {
        //head BEGIN
        $head = '';
        //icon
        if (!empty($this->_icon)) {
            $head.=$this->_icon;
        }
        //css
        if (!empty($this->_cssfile)) {
            $head.=$this->_cssfile;
        }
        if (!empty($this->_css)) {
            $head.="<style>\n<!--\n{$this->_css}\n-->\n</style>\n";
        }
        //js
        if (!empty($this->_jsfile)) {
            $head.=$this->_jsfile;
        }
        if (!empty($this->_js)) {
            $head.="<script language=\"javascript\" type=\"text/javascript\">\n//<![CDATA[\n{$this->_js}\n//]]>\n</script>\n";
        }
        //meta
        $head.=$this->_meta;
        //head END
        //output
        $this->SetField('Keywords', $this->_keywords);
        $this->SetField('Description', $this->_description);
        $this->SetField('Title', $this->_title);
        $this->SetField('Head', $head);
        $this->After(NULL);
        return parent::GetHTML();
    }

    final public function Show() {
        echo $this->GetHTML();
        exit;
    }

}

/**
 * an abstract process for the Page-Process-Box model
 *
 * @version 0.4.20130719
 */
abstract class ProcessModel {

    const MODE_NOTHING = 0;
    const MODE_BOX = 1;
    const MODE_JSON = 2;
    const MODE_RESOURCE = 3;

    public $mode = ProcessModel::MODE_NOTHING;

    /**
     * name of the module where it is deployed in the page
     * @var string 
     */
    public $module;

    /**
     * name of the corresponding function in the page
     * @var string 
     */
    public $function;

    /**
     * authenticate for whether proceed 
     * 
     * @param PageModel $page   the page that the process belongs to
     * @return bool     whether proceed with the Process() method
     */
    abstract public function Auth($page);

    /**
     * @return mixed
     * <code>
     * // nothing
     * return NULL;
     * // box
     * return $this->OutputBox('somebox', array(...));
     * // json
     * return $this->OutputJSON(array(...));
     * // resource
     * echo ...;
     * return $this->OutputRes();
     * </code>
     */
    abstract public function Process();

    public function OutputBox($box, array $params) {
        $this->mode = ProcessModel::MODE_BOX;
        return array($box, $params);
    }

    public function OutputJSON($data = NULL) {
        $this->mode = ProcessModel::MODE_JSON;
        return $data;
    }

    public function OutputRes() {
        $this->mode = ProcessModel::MODE_RESOURCE;
        return NULL;
    }

}

/**
 * a box container in the Page-Process-Box model
 *
 * @version 0.11.20130118
 */
abstract class BoxModel {

    const STATUS_NORMAL = 0;
    const STATUS_USECACHE = 1;
    const STATUS_FORWARD = 2;
    const STATUS_HIDDEN = 3;

    /**
     * 0 - normal
     * 1 - use cache
     * 2 - forward
     * 3 - hidden
     * @var int
     */
    public $status;

    /**
     * name of the module where it is deployed in the page
     * @var string 
     */
    public $module;

    /**
     * name and parameters of the Box to be forwarded
     * @var string
     */
    public $forwardbox;

    /**
     * a hash map of fields and their contents that will fill in the template.
     * 
     * <code>
     * array(
     *      '[field name]' => '[content]'
     * )
     * </code>
     * @var array 
     */
    private $_fields = array();
    private $_cacheReader = NULL;
    private static $_cacheProvider;

    /**
     * name of the class (extended class), 
     * e.g. templates/{{class name}}/[lang]/[template name].tpl
     * @var string 
     */
    protected $className;
    protected $parentClassName;

    /**
     * template name, 
     * e.g. templates/[class name]/[lang]/{{template name}}.tpl
     * @var string 
     */
    protected $containerTplName;
    protected $cacheTimeout;
    protected $cacheGroup;
    protected $cacheKey;
    protected $cacheVersion;
    protected $contentFieldName;
    protected $boxArgs;

    function __construct($classname = NULL, $args = array()) {
        $this->status = BoxModel::STATUS_NORMAL;
        $this->className = empty($classname) ? __CLASS__ : $classname;
        $this->boxArgs = $args;
        $this->parentClassName = __CLASS__;
        $this->containerTplName = '';
        $this->cacheGroup = '';
        $this->cacheKey = '';
        $this->cacheTimeout = 0;
        $this->cacheVersion = 0;
        $this->contentFieldName = 'Content';
    }

    /**
     *
     * @return ICacheProvider 
     */
    private static function GetCacheProvider() {
        $p = &self::$_cacheProvider;
        if (empty($p)) {
            $module = &$GLOBALS['IBC1_FRAMEWORK_CACHING'];
            if (isset($module)) {
                require call_user_func_array('GetSysResPath', $module);
                $p = new $module[0]();
            }
            $p = new DefaultBoxCacheProvider();
        }
        return $p;
    }

    /**
     * change box status to hidden 
     */
    final protected function Hide() {
        $this->status = BoxModel::STATUS_HIDDEN;
    }

    /**
     * set the next box to be forwarded
     * 
     * @param string $box
     * @param array $params 
     */
    final protected function Forward($box, $params) {
        $this->status = BoxModel::STATUS_FORWARD;
        $this->forwardbox = array($box, $params);
    }

    /**
     * inform the box that loading content failed so as to use cached data instead
     */
    final protected function UseCache() {
        $this->status = BoxModel::STATUS_USECACHE;
    }

    /**
     * @return string 
     */
    abstract protected function LoadContent();

    abstract public function Before($page);

    abstract public function After($page);

    final public function GetThemeID() {
        return GetThemeID();
    }

    final public function GetLang() {
        return GetLang();
    }

    final public function GetLangName() {
        return GetLangName();
    }

    final public function GetLangData($key, $group = NULL) {
        return GetLangData($key, $group);
    }

    final public function GetConfigValue($key, $group = NULL) {
        return GetConfigValue($key, $group);
    }

    final public function GetBoxArgument($name) {
        if (isset($this->boxArgs[$name]))
            return $this->boxArgs[$name];
        return '';
    }

    final public function Format($func, $value) {
        return FormatTplVar($func, $value);
    }

    final public function CreateButton($type, $text, $params = array()) {
        return CreateButton($type, $text, $params);
    }

    /**
     * reads a template, passes parameters to it and generates HTML
     *
     * @param string $tplname
     * @param array $vars
     * @param string $classname
     * @return string
     * @see GetTemplate()
     * @see Tpl2HTML()
     */
    final public function TransformTpl($tplname, $vars, $classname = NULL) {
        $tpl = GetTemplate($tplname, empty($classname) ? $this->className : $classname);

        return $this->Tpl2HTML($tpl, $vars);
    }

    /**
     * assigns parameters to the template and generates HTML
     *
     * @see toScriptString()
     * @see FormatTplField()
     * @param string $tpl   content of a template
     * @param array $vars   variables to be assigned
     * <code>
     * array(
     *     [variable1 name]=>[variable1 value],
     *     [variable2 name]=>[variable2 value],
     *     ...
     * )
     * </code>
     * @return string
     */
    final public function Tpl2HTML($tpl, $vars) {
//        foreach ($vars as $varname => $varvalue) {
//            $varvalue = toScriptString(FormatTplField($varname, $varvalue), TRUE);
//            eval("\${$varname}={$varvalue};");
//        }
        FormatTplFields($vars);
        extract($vars, EXTR_SKIP);
        $tpl = toScriptString($tpl, FALSE);
        eval("\$tpl={$tpl};");
        return $tpl;
    }

    /**
     * renders a php template (which is not static like {@link GetTemplate()}).
     * 
     * @param string $tplname
     * @param array $vars
     * @param string $classname
     * @return string
     */
    final public function RenderPHPTpl($tplname, $vars = array(), $classname = NULL) {
        $path = GetTplPath($tplname . '.tpl.php', empty($classname) ? $this->className : $classname);
        if (empty($path) || !is_file($path))
            return '';
        if (!empty($vars)) {
            FormatTplFields($vars);
            extract($vars, EXTR_SKIP);
        }
        ob_start();
        include $path;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    final public function SetField($fieldname, $value) {
        if (!empty($fieldname))
            $this->_fields[$fieldname] = $value;
    }

    final public function GetRefreshedHTML() {

        $html = $this->LoadContent();

        // during content loading, status can be changed 
        switch ($this->status) {
            case BoxModel::STATUS_NORMAL:
                // fill content in the template
                if (!empty($this->containerTplName)) {
                    $this->_fields[$this->contentFieldName] = $html;
                    $html = $this->TransformTpl(
                            $this->containerTplName, $this->_fields, $this->parentClassName
                    );
                }

                // write into cache storage
                if (!empty($this->cacheGroup)) {
                    try {
                        $cp = self::GetCacheProvider();
                        $cw = $cp->GetWriter($this->cacheGroup);
                        if (!empty($cw)) {
                            $cw->SetValue(
                                    $this->cacheKey, $html, $this->cacheTimeout, $this->cacheVersion > 0
                            );
                            $cw->Save();
                        }
                    } catch (Exception $ex) {
                        
                    }
                }
                break;

            case BoxModel::STATUS_USECACHE:

                $cr = $this->_cacheReader;
                if (!empty($cr)) {
                    $cr->SetRefreshFunction(NULL);
                    $html = $cr->GetValue($this->cacheKey, 0);
                }

                break;

            case BoxModel::STATUS_FORWARD: // forward by the caller
            default: // Box::STATUS_HIDDEN

                $html = '';

                break;
        }

        return $html;
    }

    public function GetHTML() {

        //status may change in Before()
        switch ($this->status) {
            case BoxModel::STATUS_FORWARD: // forward by the caller
            case BoxModel::STATUS_HIDDEN:

                return '';

            default:

                //read from cache storage
                if (!empty($this->cacheGroup)) {
                    $cp = self::GetCacheProvider();
                    $this->_cacheReader = $cp->GetReader($this->cacheGroup);
                    $cr = $this->_cacheReader;

                    if ($this->status == BoxModel::STATUS_USECACHE) {
                        $cr->SetRefreshFunction(NULL);
                        return $cr->GetValue($this->cacheKey, 0);
                    } else {
                        $cr->SetRefreshFunction(array($this, 'GetRefreshedHTML'));
                        return $cr->GetValue($this->cacheKey, $this->cacheVersion);
                    }
                }
                return $this->GetRefreshedHTML();
        }
    }

}

function CreateButton($type, $text, $params = array()) {

    $att = array();

    if (isset($params['id']))
        $att['id'] = $params['id'];
    if (isset($params['tiptext']))
        $att['title'] = $params['tiptext'];

    if (isset($params['class_selected']) && isset($params['selected']) && $params['selected'])
        $att['class'] = $params['class_selected'];
    else if (isset($params['class']))
        $att['class'] = $params['class'];

    $target = isset($params['target']) ? $params['target'] : '';

    switch ($type) {
        case 'button':

            if (isset($params['url'])) {
                $url = str_replace("'", '&#039;', htmlspecialchars($params['url']));
                switch (strtolower($target)) {
                    case '_blank':
                        $att['onclick'] = "window.open('{$url}','','')";
                        break;
                    case '':
                    case '_self':
                        $att['onclick'] = "window.location='{$url}'";
                        break;
                    case 'parent':
                        $att['onclick'] = "window.parent.location='{$url}'";
                        break;
                    default:
                        $att['onclick'] = "window.parent.{$target}.location='{$url}'";
                }
            }
        // NO break;
        case 'submit':
        case 'reset':
            $att['type'] = $type;
            $att['value'] = htmlspecialchars($text);
            if (!isset($params['url']) && isset($params['js']))
                $att['onclick'] = htmlspecialchars($params['js']);

            break;

        default: //case 'link':

            if (isset($params['url'])) {
                $url = htmlspecialchars($params['url']);
                if (!empty($target))
                    $att['target'] = $target;
                $att['href'] = $url;
            } else if (isset($params['js'])) {
                $att['href'] = 'javascript:' . htmlspecialchars($params['js']);
            }

            break;
    }

    if (isset($att['extra'])) {
        foreach ($att['extra'] as $k => $v)
            if (!isset($att[$k]))
                $att[$k] = htmlspecialchars($v);
    }

    $attributes = '';
    foreach ($att as $k => $v) {
        if (!empty($attributes))
            $attributes .= ' ';
        $attributes .= "{$k}=\"{$v}\"";
    }

    switch ($type) {
        case 'button':
        case 'submit':
        case 'reset':
            return "<input {$attributes} />";
        default: //case 'link':
            $text = htmlspecialchars($text);
            if (isset($params['prefix']))
                $text = $params['prefix'] . $text; // unfiltered
            if (isset($params['suffix']))
                $text .= $params['suffix']; // unfiltered
            return "<a {$attributes}>{$text}</a>";
    }
}
