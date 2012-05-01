<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007 Mike Cochrane <code@gardyneholt.co.nz>
 * Copyright 2007 Tom
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <code@gardyneholt.co.nz>
 * @author  Tom
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class JOJO_Plugin_jojo_weather extends JOJO_Plugin
{

    public function _getContent()
    {
        return self::getWeatherHTML();
    }

    public static function getWeatherHTML()
    {
        global $smarty;
        $locations = self::getLocations();
        $defaultlocation  = Jojo::getOption('weather_location', 'NZXX0003');
        $_SESSION['weatherloc'] = isset($_SESSION['weatherloc']) ? Util::getFormData('weatherloc', $_SESSION['weatherloc']) : Util::getFormData('weatherloc', $defaultlocation);
        $_SESSION['weatherloc'] = isset($locations[$_SESSION['weatherloc']]) ? $_SESSION['weatherloc'] : $defaultlocation;
        $smarty->assign('currentlocation', $_SESSION['weatherloc']);

        $partner  = Jojo::getOption('weather_partnerid');			// Partner ID
        $license  = Jojo::getOption('weather_licensekey');		// License Key
        if (!$partner || !$license) {
            return array('content' => 'Partner ID and license key missing in Options');
        }
        $xfile    =  $_SESSION['weatherloc'] . '.xml';		// Root to weather.txt
        $xfile_fc =  $_SESSION['weatherloc'] . '_fc.xml';		// Root to weather forecast xml
        $icons	 = 'images/weather';			// Path to Weather Icons from Include
        $iconsize = '31x31';			// Weather Icons size '31x31', '61x61', '93x93'
        $unit	 = 'm';				// Set to 's' (Fahrenheit & MPH) or 'm' (Celsius & KMH)
        $clock	 = 12;		// Set to '12' or '24' hour format
        $forecast = 2;		// Set to '1' - '10' days forecast (including the current day)
        $night    = false;     // include nighttime forcasts
        $url      = 'http://xoap.weather.com/weather/local/';	// URL to xoap.weather

        $currenttime = date("d M g:ia", time());
        $infotypes = explode(',', Jojo::getOption('weather_info'));
        foreach ($infotypes as $i) {
            $smarty->assign($i, $i);
        }

        /* Get Current conditions XML from file or feed */
        if (!file_exists(_CACHEDIR . '/weather/' . $xfile) || (time() - filemtime(_CACHEDIR . '/weather/' . $xfile)) > 1800 ) {
            /* Update cached copy */
            $file = sprintf("$url%s?cc=*&prod=xoap&par=$partner&key=$license&unit=$unit&link=xoap", $_SESSION['weatherloc']);
            file_put_contents(_CACHEDIR . '/weather/' . $xfile, file_get_contents($file));
        }
        $cc_tree = self::_parse_xml(_CACHEDIR . '/weather/' . $xfile);
        if ($cc_tree == 'Invalid Partner Code.') {
            return array('content' => 'Invalid Partner Code.');
        }

        // Location data
        $date = date("l, F jS Y @ g:ia");
        $cc_tree['loc']['tempunit'] = ($unit == 'm') ? '&deg;C' : '&deg;F';
        $cc_tree['loc']['speedunit'] = ($unit == 'm') ? 'kmh' : 'mph';

        $cc_tree['loc']['sunr'] = ($clock == 24) ?  date("H:i",strtotime($cc_tree['loc']['sunr'])) : $cc_tree['loc']['sunr'];
        $cc_tree['loc']['suns'] = ($clock == 24) ?  date("H:i",strtotime($cc_tree['loc']['suns'])) : $cc_tree['loc']['suns'];

        $smarty->assign('locationdata', $cc_tree['loc']);
        $smarty->assign('currentconditions', $cc_tree['cc']);


        /* Get Forecast XML from file or feed */
        if (!file_exists(_CACHEDIR . '/weather/' . $xfile_fc) || (time() - filemtime(_CACHEDIR . '/weather/' . $xfile_fc)) > 60 ) {
            /* Update cached copy */
            $file = sprintf("$url%s?dayf=$forecast&prod=xoap&par=$partner&key=$license&unit=$unit&link=xoap", $_SESSION['weatherloc']);
            file_put_contents(_CACHEDIR . '/weather/' . $xfile_fc, file_get_contents($file));
        }
        $fc_tree = self::_parse_xml(_CACHEDIR . '/weather/' . $xfile_fc);


        // Forecast Conditions data
         $smarty->assign('forecasts', $fc_tree['dayf']['day']);

        // include compulsory weather.com links (part of the conditions of use)
        foreach ($fc_tree['lnks'] as &$wl) {
            $wl['l'] =  htmlspecialchars($wl['l'], ENT_COMPAT, 'UTF-8', false);
        }
        $smarty->assign('weatherlinks', $fc_tree['lnks']);

        if (count($locations) >1){
            $smarty->assign('locations', $locations);
        } else {
            $smarty->assign('location', $locations[$_SESSION['weatherloc']]);
        }

        $content   = $smarty->fetch('jojo_weather.tpl');

        return array('content' => $content);
    }

    private static function getLocations() {
         $locations = array();
        /* First preference is fields from weather_locations.php in any plugin or theme */
        foreach (Jojo::listPlugins('weather_locations.php') as $pluginfile) {
            include($pluginfile);
        }
        /* second choice is the weather_locations.php within the weather plugin (not recommended) */
        if (!isset($fields) || !count($fields)) {
            if ( Jojo::fileExists(_BASEPLUGINDIR.'/jojo_weather/weather_locations.php')) {
                include(_BASEPLUGINDIR.'/jojo_weather/weather_locations.php');
            }
        }
        return $locations;
    }

    //////// Parse XML

    private static function _parse_xml($xmlfile) {
        $dom = new DomDocument();
        $dom->load($xmlfile);
        return self::_domtoarray($dom->documentElement);
    }

    private static function _domtoarray($node) {
        $res = array();
        $childNames = array();
        if (property_exists($node, 'childNodes')) {
            foreach ($node->childNodes as $c) {
                $name = $c->nodeName;
                $value = false;
                if ($c->nodeType == XML_TEXT_NODE && trim($c->nodeValue)) {
                    $value = trim($c->nodeValue);
                } elseif (($c->nodeType == XML_ELEMENT_NODE)) {
                    $value = self::_domtoarray($c);
                    if ($c->hasAttribute('d')) {
                        $value = (array)$value;
                        $value['d'] = ($c->getAttribute('d'));
                        $value['fdate'] = ($c->getAttribute('dt'));
                        $value['fdateday'] = ($c->getAttribute('t'));
                    } elseif ($c->getAttribute('p')) {
                        $value = (array)$value;
                        $value['p'] = $c->getAttribute('p');
                    }
                } else {
                    continue;
                }
                $childNames[$name] = isset($childNames[$name]) ? $childNames[$name] + 1 : 1;
                if (isset($res[$name])) {
                    if ($childNames[$name] == 2) {
                        $res[$name] = array($res[$name], $value);
                    } else {
                        $res[$name][] = $value;
                    }
                } else {
                    $res[$name] = $value;
                }
            }
        }

        if (count($res) == 1) {
            return array_pop($res);
        }
        return $res;
    }

    public static function inpageweather($content)
    {
        $html = self::getWeatherHTML();
        return str_replace('[[weather]]', $html['content'], $content);
    }

    public static function inpageweatherwithcode($content)
    {
        if(strpos($content, '[[weather') == false) {
        	return $content;
        }

        /* get all matches for weather locations */
        preg_match_all('/\[\[weather:([^\]]*)\]\]/', $content, $matches);
        if($matches[1]) {
        	/* set the location to the value passed */
        	foreach($matches[1] as $id => $match) {
        		$_SESSION['weatherloc'] = $match;
        		$html = self::getWeatherHTML();
        		$content = str_replace($matches[0][$id], $html['content'], $content);
        	}
        }

        return $content;
    }
}
