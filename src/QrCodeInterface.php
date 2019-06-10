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
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return $this
     */
    public function color($red, $green, $blue);

    /**
     * Changes the background color of a QrCode.
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return $this
     */
    public function backgroundColor($red, $green, $blue);

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
     *
     * @return $this
     */
  // public function merge($image, $percentage = .2);
    public function merge_icon($image);

    /**
     * Curve of the QrCode.
     *
     * @param $curve_width int size of curve width
     * @param $curve_height int size of curve height
     *
     * @return $this
     */
    public function curve($curve_width, $curve_height);
    /**
     * Frame of the QrCode.
     *
     * @param $frame string The filepath to an frame
     *
     * @return $this
     */
    public function frame($frame);
    /**
     * Size of the frame.
     *
     * @param $frame_width int size of frame width
     * @param $frame_height int size of frame height
     *
     * @return $this
     */
    public function frame_size($frame_width, $frame_height);
    
    /**
     * position of the QrCode in the frame.
     *
     * @param $position_x int The position x of the qrcode in the frame
     * @param $position_y int The position y of the qrcode in the frame
     *
     * @return $this
     */
    public function position($position_x, $position_y);
}
