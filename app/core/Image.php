<?php

namespace Core;

if (!defined("ROOTPATH")) die("Access Denied");

/**
 * Image class
 * for image manipulation
 */
class Image
{
    // Upload a image
    public function upload(string $folder, array $file) : mixed
    {
        // Check image errors
        if ($file['error'] != 0) {
            return false;
        }

        // Check if the folder to upload exists
        if (!file_exists($folder)) {
            mkdir($folder,0777,true);
        }

        // Get the extension and create a random name for the file
        $ext = explode(".",$file['name']);
        $ext = end($ext);
        $random = bin2hex(random_bytes(8)) . '.' . $ext;

        // Create the destination
        $destination = $folder . time() . "_" . $random;
        move_uploaded_file($file['tmp_name'],$destination); // Upload the file

        return $destination;
    }

    // Image resize
    public  static function resize($filepath, $max_size)
    {
        if (file_exists($filepath)) {
            $type = mime_content_type($filepath); // Get the image type

            switch ($type) { // creating a GDIMAGE from the image given
                case 'image/png':
                    $src_image = imagecreatefrompng($filepath);
                    break;
                case 'image/jpeg':
                    $src_image = imagecreatefromjpeg($filepath);
                    break;
                case 'image/webp':
                    $src_image = imagecreatefromwebp($filepath);
                    break;
                default:
                    return $filepath;
                    break;
            }

            $src_width = imagesx($src_image); // Getting the width of the image given
            $src_height = imagesy($src_image); // Getting the height of the image given

            // Logic and proportion stuff
            if ($src_width > $src_height) {
                if ($src_width < $max_size) {
                    $max_size = $src_width;
                }

                $res_width = $max_size;
                $res_height = ($src_height / $src_width) * $max_size;
            } else {
                if ($src_height < $max_size) {
                    $max_size = $src_height;
                }

                $res_height = $max_size;
                $res_width = ($src_width / $src_height) * $max_size;
            }

            $res_width = round($res_width); // New resized width
            $res_height = round($res_height); // New resized height

            $res_image = imagecreatetruecolor($res_width,$res_height); // Creating a image based on the new resized sizes

            // Fixing PNG files
            if ($type == "image/png") {
                imagealphablending($res_image,false);
                imagesavealpha($res_image,true);
            }

            // Resizing from the given to the resized version
            imagecopyresampled($res_image,$src_image,0,0,0,0,$res_width,$res_height,$src_width,$src_height);

            imagedestroy($src_image); // Destroying the old one

            // Copying to the folder
            switch ($type) {
                case 'image/png':
                    imagepng($res_image,$filepath,8);
                    break;
                case 'image/jpeg':
                    imagejpeg($res_image,$filepath,90);
                    break;
                case 'image/webp':
                    imagewebp($res_image,$filepath,90);
                    break;
                default:
                    imagejpeg($res_image,$filepath,90);
                    break;
            }

            imagedestroy($res_image); // Destroying the new one
        }
        return $filepath;
    }
    
