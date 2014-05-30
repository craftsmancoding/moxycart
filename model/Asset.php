<?php
/**
 * Asset
 * For asset management
 */
namespace Moxycart;
class Asset extends BaseModel {

    public $xclass = 'Asset';
    public $default_sort_col = 'title';

    public $src_file;
    public $dst_file;
    public $target_dir;
    
    /**
     * We house our exceptional tantrums here.
     * Use isNew()  getPK() -- gets the name  getPrimaryKey() -- gets the value
     */
    private function _validFile($src) {
        if (!is_scalar($src)) {
            throw new \Exception('Invalid data type for path');
        }
        if (!file_exists($src)) {
            throw new \Exception('File not found '.$src);
        }
        if (is_dir($src)) {
            throw new \Exception('File must not be a directory '.$src);
        }    
    }

    /**
     * Helps check for filename conflicts: given the desired name for the file,
     * this will see if the file already exists, and if so, it will generate a 
     * unique filename for the file while preserving the extension and the basename
     * of the file. E.g. if "x.txt" exists, then this returns "x 1.txt"
     *
     * @param string $dst full path candidate filename.
     * @param string $space_char (optional) to define the character that appears after 
     *      the filename but before the n integer and the extension.
     * @return string
     */
    public function getUniqueFilename($dst,$space_char=' ') {
        if (!file_exists($dst)) {
            return $dst;
        }
        
        // dirname : omits trailing slash
        // basename : same as basename()
        // extension : omits period
        // filename : w/o extension
        $p = pathinfo($dst);
        $i = 1;
        while(true){
            $filename = $p['dirname'].'/'.$p['filename'].$space_char.$i.'.'.$p['extension'];
            if (!file_exists($filename)) break;
            $i++;
        }
        return $filename;
    }
    
    /**
     *
     *
     */
    public function makeThumbnail() {
        return '';
        //Image::thumbnail($fullpath,$thumbnail_path,$thumb_w);
    }
    
    /**
     * Given a filename, this checks whether the asset already exists by
     * examining its md5 signature. 
     *
     * @string $src filename
     * @return mixed : object of the existing asset on success, boolean false on fail.
     */
    public function getExisting($src) {
        $this->_validFile($src);
        if ($obj = $this->modx->getObject('Asset',array('sig'=>md5_file($src)))) {
            $classname = '\\Moxycart\\'.$this->xclass;        
            return new $classname($this->modx, $obj); 
        }
            
        return false;
    }

