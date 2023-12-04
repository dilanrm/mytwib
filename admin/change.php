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
        extract($_POST);
        extract($_GET);

        $old_password = trim($old_password);

        $cek_pass = mysqli_query($conn, "SELECT password FROM user where id = $id");

        $row = mysqli_fetch_assoc($cek_pass);

        if (!password_verify($old_password,$row['password'])) {
            $password_err = "Password lama salah, mohon dicek kembali.";
            echo "<script>";
            echo "alert('$password_err');";
            echo "window.location.href = './change.php';";
            echo "</script>;";
            break;
        } 

        if (strlen(trim($_POST["new_password"])) < 6) {
            $password_err = "Password must have atleast 6 characters.";
            echo "<script>";
            echo "alert('$password_err');";
            echo "window.location.href = './change.php';";
            echo "</script>;";
            break;
        } else {
            $new_password = password_hash(trim($_POST["new_password"]), PASSWORD_DEFAULT);
        }

        $query = "UPDATE `user` SET password = '$new_password' WHERE id = $id";
        $result = mysqli_query($conn, $query);
        if ($result) {
            echo "<script>";
            echo "alert('Password berhasil diubah!');";
            echo "window.location.href = './change.php';";
            echo "</script>;";
        } else {
            echo "<script>";
            echo "alert('Data update failed, please try again. " . mysqli_error($conn) . "');";
            echo "window.location.href = './change.php';";
            echo "</script>;";
        }

        break;
    case 'GET':
?>
        <style>
            form i {
                cursor: pointer;
            }
        </style>
        <div class="container">
            <form action="./change.php?id=<?= $_SESSION['id'] ?>" method="post" enctype="multipart/form-data">
                <h3>Ganti Password</h3>
                <div class="form-group">
                    <label for="old_password">Password lama</label>
                    <div class="input-group mb-3">
                        <input id="old_password" name="old_password" placeholder="password lama" type="password" class="form-control" require>
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon1" title="show password">
                                <i class="fa fa-eye-slash" id="oldUnshow" onclick="showPass('old')"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_password">Password baru</label>
                    <div class="input-group mb-3">
                        <input id="new_password" name="new_password" placeholder="password baru" type="password" class="form-control" require>
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon2" title="show password">
                                <i class="fa fa-eye-slash" id="newUnshow" onclick="showPass('new')"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button name="submit" id="submit" type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <script>
                function showPass(which) {
                    const togglePassword = document
                        .querySelector(`#${which}Unshow`);

                    const password = document.querySelector(`#${which}_password`);

                    // togglePassword.addEventListener('click', () => {

                    // Toggle the type attribute using
                    // getAttribure() method
                    const type = password
                        .getAttribute('type') === 'password' ?
                        'text' : 'password';

                    password.setAttribute('type', type);

                    console.log(togglePassword)
                    // Toggle the eye and bi-eye icon
                    togglePassword.classList.toggle('fa-eye');
                    // })
                }
            </script>
        </div>
<?php
        break;
    default:
        header('Location: ./');
}
