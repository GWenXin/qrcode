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
use Intervention\Image\ImageManagerStatic as ImageQr;  // use for merge frame 
use Illuminate\Support\Facades\Storage; // use for storage
use Imagick; // use for curve qrcode 

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
//    protected $imagePercentage = .2;

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

        $overwriteFlag = isset($this->writer->overwrite);


        if($filename == null){
            $tmpFilename = time().rand(10,10000).'.'.$this->writer->format;
        }
        else{
            $tmpFilename = $filename.'.'.$this->writer->format;
        }

        $finalFilename = storage_path('app/public/'.$tmpFilename);

        if(file_exists($finalFilename)){
            if($overwriteFlag){
                file_put_contents($finalFilename, $qrCode);
            }
            else{
                throw new \InvalidArgumentException('File exists, if wish to overwrite old file please enable overwrite(true)');
            }
        }
        else{
            file_put_contents($finalFilename, $qrCode);
        }

        $this->qrOriPath = $finalFilename;
        $this->qrTmpFilename = $tmpFilename;
        $this->finalQRPath = $finalFilename;

        return $this;

//        return $finalFilename;
    }

    public function overwrite($overwrite = false){
        $this->writer->overwrite = $overwrite;

        return $this;
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

        $this->writer->format = $format;

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
        if($pixels < 0 || $pixels > 1000){
            throw new \InvalidArgumentException('Size range from 0 to 1000px.');
        }
        else{
            $this->writer->getRenderer()->setHeight($pixels);
            $this->writer->getRenderer()->setWidth($pixels);

            return $this;
        }

    }

    /**
     * Changes the foreground color of a QrCode.
     *
     * @param string $color
     *
     * @return $this
     */
    public function color($color)
    {
//        dd($color);
        $hex = "#".$color;
        list($red, $green, $blue) = sscanf($hex, "#%02x%02x%02x");

        $this->writer->getRenderer()->setForegroundColor(new Rgb($red, $green, $blue));

        return $this;
    }

    /**
     * Changes the background color of a QrCode.
     *
     * @param string $background_color
     *
     * @return $this
     */
    public function backgroundColor($background_color)
    {
        //convert background_color hex to rgb
        $hex = "#".$background_color;
        list($red, $green, $blue) = sscanf($hex, "#%02x%02x%02x");
        
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
    public function margin($margin = 0)
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
        $tmpFrameName = 'frame-'.time().rand(10,10000).'.png';

//        $this->frame = file_get_contents($frame_path);
//        $this->writer->getRenderer()->setWidth($frame_width);
//        $this->writer->getRenderer()->setHeight($frame_height);
        
        // resize frame        
        $img = ImageQr::make($frame_path);
        $img->resize($frame_width, $frame_height); //(x, y)
        $img->save(storage_path("app/public/$tmpFrameName")); // resized frame

        $this->tmpFrame = $tmpFrameName;

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
//        $this->writer->getRenderer()->setWidth($position_x);
//        $this->writer->getRenderer()->setHeight($position_y);

//        dd($this,'pois');

        //image 1 - frame resized
        $path_1 = storage_path('app/public/'.$this->tmpFrame);
        //image 2
        $path_2 = storage_path('app/public/'.$this->qrTmpFilename);

        //imagecreatefrompng($filename)
        $image_1 = imagecreatefromstring(file_get_contents($path_1));
        $image_2 = imagecreatefromstring(file_get_contents($path_2));

        $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
        imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));

        // merge                               x            y
        imagecopymerge($image_3, $image_2, $position_x, $position_y, 0, 0, imagesx($image_2), imagesy($image_2), 100);
        // merge images
        imagepng($image_3,storage_path('app/public/'.$this->qrTmpFilename));

        // delete files

        Storage::delete('public/'.$this->tmpFrame);

        return $this;



