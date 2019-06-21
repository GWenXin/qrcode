<?php

namespace Wenxin\Qrcode;

interface QrCodeInterface
{
    /**
     * Generates a QrCode.
     *
     * @param string      $text     The text to be converted into a QrCode
     * @param null|string $filename The filename and path to save the QrCode file
     *
     * @return string|void Returns a QrCode string depending on the format, or saves to a file.
     */
    public function generate($text, $filename = null);

    /**
     * Switches the format of the outputted QrCode or defaults to SVG.
     *
     * @param string $format
     *
     * @return $this
     */
    public function format($format);

    /**
     * Changes the size of the QrCode.
     *
     * @param int $pixels The size of the QrCode in pixels
     *
     * @return $this
     */
    public function size($pixels);

    /**
     * Changes the foreground color of a QrCode.
     *
     * @param string $color
     *
     * @return $this
     */
    public function color($color);

    /**
     * Changes the background color of a QrCode.
     *
     * @param string $background_color
     *
     * @return $this
     */
    public function backgroundColor($background_color);

    /**
     * Changes the error correction level of a QrCode.
     *
     * @param string $level Desired error correction level.  L = 7% M = 15% Q = 25% H = 30%
     *
     * @return $this
     */
    public function errorCorrection($level);

    /**
     * Creates a margin around the QrCode.
     *
     * @param int $margin The desired margin in pixels around the QrCode
     *
     * @return $this
     */
    public function margin($margin);

    /**
     * Sets the Encoding mode.
     *
     * @param string $encoding
     *
     * @return $this
     */
    public function encoding($encoding);

    /**
     * Merges an image with the center of the QrCode.
     *
     * @param $image string The filepath to an image
     * @param $percentage float The percentage that the merge image should take up.
     *
     * @return $this
     */
//     public function merge($image, $percentage = .2);
    /**
     * Size of the frame.
     *
     * @param string $frame_path The framepath to an frame
     * @param int $frame_width size of frame width
     * @param int $frame_height size of frame height
     *
     * @return $this
     */
    public function frame($frame_path, $frame_width, $frame_height);
    
    /**
     * position of the QrCode in the frame.
     *
     * @param int $position_x The position x of the qrcode in the frame
     * @param int $position_y The position y of the qrcode in the frame
     *
     * @return $this
     */
    public function position($position_x, $position_y);
    /**
     * Merge icon at the centre of the QrCode. 
     *
     * @param string $merge_icon The framepath to merge icon
     * @param int  $icon_size Sets size of the icon
     *
     * @return $this
     */
    public function merge_icon($merge_icon, $icon_size);
    /**
     * Curve of the QrCode.
     *
     * @param int $curve The size of curve 
     *
     * @return $this
     */
    public function curve($curve);
}
