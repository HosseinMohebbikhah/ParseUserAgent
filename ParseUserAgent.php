<?php

class infoRequest
{
    //*Publick values
    //Type Device (For quick use)
    public static $isDesktop = false;
    public static $isTablet = false;
    public static $isMobile = false;
    //
    public static $isAndroid = false;
    public static $isIOS = false;
    public static $isWindows = false;
    public static $isLinux = false;
    public static $isMac = false;

    //User-Agent  -->  getallheaders()['User-Agent']
    public static function Get($UserAgent)
    {
        //Get IP of user
        $ipSelf = self::getIpAddress();

        //Get details of device request
        $detailUserAgent = json_decode(UserAgent::Parse($UserAgent));

        //Fill quick values 
        self::$isAndroid = $detailUserAgent->isAndroid;
        self::$isIOS = $detailUserAgent->isIOS;
        self::$isWindows = $detailUserAgent->isWindows;
        self::$isLinux = $detailUserAgent->isLinux;
        self::$isMac = $detailUserAgent->isMac;
        self::$isDesktop = $detailUserAgent->isDesktop;
        self::$isTablet = $detailUserAgent->isTablet;
        self::$isMobile = $detailUserAgent->isMobile;

        //Get info IP
        $ip_info = null;
        if (!$ipSelf == "127.0.0.1")
            $ip_info = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ipSelf));
        else
            //Details for localHost
            $ip_info = json_decode(json_encode(["geoplugin_countryName" => "Iran", "geoplugin_countryCode" => "IR", "geoplugin_latitude" => "35.6892", "geoplugin_longitude" => "51.3890"]));

        $infoIP = null;
        $infoDevice = null;
        if ($ip_info != null && $ip_info->geoplugin_countryName != null) {
            $infoIP = [
                'ip' => $ipSelf,
                'country' => $ip_info->geoplugin_countryName,
                'Ccode' => $ip_info->geoplugin_countryCode,
                'lat' => $ip_info->geoplugin_latitude,
                'lng' => $ip_info->geoplugin_longitude,
            ];
            $infoDevice = [
                'browser' => $detailUserAgent->browserName == '' ? 'Null' : $detailUserAgent->browserName,
                'vBrowser' => $detailUserAgent->browserVersion == '' ? 'Null' : $detailUserAgent->browserVersion,
                'device' => $detailUserAgent->deviceName == '' ? 'Null' : $detailUserAgent->deviceName,
                'isAndroid' => self::$isAndroid,
                'isIOS' => self::$isIOS,
                'isWindows' => self::$isWindows,
                'isLinux' => self::$isLinux,
                'isMac' => self::$isMac,
            ];
        } else {
            $infoDevice = [
                'browser' => $detailUserAgent->browserName == '' ? 'Null' : $detailUserAgent->browserName,
                'vBrowser' => $detailUserAgent->browserVersion == '' ? 'Null' : $detailUserAgent->browserVersion,
                'device' => $detailUserAgent->deviceName == '' ? 'Null' : $detailUserAgent->deviceName,
                'isAndroid' => self::$isAndroid,
                'isIOS' => self::$isIOS,
                'isWindows' => self::$isWindows,
                'isLinux' => self::$isLinux,
                'isMac' => self::$isMac,
            ];
        }
        return json_encode(["infoIP" => $infoIP, "infoDevice" => $infoDevice]);
    }

    private static function getIpAddress()
    {
        $ipAddress = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddressList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ipAddressList as $ip) {
                if (!empty($ip)) {
                    $ipAddress = $ip;
                    break;
                }
            }
        } else if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } else if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        return $ipAddress;
    }
}

class UserAgent
{
    private static $string = '';
    private static $browserName = 'Null';
    private static $browserVersion = '';
    private static $systemString = '';
    private static $osPlatform = '';
    private static $osVersion = '';
    private static $osShortVersion = '';
    //*
    public static $isDesktop = false;
    public static $isTablet = false;
    public static $isMobile = false;
    //*
    public static $isAndroid = false;
    public static $isIOS = false;
    public static $isWindows = false;
    public static $isLinux = false;
    public static $isMac = false;
    //*
    private static $mobileName = '';
    private static $osArch = '';
    private static $isIntel = false;
    private static $isAMD = false;
    private static $isPPC = false;

