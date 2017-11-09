# upload
A simple upload class of php.
# usage
Depends on a OO file object, like [this](https://github.com/brandonsavage/Upload/blob/master/src/Upload/FileInfo.php), or you can implement your own file class.
```php
$config = [
      'scene1' => [
           'baseDir' => 'path/to/save/images/',
           'urlPrefix' => '/website/upload/images/',
           'rules' => [
               'size' => [1000000, '文件大小不超过1M'],
               'type' => [['image/jpeg', 'image/png'], 'Only jpg and png allowed']，
               'exts' => [['jpg', 'png'], 'Only jpg and png allowed'],
               'dims' => [[600,800], 'Required image width is 600 and height is 800']
           ]
      ]
];
  
$uploader = new Uploader($config);
if (!$uploader->upload($file)) {
  echo $uploader->getError();
}
```
