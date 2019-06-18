# QR Code Generator

This is a qr code generator package that can customize your own QRcode with size, color, background color, margin, icon, curve, and frame.

### Run this command to install package
```php
composer require wenxin/qrcode
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
 
#### Workflow of the QR code generator
First, generate a basic qrcode and saved file as 'qrcode.png'.
Then, use 'qrcode.png' file to implement the customize with curve, icon, and frame.

#### Important
Before custom your QR code with curve, icon, and frame, you must generate a original qrcode.

## Example Code
```php
//original QR code
$qrcodeImage = QrCode::format('png')      
                     ->size(380) // set qrcode size                                                                   
                     ->color(22, 160, 133) // set qrcode color
                     ->backgroundColor(255, 255, 255) // set qrcode background color                              
                     ->margin(1) // set qrcode border                       
                     ->errorCorrection('H') //set qrcode error correction  
                     ->encoding('UTF-8') //set the character encoder.                           
                     ->generate('This is a custom QR code generator.',storage_path('app/qrcode.png'));
                     //QR code content value and save file path
                     
                                                                
//customize QR code with curve, icon, and frame       
$customqrcode = QrCode::curve(5,5) // set curve of the qrcode
                      ->merge_icon(storage_path('icon/logo6.png'))  // merge icon at the center of the qrcode
                      ->frame(storage_path('frame/frame2.png'), 630, 630) //frame file ,frame width and height  
                      ->position(125,125);  //set qrcode position x and y in the frame                                       
        

```