    public static function Parse($user_agent_string = null)
    {
        self::$string = $user_agent_string;
        self::$string = self::removeLocales(self::$string);
        self::analyzeString();
        $agentInfo = array();
        $agentInfo['browserName'] = self::$browserName;
        $agentInfo['browserVersion'] = self::$browserVersion;
        $agentInfo['osPlatform'] = self::$osPlatform;
        $agentInfo['osVersion'] = self::$osVersion;
        $agentInfo['osArch'] = self::$osArch;
        $agentInfo['isAndroid'] = self::$isAndroid;
        $agentInfo['isIOS'] = self::$isIOS;
        $agentInfo['isWindows'] = self::$isWindows;
        $agentInfo['isLinux'] = self::$isLinux;
        $agentInfo['isMac'] = self::$isMac;
        $agentInfo['isDesktop'] = self::$isDesktop;
        $agentInfo['isTablet'] = self::$isTablet;
        $agentInfo['isMobile'] = self::$isMobile;
        //*
        $name = '';
        if (self::$isAndroid == false && self::$isIOS == false) {
            if (self::$isWindows == 1) {
                self::$mobileName = 'Windows ' . self::$osVersion;
                $name = (self::$osArch == '64') ? ' (x64)' : ' (x84)';
            } else if (self::$isLinux == 1) {
                self::$mobileName = 'Linux';
                if (strpos(self::$string, 'Ubuntu'))
                    $name = ' (Ubuntu ' . self::$osVersion . ')';
                else $name = ' (' . self::$osVersion . ')';
            } else if (self::$isMac == 1) {
                self::$mobileName = 'Macintosh';
                $s1 = self::$osVersion ? ' v' : '';
                if (self::$isIntel)
                    $name = ' (Intel' . $s1 . self::$osVersion . ')';
                else if (self::$isAMD)
                    $name = ' (AMD' . $s1 . self::$osVersion . ')';
                else if (self::$isPPC)
                    $name = ' (PPC' . $s1 . self::$osVersion . ')';
            }
        } else if (self::$isIOS)
            $name = ' (v' . self::$osVersion . ')';
        else if (self::$isAndroid)
            $name = ' (v' . self::$osVersion . ')';
        //*
        $agentInfo['deviceName'] = self::$mobileName . $name;
        return json_encode($agentInfo);
    }

    private static function analyzeString()
    {
        if (strpos(self::$string, 'Windows') || (strpos(self::$string, 'Linux') || strpos(self::$string, 'X11')) && !strpos(self::$string, 'Android')  || strpos(self::$string, 'Macintosh')) {
            self::$isDesktop = 1;
            if (strpos(self::$string, 'Windows')) self::$isWindows = 1;
            else if (strpos(self::$string, 'Linux') || strpos(self::$string, 'X11')) self::$isLinux = 1;
            else  self::$isMac = 1;
        } else if (strpos(self::$string, 'iPhone') !== false || strpos(self::$string, 'Android') !== false) {
            self::$isMobile = 1;
            if (strpos(self::$string, 'iPhone')) self::$isIOS = 1;
            else {
                self::$isAndroid = 1;
                self::$osPlatform = 'Android';
                self::$osVersion = '5.0.0';
                self::$mobileName = 'Mobile';
            }
        } else if (strpos(self::$string, 'iPad') !== false) {
            self::$isTablet = 1;
            self::$isIOS  = 1;
        }

        self::analyzeBrowser();
        self::analyzePlatform();
    }

