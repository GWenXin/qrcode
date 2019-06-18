# QR Code Generator

This is a qr code generator package that can customize your own QRcode with size, color, background color, margin, icon, curve, and frame.

### Run this command to install package
```php
$ composer require wenxin/qrcode
```

### Open config/app.php file and add this class to the providers array.
```php
Wenxin\Qrcode\QrCodeServiceProvider::class
```

### At the same file (config/app.php) add the following line to aliases array. 
```php
'QrCode' => Wenxin\Qrcode\Facades\QrCode::class
```
### Add 'use QrCode' on top when you are implementing.
```php
use QrCode;
```
## Description Parameters and Example

##### format('png') - string
QR code file type
##### size(500) - integer
Sets QR code size(px). Size range from 0 to 1000px.
##### color('8E44AD') - string
Sets QR code color(Hex). Keyin without '#', example: 28B463, 8E44AD  
##### backgroundColor('FDEBD0') - string
Sets QR code background color(Hex). Keyin without '#', example: F9E79F, FDEBD0
##### margin(2) - integer
Sets border size(px) of the QR code. Size range from 0 to 10px.
##### errorCorrection('H') - string
Sets QR code error correction. QR Code has error correction capability to restore data if the code is dirty or damaged. 
Four error correction levels ('L' - 7%, 'M' - 15%, 'Q' - 25%, 'H' - 30%) are available for users to choose according
to the operating environment. Raising this level improves error correction capability but also increases the amount of
data QR Code size.
##### encoding('UTF-8') - string
Sets the character encoder. Encoding list:-

ISO-8859-1, ISO-8859-2 ,ISO-8859-3, ISO-8859-4, ISO-8859-5, ISO-8859-6, ISO-8859-7, ISO-8859-8, ISO-8859-9,
ISO-8859-10, ISO-8859-11, ISO-8859-12, ISO-8859-13, ISO-8859-14, ISO-8859-15, ISO-8859-16, SHIFT-JIS, WINDOWS-1250, WINDOWS-1251, WINDOWS-1252, WINDOWS-1256, UTF-16BE, UTF-8, ASCII, GBK, EUC-KR.
##### generate('This is a QR code generator.', storage_path('qrcode.png')) - string
QR code content value e.g Text, URl, Tel.
Save 'qrcode.png' file at laravel storage.
##### curve(5) - integer
Sets curve size (px) of QR code. Size range from 1 to 10px.
##### merge_icon('icon/logo14.png') - string
Upload and merge icon at the center of the QR code. Maximum 5MB (upload .png file only)
##### frame('frame/frame8.png', 630, 630) - string, integer
Upload frame for merge QR code. Maximum 5MB (upload .png file only).
Sets frame width and height size(px) of QR code frame. Maximum 1100px.
##### position(100, 100) - integer
Sets position X and Y (px) of QR code in the frame. Position range from 0 to 300px.


### Workflow of the QR code generator
First, generate a basic qrcode and saved file as 'qrcode.png'.
Then, use 'qrcode.png' file to implement the customize with curve, icon, and frame.

#### Important
Before custom your QR code with curve, icon, and frame, you must generate a original qrcode.

## Example Code
```php
//original QR code
$qrcodeImage = QrCode::format('png')      
                     ->size(380) // set qrcode size                                                                   
                     ->color('FA8072') // set qrcode color
                     ->backgroundColor('FEF9E7') // set qrcode background color                              
                     ->margin(1) // set qrcode border                       
                     ->errorCorrection('H') //set qrcode error correction  
                     ->encoding('UTF-8') //set the character encoder.                           
                     ->generate('This is a custom QR code generator.',storage_path('app/qrcode.png'));
                     //QR code content value and save file path
                     
                                                                
//customize QR code with curve, icon, and frame       
$customqrcode = QrCode::curve(8) // set curve of the qrcode
                      ->merge_icon('icon/logo6.png')  // merge icon at the center of the qrcode
                      ->frame('frame/frame2.png', 630, 630) //frame file ,frame width and height  
                      ->position(125,125);  //set qrcode position x and y in the frame                                       
        

```




