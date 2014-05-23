<?php
/**
 * Asset
 * For asset management
 */
namespace Moxycart;
class Asset extends BaseModel {

    public $xclass = 'Asset';
    public $default_sort_col = 'name';

    /**
     * Create a new asset or find an existing one from a given file 
     * TODO: dynamically read duration via ffmpeg
     *
     * @param string $fullpath to file
     * @param array $props any additional props to set for the asset. Some props cannot be set here!
     * @param string $prefix base storage directory. Null will defer to the location 
     *      defined by moxycart.upload_dir System Setting. Override for testing.
     * @param string $thumb_prfix base storage directory for thumbnails
     * @return object instance representing new or existing asset
     */
    public function fromFile($fullpath, array $props=array(), $prefix=null,$thumb_prefix=null) {
        if (!is_scalar($fullpath)) {
            throw new \Exception('Invalid data type for path');
        }
        if (!$prefix) {
            $prefix = $this->modx->getOption('assets_path').$this->modx->getOption('moxycart.upload_dir');
        }
        if (!$thumb_prefix) {
            $thumb_prefix = $this->modx->getOption('assets_path').$this->modx->getOption('moxycart.thumbnail_dir');
        }
        // cleanup
        $prefix = rtrim($prefix,'/').'/'; 
        $thumb_prefix = rtrim($thumb_prefix,'/').'/'; 
        // Does the Asset exist already?
        $path = $this->getRelPath($fullpath, $prefix);
        if ($Asset = $this->modx->getObject($this->xclass, array('path'=>$path))) {
            $this->modelObj = $Asset;        
            $this->modelObj->set('prefix', $prefix); // pseudo cache
            return $Asset;
        }
        
        // Does the file exist?
        if (!file_exists($fullpath)) {
            throw new \Exception('File not found '.$filename);
        }        
        
        // No?  Then we create 
        $info = $this->getImageInfo($fullpath);        
        $props['content_type_id'] = (isset($props['content_type_id'])) ? $props['content_type_id'] : $this->getContentType($fullpath);
        $props['url'] = $path;
        $props['path'] = $path;
        if (!isset($props['thumbnail_url'])) {
            $thumbnail_path = $prefix.$this->modx->getOption('moxycart.thumbnail_dir').basename($fullpath);
//            print $thumbnail_path ."\n"; exit;
            $thumb_w = $this->modx->getOption('moxycart.thumbnail_width');
            $result = Image::thumbnail($fullpath,$thumbnail_path,$thumb_w);
//            print 'Result: '.$result."\n";
//            print 'Prefix: '.$prefix."\n";
//            print 'Thumb path: '.$thumbnail_path ."\n"; exit;
            $props['thumbnail_url'] = $this->getRelPath($result, $prefix);
        }
        $props['width'] = ($info) ? $info['width'] : 0;
        $props['height'] = ($info) ? $info['height'] : 0;
        $props['length'] = '';
        $props['size'] = filesize($fullpath);
        
        $this->modelObj = $this->modx->newObject($this->xclass);
        $this->modelObj->fromArray($props);
        return $this->modelObj;
    }
    
    /**
     * Given a full path to a file, this strips out the MODX_ASSET_PATH and moxycart.upload_dir
     * 
     * @param string $fullpath
     * @param mixed $prefix to remove. Leave null to use MODX settings
     */
    public function getRelPath($fullpath, $prefix=null) {
        if (!is_scalar($fullpath)) {
            throw new \Exception('Invalid data type for path');
        }
        if (!$prefix) {
            $prefix = $this->modx->getOption('assets_path').$this->modx->getOption('moxycart.upload_dir');
        }
        
        if (substr($fullpath, 0, strlen($prefix)) == $prefix) {
            return ltrim(substr($fullpath, strlen($prefix)),'/');
        }
        else {
            // either the path was to some other place, or it has already been made relative??
            throw new \Exception('Prefix not found in path');
        }
    }
    
    /**
     * Creates the assets path if is is not already there
     *
     * @param string $path full
     * @param string $umask default 0777
     * @return boolean true on success, Exception on fail
     */
    public function preparePath($path,$umask=0777) {

        if (!is_scalar($path)) {
            throw new \Exception('Invalid data type for path');
        }
        if (file_exists($path)) {
            if (is_dir($path)) {
                return true; // already done!
            }
            else {
                throw new \Exception('Path must be a directory. File found instead.');
            }
        }
        
        if (!mkdir($path,$umask,true)) {
            throw new \Exception('Failed to create directory '.$path);
        }

        return true;
    }

    /**
     * We must organize assets somehow into subfolders.
     *
     * @return string sub directory with trailing slash, e.g. "2014/05/28/"
     */
    public function getCalculatedSubdir() {
        return date('Y/m/d/');
    }

    /** 
     * Given a filename, get the file extension WITHOUT the period
     *
     * @param string $filename
     * @return string 
     */
    public function getExt($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /** 
     * Find a MODX content type based on a filename
     *
     * @param string $filename
     * @return integer primary key from modx_content_types or die
     */
    public function getContentType($filename) {
        if (!file_exists($filename)) {
            throw new \Exception('File not found '.$filename);
        }
        // More thorough is to lookup by the mime-type
        if (function_exists('finfo_file') && function_exists('finfo_open')) {
            $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Looking up content type for file '.$filename.' by mime-type','',__CLASS__,__FILE__,__LINE__);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($mime_type = finfo_file($finfo, $filename)) {
                if ($C = $this->modx->getObject('modContentType', array('mime_type'=>$mime_type))) {
                    return $C->get('id');
                }
            }
        }
        // Fallback to file extension
        $this->modx->log(\modX::LOG_LEVEL_DEBUG, 'Looking up content type for file '.$filename.' by file extension','',__CLASS__,__FILE__,__LINE__);
        if (!$ext = $this->getExt($filename)) {
            throw new \Exception('Extension not found '.$filename);
        }
        if ($C = $this->modx->getObject('modContentType', array('file_extensions'=>'.'.$ext))) {
            return $C->get('id');
        }
        
        throw new \Exception('Content type not defined.');
    }

    /**
     * Some cleaner repackaging of getimagesize
     *
     * @param string $filename full path to image
     * @return mixed array on success, false on fail
     */
    public function getImageInfo($filename) {
        if($info = getimagesize($filename)) {
            $output = array();
            $output['width'] = $info[0];
            $output['height'] = $info[1];
            $output['type'] = $info[2]; // <-- see http://www.php.net/manual/en/image.constants.php
            $output['mime'] = $info['mime'];
            return $output;
        }
        return false;
    }
    
    /** 
     * Override so we can clean out the asset files
     *
     */
    public function remove() {
        // remove file
        // remove thumbnail?? this may be a reusable thing
        return parent::remove();
//        return $this->modelObj->remove();
    }    
    /**
     * Override here to make the url and path relative to the defined moxycart.upload_dir
     */
    public function save() {
        // calculate thumbnail?
        return parent::save();
    }
}
/*EOF*/