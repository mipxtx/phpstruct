<?php

class CorpCMS_App
{
    /**
     * Это поэзия! Be or not to be...
     */
    private static $I;

    public static function I() {
        self::$I or
        self::$I = new
        self;;

        return
            self::$I;
    }

    protected
        $lang = ''
    , $langPathPrefix = ''
    , $path = array()
    , $ctrln = ''
    , $langAliases = array(
        'ru' => Language_Helper::LANGUAGE_RUSSIAN,
        'en' => Language_Helper::LANGUAGE_ENGLISH
    );

    protected static $currentPath = '';

    private function __construct() {
        $ru = parse_url($_SERVER['REQUEST_URI']);
        if (substr($ru['path'], -1) == '/') {
            $ru['path'] = substr($ru['path'], 0, -1);
        }

        // #42262
        $ru['path'] = implode('/', array_map('urlencode', explode('/', $ru['path'])));

        self::$currentPath = $ru['path'] . '/';

        $ap = explode('/', $ru['path']);
        array_shift($ap);

        if (!$ap[0] || strlen($ap[0]) != 2) {
            return $this->setLanguagePage();
        }

        if (isset ($ap[0]) && $ap[0]) {
            if (isset ($this->langAliases[$ap[0]])) {
                $this->setLanguage($ap);
            } else {
                throw new Exception('URI not found!', 404);
            }
        }

        $this->ctrln = ucfirst($ap[count($ap) - 1]);
        $this->path = implode('/', $ap);
    }

    public static function getCurrentPath() {
        return self::$currentPath;
    }

    function getCtrln() {
        return $this->ctrln;
    }

    function getPath() {
        return $this->path;
    }

    function getLangPath() {
        return $this->langPathPrefix;
    }

    public function setLanguage(array & $ap) {
        $lang = array_shift($ap);

        $this->lang = isset ($this->langAliases[$lang])
            ? $this->langAliases[$lang]
            : false;

        $a = array_flip($this->langAliases);
        $this->langPathPrefix = $a[$this->lang];

        define('CORPCMS_LANGNAME', $this->langPathPrefix);

        // Adapter for old code
        $_REQUEST['lang_id'] =
        $_GET    ['lang_id'] = $this->lang;

        $this->setLangCookie();

        Language_Helper::getInstance()->getCurrent()->getId();
    }

    public function setLangCookie() {
        if (!isset ($_COOKIE['lang_id']) || ($_COOKIE['lang_id'] != $this->lang)) {
            setcookie('lang_id', $this->lang, time() + 1576800000);
            setcookie('unauth_lang', $this->lang, time() + 1576800000);
        }
    }

    public function setLanguagePage() {
        $a = array_flip($this->langAliases);
        $langName = isset ($_COOKIE['lang_id']) ? $a[$_COOKIE['lang_id']] : false;

        if ($langName) {
            $lang = $langName;
        } else {
            $lang = CURRENT_CORP_DOMAIN == 'wamba' ? 'en' : 'ru';
        }

        @ob_clean();
        die (header("Location: /$lang"));
    }

    public function getLangId() {
        return $this->lang;
    }

    protected $article;


}

function CorpCMS_App() {
    return CorpCMS_App::I();
}

//EOF//