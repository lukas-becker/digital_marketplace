<?php

if (isset($_REQUEST["square_pipeline"])) {
    $image_pipe = new image_pipeline();
    $image = $image_pipe->squared_pipeline($_FILES['upload']['tmp_name'], $_FILES['upload']["type"]);
    $resultArray = ["status" => true, "image" => $image];
    echo json_encode($resultArray, JSON_UNESCAPED_UNICODE);
}

class image_pipeline
{
    function squared_pipeline($location, $type): string
    {


        $size = $this->get_size($location);
        $image = $this->resize($location, $size);

        ob_start();

        if ($type == "image/jpeg") {
            imagejpeg($image);
        } else if ($type == "image/png") {
            imagepng($image);
        }

        $image_data = ob_get_contents();

        ob_end_clean();

        imagedestroy($image);

        return base64_encode($image_data);
    }

    private function get_size($location): array
    {
        $size = [];
        $temp = getimagesize($location);
        array_push($size, $temp[0], $temp[1]);
        return $size;
    }

    private function resize($image, $size)
    {
        $offsetX = 0;
        $offsetY = 0;
        if ($size[0] < $size[1]) {
            $new_size = $size[0];
            $offsetY = ($size[1] - $size[0]) / 2;
        } else {
            $new_size = $size[1];
            $offsetX = ($size[0] - $size[1]) / 2;
        }
        $new = imagecreatetruecolor($new_size, $new_size);
        $image = file_get_contents($image);
        $old = imagecreatefromstring($image);
        imagecopyresized($new, $old, 0, 0, $offsetX, $offsetY, $new_size, $new_size, $new_size, $new_size);
        if ($new_size > 480) {
            $new = imagescale($new, 480);
        }
        return $new;
    }

}