    private static function analyzeBrowser()
    {
        if (preg_match('/(?:Opera\/([0-9\.\w]+)\s\((.+?)\)(?:(?=.*Version\/).*Version\/([0-9\.\w]+)|.*))|\((.+?)\).+?Opera(?:[\s\/]([0-9\.\w]+))?/', self::$string, $opera)) {
            //Opera
            self::$browserName = 'Opera';
            self::$browserVersion = isset($opera[5]) ? $opera[5] : (isset($opera[3]) ? $opera[3] : (isset($opera[1]) ? $opera[1] : ''));
            self::$systemString = isset($opera[4]) ? $opera[4] : $opera[2];
        } else if (preg_match('/(?:MSIE\s+([0-9\.\w]+)(?:(?=.+Win).+?(Win.+[0-9\.\w]+)|.*))|\((.+?);.+?Trident.*?rv:([0-9\.\w]+)/i', self::$string, $ie)) {
            //Edge
            self::$browserName = 'Internet Explorer';
            self::$browserVersion = isset($ie[4]) ? $ie[4] : $ie[1];
            self::$systemString = isset($ie[3]) ? $ie[3] : (isset($ie[2]) ? $ie[2] : '');
        } else if (preg_match('/\((.+?)(?:(?=.*rv:)[\s;]+rv:([\.\d\w]+)|\)).+?Gecko.+?Firefox[\s\/]?([\w\d\.]+)?/i', self::$string, $mozilla)) {
            // Firefox
            self::$browserName = 'Mozilla Firefox';
            self::$browserVersion = isset($mozilla[3]) ? $mozilla[3] : $mozilla[2];
            self::$systemString = $mozilla[1];
        } else if (preg_match('/(?:\((.+?)\).+?Chrome\/([\d\.\w]+))|Chrome\/([\d\.\w]+).+?\((.+?)\)(?:(?=.*Version\/).+?Version\/([\d\w\.]+)|.*?)/i', self::$string, $chrome)) {
            // Chrome
            self::$browserName = 'Google Chrome';
            self::$browserVersion = isset($chrome[5]) ? $chrome[5] : (isset($chrome[3]) ? $chrome[3] : $chrome[2]);
            self::$systemString = isset($chrome[4]) ? $chrome[4] : (isset($chrome[1]) ? $chrome[1] : '');
        } else if (preg_match('/\((.+?)\).+?AppleWebKit(?:(?=.*Version\/).*?Version\/([\d\w\.]+)|.*?)(?:(?=.*Safari\/).*?Safari\/([\d\w\.]+)|.*?)/i', self::$string, $safari)) {
            if (self::$isAndroid) {
                //Android Browser
                self::$browserName = 'Android Browser';
                self::$browserVersion = isset($safari[3]) ? $safari[3] : (isset($safari[2]) ? $safari[2] : '');
                self::$systemString = $safari[1];
            } else {
                if (preg_match('/(?:\((.+?)\).+?Safari\/([\d\.\w]+))|Safari\/([\d\.\w]+).+?\((.+?)\)(?:(?=.*Version\/).+?Version\/([\d\w\.]+)|.*?)/i', self::$string, $theGym)) {
                    // IOS
                    self::$browserName = 'Safari';
                    self::$browserVersion = isset($theGym[5]) ? $theGym[5] : (isset($theGym[3]) ? $theGym[3] : $theGym[2]);
                    self::$systemString = isset($theGym[4]) ? $theGym[4] : (isset($theGym[1]) ? $theGym[1] : '');
                } else if (preg_match('/(?:\((.+?)\).+?Mobile\/([\d\.\w]+))|Mobile\/([\d\.\w]+).+?\((.+?)\)(?:(?=.*Version\/).+?Version\/([\d\w\.]+)|.*?)/i', self::$string, $theGym)) {
                    // Mobile
                    self::$browserName = 'Mobile';
                    self::$browserVersion = isset($theGym[5]) ? $theGym[5] : (isset($theGym[3]) ? $theGym[3] : $theGym[2]);
                    self::$systemString = isset($theGym[4]) ? $theGym[4] : (isset($theGym[1]) ? $theGym[1] : '');
                } else if (preg_match('/(?:\((.+?)\).+?AppleWebKit\/([\d\.\w]+))|AppleWebKit\/([\d\.\w]+).+?\((.+?)\)(?:(?=.*Version\/).+?Version\/([\d\w\.]+)|.*?)/i', self::$string, $theGym)) {
                    //TODO
                    self::$browserName = 'AppleWebKit';
                    self::$browserVersion = $theGym[2];
                    self::$systemString = $theGym[1];
                }
            }
        } else if (preg_match('/(?:\((.+?)\).+?Minefield\/([\d\.\w]+))|Minefield\/([\d\.\w]+).+?\((.+?)\)(?:(?=.*Version\/).+?Version\/([\d\w\.]+)|.*?)/i', self::$string, $theGym)) {
            // Minefield
            self::$browserName = 'Minefield';
            self::$browserVersion = isset($theGym[5]) ? $theGym[5] : (isset($theGym[3]) ? $theGym[3] : $theGym[2]);
            self::$systemString = isset($theGym[4]) ? $theGym[4] : (isset($theGym[1]) ? $theGym[1] : '');
        } else if (preg_match('/(?:\((.+?)\).+?Netscape\/([\d\.\w]+))|Netscape\/([\d\.\w]+).+?\((.+?)\)(?:(?=.*Version\/).+?Version\/([\d\w\.]+)|.*?)/i', self::$string, $theGym)) {
            // Netscape
            self::$browserName = 'Netscape';
            self::$browserVersion = isset($theGym[5]) ? $theGym[5] : (isset($theGym[3]) ? $theGym[3] : $theGym[2]);
            self::$systemString = isset($theGym[4]) ? $theGym[4] : (isset($theGym[1]) ? $theGym[1] : '');
        } else if (preg_match('/(?:\((.+?)\).+?Gecko\/([\d\.\w]+))|Gecko\/([\d\.\w]+).+?\((.+?)\)(?:(?=.*Version\/).+?Version\/([\d\w\.]+)|.*?)/i', self::$string, $theGym)) {
            // Gecko
            self::$browserName = 'Gecko';
            self::$browserVersion = isset($theGym[5]) ? $theGym[5] : (isset($theGym[3]) ? $theGym[3] : $theGym[2]);
            self::$systemString = isset($theGym[4]) ? $theGym[4] : (isset($theGym[1]) ? $theGym[1] : '');
        } else {
            $a = strpos(self::$string, '(') + 1;
            $b = strpos(self::$string, ')');
            self::$systemString = trim(mb_substr(self::$string, $a, $b - $a));
        }
    }

