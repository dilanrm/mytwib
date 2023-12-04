<?php
use function PHPSTORM_META\type;
error_reporting(E_ERROR | E_PARSE);
$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base_url .= "://".$_SERVER['HTTP_HOST'];
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);

// echo $base_url;
$frames = json_decode(file_get_contents($base_url.'../data/frame/'));
require '../header.php';
$new_array = array_filter($frames, function ($obj) {
    if ($obj->id == explode("/", $_GET['id'])[0]) return true;
    return false;
});
// print_r($new_array);
// print_r($url_data);
?>


<style>
    #canvas,
    #canvas-upper {
        position: absolute;
        width: 352px;
        height: 352px;
        left: 90px;
        top: 0px;
        touch-action: none;
        user-select: none;
    }

    #canvas-upper {
        cursor: move;
    }

    @media only screen and (max-width: 480px) {

        #canvas,
        #canvas-upper {
            left: 5px;
        }
    }
</style>

<main style="background: #f9f9f9">

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 col-md-12 mb-4">
                <form id="genTwib" enctype="multipart/form-data">
                    <div class="card card-body shadow-sm mb-4">
                        <div class="mb-4 font-weight-light py-2" onmousedown="return false;" style="
                        width: 352px;
                        height: 352px;
                        user-select: none;
                        ">
                            <img src="<?php echo "../frames/" . $new_array[0]->file ?>" hidden="true" id="img2" class="img-fluid">
                            <canvas id="canvas"></canvas>
                            <canvas id="canvas-upper"></canvas>
                        </div>
                        <img src="../assets/images/download.png" id="img1" hidden="true" class="img-fluid">
                        <!-- <label class="font-weight-light"><b>Change size</b></label> -->
                        <!-- <div class="input-group"> -->
                        <!-- <input type="number" class="form-control" placeholder="width" id="lebar" name="lebar" /> -->
                        <input type="number" class="form-control" placeholder="width" id="lebarhid" hidden />
                        <!-- <span class="input-group-addon"> x </span> -->
                        <!-- <input type="number" class="form-control" placeholder="heigth" id="tinggi" name="tinggi" /> -->
                        <input type="number" class="form-control" placeholder="heigth" id="tinggihid" hidden />
                        <!-- </div> -->
                        <!-- <button class="btn btn-secondary btn-sm" type="button" onclick="resetSize()">Reset size</button> -->
                        <div class="slidecontainer">
                            <label class="font-weight-light"><b>Zoom In/Out</b></label>
                            <input type="range" class="slider" id="myRange">
                        </div>
                        <label class="font-weight-light"><b>Upload image</b></label>
                        <input type="file" name="gambar" id="image" accept="image/*" class="p-1 img-thumbnail btn-block">
                        <article class="my-4 mb-4">
                            <p class="mb-0 text-md-left"><?= $new_array[0]->deskrip ?></p>
                        </article>
                        <button name="submit" type="button" class="btn btn-primary btn-sm" onclick="downloadCanvas()">
                            Generate Frame!
                        </button>
                        <br>
                        <button type="submit" class="btn btn-success btn-sm">Share!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="../detail.js"></script>
    <script>
        $('#genTwib').on('submit', function(e) {
            e.preventDefault();

            let img = document.getElementById("img2");

            $.ajax({
                method: 'post',
                url: `./share.php?id=<?php echo $new_array[0]->id ?>&lebar=${img.width}&tinggi=${img.height}`,
                data: {
                    gambar: downloadCanvas(false)
                }
            }).done(function(response) {
                console.log(response);
                window.location.href = `<?php echo $base_url ?>share.php?im=${response.nama_file}&w=${response.lebar}&h=${response.tinggi}`;
            })
        })
    </script>
</main>

<?php
require '../footer.php';