    /**
     * Create a new asset object from a given file. This does not SAVE the object yet!
     * This needs to handle both uploaded files and existing files (e.g. manually uploaded).
     * If the file has just been uploaded, we move it to a temporary directory $tmpdir.
     *
     * Keep in mind that certain tasks are postponed until saving, including calculating 
     * the thumbnail and moving the file into position.
     * We have 2 parameters here for $src and $basename because the PHP upload functionality
     * ref's separate attributes in the $_FILES array: tmp_name and name.
     *
     * @param string $src file
     * @param string $basename string (optional, but when we use 
     * @param string $tmpdir temporary where uploaded assets are moved.
     *
     * @return object instance representing new or existing asset
     */
    public function fromFile($src,$basename=null,$tmpdir=null) {
        $this->_validFile($src);
        $obj = $this->modx->newObject($this->xclass);
        
        if (!$basename) $basename = basename($src);
        if (is_uploaded_file($src)) {
            $this->preparePath($tmpdir);
            $src = $this->uploadTmp($src,$basename,$tmpdir);
        }
        // These properties are not persisted, but we need them during saveTo()
        $obj->set('src_file',$src);
        $obj->set('src_basename',$basename);
        
        $obj->set('sig', md5_file($src));
        $obj->set('size', filesize($src));
           
        if ($info = $this->getImageInfo($src)) {
            $obj->set('is_image', 1);
            $obj->set('width', $info['width']);
            $obj->set('height', $info['height']);
            $obj->set('duration', $info['duration']);
        }
        else {
            $obj->set('is_image', 0);
        }

        $obj->set('content_type_id', $this->getContentType($src));
        
        $classname = '\\Moxycart\\'.$this->xclass;
        return new $classname($this->modx, $obj); 
    }
    
    
    /**
     * Given a full path to a file, this strips out the $prefix.
     * (default if null: MODX_ASSET_PATH . moxycart.upload_dir)
     * The result ALWAYS omits the leading slash, e.g. "/path/to/something.txt"
     * stripped of "/path/to" becomes "something.txt"
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
     * @return boolean path name on success (w trailing slash), Exception on fail
     */
    public function preparePath($path,$umask=0777) {

        if (!is_scalar($path)) {
            throw new \Exception('Invalid data type for path');
        }
        if (file_exists($path)) {
            if (is_dir($path)) {
                return rtrim($path,'/').'/'; // already done!
            }
            else {
                throw new \Exception('Path must be a directory. File found instead.');
            }
        }
        
        if (!mkdir($path,$umask,true)) {
            throw new \Exception('Failed to create directory '.$path);
        }

        return rtrim($path,'/').'/';
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
            $output['duration'] = '';
            $output['mime'] = $info['mime'];
            return $output;
        }
        return false;
    }
    
    /**
     * Determine whether or not the asset is hosted remotely by examining its url
     * @param string $url
     * @return boolean
     */
    public function isRemote($url) {
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * upload a file to a target directory
     *
     * @param string $tmp_name (from $_FILES['xyz']['tmp_name'])
     * @param string $name (basename from $_FILES['xyz']['name']) 
     * @param string $target_dir where we will write the uploaded file
     * @return string fullpath to new file location or Exception on fail
     */
    public function uploadTmp($tmp_name, $name, $target_dir) {
        $this->_validFile($tmp_name);
        $this->preparePath($target_dir);
        $candidate = rtrim($target_dir,'/').'/'.$name;
        $dst = $this->getUniqueFilename($candidate);
        if (@move_uploaded_file($tmp_name, $dst)) {
            return $dst; // success
        }
        throw new \Exception('Unable to move uploaded file '.$tmp_name.' to '.$dst);
    }
    
    /** 
     * Override parent so we can clean out the asset files
     *
     */
    public function remove($prefix=null) {
        if (!$prefix) {
            $prefix = $this->modx->getOption('assets_path').$this->modx->getOption('moxycart.upload_dir');
        }
        $file = $prefix.$this->modelObj->get('path');
        if (file_exists($file)) {
            if (!unlink($file)) {
                throw new \Exception('Failed to delete asset file.');
            }
        }
        // remove thumbnail
/*
        $file = $prefix.$this->modelObj->get('thumbnail_url');
        if (file_exists($file)) {
            if (!unlink($file)) {
                throw new \Exception('Failed to delete thumbnail file.');
            }
        }
*/
        
        return parent::remove();
    }  

    /** 
     * Recursively remove a non-empty directory
     *
     */
    public static function rrmdir($dir) { 
        if (is_dir($dir)) { 
            $dir = rtrim($dir,'/');
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != '.' && $object != '..') { 
                    if (filetype($dir.'/'.$object) == 'dir') {
                        self::rrmdir($dir.'/'.$object); 
                    }
                    else {
                        unlink($dir.'/'.$object); 
                    }
                } 
            } 
            reset($objects); 
            rmdir($dir); 
        } 
    }
    
    /**
     * Save the asset to the defined storage directory. This means that various sub-directories
     * will be created within the $storage_basedir.  In normal operation, pass this the 
     * moxycart.upload_dir setting.
     *
     * @param string $storage_basedir full path
     */
    public function saveTo($storage_basedir) {
        $storage_basedir = $this->preparePath($storage_basedir);
//print "\n".$storage_basedir."\n"; exit;
        $src = $this->modelObj->get('src_file');
        $basename = $this->modelObj->get('src_basename');
//        print "\n".$basename."\n"; exit;
        $this->_validFile($src);

        $target_dir = $this->preparePath($storage_basedir.$this->getCalculatedSubdir());
//print $target_dir; exit;        
        $dst = $this->getUniqueFilename($target_dir.$basename);
        if(!rename($src,$dst)) {
            throw new \Exception('Could not move file from '.$src.' to '.$dst);
        }

        $this->modelObj->set('path', $this->getRelPath($dst, $storage_basedir));
        $this->modelObj->set('url', $this->getRelPath($dst, $storage_basedir));
        $this->modelObj->set('thumbnail_url',$this->makeThumbnail());
        
        return $this->save();
    }
      
    
    /**
     * Override here to make the url and path relative to the defined moxycart.upload_dir
     */
    public function save() {
        // move to 
        // calculate thumbnail?
        // $result = Image::thumbnail($fullpath,$thumbnail_path,$thumb_w);
        //$this->preparePath($this->modelObj->get('target_dir'));
        return parent::save();
    }
}
/*EOF*/