    private static function analyzePlatform()
    {
        if (self::$systemString) {
            // Mobile
            if (self::$isAndroid) {
                // No Arch for Mobiles
                self::$osArch = null;
                if (preg_match('/Android.*?([\d\.]+)(?:(?=).+?\b([\w\d\_\-\s]+)|.*?)/i', self::$systemString, $info)) {
                    // Android 
                    self::$osPlatform = 'Android';
                    self::$osVersion = $info[1];
                    self::$mobileName = isset($info[2]) ? str_replace(' Build', '', $info[2]) : 'null';
                } else if (preg_match('/((?:iPhone)|(?:iPad)|(?:iPod)).+?OS\s([\d\_\w\.]+)/i', self::$systemString, $info)) {
                    // IOS
                    self::$osPlatform = 'iOS';
                    self::$osVersion = self::$osShortVersion = str_replace('_', '.', $info[2]);
                    self::$mobileName = $info[1];
                } else if (preg_match('/Windows\sPhone\s(?:OS\s)?([\d\_\w\.]+)(?: (?=.*?(NOKIA|SAMSUNG|LG)) .+?\2 (?: (?=.{4,}$) .*?\b([\w\d\-\s]+)\b|.*?  )|.*?)/x', self::$systemString, $info)) {
                    // Windows Phone
                    self::$osPlatform = 'Windows Phone';
                    self::$osVersion = self::$osShortVersion = str_replace('_', '.', $info[1]);
                    self::$mobileName = @$info[2] . ' ' . @$info[3];
                }
            }
            //IOS
            else if (preg_match('/((?:iPhone)|(?:iPad)|(?:iPod)).+?OS\s([\d\_\w\.]+)/i', self::$systemString, $info)) {
                // IOS
                self::$osPlatform = 'iOS';
                self::$osVersion = self::$osShortVersion = str_replace('_', '.', $info[2]);
                self::$mobileName = $info[1];
            }
            // Computer
            else if (strpos(self::$systemString, 'Macintosh') !== false) {
                // Macintosh
                self::$osPlatform = 'Macintosh';
                if (preg_match('/(\w+)\sMac\sOS\sX\s?([\d_\.]+)?/i', self::$systemString, $info)) {
                    self::$osVersion = isset($info[2]) ? str_replace('_', '.', $info[2]) : '';
                    self::setOSShortVersionWithout(0);
                    if (self::$osShortVersion >= 6) {
                        self::$isIntel = 1;
                        if (self::$osShortVersion >= 7)
                            self::$osArch = '64';
                        else
                            self::checkArch();
                    } else {
                        if ($info[1] == 'PPC')
                            self::$isPPC = 1;
                        else
                            self::$isIntel = 1;
                        self::checkArch();
                    }
                } else if (strpos(self::$systemString, 'PPC')) {
                    self::$isPPC = 1;
                    if (preg_match('/rv.*?([\w\.]+)(?:(?=).+?\b|.*?)/i', self::$string, $info)) {
                        self::$osVersion = $info[1];
                    }
                }
            }
            // Windows OR compatible
            else if (strpos(self::$systemString, 'Windows') !== false || strpos(self::$systemString, 'compatible')) {
                // Windows
                self::$osPlatform = 'Windows';
                if (preg_match('/Windows\s(?:NT\s)?([\.\d]+)/i', self::$systemString, $info))
                    self::$osShortVersion = self::$osVersion = $info[1];
                self::checkArch();
            } else if (strpos(self::$systemString, 'X11') !== false || strpos(self::$systemString, 'Linux') !== false) {
                // Linux
                self::$osPlatform = 'Linux';
                self::$isLinux = 1;
                self::$isDesktop  = 1;
                if (preg_match('/rv.*?([\w\.]+)(?:(?=).+?\b|.*?)/i', self::$string, $info)) {
                    self::$osVersion = $info[1];
                } else if (preg_match('/Linux.*?([\w\.]+)(?:(?=).+?\b|.*?)/i', self::$systemString, $info)) {
                    self::$osVersion = $info[1];
                }
                self::checkArch();
            }
        } else if (strpos(self::$string, 'Mac_PowerPC') || strpos(self::$string, 'Macintosh')) {
            self::$isMac = 1;
            self::$isDesktop = 1;
            self::$osPlatform = 'Macintosh';
            if (strpos(self::$string, 'Macintosh')) {
                if (strpos(self::$string, 'PPC')) {
                    self::$isPPC = 1;
                    if (preg_match('/rv.*?([\w\.]+)(?:(?=).+?\b|.*?)/i', self::$string, $info)) {
                        self::$osVersion = $info[1];
                    }
                }
            }
        }
    }

