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
     * @return object instance representing new or existing asset
     */
    public function fromFile($fullpath, array $props=array(), $prefix=null) {
        if (!is_scalar($fullpath)) {
            throw new \Exception('Invalid data type for path');
        }
        if (!$prefix) {
            $prefix = $this->modx->getOption('assets_path').$this->modx->getOption('moxycart.upload_dir');
        }
        $prefix = rtrim($prefix,'/').'/'; // cleanup        
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
        $info = $this->getImageSize($fullpath);        
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
     * @param string $path
     * @param string $umask default 0777
     * @return boolean true on success, Exception on fail
     */
    public function preparePath($path,$umask=0777) {

        if (!is_scalar($path)) {
            throw new \Exception('Invalid data type for path');
        }
        if (file_exists($path)) {
            if (is_dir($path)) {
                return true;
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
    public function getImageSize($filename) {
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
     * Crop an image in place (original file is destructively edited).
     * In this AddOn, an image MUST be rep'd by a Image, so the id must be set.
     * We return an <img> tag as well to save the hastle of another ajax post.
     * 
     * @params array $args including key for id
     * @return string JSON array
     */
    public function image_crop($args) { 

        
        $out = array(
            'success' => true,
            'msg' => '',
            'img' => ''
        );
        
        $id = (int) $this->modx->getOption('image_id', $args);
        $Image = $this->modx->getObject('Image', $id);
     
        $thumbnail_url = $Image->get('thumbnail_url');
        $filename = basename($thumbnail_url); 
        $target_path_thumb = dirname($thumbnail_url). '/';
        $target_path = MODX_ASSETS_PATH.dirname($target_path_thumb). '/';


        if (!$Image) {
            $out['success'] = false;
            $out['msg'] = 'Image and Image not found.';
            return json_encode($out);
        }
        // http://www.php.net/manual/en/function.imagecopy.php
        $src = $Image->get('path');
       // $src = $Image->get('url');
        if (!file_exists($src)) {
            $out['success'] = false;
            $out['msg'] = 'Image ('.$id.') Image not found: '.$src;
            return json_encode($out);            
        }
        
        $srcImg = '';
        $ext = strtolower(substr($src, -4));
        $image_func = '';
        $quality = null; // different vals for different funcs
        switch ($ext) {
            case '.gif':
                $srcImg = @imagecreatefromgif($src);
                $image_func = 'imagegif';
                break;
            case '.jpg':
            case 'jpeg':
                $srcImg = @imagecreatefromjpeg($src);
                $image_func = 'imagejpeg';
                $quality = 100;
                break;
            case '.png':
                $srcImg = @imagecreatefrompng($src);
                $image_func = 'imagepng';
                $quality = 0;
                break;
            default:
                $out['success'] = false;
                $out['msg'] = 'Image ('.$id.') Unrecognized extension: '.$ext;
                return json_encode($out);                            
        }
        
        if (!$srcImg) {
            $out['success'] = false;
            $out['msg'] = 'Image ('.$id.') could not create image: '.$src;
            return json_encode($out);                        
        }
        
        // Cleared for launch.
        $ratio = 1;
        if ($Image->get('width') > $this->max_image_width) {
            $ratio = $Image->get('width') / $this->max_image_width;
        }
        // Remember: order of ops for type-casting. (int) filters ONLY the variable to its right!!
        $src_x = (int) ($ratio * $this->modx->getOption('x',$args));
        $src_y = (int) ($ratio * $this->modx->getOption('y',$args));
        $src_w = (int) ($ratio * $this->modx->getOption('w',$args));
        $src_h = (int) ($ratio * $this->modx->getOption('h',$args));

        // Remember: at this point, if the user selects the full width of the *displayed*
        // image, it is not necessarily equal to the dimensions of the original image.
        $new_w = (int) ($ratio * $this->modx->getOption('w',$args));
        $new_h = (int) ($ratio * $this->modx->getOption('h',$args));
        $destImg = imagecreatetruecolor($src_w, $src_h);

        if (!imagecopy($destImg, $srcImg, 0, 0, $src_x, $src_y, $src_w, $src_h)) {
            $out['success'] = false;
            $out['msg'] = 'Image ('.$id.') could not crop image: '.$src;
            imagedestroy($srcImg);
            imagedestroy($destImg);
            return json_encode($out);                                    
        }
        
        if (!$image_func($destImg,$Image->get('path'),$quality)) {
            $out['success'] = false;
            $out['msg'] = 'Image ('.$id.') could not save cropped image: '.$src;
            imagedestroy($srcImg);
            imagedestroy($destImg);
            return json_encode($out);                                    
        }
        
        imagedestroy($srcImg);
        imagedestroy($destImg);

        $Image->set('height', $new_h);
        $Image->set('width', $new_w);
        $Image->set('size', filesize($Image->get('path')));

        if (!$Image->save()) {
            $out['success'] = false;
            $out['msg'] = 'Could not update Image: '.$id;            
            return json_encode($out);                                            
        }

         // start create new thumb
        $thumbnail_url = $Image->get('thumbnail_url');
        $filename = basename($thumbnail_url); 
        $target_path_thumb = substr(dirname($thumbnail_url),8). '/';
        $target_path = MODX_ASSETS_PATH.dirname($target_path_thumb). '/';
        $this->create_thumbnail($filename,$target_path,$target_path_thumb);
        // start create new thumb

        $out['msg'] = 'Image cropped successfully.';
        $out['img'] = $this->get_image_tag(array('image_id'=>$id));

        return json_encode($out);
    }
    
    
    /**
     * Generate a thumbnail from an image at a $src_path.
     * 
     *
     * @param string $src_path
     * @param string $target_path
     * @param integer $max_h in pixels
     * @param integer $max_w in pixels
     */
    public function thumbnail($src_path,$target_path,$max_h,$max_w) {
        if (!$info = $this->getImageSize($src_path)) {
            throw new \Exception('File not found '.$src_path);
        }
        $ext = $this->getExt($src_path);
        
        $srcImg = '';
        $image_func = '';
        $quality = null; // different vals for different funcs
        switch ($ext) {
            case 'gif':
                $srcImg = @imagecreatefromgif($src);
                $image_func = 'imagegif';
                break;
            case 'jpg':
            case 'jpeg':
                $srcImg = @imagecreatefromjpeg($src);
                $image_func = 'imagejpeg';
                $quality = 100;
                break;
            case 'png':
                $srcImg = @imagecreatefrompng($src);
                $image_func = 'imagepng';
                $quality = 0;
                break;
        }
        
        if (!$srcImg) {
            throw new \Exception('Could not read image '.$src_path);
        }
        
    }
    
    /** 
     * Override so we can clean out the asset files
     *
     */
    public function remove() {
        return parent::remove();
//        return $this->modelObj->remove();
    }    
    /**
     * Override here to make the url and path relative to the defined moxycart.upload_dir
     */
    public function save() {

        return parent::save();
    }
}
/*EOF*/