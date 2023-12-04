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
        if ($_GET['act'] == "create") {
            extract($_POST);
            $url = trim($nama);
            $url = str_replace(' ', '-', $url);
            $url = str_replace('/', '-slash-', $url);
            $slug = strtolower(rawurlencode($url));
            $query = "INSERT INTO `category`(`name`,`slug`) VALUES('$nama',
                CONCAT((SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'category'),'-$slug')
                )";
            $result = mysqli_query($conn, $query);
            if ($result) {
                echo "<script>";
                echo "alert('Data berhasil ditambah!');";
                echo "window.location.href = './category.php';";
                echo "</script>;";
            } else {
                echo "<script>";
                echo "alert('File upload failed, please try again. " . mysqli_error($conn) . "');";
                echo "window.location.href = './category.php';";
                echo "</script>;";
            }
        } else if ($_GET['act'] == "update") {
            extract($_POST);
            extract($_GET);
            $url = trim($nama);
            $url = str_replace(' ', '-', $url);
            $url = str_replace('/', '-slash-', $url);
            $slug = strtolower(rawurlencode($url));
            $query = "UPDATE `category` set `name` = '$nama', `slug` = CONCAT('$id','-$slug') WHERE `id` = $id";
            $result = mysqli_query($conn, $query);
            if ($result) {
                echo "<script>";
                echo "alert('Data berhasil diubah!');";
                echo "window.location.href = './category.php';";
                echo "</script>;";
            } else {
                echo "<script>";
                echo "alert('File upload failed, please try again. " . mysqli_error($conn) . "');";
                echo "window.location.href = './category.php';";
                echo "</script>;";
            }
        }
        break;
    case 'GET':
        if (isset($_GET['id'])) {
            if ($_GET['act'] === 'hapus') {
                extract($_GET);
                $query = "DELETE FROM `category` WHERE `id` = $id";

                if (mysqli_query($conn, $query)) {
                    header('Location: ./category.php');
                } else {
                    echo ("Error description: " . mysqli_error($conn));
                }
            } else if ($_GET['act'] === 'update') {
                $new_array = array_filter($categories, function ($obj) {
                    if ($obj->id == $_GET['id']) return true;
                    return false;
                });
?>
                <div class="container">
                    <form action="./category.php?act=update&id=<?php echo $_GET['id'] ?>" method="POST">
                    <h4>Edit data</h4>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="nama" placeholder="Tambah kategori baru (max. 50 karakter)" value="<?php echo $new_array[0]->name ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="container">
                <h2>List Kategori</h2>
                <form action="./category.php?act=create" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="nama" placeholder="Tambah kategori baru (max. 50 karakter)">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Tambah</button>
                        </div>
                    </div>
                </form>
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (empty($categories)) {
                        ?>
                            <tr>
                                <td colspan="3">No data</td>
                            </tr>
                        <?php } else { ?>
                            <?php
                            foreach ($categories as $i => $cat) {
                                $i++;
                                echo "<tr>";
                                echo "<td>$i</td>";
                                echo "<td>$cat->name</td>";
                            ?>
                                <td>
                                    <a href='./category.php?act=update&id=<?php echo $cat->id ?>' class='btn btn-sm btn-warning' style="color:#fff">Edit</a>
                                    <a href='./category.php?act=hapus&id=<?php echo $cat->id ?>' class='btn btn-sm btn-danger' onclick='return confirm("Anda yakin?")'>Hapus</a>
                                </td>
                        <?php
                                echo "</tr>";
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
<?php
        }
        break;
    default:
        break;
}
