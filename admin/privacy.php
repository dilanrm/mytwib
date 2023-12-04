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
        $policy = str_replace("'","&#39;",htmlspecialchars($policy));
        $query = "UPDATE `settings` SET `policy` = '$policy' WHERE `id` = 1";

        $result = mysqli_query($conn, $query);
        if ($result) {
            $statusMsg = "Privacy updated.";
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


        // Display status message
        echo $statusMsg;
        break;
    case 'GET':
        $policy = html_entity_decode($settings->policy);
?>
        <div class="container">
            <form action="./privacy.php" method="post">
                <h3>Privacy website</h3>
                <div class="form-group">
                    <label for="deskrip">Privacy Website</label>
                    <textarea id="policy" name="policy" cols="40" rows="3" class="form-control" aria-describedby="policyHelpBlock"><?php echo $policy ?></textarea>
                </div>
                <div class="form-group">
                    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
            <script src="https://cdn.tiny.cloud/1/x3lcmvnx4p69j7ngmeabkselclv67zn95ziav80tc003i08n/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
            <script>
                tinymce.init({
                    selector: 'textarea#policy',
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
        </div>

<?php
        break;
}
?>