# QR Code Generator

This is a qr code generator that can customize your our QRcode.

## Run this command to install package
composer require wenxin/qrcode

## Open config/app.php file and add this class to the providers array.
Wenxin\Qrcode\QrCodeServiceProvider::class

## At the same file add the following line to aliases array.
'QrCode' => Wenxin\Qrcode\Facades\QrCode::class


## Example Code
```php
  $customQrcode = QrCode::format('png')     
                            ->curve(5,5) // curve of the qrcode
                            ->merge_icon(storage_path('icon/logo4.png'))  // merge icon at the center of the qrcode
                            ->frame(storage_path('frame/frame2.png'), 630, 630) //frame file , frame with and height  
                            ->position(125,125)  //set qrcode position x and y in the frame 
                            ->size(380) //set qrcode size                                                                   
                            ->color(22, 160, 80) //qrcode color
                            ->backgroundColor(255, 255, 255) //qrcode background color
                            ->margin(1) //qrcode border                       
                            ->errorCorrection('H') //qrcode error correction  
                            ->encoding('UTF-8')   //qrcode encoding                         
                            ->generate('This is a qrcode generator.', storage_path('qrcode.png')); //value and qrcode path
```
