<?php
 include 'LIB_download_images.class.php';
 $data = download_binary_file('https://pixabay.com/zh/photos/download/school-845196_1920.png?attachment','','cookie.txt');
 echo '<pre>';
 var_dump($data);
 file_put_contents('test.jpg', $data['imagesinfo']);