    private static function checkArch()
    {
        if (preg_match('/((?:x86_64)|(?:x86-64)|(?:Win64)|(?:WOW64)|(?:x64)|(?:ia64)) | (amd64) | (ppc64) | (sparc64) | (IRIX64)/ix', self::$systemString, $info)) {
            // Set 64 Architecture
            self::$osArch = '64';
            if (!empty($info[1]))
                self::$isIntel = 1;
            else if (!empty($info[2]))
                self::$isAMD = 1;
            else if (!empty($info[3]))
                self::$isPPC = 1;
        } else {
            // Set 32 Architecture
            self::$osArch = '32';
            // Set CPU Brand
            if (strpos('amd', self::$systemString) !== false)
                self::$isAMD = 1;
            elseif (strpos('i386', self::$systemString) !== false || strpos('x86', self::$systemString) !== false || strpos('ia32', self::$systemString) !== false)
                self::$isIntel = 1;
        }
    }

    private static function setOSShortVersionWithout($excluded_index)
    {
        if ($excluded_index === null) {
            self::$osShortVersion = self::$osVersion;
            return;
        }
        $parts = explode('.', self::$osVersion);
        unset($parts[$excluded_index]);
        self::$osShortVersion = implode('.', $parts);
    }

    public function __get($property)
    {
        if (property_exists($this, $property))
            return self::$$property;
        else
            throw new \RuntimeException('Property Does not Exists: ' . __CLASS__ . '::' . $property);
    }