    // Function to crop a image on the center
    public function crop($filepath,$crop_width,$crop_height)
    {
        if (file_exists($filepath)) {
            $type = mime_content_type($filepath); // Get the image type
            $func = "imagecreatefromjpeg"; // Saving the function based on the image type

            switch ($type) { // creating a GDIMAGE from the image given
                case 'image/png':
                    $src_image = imagecreatefrompng($filepath);
                    $func = "imagecreatefrompng"; // Saving the function based on the image type
                    break;
                case 'image/jpeg':
                    $src_image = imagecreatefromjpeg($filepath);
                    $func = "imagecreatefromjpeg"; // Saving the function based on the image type
                    break;
                case 'image/webp':
                    $src_image = imagecreatefromwebp($filepath);
                    $func = "imagecreatefromwebp"; // Saving the function based on the image type
                    break;
                default:
                    return $filepath;
                    break;
            }

            $src_width = imagesx($src_image); // Getting the src width
            $src_height = imagesy($src_image); // Getting the src height

            if ($crop_width > $src_width) return "The width given is higher than the image width";  
            if ($crop_height > $src_height) return "The height given is higher than the image height";  

            // Making the resize logic (the bigger value in the crop needs to be in the src too)
            if ($src_width > $src_height) {
                if ($crop_width > $crop_height) {
                    $resize = $crop_width;
                } else {
                    $resize = ($src_width / $src_height) * $crop_height;
                }
            } else {
                if ($crop_height > $crop_width) {
                    $resize = $crop_height;
                } else {
                    $resize = ($src_height / $src_width) * $crop_width;
                }
            }

            $this->resize($filepath,$resize); // Resizing to crop

            $src_image = $func($filepath); // Using the function saved early

            $src_width = imagesx($src_image); // Getting the src width (resized)
            $src_height = imagesy($src_image); // Getting the src height (resized)

            // Setting up the X and Y to crop
            $src_x = 0; 
            $src_y = 0;
            if ($crop_width > $crop_height) {
                $src_y = round(($src_height - $crop_height) / 2);
            } else {
                $src_x = round(($src_width - $crop_width) / 2);
            }

            // Croping the image
            $crop_image = imagecreatetruecolor($crop_width,$crop_height); // Creating a image based on the crop sizes

            // Fixing PNG files
            if ($type == "image/png") {
                imagealphablending($crop_image,false);
                imagesavealpha($crop_image,true);
            }

            // Croping from the src (resized) to the crop version
            imagecopyresampled($crop_image,$src_image, 0, 0, $src_x, $src_y,$crop_width,$crop_height,$crop_width,$crop_height);

            imagedestroy($src_image); // Destroying the old one

            // Copying to the folder
            switch ($type) {
                case 'image/png':
                    imagepng($crop_image,$filepath,8);
                    break;
                case 'image/jpeg':
                    imagejpeg($crop_image,$filepath,90);
                    break;
                case 'image/webp':
                    imagewebp($crop_image,$filepath,90);
                    break;
                default:
                    imagejpeg($crop_image,$filepath,90);
                    break;
            }

            imagedestroy($crop_image); // Destroying the new one
        }
        return $filepath;
    }

    // Function to create a thumbnail file with a crop
    public function getThumbnail($filepath,$thumb_width,$thumb_height)
    {
        if (file_exists($filepath)) {
            $type = mime_content_type($filepath); // Get the image type

            switch ($type) { // creating a GDIMAGE from the image given
                case 'image/png':
                    $src_image = imagecreatefrompng($filepath);
                    break;
                case 'image/jpeg':
                    $src_image = imagecreatefromjpeg($filepath);
                    break;
                case 'image/webp':
                    $src_image = imagecreatefromwebp($filepath);
                    break;
                default:
                    return $filepath;
                    break;
            }

            $src_width = imagesx($src_image); // Getting the width of the image given
            $src_height = imagesy($src_image); // Getting the height of the image given

            if ($thumb_width > $src_width) return "The width given is higher than the image width";  
            if ($thumb_height > $src_height) return "The height given is higher than the image height"; 
            unset($type,$src_image,$src_width,$src_height);

            // Getting the extension
            $ext = explode(".",$filepath);
            $ext = end($ext);

            $filepath = preg_replace("/_thumbnail\.[a-zA-Z0-9]+$/","." . $ext,$filepath);

            // Adding the _thumbnail to it
            $thumb_path = preg_replace("/\.{$ext}$/","_thumbnail." . $ext, $filepath);

            // Checking if already exists
            if (file_exists($thumb_path)) {
                return $thumb_path;
            }

            // Creating a copy
            copy($filepath,$thumb_path);
            $this->crop($thumb_path,$thumb_width,$thumb_height); // Croping the thumbnail one

            $filepath = $thumb_path; // returning the thumb path
        }
        return $filepath;
    }
}

