<?php
/**
 * Image
 * For image manipulation
 */
namespace Moxycart;
class Image {

    public $modx;

    /** 
     *
     * @param object $modx
     * @param integer primary key (optional) used when retrieving objects only
     *
     */
    public function __construct(\modX &$modx, $primary_key=null) {
        $this->modx =& $modx;
    }
    
    /**
     * Crops an image specified at path $src to path $dst. If $src = $target,
     * the image will be destructively edited in place.
     
     * $ratio is the ratio of image's ACTUAL pixel width to the pixel width that was DISPLAYED.
     * E.g. if the image was ACTUALLY 2000 pixels wide, but it had to be displayed at 1000 pixels
     * wide in order to fit onto the screen, then pass a ratio of 2.
     * This argument can be useful when reading parameters from various Javascript cropping libraries
     * (e.g. jCrop): just pass in the ratio between actual/displayed and the crop will adjust.
     *
     * @param string $src path
     * @param string $dst path
     * @param integer $x x-coordinate of crop area start point
     * @param integer $y y-coordinate of crop area start point
     * @param integer $w width of crop selection
     * @param integer $h height of crop selection
     * @param numeric $ratio multiplier of actual-width/displayed-width (default: 1)
     * @return string $dst
     */
    public static function crop($src,$dst,$x,$y,$w,$h,$ratio=1) { 
        if (!file_exists($src)) {
            throw new \Exception('File not found '.$src);
        }
        
        $src_img = '';
        $ext = strtolower(substr($src, -4));
        $image_func = '';
        $quality = null; // different vals for different funcs
        switch ($ext) {
            case '.gif':
                $src_img = @imagecreatefromgif($src);
                $image_func = 'imagegif';
                break;
            case '.jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($src);
                $image_func = 'imagejpeg';
                $quality = 100;
                break;
            case '.png':
                $src_img = @imagecreatefrompng($src);
                $image_func = 'imagepng';
                $quality = 0;
                break;
        }
        
        if (!$src_img) {
            throw new \Exception('Could not read image '.$src); 
        }
        if (!file_exists(dirname($dst))) {
            if (!mkdir(dirname($dst),0777,true)) {
                throw new \Exception('Failed to create directory '.dirname($dst));
            }            
        }

        // With jCrop, the user will select the full width of the *displayed* image, 
        // which is not necessarily equal to the dimensions of the original image, so we 
        // adjust values via the $ratio var.        
        // Remember: order of ops for type-casting. (int) filters ONLY the variable to its right!!
        $src_x = (int) ($ratio * $x);
        $src_y = (int) ($ratio * $y);
        $src_w = (int) ($ratio * $w);
        $src_h = (int) ($ratio * $h);

        $dst_img = imagecreatetruecolor($src_w, $src_h);

        // The un-aptly named: imagecopy: Copy part of src image to dst img defined by points on the src
        if (!imagecopy($dst_img, $src_img, 0, 0, $src_x, $src_y, $src_w, $src_h)) {
            imagedestroy($src_img);
            imagedestroy($dst_img);
            throw new \Exception('Could not crop image');
        }
        // Write the cropped image resource to the filesystem
        if (!$image_func($dst_img,$dst,$quality)) {
            imagedestroy($src_img);
            imagedestroy($dst_img);
            throw new \Exception('Could not save cropped image to '.$dst);
        }
        
        // Cleanup
        imagedestroy($src_img);
        imagedestroy($dst_img);

        return $dst;
    }

    /**
     * Scale an image to a new width maintaining aspect ratio.
     * Processes image at $src path and writes it to the $dst filename,
     * changing the image type according to the extensions detected.
     * This will attemp to create the destination directory if it 
     * does not exist. 
     *
     * Throws tantrums if things don't work out its way.
     *
     * @param string $src full path to source image
     * @param string $dst full name of image including path
     * @param integer $new_w new width in pixels
     * @return string $dst on success. Throws exception on fail.
     */
    public static function scale($src,$dst,$new_w) {
        // Careful!
        // is it an image?
        if (!in_array(strtolower(substr($dst, -4)), array('.jpg','jpeg','.gif','.png'))) {
            throw new \Exception('Destination file must be an image: '.$dst);
        }        
        if(file_exists($target)) {
            if (!unlink($target)) {
                throw new \Exception('Unable to overwrite destination file '.$dst);
            }
        }
        if (!file_exists(dirname($dst))) {
            if (!mkdir(dirname($dst),0777,true)) {
                throw new \Exception('Failed to create directory '.dirname($dst));
            }            
        }
        
        $ext = strtolower(substr($src, -4));
        switch ($ext) {
            case '.jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($src);
                break;
            case '.gif':
                $src_img = @imagecreatefromgif($src);
                break;
            case '.png':
                $src_img = @imagecreatefrompng($src);
                break;
        }
        if (!$src_img) {
            throw new \Exception('Failed to create image');
        }
        
        // old XY (from src) to new XY
        $ox = imagesx($src_img);
        $oy = imagesy($src_img);
        
        $nx = ( $ox >= $new_w ) ? $new_w : $ox;
        $ny = floor($oy * ($nx / $ox));
        
        $dst_img = imagecreatetruecolor($nx, $ny);

        // Special behavior for PNGs
        if ($ext=='.png') {
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($dst_img, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($dst_img, $background);

                // turning off alpha blending (to ensure alpha channel information 
                // is preserved, rather than removed (blending with the rest of the 
                // image in the form of black))
                imagealphablending($dst_img, false);

                // turning on alpha channel information saving (to ensure the full range 
                // of transparency is preserved)
                imagesavealpha($dst_img, true);
        } 

        
        imagecopyresized($dst_img, $src_img, 0,0,0,0,$nx,$ny,$ox,$oy);
        
        $ext = strtolower(substr($dst, -4));
        switch ($ext) {
            case '.jpg':
            case 'jpeg':
                if(!imagejpeg($dst_img, $dst,100)) {
                    throw new \Exception('Failed to create thumbnail image at '.$dst);
                }
                break;
            case '.gif':
                if(!imagegif($dst_img, $dst)) {
                    throw new \Exception('Failed to create thumbnail image at '.$dst);
                }
                break;
            case '.png':
                if(!imagepng($dst_img, $dst,0)) {
                    throw new \Exception('Failed to create thumbnail image at '.$dst);
                }
                break;
        }
        
        return $dst;
    }

    /**
     * Get a single image tag for Ajax update
     *
     */
/*
    public function get_image_tag($args) {
               
        $id = (int) $this->modx->getOption('image_id', $args);
        
        $Image = $this->modx->getObject('Image',$id);
        
        if (!$Image) {
            return 'Error loading image.';
        }
        
        $data = $Image->toArray();
         
        $data['wide_load'] = '';
        $data['visible_height'] = $data['height'];
        $data['visible_width'] = $data['width'];        
        if ($data['width'] > $this->max_image_width) {
            $data['wide_load'] = 'Warning! This image is larger than it appears.';
            $ratio = $this->max_image_width / $data['width'];
            $data['visible_height'] = (int) ($data['height'] * $ratio);
            $data['visible_width'] = $this->max_image_width;
        }

        $img = $this->_load_view('image.php',$data);

        return $img;
    }
*/
    
}
/*EOF*/