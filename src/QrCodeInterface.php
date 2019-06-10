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
     * Sets the curve width and height of the qrcode.
     *
     * @param int $curve_width
     * @param int $curve_height
     * 
     * @return $this
     */
    public function curve($curve_width, $curve_height);

    /**
     * Merges an icon with the center of the QrCode.
     *
     * @param $icon string The filepath to an icon
     *
     * @return $this
     */
    public function merge_icon($merge_icon);

    /**
     * Upload frame.
     *
     * @param $frame string The filepath to the frame
     * 
     */
    public function frame($frame);

    /**
     * Sets the size of the frame width and height.
     *
     * @param int $frame_width
     * @param int $frame_height
     * 
     * @return $this
     */
    public function frame_size($frame_width, $frame_height);

    /**
     * Sets the position x and position y of merge qrcode in the frame.
     *
     * @param int $position_x
     * @param int $ position_y
     * 
     * @return $this
     */
    public function position($position_x, $position_y);

}
