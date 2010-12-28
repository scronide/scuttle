<?php
// UTF-8 functions
require_once dirname(__FILE__) .'/includes/utf8/utf8.php';

// Translation
require_once dirname(__FILE__) .'/includes/php-gettext/gettext.inc';
$domain = 'messages';
T_setlocale(LC_ALL, $locale);
T_bindtextdomain($domain, dirname(__FILE__) .'/locales');
T_bind_textdomain_codeset($domain, 'UTF-8');
T_textdomain($domain);

// Converts tags:
// - direction = out: convert spaces to underscores;
// - direction = in: convert underscores to spaces.
function convertTag($tag, $direction = 'out') {
  if ($direction == 'out') {
    return str_replace(' ', '_', $tag);
  }
  else {
    return str_replace('_', ' ', $tag);
  }
}

function filter($data, $type = NULL) {
    if (is_string($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        switch ($type) {
            case 'url':
                $data = rawurlencode($data);
                break;
            default:
                $data = htmlspecialchars($data);
                break;
        }
    } else if (is_array($data)) {
        foreach(array_keys($data) as $key) {
            $row =& $data[$key];
            $row = filter($row, $type);
        }
    }
    return $data;
}

function getPerPageCount() {
  global $defaultPerPage;
  return $defaultPerPage;
}

function getSortOrder($override = NULL) {
    global $defaultOrderBy;

    if (isset($_GET['sort'])) {
        return $_GET['sort'];
    } else if (isset($override)) {
        return $override;
    } else {
        return $defaultOrderBy;
    }
}

function multi_array_search($needle, $haystack) {
    if (is_array($haystack)) {
        foreach(array_keys($haystack) as $key) {
            $value =& $haystack[$key];
            $result = multi_array_search($needle, $value);
            if (is_array($result)) {
                $return = $result;
                array_unshift($return, $key);
                return $return;
            } elseif ($result == TRUE) {
                $return[] = $key;
                return $return;
            }
        }
        return FALSE;
    } else {
        if ($needle === $haystack) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

function createURL($page = '', $ending = '') {
    global $cleanurls, $root;
    if (!$cleanurls && $page != '') {
        $page .= '.php';
    }
    return $root . $page .'/'. $ending;
}

/**
 * Return the size of the given file in bytes
 */
function file_get_filesize($filename) {
  if (function_exists('curl_init')) {
    $ch = curl_init($filename);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    $data = curl_exec($ch);
    curl_close($ch);

    if (FALSE !== $data && preg_match('/Content-Length: (\d+)/', $data, $matches)) {
      return (float)$matches[1];
    }
  }
  return FALSE;
}

/**
 * Determine MIME type from a filename
 */
function file_get_mimetype($filename, $mapping = NULL) {
  if (!is_array($mapping)) {
    $mapping = array(
      'ez' => 'application/andrew-inset', 
      'atom' => 'application/atom', 
      'atomcat' => 'application/atomcat+xml', 
      'atomsrv' => 'application/atomserv+xml', 
      'cap|pcap' => 'application/cap', 
      'cu' => 'application/cu-seeme', 
      'tsp' => 'application/dsptype', 
      'spl' => 'application/x-futuresplash', 
      'hta' => 'application/hta', 
      'jar' => 'application/java-archive', 
      'ser' => 'application/java-serialized-object', 
      'class' => 'application/java-vm', 
      'hqx' => 'application/mac-binhex40', 
      'cpt' => 'image/x-corelphotopaint', 
      'nb' => 'application/mathematica', 
      'mdb' => 'application/msaccess', 
      'doc|dot' => 'application/msword', 
      'bin' => 'application/octet-stream', 
      'oda' => 'application/oda', 
      'ogg|ogx' => 'application/ogg', 
      'pdf' => 'application/pdf', 
      'key' => 'application/pgp-keys', 
      'pgp' => 'application/pgp-signature', 
      'prf' => 'application/pics-rules', 
      'ps|ai|eps' => 'application/postscript', 
      'rar' => 'application/rar', 
      'rdf' => 'application/rdf+xml', 
      'rss' => 'application/rss+xml', 
      'rtf' => 'application/rtf', 
      'smi|smil' => 'application/smil', 
      'wpd' => 'application/wordperfect', 
      'wp5' => 'application/wordperfect5.1', 
      'xhtml|xht' => 'application/xhtml+xml', 
      'xml|xsl' => 'application/xml', 
      'zip' => 'application/zip', 
      'cdy' => 'application/vnd.cinderella', 
      'kml' => 'application/vnd.google-earth.kml+xml', 
      'kmz' => 'application/vnd.google-earth.kmz', 
      'xul' => 'application/vnd.mozilla.xul+xml', 
      'xls|xlb|xlt' => 'application/vnd.ms-excel', 
      'cat' => 'application/vnd.ms-pki.seccat', 
      'stl' => 'application/vnd.ms-pki.stl', 
      'ppt|pps' => 'application/vnd.ms-powerpoint', 
      'odc' => 'application/vnd.oasis.opendocument.chart', 
      'odb' => 'application/vnd.oasis.opendocument.database', 
      'odf' => 'application/vnd.oasis.opendocument.formula', 
      'odg' => 'application/vnd.oasis.opendocument.graphics', 
      'otg' => 'application/vnd.oasis.opendocument.graphics-template', 
      'odi' => 'application/vnd.oasis.opendocument.image', 
      'odp' => 'application/vnd.oasis.opendocument.presentation', 
      'otp' => 'application/vnd.oasis.opendocument.presentation-template', 
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet', 
      'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template', 
      'odt' => 'application/vnd.oasis.opendocument.text', 
      'odm' => 'application/vnd.oasis.opendocument.text-master', 
      'ott' => 'application/vnd.oasis.opendocument.text-template', 
      'oth' => 'application/vnd.oasis.opendocument.text-web', 
      'docm' => 'application/vnd.ms-word.document.macroEnabled.12', 
      'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
      'dotm' => 'application/vnd.ms-word.template.macroEnabled.12', 
      'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', 
      'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12', 
      'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template', 
      'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12', 
      'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', 
      'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow', 
      'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', 
      'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 
      'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12', 
      'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12', 
      'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12', 
      'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
      'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12', 
      'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 
      'cod' => 'application/vnd.rim.cod', 
      'mmf' => 'application/vnd.smaf', 
      'sdc' => 'application/vnd.stardivision.calc', 
      'sds' => 'application/vnd.stardivision.chart', 
      'sda' => 'application/vnd.stardivision.draw', 
      'sdd' => 'application/vnd.stardivision.impress', 
      'sdf' => 'application/vnd.stardivision.math', 
      'sdw' => 'application/vnd.stardivision.writer', 
      'sgl' => 'application/vnd.stardivision.writer-global', 
      'sxc' => 'application/vnd.sun.xml.calc', 
      'stc' => 'application/vnd.sun.xml.calc.template', 
      'sxd' => 'application/vnd.sun.xml.draw', 
      'std' => 'application/vnd.sun.xml.draw.template', 
      'sxi' => 'application/vnd.sun.xml.impress', 
      'sti' => 'application/vnd.sun.xml.impress.template', 
      'sxm' => 'application/vnd.sun.xml.math', 
      'sxw' => 'application/vnd.sun.xml.writer', 
      'sxg' => 'application/vnd.sun.xml.writer.global', 
      'stw' => 'application/vnd.sun.xml.writer.template', 
      'sis' => 'application/vnd.symbian.install', 
      'vsd' => 'application/vnd.visio', 
      'wbxml' => 'application/vnd.wap.wbxml', 
      'wmlc' => 'application/vnd.wap.wmlc', 
      'wmlsc' => 'application/vnd.wap.wmlscriptc', 
      'wk' => 'application/x-123', 
      '7z' => 'application/x-7z-compressed', 
      'abw' => 'application/x-abiword', 
      'dmg' => 'application/x-apple-diskimage', 
      'bcpio' => 'application/x-bcpio', 
      'torrent' => 'application/x-bittorrent', 
      'cab' => 'application/x-cab', 
      'cbr' => 'application/x-cbr', 
      'cbz' => 'application/x-cbz', 
      'cdf' => 'application/x-cdf', 
      'vcd' => 'application/x-cdlink', 
      'pgn' => 'application/x-chess-pgn', 
      'cpio' => 'application/x-cpio', 
      'csh' => 'text/x-csh', 
      'deb|udeb' => 'application/x-debian-package', 
      'dcr|dir|dxr' => 'application/x-director', 
      'dms' => 'application/x-dms', 
      'wad' => 'application/x-doom', 
      'dvi' => 'application/x-dvi', 
      'rhtml' => 'application/x-httpd-eruby',
      'pages' => 'application/x-iwork-pages-sffpages',
      'flac' => 'application/x-flac', 
      'pfa|pfb|gsf|pcf|pcf.Z' => 'application/x-font', 
      'mm' => 'application/x-freemind', 
      'gnumeric' => 'application/x-gnumeric', 
      'sgf' => 'application/x-go-sgf', 
      'gcf' => 'application/x-graphing-calculator', 
      'gtar|tgz|taz' => 'application/x-gtar', 
      'hdf' => 'application/x-hdf', 
      'phtml|pht|php' => 'application/x-httpd-php', 
      'phps' => 'application/x-httpd-php-source', 
      'php3' => 'application/x-httpd-php3', 
      'php3p' => 'application/x-httpd-php3-preprocessed', 
      'php4' => 'application/x-httpd-php4', 
      'ica' => 'application/x-ica', 
      'ins|isp' => 'application/x-internet-signup', 
      'iii' => 'application/x-iphone', 
      'iso' => 'application/x-iso9660-image', 
      'jnlp' => 'application/x-java-jnlp-file', 
      'js' => 'application/x-javascript', 
      'jmz' => 'application/x-jmol', 
      'chrt' => 'application/x-kchart', 
      'kil' => 'application/x-killustrator', 
      'skp|skd|skt|skm' => 'application/x-koan', 
      'kpr|kpt' => 'application/x-kpresenter', 
      'ksp' => 'application/x-kspread', 
      'kwd|kwt' => 'application/x-kword', 
      'latex' => 'application/x-latex', 
      'lha' => 'application/x-lha', 
      'lyx' => 'application/x-lyx', 
      'lzh' => 'application/x-lzh', 
      'lzx' => 'application/x-lzx', 
      'frm|maker|frame|fm|fb|book|fbdoc' => 'application/x-maker', 
      'mif' => 'application/x-mif', 
      'wmd' => 'application/x-ms-wmd', 
      'wmz' => 'application/x-ms-wmz', 
      'com|exe|bat|dll' => 'application/x-msdos-program', 
      'msi' => 'application/x-msi', 
      'nc' => 'application/x-netcdf', 
      'pac' => 'application/x-ns-proxy-autoconfig', 
      'nwc' => 'application/x-nwc', 
      'o' => 'application/x-object', 
      'oza' => 'application/x-oz-application', 
      'p7r' => 'application/x-pkcs7-certreqresp', 
      'crl' => 'application/x-pkcs7-crl', 
      'pyc|pyo' => 'application/x-python-code', 
      'qtl' => 'application/x-quicktimeplayer', 
      'rpm' => 'application/x-redhat-package-manager', 
      'sh' => 'text/x-sh', 
      'shar' => 'application/x-shar', 
      'swf|swfl' => 'application/x-shockwave-flash', 
      'sit|sitx' => 'application/x-stuffit', 
      'sv4cpio' => 'application/x-sv4cpio', 
      'sv4crc' => 'application/x-sv4crc', 
      'tar' => 'application/x-tar', 
      'tcl' => 'application/x-tcl', 
      'gf' => 'application/x-tex-gf', 
      'pk' => 'application/x-tex-pk', 
      'texinfo|texi' => 'application/x-texinfo', 
      '~|%|bak|old|sik' => 'application/x-trash', 
      't|tr|roff' => 'application/x-troff', 
      'man' => 'application/x-troff-man', 
      'me' => 'application/x-troff-me', 
      'ms' => 'application/x-troff-ms', 
      'ustar' => 'application/x-ustar', 
      'src' => 'application/x-wais-source', 
      'wz' => 'application/x-wingz', 
      'crt' => 'application/x-x509-ca-cert', 
      'xcf' => 'application/x-xcf', 
      'fig' => 'application/x-xfig', 
      'xpi' => 'application/x-xpinstall',
      'aac' => 'audio/aac',
      'au|snd' => 'audio/basic', 
      'mid|midi|kar' => 'audio/midi', 
      'mpga|mpega|mp2|mp3|m4a' => 'audio/mpeg', 
      'f4a|f4b' => 'audio/mp4', 
      'm3u' => 'audio/x-mpegurl', 
      'oga|spx' => 'audio/ogg', 
      'sid' => 'audio/prs.sid', 
      'aif|aiff|aifc' => 'audio/x-aiff', 
      'gsm' => 'audio/x-gsm', 
      'wma' => 'audio/x-ms-wma', 
      'wax' => 'audio/x-ms-wax', 
      'ra|rm|ram' => 'audio/x-pn-realaudio', 
      'ra' => 'audio/x-realaudio', 
      'pls' => 'audio/x-scpls', 
      'sd2' => 'audio/x-sd2', 
      'wav' => 'audio/x-wav', 
      'alc' => 'chemical/x-alchemy', 
      'cac|cache' => 'chemical/x-cache', 
      'csf' => 'chemical/x-cache-csf', 
      'cbin|cascii|ctab' => 'chemical/x-cactvs-binary', 
      'cdx' => 'chemical/x-cdx', 
      'cer' => 'chemical/x-cerius', 
      'c3d' => 'chemical/x-chem3d', 
      'chm' => 'chemical/x-chemdraw', 
      'cif' => 'chemical/x-cif', 
      'cmdf' => 'chemical/x-cmdf', 
      'cml' => 'chemical/x-cml', 
      'cpa' => 'chemical/x-compass', 
      'bsd' => 'chemical/x-crossfire', 
      'csml|csm' => 'chemical/x-csml', 
      'ctx' => 'chemical/x-ctx', 
      'cxf|cef' => 'chemical/x-cxf', 
      'emb|embl' => 'chemical/x-embl-dl-nucleotide', 
      'spc' => 'chemical/x-galactic-spc', 
      'inp|gam|gamin' => 'chemical/x-gamess-input', 
      'fch|fchk' => 'chemical/x-gaussian-checkpoint', 
      'cub' => 'chemical/x-gaussian-cube', 
      'gau|gjc|gjf' => 'chemical/x-gaussian-input', 
      'gal' => 'chemical/x-gaussian-log', 
      'gcg' => 'chemical/x-gcg8-sequence', 
      'gen' => 'chemical/x-genbank', 
      'hin' => 'chemical/x-hin', 
      'istr|ist' => 'chemical/x-isostar', 
      'jdx|dx' => 'chemical/x-jcamp-dx', 
      'kin' => 'chemical/x-kinemage', 
      'mcm' => 'chemical/x-macmolecule', 
      'mmd|mmod' => 'chemical/x-macromodel-input', 
      'mol' => 'chemical/x-mdl-molfile', 
      'rd' => 'chemical/x-mdl-rdfile', 
      'rxn' => 'chemical/x-mdl-rxnfile', 
      'sd|sdf' => 'chemical/x-mdl-sdfile', 
      'tgf' => 'chemical/x-mdl-tgf', 
      'mcif' => 'chemical/x-mmcif', 
      'mol2' => 'chemical/x-mol2', 
      'b' => 'chemical/x-molconn-Z', 
      'gpt' => 'chemical/x-mopac-graph', 
      'mop|mopcrt|mpc|dat|zmt' => 'chemical/x-mopac-input', 
      'moo' => 'chemical/x-mopac-out', 
      'mvb' => 'chemical/x-mopac-vib', 
      'asn' => 'chemical/x-ncbi-asn1-spec', 
      'prt|ent' => 'chemical/x-ncbi-asn1-ascii', 
      'val|aso' => 'chemical/x-ncbi-asn1-binary', 
      'pdb|ent' => 'chemical/x-pdb', 
      'ros' => 'chemical/x-rosdal', 
      'sw' => 'chemical/x-swissprot', 
      'vms' => 'chemical/x-vamas-iso14976', 
      'vmd' => 'chemical/x-vmd', 
      'xtel' => 'chemical/x-xtel', 
      'xyz' => 'chemical/x-xyz', 
      'gif' => 'image/gif', 
      'ief' => 'image/ief', 
      'jpeg|jpg|jpe' => 'image/jpeg', 
      'pcx' => 'image/pcx', 
      'png' => 'image/png', 
      'svg|svgz' => 'image/svg+xml', 
      'tiff|tif' => 'image/tiff', 
      'djvu|djv' => 'image/vnd.djvu', 
      'wbmp' => 'image/vnd.wap.wbmp', 
      'ras' => 'image/x-cmu-raster', 
      'cdr' => 'image/x-coreldraw', 
      'pat' => 'image/x-coreldrawpattern', 
      'cdt' => 'image/x-coreldrawtemplate', 
      'ico' => 'image/x-icon', 
      'art' => 'image/x-jg', 
      'jng' => 'image/x-jng', 
      'bmp' => 'image/x-ms-bmp', 
      'psd' => 'image/x-photoshop', 
      'pnm' => 'image/x-portable-anymap', 
      'pbm' => 'image/x-portable-bitmap', 
      'pgm' => 'image/x-portable-graymap', 
      'ppm' => 'image/x-portable-pixmap', 
      'rgb' => 'image/x-rgb', 
      'xbm' => 'image/x-xbitmap', 
      'xpm' => 'image/x-xpixmap', 
      'xwd' => 'image/x-xwindowdump', 
      'eml' => 'message/rfc822', 
      'igs|iges' => 'model/iges', 
      'msh|mesh|silo' => 'model/mesh', 
      'wrl|vrml' => 'model/vrml', 
      'ics|icz' => 'text/calendar', 
      'css' => 'text/css', 
      'csv' => 'text/csv', 
      '323' => 'text/h323', 
      'html|htm|shtml' => 'text/html', 
      'uls' => 'text/iuls', 
      'mml' => 'text/mathml', 
      'asc|txt|text|pot' => 'text/plain', 
      'rtx' => 'text/richtext', 
      'sct|wsc' => 'text/scriptlet', 
      'tm|ts' => 'text/texmacs', 
      'tsv' => 'text/tab-separated-values', 
      'jad' => 'text/vnd.sun.j2me.app-descriptor', 
      'wml' => 'text/vnd.wap.wml', 
      'wmls' => 'text/vnd.wap.wmlscript', 
      'bib' => 'text/x-bibtex', 
      'boo' => 'text/x-boo', 
      'h++|hpp|hxx|hh' => 'text/x-c++hdr', 
      'c++|cpp|cxx|cc' => 'text/x-c++src', 
      'h' => 'text/x-chdr', 
      'htc' => 'text/x-component', 
      'c' => 'text/x-csrc', 
      'd' => 'text/x-dsrc', 
      'diff|patch' => 'text/x-diff', 
      'hs' => 'text/x-haskell', 
      'java' => 'text/x-java', 
      'lhs' => 'text/x-literate-haskell', 
      'moc' => 'text/x-moc', 
      'p|pas' => 'text/x-pascal', 
      'gcd' => 'text/x-pcs-gcd', 
      'pl|pm' => 'text/x-perl', 
      'py' => 'text/x-python', 
      'etx' => 'text/x-setext', 
      'tcl|tk' => 'text/x-tcl', 
      'tex|ltx|sty|cls' => 'text/x-tex', 
      'vcs' => 'text/x-vcalendar', 
      'vcf' => 'text/x-vcard', 
      '3gp' => 'video/3gpp', 
      'dl' => 'video/dl', 
      'dif|dv' => 'video/dv', 
      'fli' => 'video/fli', 
      'gl' => 'video/gl', 
      'mpeg|mpg|mpe' => 'video/mpeg', 
      'mp4|f4v|f4p' => 'video/mp4', 
      'flv' => 'video/x-flv', 
      'ogv' => 'video/ogg', 
      'qt|mov' => 'video/quicktime', 
      'mxu' => 'video/vnd.mpegurl', 
      'lsf|lsx' => 'video/x-la-asf', 
      'mng' => 'video/x-mng', 
      'asf|asx' => 'video/x-ms-asf', 
      'wm' => 'video/x-ms-wm', 
      'wmv' => 'video/x-ms-wmv', 
      'wmx' => 'video/x-ms-wmx', 
      'wvx' => 'video/x-ms-wvx', 
      'avi' => 'video/x-msvideo', 
      'm4v' => 'video/x-m4v',
      'movie' => 'video/x-sgi-movie', 
      'ice' => 'x-conference/x-cooltalk', 
      'sisx' => 'x-epoc/x-sisx-app', 
      'vrm|vrml|wrl' => 'x-world/x-vrml', 
      'xps' => 'application/vnd.ms-xpsdocument',
    );
  }
  foreach ($mapping as $ext_preg => $mime_match) {
    if (preg_match('!\.(' . $ext_preg . ')$!i', $filename)) {
      return $mime_match;
    }
  }

  return 'application/octet-stream';
}

function message_die($msg_code, $msg_text = '', $msg_title = '', $err_line = '', $err_file = '', $sql = '', $db = NULL) {
    if(defined('HAS_DIED'))
        die(T_('message_die() was called multiple times.'));
    define('HAS_DIED', 1);
  
  $sql_store = $sql;
  
  // Get SQL error if we are debugging. Do this as soon as possible to prevent 
  // subsequent queries from overwriting the status of sql_error()
  if (DEBUG && ($msg_code == GENERAL_ERROR || $msg_code == CRITICAL_ERROR)) {
    $sql_error = is_null($db) ? '' : $db->sql_error();
    $debug_text = '';
    
    if ($sql_error['message'] != '')
      $debug_text .= '<br /><br />'. T_('SQL Error') .' : '. $sql_error['code'] .' '. $sql_error['message'];

    if ($sql_store != '')
      $debug_text .= '<br /><br />'. $sql_store;

    if ($err_line != '' && $err_file != '')
      $debug_text .= '</br /><br />'. T_('Line') .' : '. $err_line .'<br />'. T_('File') .' :'. $err_file;
  }

  switch($msg_code) {
    case GENERAL_MESSAGE:
      if ($msg_title == '')
        $msg_title = T_('Information');
      break;

    case CRITICAL_MESSAGE:
      if ($msg_title == '')
        $msg_title = T_('Critical Information');
      break;

    case GENERAL_ERROR:
      if ($msg_text == '')
        $msg_text = T_('An error occured');

      if ($msg_title == '')
        $msg_title = T_('General Error');
      break;

    case CRITICAL_ERROR:
      // Critical errors mean we cannot rely on _ANY_ DB information being
      // available so we're going to dump out a simple echo'd statement

      if ($msg_text == '')
        $msg_text = T_('An critical error occured');

      if ($msg_title == '')
        $msg_title = T_('Critical Error');
      break;
  }

  // Add on DEBUG info if we've enabled debug mode and this is an error. This
  // prevents debug info being output for general messages should DEBUG be
  // set TRUE by accident (preventing confusion for the end user!)
  if (DEBUG && ($msg_code == GENERAL_ERROR || $msg_code == CRITICAL_ERROR)) {
    if ($debug_text != '')
      $msg_text = $msg_text . '<br /><br /><strong>'. T_('DEBUG MODE') .'</strong>'. $debug_text;
  }

  echo "<html>\n<body>\n". $msg_title ."\n<br /><br />\n". $msg_text ."</body>\n</html>";
  exit;
}
