<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
    header('Location: ./');
}
require '../conn.php';
require './header.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $statusMsg = '';

        // File upload path
        extract($_POST);
        $targetDir = "../assets/images/";
        $fileName = date('dmYHis') . "_" . str_replace(" ", "", basename($_FILES["file"]["name"]));
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);;
        $query = "";

        if (!empty($_FILES["file"]["name"])) {
            $query = "UPDATE `settings` SET `judul` = '$judul', `deskrip` = '$deskrip', `icon` = '" . $fileName . "' WHERE `id` = 1";
            // Allow certain file formats
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif', 'pdf'];
            if (in_array($fileType, $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                    // Insert image file name into database

                    $result = mysqli_query($conn, $query);
                    if ($result) {
                        $statusMsg = "The file " . $fileName . " has been uploaded successfully.";
                        echo "<script>";
                        echo "alert('$statusMsg');";
                        echo "window.location.href = '$base_url'";
                        echo "</script>";
                    } else {
                        $statusMsg = "File upload failed, please try again. $query " . mysqli_error($conn);
                        echo "<script>";
                        echo "alert('$statusMsg');";
                        echo "window.location.href = '$base_url'";
                        echo "</script>";
                    }
                } else {
                    $statusMsg = "Sorry, there was an error uploading your file. $targetFilePath ";
                    echo "<script>";
                    echo "alert('$statusMsg');";
                    echo "window.location.href = '$base_url'";
                    echo "</script>";
                }
            } else {
                $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
                echo "<script>";
                echo "alert('$statusMsg');";
                echo "window.location.href = '$base_url'";
                echo "</script>";
            }
        } else {
            $query = "UPDATE `settings` SET `judul` = '$judul', `deskrip` = '$deskrip' WHERE `id` = 1";
            $result = mysqli_query($conn, $query);
                if ($result) {
                    $statusMsg = "The file " . $fileName . " has been uploaded successfully.";
                    echo "<script>";
                    echo "alert('$statusMsg');";
                    echo "window.location.href = '$base_url'";
                    echo "</script>";
                } else {
                    $statusMsg = "File upload failed, please try again. $query " . mysqli_error($conn);
                    echo "<script>";
                    echo "alert('$statusMsg');";
                    echo "window.location.href = '$base_url'";
                    echo "</script>";
                }
        }
        
        // Display status message
        echo $statusMsg;
        break;
    case 'GET':
?>
        <div class="container">
            <form action="./setting.php" method="post" enctype="multipart/form-data">
                <h3>Setingan website</h3>
                <div class="form-group">
                    <label for="name">Judul Website</label>
                    <input id="name" name="judul" placeholder="Judul Website (max. 25 karakter)" type="text" class="form-control" value="<?php echo $settings->judul ?>">
                </div>
                <div class="form-group">
                    <label for="deskrip">Deskripsi Website</label>
                    <textarea id="deskrip" name="deskrip" cols="40" rows="3" class="form-control" aria-describedby="deskripHelpBlock"><?php echo $settings->deskrip ?>
                    </textarea>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon01">Upload Icon</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                    </div>
                </div>
                <br>
                <img src="../assets/images/<?php echo $settings->icon ?>" alt="review gambar" class="img-fluid" id="review" width="200px">
                <br><br><br>
                <div class="form-group">
                    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <script>
                $(document).ready(function() {
                    $("#inputGroupFile01").on('change', function(e) {
                        // $('#review').attr('src', URL.createObjectURL($("#inputGroupFile01").val()));
                        let output = document.getElementById('review');
                        output.src = URL.createObjectURL(e.target.files[0]);
                        output.onload = function() {
                            URL.revokeObjectURL(output.src) // free memory
                        }
                    })
                });
            </script>
        </div>

<?php
        break;
}
?>