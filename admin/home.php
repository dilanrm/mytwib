<?php require './header.php' ?>

<main role="main">
    <?php require_once './cards.php' ?>
    <section class="jumbotron text-center bg-secondary">
        <div class="container">
            <!-- <h1 class="jumbotron-heading"><?php echo $settings->judul ?></h1>
            <p class="lead text-muted"><?php echo $settings->deskrip ?></p> -->
            <p>
                <a href="./tambah.php" class="btn btn-lg btn-success my-2">Tambah bingkai baru</a>
                <a href="./category.php" class="btn btn-lg btn-info my-2">List kategori</a>
                <a href="./contact.php" class="btn btn-lg btn-primary my-2">Edit halaman contact</a>
                <a href="./disclaimer.php" class="btn btn-lg btn-primary my-2">Edit halaman Disclaimer</a>
                <a href="./terms.php" class="btn btn-lg btn-primary my-2">Edit halaman Terms</a>
                <a href="./privacy.php" class="btn btn-lg btn-warning my-2" style="color:#fff">Edit Privacy & Policy</a>
                <a href="./setting.php" class="btn btn-lg btn-dark my-2">Ubah settingan website</a>
            </p>
        </div>
    </section>

    <div class="album py-5 bg-light">
        <div class="container">

            <div class="row">
                <?php
                $search = strtolower($_GET['search']);
                $frames = isset($_GET['cat']) && $_GET['cat'] !== 'all' ? array_filter($frames, function ($obj) {
                    if ($obj->cat == $_GET['cat']) return true;
                    return false;
                }) : (isset($_GET['search']) && $_GET['search'] !== 'all'
                    ? array_filter($frames, function ($obj) use ($search) {
                        if (
                            strpos(strtolower($obj->name), $search) ||
                            strpos(strtolower($obj->keywords), $search) ||
                            strpos(strtolower($obj->deskrip), $search)
                        ) return true;
                        return false;
                    }) :
                    $frames);

                // print_r($frames);

                $batas = 10;
                $halaman = (isset($_GET['halaman'])) ? (int)$_GET['halaman'] : 1;
                $halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

                $jumlah_data = count($frames);
                $total_halaman = ceil($jumlah_data / $batas);

                $data_frame = mysqli_query($conn, "SELECT `frames`.*,`category`.`name` as kategori,`category`.`slug` as cat FROM `frames` INNER JOIN `category` ON `frames`.`category` = `category`.`id` ORDER BY `frames`.`id` DESC limit $halaman_awal, $batas");
                $nomor = $halaman_awal + 1;

                $previous = $halaman - 1;
                $next = $halaman + 1;

                $frame_rows = array();
                while ($r = mysqli_fetch_assoc($data_frame)) {
                    $frame_rows[] = $r;
                }

                $frame_rows = json_decode(json_encode($frame_rows));
                $frame_rows = isset($_GET['cat']) && $_GET['cat'] !== 'all' ? array_filter($frame_rows, function ($obj) {
                    if ($obj->cat == $_GET['cat']) return true;
                    return false;
                }) : (isset($_GET['search']) && $_GET['search'] !== 'all'
                    ? array_filter($frame_rows, function ($obj) use ($search) {
                        if (
                            strpos(strtolower($obj->name), $search) ||
                            strpos(strtolower($obj->keywords), $search) ||
                            strpos(strtolower($obj->deskrip), $search)
                        ) return true;
                        return false;
                    }) :
                    $frame_rows);
                // print_r($frame_rows);
                foreach ($frame_rows as $key => $value) {
                ?>
                    <div class="col-lg-3 col-md-5 col-sm-4 col-10">
                        <div class="card mb-4 box-shadow h-100">
                            <img class="card-img-top" src="<?php echo $base_url . '../assets/frames/' . $value->file ?>" alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $value->name ?></h5>
                                <p class="card-text">
                                    <?php
                                    $string = html_entity_decode($value->deskrip);
                                    if (strlen($string) > 25) {

                                        // truncate string
                                        $stringCut = substr($string, 0, 45);
                                        $endPoint = strrpos($stringCut, ' ');

                                        //if the string doesn't contain any space then it will cut without word basis.
                                        $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                                        $string .= '...';
                                        $string = $tidy->repairString($string, array(
                                            'output-xml' => true,
                                            'input-xml' => true
                                        ));
                                    }
                                    echo ($string);
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="btn-group">
                                        <a onclick="return confirm('Anda yakin?')" href="./hapus.php?id=<?php echo $value->id ?>&file=<?php echo $value->file ?>" class="btn btn-md btn-outline-danger">hapus</a>
                                        <a href="./edit.php?id=<?= $value->id ?>" class="btn btn-md btn-outline-secondary">Edit</a>
                                    </div>
                                    <!-- <small class="text-muted">9 mins</small> -->
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="<?= $base_url ?>?cat=<?= $value->cat ?>" class="text-muted">Category: <?php echo $value->kategori ?></a>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <nav class="mb-2">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link" <?php
                                        echo $halaman > 1 ? 'href="' . $base_url . '?cat=' . (isset($_GET['cat']) ? $_GET['cat'] : 'all') . '&halaman=' . $previous . '"' : "disabled";
                                        ?>>
                    Previous
                </a>
            </li>
            <?php
            for ($x = 1; $x <= $total_halaman; $x++) {
                error_reporting(E_ALL ^ E_WARNING);
            ?>
                <li class="page-item"><a class="page-link" href="<?= $base_url ?><?= '?cat=' . (isset($_GET['cat']) ? $_GET['cat'] : 'all') ?>&halaman=<?= $x ?>"><?= $x; ?></a></li>
            <?php
            }
            ?>
            <li class="page-item">
                <a class="page-link" <?php
                                        echo $halaman < $total_halaman ? 'href="' . $base_url . '?cat=' . (isset($_GET['cat']) ? $_GET['cat'] : 'all') . '&halaman=' . $next . '"' : "disabled";
                                        ?>>
                    Next
                </a>
            </li>
        </ul>
    </nav>

</main>

<?php require_once './footer.php' ?>