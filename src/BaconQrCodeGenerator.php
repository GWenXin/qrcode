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

        if ($filename === null) {
            return $qrCode;
        }          
        
            Storage::delete('qrCurve.png');
            Storage::delete('QRHasLogo.png');
            Storage::delete('new_frame.png');
            Storage::delete('mergeFrameQr.png');
        
        return file_put_contents($filename, $qrCode);
    }
    
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
    public function color($color)
    {
        //convert color hex to rgb
        $hex = "#".$color;
        list($red, $green, $blue) = sscanf($hex, "#%02x%02x%02x");
        
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
        //convert color hex to rgb
//         $hex = "#".$background_color;
//         list($red, $green, $blue) = sscanf($hex, "#%02x%02x%02x");
        
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
        $img->save(storage_path('app/new_frame.png')); // resized frame

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
                    
                if(file_exists(storage_path('app/qrcode.png'))){
                    //image 1 - frame resized
                    $path_1 = storage_path('app/new_frame.png');
                    //image 2
                    $path_2 = storage_path('app/qrcode.png'); 

                    //imagecreatefrompng($filename)
                    $image_1 = imagecreatefromstring(file_get_contents($path_1));
                    $image_2 = imagecreatefromstring(file_get_contents($path_2));             

                    $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
                    imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));

                    // merge                               x            y
                    imagecopymerge($image_3, $image_2, $position_x, $position_y, 0, 0, imagesx($image_2), imagesy($image_2), 100); 
                    // merge images   
                    var_dump(imagepng($image_3,storage_path('app/mergeFrameQr.png')));

                    Storage::delete('qrcode.png');
                    Storage::delete('QRHasLogo.png');
                    Storage::delete('new_frame.png');
                    Storage::delete('qrCurve.png');

                return $this;
                }elseif(file_exists(storage_path('app/qrCurve.png'))){
                     //image 1 - frame resized
                    $path_1 = storage_path('app/new_frame.png');
                    //image 2
                    $path_2 = storage_path('app/qrCurve.png'); 

                    //imagecreatefrompng($filename)
                    $image_1 = imagecreatefromstring(file_get_contents($path_1));
                    $image_2 = imagecreatefromstring(file_get_contents($path_2));             

                    $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
                    imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));

                    // merge                               x            y
                    imagecopymerge($image_3, $image_2, $position_x, $position_y, 0, 0, imagesx($image_2), imagesy($image_2), 100); 
                    // merge images   
                    var_dump(imagepng($image_3,storage_path('app/mergeFrameQr.png')));

                    Storage::delete('qrcode.png');
                    Storage::delete('QRHasLogo.png');
                    Storage::delete('new_frame.png');
                    Storage::delete('qrCurve.png');

                return $this;
                }elseif(file_exists(storage_path('app/QRHasLogo.png'))){
                    //image 1 - frame resized
                    $path_1 = storage_path('app/new_frame.png');
                    //image 2
                    $path_2 = storage_path('app/QRHasLogo.png'); 

                    //imagecreatefrompng($filename)
                    $image_1 = imagecreatefromstring(file_get_contents($path_1));
                    $image_2 = imagecreatefromstring(file_get_contents($path_2));             

                    $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
                    imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));

                    // merge                               x            y
                    imagecopymerge($image_3, $image_2, $position_x, $position_y, 0, 0, imagesx($image_2), imagesy($image_2), 100); 
                    // merge images   
                    var_dump(imagepng($image_3,storage_path('app/mergeFrameQr.png')));

                    Storage::delete('qrcode.png');
                    Storage::delete('QRHasLogo.png');
                    Storage::delete('new_frame.png');
                    Storage::delete('qrCurve.png');

                 return $this;
                }                
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
            $this->merge_icon = file_get_contents($merge_icon);
        
             if(file_exists(storage_path('app/qrCurve.png'))){
                     $QR = imagecreatefromstring(file_get_contents(storage_path('app/qrCurve.png')));   // qr code
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
                     $QRHasLogo = storage_path('app/QRHasLogo.png');
                     imagepng($QR, $QRHasLogo);
                 
                     Storage::delete('qrcode.png'); 
                     Storage::delete('qrCurve.png');
                 
                 return $this;
             }elseif(file_exists(storage_path('app/qrcode.png'))){
                     $QR = imagecreatefromstring(file_get_contents(storage_path('app/qrcode.png')));   // qr code
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
                     $QRHasLogo = storage_path('app/QRHasLogo.png');
                     imagepng($QR, $QRHasLogo);
                 
                     Storage::delete('qrcode.png');
                 return $this;
             }
            
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

        $imagick = new Imagick(storage_path('app/qrcode.png'));
        $imagick->statisticImage(
                Imagick::STATISTIC_MEDIAN,
                $curve_width, //width
                $curve_height, // height
                Imagick::CHANNEL_DEFAULT
            );
//         $imagick = storage_path('qrCurve.png');
        Storage::disk('local')->put('qrCurve.png', $imagick);
        Storage::delete('qrcode.png');
        
       return $this;
    }  
}
