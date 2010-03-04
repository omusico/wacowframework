<?php
/**
 * Smarty plugin
 *
 * Inject the css and js tag to html head.
 *
 * @package     Wacow_View
 * @subpackage  Wacow_View_Html_Smarty
 * @version     $Id: outputfilter.html.php 669 2009-04-10 05:01:12Z jaceju $
 */

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * smarty_outputfilter_html
 *
 * @param string $output
 * @param Smarty $smarty
 * @return string
 */
function smarty_outputfilter_html($output, Smarty &$smarty)
{
    $result = '';
    $view = $smarty->_tpl_vars['this'];

    if (false === stripos($output, '</head>')) {
        return $output;
    }

    if (false === stripos($output, '</body>')) {
        return $output;
    }

    // process css
    $tempLoadedStyleSheets = (array) $view->loadedStyleSheets;
    ksort($tempLoadedStyleSheets);
    foreach ($tempLoadedStyleSheets as $layoutType => $loadedStyleSheets) {
        foreach ((array) $loadedStyleSheets as $browserVersion => $srcs) {

            if ('compact' === $browserVersion) {
                $compactResult = _assetGenerateCss($srcs, $smarty);
                $browserVersion = 'none';
                if ($compactResult) {
                    $result .= $compactResult;
                    continue;
                }
            }

            if ('none' !== $browserVersion) {
                $result .= "<!--[if $browserVersion]>\n";
            }
            foreach ($srcs as $href => $media) {
                $result .= '<link rel="stylesheet" type="text/css" href="' . _getSrcWithVersion($href, $smarty) . '" media="' . $media . '" />' . "\n";
            }
            if ('none' !== $browserVersion) {
                $result .= "<![endif]-->\n";
            }
        }
    }

    // process constants
    $result .= '<script type="text/javascript">' . "\n";
    $frontendVars = (array) $smarty->_tpl_vars['frontendVars'];
    $vars = array();
    foreach ($frontendVars as $name => $value) {
        if (is_string($frontendVars[$name])) {
            $vars[$name] = $value;
        }
    }
    foreach ((array) $frontendVars['constants'] as $name => $value) {
        $vars[$name] = $value;
    }
    $result .= 'var $frontendVars = ' . Zend_Json::encode($vars) . ";\n";
    $result .= "</script>\n";

    $pattern = '/(<\/head>)/i';
    $replace = "$result\n\\1";
    $output = preg_replace($pattern, $replace, $output);

    // process javascript
    $tempLoadedScripts = (array) $view->loadedScripts;
    ksort($tempLoadedScripts);
    foreach ($tempLoadedScripts as $layoutType => $loadedScripts) {
        $result = '';
        foreach ((array) $loadedScripts as $position => $subLoadedScripts) {
            foreach ((array) $subLoadedScripts as $browserVersion => $srcs) {

                // process asset compact javascript by racklin
                if ('compact' === $browserVersion) {
                    $compactResult = _assetGenerateJs($srcs, $smarty);
                    $browserVersion = 'none';
                    if($compactResult) {
                        $result .= $compactResult;
                        continue;
                    }
                }

                if ('none' !== $browserVersion) {
                    $result .= "<!--[if $browserVersion]>\n";
                }

                foreach ($srcs as $src) {
                    $result .= '<script type="text/javascript" src="' . _getSrcWithVersion($src, $smarty) . '"></script>' . "\n";
                }

                if ('none' !== $browserVersion) {
                    $result .= "<![endif]-->\n";
                }
            }

            $scriptBlock = '<!-- [script block] -->';
            if ('body' === $position && stripos($output, $scriptBlock)) {
                $output = str_replace($scriptBlock, $result, $output);
            } else {
                $pattern = '/(<\/' . $position . '>)/i';
                $replace = "$result\n\\1";
                $output = preg_replace($pattern, $replace, $output);
            }
        }
    }

    return $output;
}

/**
 * Get file version
 *
 * @see smarty_function_js
 * @param string $src
 * @param Smarty $smarty
 * @return string
 */
function _getSrcWithVersion($src, &$smarty)
{
    static $baseUrl = null;
    static $app = null;
    static $debugMode = null;

    // add the modified time
    if (null === $baseUrl) {
        $baseUrl = $smarty->_tpl_vars['this']->frontendVars['baseUrl'];
    }
    if (null === $app) {
        $app = Wacow_Application::getInstance();
    }
    if (null === $debugMode) {
        $debugMode = $app->debugMode;
    }

    $filePath  = Wacow_Application::translatePath(':rootPath') . '/' . ltrim($src, $baseUrl);
    $modifyDateTime = !$debugMode && file_exists($filePath) ? filemtime($filePath) : time();
	$q = '&';
	if (strpos($src, '?') === false) { $q = '?'; }
    return $src . $q . 'v=' . $modifyDateTime;
}

