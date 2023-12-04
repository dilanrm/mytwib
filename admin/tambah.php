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
        $slug = slugify($name);
        $deskrip = mysqli_real_escape_string($conn,$deskrip);
        $deskrip = str_replace("'","&#39;",htmlspecialchars($deskrip));
        $targetDir = "../assets/frames/";
        $fileName = date('dmYHis') . "_" . str_replace(" ", "", basename($_FILES["file"]["name"]));
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        echo $slug;
        print_r($_POST);

        if (!empty($_FILES["file"]["name"])) {
            // Allow certain file formats
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif', 'pdf','webp'];
            if (in_array($fileType, $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                    // Insert image file name into database
                    $query = "INSERT into `frames` (`name`, `deskrip`, `file`, `keywords`, `slug`, `category`) VALUES ('$name','$deskrip','" . $fileName . "', '$keywords', '$slug', '$category')";
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
                        echo "window.location.href = '$base_url/tambah.php'";
                        echo "</script>";
                    }
                } else {
                    $statusMsg = "Sorry, there was an error uploading your file. $targetFilePath ";
                    echo "<script>";
                    echo "alert('$statusMsg');";
                    echo "window.location.href = '$base_url/tambah.php'";
                    echo "</script>";
                }
            } else {
                $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, WEBP, & PDF files are allowed to upload.';
                echo "<script>";
                echo "alert('$statusMsg');";
                echo "window.location.href = '$base_url/tambah.php'";
                echo "</script>";
            }
        } else {
            $statusMsg = 'Please select a file to upload. ';
            echo "<script>";
            echo "alert('$statusMsg');";
            echo "window.location.href = '$base_url/tambah.php'";
            echo "</script>";
        }

        // Display status message
        echo $statusMsg;
        break;
    case 'GET':
?>
        <div class="container">
            <form action="./tambah.php" method="post" enctype="multipart/form-data">
                <h3>Tambah Bingkai</h3>
                <div class="form-group">
                    <label for="name">Nama Bingkai</label>
                    <input id="name" name="name" placeholder="Nama Bingkai (max. 150 karakter)" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label for="deskrip">Deskripsi Bingkai</label>
                    <textarea id="deskrip" name="deskrip" cols="40" rows="3" class="form-control" aria-describedby="deskripHelpBlock"></textarea>
                    <span id="deskripHelpBlock" class="form-text text-muted">max. 500 karakter</span>
                </div>
                <script src="https://cdn.tiny.cloud/1/x3lcmvnx4p69j7ngmeabkselclv67zn95ziav80tc003i08n/tinymce/5/tinymce.min.js" referrerterms="origin"></script>
                <script>
                    tinymce.init({
                        selector: 'textarea#deskrip',
                        plugins: 'link code lists',
                        height: 600,
                        toolbar: 'undo redo | formatselect | ' +
                            'bold italic backcolor | alignleft aligncenter ' +
                            'alignright alignjustify | bullist numlist outdent indent | ' +
                            'removeformat | help',
                        a_plugin_option: true,
                        a_configuration_option: 400
                    });
                    // let myContent = tinymce.activeEditor.getContent({
                    //     format: "text"
                    // });
                </script>
                <div class="form-group">
                    <label for="keywords">Keywords Bingkai (dipisahkan oleh koma)</label>
                    <textarea id="keywords" name="keywords" cols="40" rows="3" class="form-control" aria-describedby="keywordsHelpBlock"></textarea>
                    <span id="keywordsHelpBlock" class="form-text text-muted">max. 500 karakter</span>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect04">Pilih kategori</label>
                    </div>
                    <select class="custom-select" id="inputGroupSelect04" name="category">
                        <option value="" selected disabled>Pilih kategori...</option>
                        <?php
                        foreach ($categories as $key => $value) {
                        ?>
                            <option value="<?php echo $value->id ?>"><?php echo $value->name ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon01">Upload</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                        <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                    </div>
                </div>
                <br>
                <img src="" alt="review gambar" class="img-fluid" id="review" style="width: 20%;">
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
    default:
        header('Location: ./');
}
