<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    // print_r($_FILES);
    $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
    $base_url .= "://" . $_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
    extract($_POST);
    extract($_GET);
    // $namafile       = $_FILES["gambar"]["name"];
    // $filegambar     = substr($namafile, 0, strripos($namafile, '.'));
    $ekstensifile   = ".png";
    // $ekstensifile   = substr($namafile, strripos($namafile, '.'));
    // $ukuranfile     = $_FILES["gambar"]["size"];
    $jenisfile      = array('.jpg', '.jpeg', '.png', '.bmp', '.JPG', '.JPEG', '.PNG', '.BMP', '.webp');

    $img = str_replace('data:image/png;base64,', '', $gambar);
    $img = str_replace(' ', '+', $img);
    $fileData = base64_decode($img);
    $ukuranfile = (int) (strlen(rtrim($fileData, '=')) * 3 / 4);

    if (!empty($fileData)) {
        if ($ukuranfile <= 1000000) {
            if (in_array($ekstensifile, $jenisfile) && ($ukuranfile <= 1000000)) {
                $namabaru = time() . '_' . uniqid() . '_' . date("Ymdhis") . '_n' . $ekstensifile;
                if (file_exists("../assets/images/" . $namabaru)) {
                    // echo '<script>';
                    // echo 'alert("Error! Terjadi kesalahan, silahkan coba lagi", "error");';
                    // echo 'history.back();';
                    // echo '</script>';
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error! Terjadi kesalahan, silahkan coba lagi'
                    ]);
                } else {
                    // move_uploaded_file($fileData, "./assets/images/" . $namabaru);
                    file_put_contents("../assets/images/" . $namabaru, $fileData);
                    $namabaru2 = in_array($ekstensifile, ['.jpg', '.jpeg', '.JPEG', '.JPG']) ? imagecreatefromjpeg("../assets/images/" . $namabaru) : (in_array($ekstensifile, ['.PNG', '.png']) ? imagecreatefrompng("../assets/images/" . $namabaru) : $namabaru);
                    $w = imagesx($namabaru2);
                    $h = imagesy($namabaru2);
                    $webp = imagecreatetruecolor($w, $h);
                    imagecopy($webp, $namabaru2, 0, 0, 0, 0, $w, $h);
                    $namabaru3 = time() . '_' . uniqid() . '_' . date("Ymdhis") . '_n.webp';
                    $gambar = "../assets/images/" . $namabaru3;
                    imagewebp($webp, $gambar, 80);
                    imagedestroy($namabaru2);
                    imagedestroy($webp);
                    unlink("../assets/images/" . $namabaru);
                    // file_put_contents($gambar, $fileData);
                    // echo "<script>";
                    // echo "window.location.href = '$base_url/share.php?id=$id&w=$lebar&h=$tinggi&im=$namabaru'";
                    // echo "</script>";
                    echo json_encode([
                        'success' => true,
                        'id' => $id,
                        'tinggi' => $height,
                        'lebar' => $width,
                        'nama_file' => $namabaru3
                    ]);
                }
            } else {
                // echo '<script>';
                // echo 'alert("Error! File yang diupload harus gambar", "error");';
                // echo 'history.back();';
                // echo '</script>';
                unlink($_FILES["gambar"]["tmp_name"]);
                echo json_encode([
                    'success' => false,
                    'message' => "Error! File yang diupload harus gambar"
                ]);
            }
        } else {
            // echo '<script>';
            // echo 'alert("Error! Ukuran file tidak boleh lebih dari 5MB", "error");';
            // echo 'history.back();';
            // echo '</script>';
            echo json_encode([
                'success' => false,
                'message' => "Error! Ukuran file tidak boleh lebih dari 5MB",
                'filesize' => $ukuranfile
            ]);
        }
    } else {
        // echo '<script>';
        // echo 'alert("Error! Gambar tidak boleh kosong", "error");';
        // echo 'history.back();';
        // echo '</script>';
        echo json_encode([
            'success' => false,
            'message' => "Error! Gambar tidak boleh kosong"
        ]);
    }
}
