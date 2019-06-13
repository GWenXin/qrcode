<?php

namespace Wenxin\Qrcode;

use BaconQrCode;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\Eps;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Renderer\Image\RendererInterface;
use BaconQrCode\Renderer\Image\Svg;
use BaconQrCode\Writer;
use Intervention\Image\ImageManagerStatic as ImageQr;
use Illuminate\Support\Facades\Storage;
use Imagick;

class BaconQrCodeGenerator implements QrCodeInterface
{
    /**
     * Holds the BaconQrCode Writer Object.
     *
     * @var \BaconQrCode\Writer
     */
    protected $writer;

    /**
     * Holds the QrCode error correction levels.  This is stored by using the BaconQrCode ErrorCorrectionLevel class constants.
     *
     * @var \BaconQrCode\Common\ErrorCorrectionLevel
     */
    protected $errorCorrection = ErrorCorrectionLevel::L;

    /**
     * Holds the Encoder mode to encode a QrCode.
     *
     * @var string
     */
    protected $encoding = Encoder::DEFAULT_BYTE_MODE_ECODING;

    /**
     * Holds an image string that will be merged with the QrCode.
     *
     * @var null|string
     */
//     protected $imageMerge = null;

    /**
     * The percentage that a merged image should take over the source image.
     *
     * @var float
     */
    protected $imagePercentage = .2;

    /**
     * BaconQrCodeGenerator constructor.
     *
     * @param Writer|null            $writer
     * @param RendererInterface|null $format
     */
    public function __construct(Writer $writer = null, RendererInterface $format = null)
    {
        $format = $format ?: new Svg();
        $this->writer = $writer ?: new Writer($format);
    }

    /**
     * Generates a QrCode.
     *
     * @param string      $text     The text to be converted into a QrCode
     * @param null|string $filename The filename and path to save the QrCode file
     *
     * @return string|void Returns a QrCode string depending on the format, or saves to a file.
     */
    public function generate($text, $filename = null)
    {
        $qrCode = $this->writer->writeString($text, $this->encoding, $this->errorCorrection);

//         if ($this->imageMerge !== null) {
//             $merger = new ImageMerge(new Image($qrCode), new Image($this->imageMerge));
//             $qrCode = $merger->merge($this->imagePercentage);
//         } 

        if ($filename === null) {
            return $qrCode;
        }          
            $this->$curve->curve($curve_width, $curve_height);
            $this->$merge_icon->merge_icon($merge_icon);
            $this->$frame->frame($frame_path, $frame_width, $frame_height);
            $this->$position->position($position_x, $position_y);
        
          return $this->file_put_contents($filename, $qrCode);   
    }
    
    /**
     * Merges an image with the center of the QrCode.
     *
     * @param $filepath string The filepath to an image
     * @param $percentage float The amount that the merged image should be placed over the qrcode.
     * @param $absolute boolean Whether to use an absolute filepath or not.
     *
     * @return $this
     */
//     public function merge($filepath, $percentage = .2, $absolute = false)
//     {
//         if (function_exists('base_path') && !$absolute) {
//             $filepath = base_path().$filepath;
//         }

//         $this->imageMerge = file_get_contents($filepath);
//         $this->imagePercentage = $percentage;

//         return $this;
//     }

    /**
     * Merges an image string with the center of the QrCode, does not check for correct format.
     *
     * @param $content string The string contents of an image.
     * @param $percentage float The amount that the merged image should be placed over the qrcode.
     *
     * @return $this
     */
//     public function mergeString($content, $percentage = .2)
//     {
//         $this->imageMerge = $content;
//         $this->imagePercentage = $percentage;

//         return $this;
//     }

    /**
     * Switches the format of the outputted QrCode or defaults to SVG.
     *
     * @param string $format The desired format.
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function format($format)
    {
        switch ($format) {
            case 'png':
                $this->writer->setRenderer(new Png());
                break;
            case 'eps':
                $this->writer->setRenderer(new Eps());
                break;
            case 'svg':
                $this->writer->setRenderer(new Svg());
                break;
            default:
                throw new \InvalidArgumentException('Invalid format provided.');
        }

        return $this;
    }

    /**
     * Changes the size of the QrCode.
     *
     * @param int $pixels The size of the QrCode in pixels
     *
     * @return $this
     */
    public function size($pixels)
    {
        $this->writer->getRenderer()->setHeight($pixels);
        $this->writer->getRenderer()->setWidth($pixels);

        return $this;
    }

    /**
     * Changes the foreground color of a QrCode.
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return $this
     */
    public function color($red, $green, $blue)
    {
        $this->writer->getRenderer()->setForegroundColor(new Rgb($red, $green, $blue));

        return $this;
    }

    /**
     * Changes the background color of a QrCode.
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return $this
     */
    public function backgroundColor($red, $green, $blue)
    {
        $this->writer->getRenderer()->setBackgroundColor(new Rgb($red, $green, $blue));

        return $this;
    }

    /**
     * Changes the error correction level of a QrCode.
     *
     * @param string $level Desired error correction level.  L = 7% M = 15% Q = 25% H = 30%
     *
     * @return $this
     */
    public function errorCorrection($level)
    {
        $this->errorCorrection = constant("BaconQrCode\Common\ErrorCorrectionLevel::$level");

        return $this;
    }