/**
 * Asset CSS
 *
 * @param array $srcs
 * @param Smarty $smarty
 * @return string
 */
function _assetGenerateCss($srcs, Smarty &$smarty)
{
    return _assetProcess($srcs, $smarty, 'css');
}

/**
 * Asset JavaScript
 *
 * @param array $srcs
 * @param Smarty $smarty
 * @return string
 */
function _assetGenerateJs($srcs, Smarty &$smarty)
{
    return _assetProcess($srcs, $smarty, 'js');
}

/**
 * Asset
 *
 * @param array $srcs
 * @param Smarty $smarty
 * @return string
 */
function _assetProcess($srcs, Smarty &$smarty, $type)
{

    $baseUrl     = $smarty->_tpl_vars['frontendVars']['baseUrl'];
    $app         = Wacow_Application::getInstance();
    $assetConfig = Wacow_Application::translatePath($app->getConfig('common')->asset);
    $rootPath    = Wacow_Application::translatePath(':rootPath');
    $compress    = isset($assetConfig->compress);

    if ('js' === $type) {
        $packedURL = $assetConfig->js->packedURL;
    } else {
        $packedURL = $assetConfig->css->packedURL;
    }
    $packedDir = $rootPath . $packedURL;
    $debugMode = $app->debugMode;

    // ignore when debugMode or packedDir not exist
    if ($debugMode || !is_dir($packedDir)) {
        return false;
    }

    $packedFileName = "";
    $latestTS = 0;

    // process packedFileName
    if ('js' === $type) {
        foreach ($srcs as $src => $srcWithBaseUrl) {
            $packedFileName .= '_' . str_replace('.' . $type, '', basename($src));
            $latestTS = max($latestTS, filemtime($rootPath . $src));
        }
    } else {
        foreach ($srcs as $srcWithBaseUrl => $media) {
            $packedFileName .= '_' . str_replace('.' . $type, '', basename($srcWithBaseUrl));
            $latestTS = max($latestTS, filemtime($rootPath . str_replace($baseUrl, '', $srcWithBaseUrl)));
        }
    }
    $packedFileName = substr($packedFileName, 1) . "." . $type;
    $packedTS = file_exists($packedDir . '/' . $packedFileName) ? filemtime($packedDir . '/' . $packedFileName) : 0;

    // an original file is newer.
    // need to rebuild
    if ($latestTS > $packedTS){
        if (file_exists($packedDir . '/' . $packedFileName)) {
            unlink($packedDir . '/' . $packedFileName);
        }

        // merge the script
        $scriptBuffer = '';
        switch ($type) {
            case 'js':
                foreach ($srcs as $src => $srcWithBaseUrl) {
                    $scriptBuffer .= file_get_contents($rootPath . $src) . "\n\n";
                }
                if ($compress) {
                    require_once('Wacow/vendor/jsmin/jsmin.php');
                    $scriptBuffer = JSMin::minify($scriptBuffer);
                }
                break;
            case 'css':
                foreach ($srcs as $srcWithBaseUrl => $media) {
                    $scriptBuffer .= file_get_contents($rootPath . str_replace($baseUrl, '', $srcWithBaseUrl)) . "\n\n";
                }
                if ($compress) {
                    require_once('Wacow/vendor/css_tidy/class.csstidy.php');
                    $tidy = new csstidy();
                    $tidy->settings['merge_selectors'] = false;
                    $tidy->load_template('high_compression');
                    $tidy->parse($scriptBuffer);
                    $scriptBuffer = $tidy->print->plain();
                }
                break;
        }

        file_put_contents($packedDir . '/' . $packedFileName, $scriptBuffer);
        $packedTS = file_exists($packedDir . '/' . $packedFileName) ? filemtime($packedDir . '/' . $packedFileName) : 0;
        if ($packedTS == 0) {
            return false;
        }
    }

    $src = $baseUrl . $packedURL . '/' . $packedFileName . "?v=" . $packedTS;

    if ('js' === $type) {
        return '<script type="text/javascript" src="' . $src .  '"></script>' . "\n";
    } else {
        return '<link rel="stylesheet" type="text/css" href="' . $src . '" />' . "\n";
    }
}