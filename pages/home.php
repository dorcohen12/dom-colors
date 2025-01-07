<?php
	defined('INSITE') or die('No direct script access allowed');
    require TEMPLATE_DIR.'header.php';
    $cached_images = $Website->GetPersonalCachedImages();
?>

<div class="file-upload-container shadow-lg p-5">
    <h1>העלאת קובץ</h1>
    <div class="text-center mb-2 p-5 upload-area">
        <form class="w-100 m-0 m-auto text-center d-block" action="/" method="POST" id="uploadForm">
            <div class="col-md-8 col-8 file-drop-area m-auto text-center">
                <span class="choose-file-button">בחר קובץ</span>
                <span class="file-message">או גרור לכאן</span>
                <input class="file-input" type="file" name="file" accept=".png, .jpeg, .jpg" required>
            </div>
            <div class="col-md-6 col-6 text-center m-auto pt-3">
                <button class="button" type="submit">העלה</button>
            </div>
        </form>
        <div class="upload-info">
            <h3 id="status">אנא המתן בזמן שהמערכת מעבדת את הקובץ</h3>
            <div class="progress" style="height: 25px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="uploadStatus" id="uploadStatus">
            </div>
        </div>
    </div>
    <hr/>
    <h1>היסטוריית העלאות</h1>
    <?php 
        if(is_array($cached_images) && count($cached_images) > 0) { 
    ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">קובץ</th>
                        <th scope="col">צבעים</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cached_images as $key => $val) { 
                        ?>
                        <tr>
                            <td>
                                <a class="font-weight-bold text-white d-block btn btn-primary" target="_blank" title="link" href="<?php echo $Website->settings->web_url;?>/assets/uploads/<?php echo getUserIp();?>/<?php echo $val['file'];?>">View</a>
                            </td>
                            <td>
                                <div class="row">
                                    <?php foreach($val['data'] as $data) { ?>
                                        <div class="col-md-3 col-3">
                                            <div class="circle" data-toggle="tooltip" title="<?php echo $data['color']['hex'];?> (<?php echo (int)$data['percentage'];?>%)" style="border: 1px solid black; color: gold; text-shadow: 0px 0px 5px white; line-height: 35px; display: inline-block; border-radius: 50%; width: 35px; height: 35px; background: <?php echo htmlspecialchars($data['color']['hex']);?>"></div>
                                            <h5><?php echo (int)$data['percentage'];?>%</h5>
                                            <h5>R: <?php echo $data['color']['r']; ?> G: <?php echo $data['color']['g']; ?> B: <?php echo $data['color']['b'];?></h5>
                                            <hr/>
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php 
        } else {
            echo Message('info', 'לא נמצאו קבצים!');
        } 
    ?>
</div>

<?php
    require TEMPLATE_DIR.'footer.php';