    /**
     * Creates a margin around the QrCode.
     *
     * @param int $margin The desired margin in pixels around the QrCode
     *
     * @return $this
     */
    public function margin($margin)
    {
        $this->writer->getRenderer()->setMargin($margin);

        return $this;
    }

    /**
     * Sets the Encoding mode.
     *
     * @param string $encoding
     *
     * @return $this
     */
    public function encoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    } 
    /**
     * Creates a new datatype object and then generates a QrCode.
     *
     * @param $method
     * @param $arguments
     */
    public function __call($method, $arguments)
    {
        $dataType = $this->createClass($method);

        $dataType->create($arguments);

        return $this->generate(strval($dataType));
    }

    /**
     * Creates a new DataType class dynamically.
     *
     * @param string $method
     *
     * @return Wenxin\Qrcode\DataTypes\DataTypeInterface
     */
    private function createClass($method)
    {
        $class = $this->formatClass($method);

        if (!class_exists($class)) {
            throw new \BadMethodCallException();
        }

        return new $class();
    }

    /**
     * Formats the method name correctly.
     *
     * @param $method
     *
     * @return string
     */
    private function formatClass($method)
    {
        $method = ucfirst($method);

        $class = "Wenxin\Qrcode\DataTypes\\".$method;

        return $class;
    }
    /**
     * Changes the size of the Frame.
     *
     * @param int $frame_path The frame path of the frame
     * @param int $frame_width The size of the frame
     * @param int $frame_height The size of the frame
     *
     * @return $this
     */
    public function frame($frame_path, $frame_width, $frame_height)
    {
        $this->frame = file_get_contents($frame_path);
        $this->writer->getRenderer()->setWidth($frame_width);
        $this->writer->getRenderer()->setHeight($frame_height);
        
        // resize frame        
        $img = ImageQr::make($frame_path);
        $img->resize($frame_width, $frame_height); //(x, y)
        $img->save(storage_path('new_frame.png')); // resized frame

        return $this;
       // return $img;
    }
    /**
     * Changes the qrcode in the frame
     *     
     * @param int $position_x The position x of the qrcode in the frame
     * @param int $position_y The position y of the qrcode in the frame
     *
     * @return $this
     */
    public function position($position_x, $position_y)
    {
        $this->writer->getRenderer()->setWidth($position_x);
        $this->writer->getRenderer()->setHeight($position_y);
        
        //image 1 - frame resized
        $path_1 = storage_path('new_frame.png');
        //image 2
        $path_2 = storage_path('QRHasLogo.png');  // $path_2 = 'qrcode.png'; || $path_2 = $imagick;
        
        //imagecreatefrompng($filename)
        $image_1 = imagecreatefromstring(file_get_contents($path_1));
        $image_2 = imagecreatefromstring(file_get_contents($path_2));             
        
        $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
        imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));

        // merge                               x            y
        imagecopymerge($image_3, $image_2, $position_x, $position_y, 0, 0, imagesx($image_2), imagesy($image_2), 100); 
        // merge images   
        var_dump(imagepng($image_3,storage_path('merge.png')));

        //return $image3;
        return $this;
    }
    /**
     * Merge the icon in the center of the qrcode
     *     
     * @param string $merge_icon The filepath to an icon
     *
     * @return $this
     */
    public function merge_icon($merge_icon)
    {
//         if (function_exists('base_path')) {
//             $merge_icon = base_path().$merge_icon;
//         }
            //qrcode merge icon       
            $this->merge_icon = file_get_contents($merge_icon);
        
             $QR = imagecreatefromstring(file_get_contents(storage_path('app/qr.png')));   // qr code
             $logo = imagecreatefrompng($merge_icon); // icon 
             $QR_width = imagesx($QR);                // qr code width
             $QR_height = imagesy($QR);               // qr code height
             $logo_width = imagesx($logo);            // logo weight
             $logo_height = imagesy($logo);           // logo height
             $logo_qr_width = $QR_width / 4;
             $scale = $logo_width/$logo_qr_width;
             $logo_qr_height = $logo_height/$scale;
             $from_width = ($QR_width - $logo_qr_width) / 2;
             // reassemble the image and resize
             imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
             // qr code with icon at the center
             $QRHasLogo = storage_path('QRHasLogo.png');
             imagepng($QR, $QRHasLogo);
        
        return $this;
    }
    /**
     * Sets Qrcode curve
     *     
     * @param int $curve_width The curve width of the qrcode
     * @param int $curve_height The curve height of the qrcode
     *
     * @return $this
     */
    public function curve($curve_width,$curve_height)
    {
        $this->writer->getRenderer()->setWidth($curve_width);
        $this->writer->getRenderer()->setHeight($curve_height);

        $imagick = new Imagick(storage_path('qrcode.png'));
        $imagick->statisticImage(
                Imagick::STATISTIC_MEDIAN,
                $curve_width, //width
                $curve_height, // height
                Imagick::CHANNEL_DEFAULT
            );
            Storage::disk('local')->put('qr.png', $imagick);

       return $this;
       // return $imagick;
    }  
}