    public static function removeLocales($str)
    {
        $res = str_replace(array(
            " af-za;", " am-et;", " ar-ae;", " ar-bh;", " ar-dz;", " ar-eg;", " ar-iq;",
            " ar-jo;", " ar-kw;", " ar-lb;", " ar-ly;", " ar-ma;",
            " arn-cl;", " ar-om;", " ar-qa;", " ar-sa;", " ar-sd;", " ar-sy;",
            " ar-tn;", " ar-ye;", " as-in;", " az-az;", " az-cyrl-az;",
            " az-latn-az;", " ba-ru;", " be-by;", " bg-bg;", " bn-bd;",
            " bn-in;", " bo-cn;", " br-fr;", " bs-cyrl-ba;", " bs-latn-ba;",
            " ca-es;", " co-fr;", " cs-cz;", " cy-gb;", " da-dk;", " de-at;",
            " de-ch;", " de-de;", " de-li;", " de-lu;", " dsb-de;", " dv-mv;",
            " el-cy;", " el-gr;", " en-029;", " en-au;", " en-bz;", " en-ca;",
            " en-cb;", " en-gb;", " en-ie;", " en-in;", " en-jm;", " en-mt;",
            " en-my;", " en-nz;", " en-ph;", " en-sg;", " en-tt;", " en-us;",
            " en-za;", " en-zw;", " es-ar;", " es-bo;", " es-cl;", " es-co;",
            " es-cr;", " es-do;", " es-ec;", " es-es;", " es-gt;", " es-hn;",
            " es-mx;", " es-ni;", " es-pa;", " es-pe;", " es-pr;", " es-py;",
            " es-sv;", " es-us;", " es-uy;", " es-ve;", " et-ee;", " eu-es;",
            " fa-ir;", " fi-fi;", " fil-ph;", " fo-fo;", " fr-be;", " fr-ca;",
            " fr-ch;", " fr-fr;", " fr-lu;", " fr-mc;", " fy-nl;", " ga-ie;", " gd-gb;",
            " gd-ie;", " gl-es;", " gsw-fr;", " gu-in;", " ha-latn-ng;", " he-il;",
            " hi-in;", " hr-ba;", " hr-hr;", " hsb-de;", " hu-hu;", " hy-am;", " id-id;",
            " ig-ng;", " ii-cn;", " in-id;", " is-is;", " it-ch;", " it-it;", " iu-cans-ca;",
            " iu-latn-ca;", " iw-il;", " ja-jp;", " ka-ge;", " kk-kz;", " kl-gl;", " km-kh;",
            " kn-in;", " kok-in;", " ko-kr;", " ky-kg;", " lb-lu;", " lo-la;", " lt-lt;",
            " lv-lv;", " mi-nz;", " mk-mk;", " ml-in;", " mn-mn;", " mn-mong-cn;",
            " moh-ca;", " mr-in;", " ms-bn;", " ms-my;", " mt-mt;", " nb-no;", " ne-np;",
            " nl-be;", " nl-nl;", " nn-no;", " no-no;", " nso-za;", "oc-fr;", "or-in;",
            " pa-in;", " pl-pl;", " prs-af;", " ps-af;", " pt-br;", " pt-pt;", " qut-gt;",
            " quz-bo;", " quz-ec;", " quz-pe;", " rm-ch;", " ro-mo;", " ro-ro;", " ru-mo;",
            " ru-ru;", " rw-rw;", " sah-ru;", " sa-in;", " se-fi;", " se-no;", " se-se;",
            " si-lk;", " sk-sk;", " sl-si;", " sma-no;", " sma-se;", " smj-no;", " smj-se;",
            " smn-fi;", " sms-fi;", " sq-al;", " sr-ba;", " sr-cs;", " sr-cyrl-ba;", " sr-cyrl-cs;",
            " sr-cyrl-me;", " sr-cyrl-rs;", " sr-latn-ba;", " sr-latn-cs;", " sr-latn-me;",
            " sr-latn-rs;", " sr-me;", " sr-rs;", " sr-sp;", " sv-fi;", " sv-se;", " sw-ke;",
            " syr-sy;", " ta-in;", " te-in;", " tg-cyrl-tj;", " th-th;", " tk-tm;", " tlh-qs;",
            " tn-za;", " tr-tr;", " tt-ru;", " tzm-latn-dz;", " ug-cn;", " uk-ua;", " ur-pk;",
            " uz-cyrl-uz;", " uz-latn-uz;", " uz-uz;", " vi-vn;", " wo-sn;", " xh-za;", " yo-ng;",
            " zh-cn;", " zh-hk;", " zh-mo;", " zh-sg;", " zh-tw;", " zu-za;",
            " af-ZA;", " am-ET;", " ar-AE;", " ar-BH;", " ar-DZ;", " ar-EG;",
            " ar-IQ;", " ar-JO;", " ar-KW;", " ar-LB;", " ar-LY;", " ar-MA;", " arn-CL;", " ar-OM;",
            " ar-QA;", " ar-SA;", " ar-SD;", " ar-SY;", " ar-TN;", " ar-YE;", " as-IN;", " az-az;", " az-Cyrl-AZ;", " az-Latn-AZ;",
            " ba-RU;", " be-BY;", " bg-BG;", " bn-BD;", " bn-IN;", " bo-CN;", " br-FR;", " bs-Cyrl-BA;", " bs-Latn-BA;", " ca-ES;",
            " co-FR;", " cs-CZ;", " cy-GB;", " da-DK;", " de-AT;", " de-CH;", " de-DE;", " de-LI;", " de-LU;", " dsb-DE;",
            " dv-MV;", " el-CY;", " el-GR;", " en-029;", " en-AU;", " en-BZ;", " en-CA;", " en-cb;", " en-GB;", " en-IE;", " en-IN;",
            " en-JM;", " en-MT;", " en-MY;", " en-NZ;", " en-PH;", " en-SG;", " en-TT;", " en-US;", " en-ZA;", " en-ZW;", " es-AR;",
            " es-BO;", " es-CL;", " es-CO;", " es-CR;", " es-DO;", " es-EC;", " es-ES;", " es-GT;", " es-HN;", " es-MX;", " es-NI;",
            " es-PA;", " es-PE;", " es-PR;", " es-PY;", " es-SV;", " es-US;", " es-UY;", " es-VE;", " et-EE;", " eu-ES;", " fa-IR;",
            " fi-FI;", " fil-PH;", " fo-FO;", " fr-BE;", " fr-CA;", " fr-CH;", " fr-FR;", " fr-LU;", " fr-MC;", " fy-NL;", " ga-IE;",
            " gd-GB;", " gd-ie;", " gl-ES;", " gsw-FR;", " gu-IN;", " ha-Latn-NG;", " he-IL;", " hi-IN;", " hr-BA;", " hr-HR;", " hsb-DE;",
            " hu-HU;", " hy-AM;", " id-ID;", " ig-NG;", " ii-CN;", " in-ID;", " is-IS;", " it-CH;", " it-IT;", " iu-Cans-CA;",
            " iu-Latn-CA;", " iw-IL;", " ja-JP;", " ka-GE;", " kk-KZ;", " kl-GL;", " km-KH;", " kn-IN;", " kok-IN;", " ko-KR;", " ky-KG;",
            " lb-LU;", " lo-LA;", " lt-LT;", " lv-LV;", " mi-NZ;", " mk-MK;", " ml-IN;", " mn-MN;", " mn-Mong-CN;", " moh-CA;", " mr-IN;",
            " ms-BN;", " ms-MY;", " mt-MT;", " nb-NO;", " ne-NP;", " nl-BE;", " nl-NL;", " nn-NO;", " no-no;", " nso-ZA;", " oc-FR;",
            " or-IN;", " pa-IN;", " pl-PL;", " prs-AF;", " ps-AF;", " pt-BR;", " pt-PT;", " qut-GT;", " quz-BO;", " quz-EC;", " quz-PE;",
            " rm-CH;", " ro-mo;", " ro-RO;", " ru-mo;", " ru-RU;", " rw-RW;", " sah-RU;", " sa-IN;", " se-FI;", " se-NO;", " se-SE;",
            " si-LK;", " sk-SK;", " sl-SI;", " sma-NO;", " sma-SE;", " smj-NO;", " smj-SE;", " smn-FI;", " sms-FI;", " sq-AL;", " sr-BA;",
            " sr-CS;", " sr-Cyrl-BA;", " sr-Cyrl-CS;", " sr-Cyrl-ME;", " sr-Cyrl-RS;", " sr-Latn-BA;", " sr-Latn-CS;", " sr-Latn-ME;",
            " sr-Latn-RS;", " sr-ME;", " sr-RS;", " sr-sp;", " sv-FI;", " sv-SE;", " sw-KE;", " syr-SY;", " ta-IN;", " te-IN;", " tg-Cyrl-TJ;",
            " th-TH;", " tk-TM;", " tlh-QS;", " tn-ZA;", " tr-TR;", " tt-RU;", " tzm-Latn-DZ;", " ug-CN;", " uk-UA;", " ur-PK;", " uz-Cyrl-UZ;",
            " uz-Latn-UZ;", " uz-uz;", " vi-VN;", " wo-SN;", " xh-ZA;", " yo-NG;", " zh-CN;", " zh-HK;", " zh-MO;", " zh-SG;", " zh-TW;", " zu-ZA;"
        ), '', $str);
        return $res;
    }
}