//                if(file_exists(storage_path('app/qrcode.png'))){
//                    //image 1 - frame resized
//                    $path_1 = storage_path('app/new_frame.png');
//                    //image 2
//                    $path_2 = storage_path('app/qrcode.png');
//
//                    //imagecreatefrompng($filename)
//                    $image_1 = imagecreatefromstring(file_get_contents($path_1));
//                    $image_2 = imagecreatefromstring(file_get_contents($path_2));
//
//                    $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
//                    imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));
//
//                    // merge                               x            y
//                    imagecopymerge($image_3, $image_2, $position_x, $position_y, 0, 0, imagesx($image_2), imagesy($image_2), 100);
//                    // merge images
//                    var_dump(imagepng($image_3,storage_path('app/mergeFrameQr.png')));
//
//                    // delete files
//                    Storage::delete('qrcode.png');
//                    Storage::delete('QRHasLogo.png');
//                    Storage::delete('new_frame.png');
//                    Storage::delete('qrCurve.png');
//
//                return $this;
//                }elseif(file_exists(storage_path('app/qrCurve.png'))){
//                     //image 1 - frame resized
//                    $path_1 = storage_path('app/new_frame.png');
//                    //image 2
//                    $path_2 = storage_path('app/qrCurve.png');
//
//                    //imagecreatefrompng($filename)
//                    $image_1 = imagecreatefromstring(file_get_contents($path_1));
//                    $image_2 = imagecreatefromstring(file_get_contents($path_2));
//
//                    $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
//                    imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));
//
//                    // merge                               x            y
//                    imagecopymerge($image_3, $image_2, $position_x, $position_y, 0, 0, imagesx($image_2), imagesy($image_2), 100);
//                    // merge images
//                    var_dump(imagepng($image_3,storage_path('app/mergeFrameQr.png')));
//
//                    // delete files
//                    Storage::delete('qrcode.png');
//                    Storage::delete('QRHasLogo.png');
//                    Storage::delete('new_frame.png');
//                    Storage::delete('qrCurve.png');
//
//                return $this;
//                }elseif(file_exists(storage_path('app/QRHasLogo.png'))){
//                    //image 1 - frame resized
//                    $path_1 = storage_path('app/new_frame.png');
//                    //image 2
//                    $path_2 = storage_path('app/QRHasLogo.png');
//
//                    //imagecreatefrompng($filename)
//                    $image_1 = imagecreatefromstring(file_get_contents($path_1));
//                    $image_2 = imagecreatefromstring(file_get_contents($path_2));
//
//                    $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
//                    imagecopyresampled($image_3, $image_1, 0, 0, 0, 0, imagesx($image_1), imagesy($image_1), imagesx($image_1), imagesy($image_1));
//
//                    // merge                               x            y
//                    imagecopymerge($image_3, $image_2, $position_x, $position_y, 0, 0, imagesx($image_2), imagesy($image_2), 100);
//                    // merge images
//                    var_dump(imagepng($image_3,storage_path('app/mergeFrameQr.png')));
//
//                    // delete files
//                    Storage::delete('qrcode.png');
//                    Storage::delete('QRHasLogo.png');
//                    Storage::delete('new_frame.png');
//                    Storage::delete('qrCurve.png');
//
//                 return $this;
//                }
    }
    /**
     * Merge the icon in the center of the qrcode
     *     
     * @param string $merge_icon The filepath to an icon
     * @param $icon_size
     *
     * @return $this
     */
    public function merge_icon($merge_icon, $icon_size)
    {           
//            $this->merge_icon = file_get_contents($merge_icon);

            $QR = imagecreatefromstring(file_get_contents(storage_path('app/public/'.$this->qrTmpFilename)));   // qr code
            $logo = imagecreatefrompng($merge_icon); // icon
            $QR_width = imagesx($QR);                // qr code width
            $QR_height = imagesy($QR);               // qr code height
            $logo_width = imagesx($logo);            // logo weight
            $logo_height = imagesy($logo);           // logo height
            $logo_qr_width = $QR_width / $icon_size; // set icon size
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            // reassemble the image and resize
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            // qr code with icon at the center
            $finalFilename = 'app/public/'.$this->qrTmpFilename;

            $QRHasLogo = storage_path($finalFilename);
            imagepng($QR, $QRHasLogo);

            return $this;

//
//             if(file_exists(storage_path('app/qrCurve.png'))){
//                     $QR = imagecreatefromstring(file_get_contents(storage_path('app/qrCurve.png')));   // qr code
//                     $logo = imagecreatefrompng($merge_icon); // icon
//                     $QR_width = imagesx($QR);                // qr code width
//                     $QR_height = imagesy($QR);               // qr code height
//                     $logo_width = imagesx($logo);            // logo weight
//                     $logo_height = imagesy($logo);           // logo height
//                     $logo_qr_width = $QR_width / $icon_size; // set icon size
//                     $scale = $logo_width/$logo_qr_width;
//                     $logo_qr_height = $logo_height/$scale;
//                     $from_width = ($QR_width - $logo_qr_width) / 2;
//                     // reassemble the image and resize
//                     imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
//                     // qr code with icon at the center
//                     $QRHasLogo = storage_path('app/QRHasLogo.png');
//                     imagepng($QR, $QRHasLogo);
//
//                     // delete files
//                     Storage::delete('qrcode.png');
//                     Storage::delete('qrCurve.png');
//
//                 return $this;
//             }elseif(file_exists(storage_path('app/qrcode.png'))){
//                     $QR = imagecreatefromstring(file_get_contents(storage_path('app/qrcode.png')));   // qr code
//                     $logo = imagecreatefrompng($merge_icon); // icon
//                     $QR_width = imagesx($QR);                // qr code width
//                     $QR_height = imagesy($QR);               // qr code height
//                     $logo_width = imagesx($logo);            // logo weight
//                     $logo_height = imagesy($logo);           // logo height
//                     $logo_qr_width = $QR_width / 4;
//                     $scale = $logo_width/$logo_qr_width;
//                     $logo_qr_height = $logo_height/$scale;
//                     $from_width = ($QR_width - $logo_qr_width) / 2;
//                     // reassemble the image and resize
//                     imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
//                     // qr code with icon at the center
//                     $QRHasLogo = storage_path('app/QRHasLogo.png');
//                     imagepng($QR, $QRHasLogo);
//
//                     // delete files
//                     Storage::delete('qrcode.png');
//                 return $this;
//             }
            
    }
    /**
     * Sets Qrcode curve
     *     
     * @param int $curve_width The curve width of the qrcode
     * @param int $curve_height The curve height of the qrcode
     *
     * @return $this
     */
    public function curve($curve)
    {

//        $this->writerCurve->getRenderer()->setWidth($curve);
//        $this->writerCurve->getRenderer()->setHeight($curve);


        $imagick = new Imagick($this->qrOriPath);
        $imagick->statisticImage(
                Imagick::STATISTIC_MEDIAN,
                $curve, // curve width
                $curve, // curve height
                Imagick::CHANNEL_DEFAULT
            );

        $tmpFilename = $this->qrTmpFilename;

        $finalFilename = storage_path('app/public/'.$tmpFilename);

        $this->finalQRPath = $finalFilename;

        Storage::delete($this->qrTmpFilename);

//        dd($tmpFilename);

        Storage::disk('local')->put('public/'.$tmpFilename, $imagick);
        // delete files
        return $this;
    }  
}
