<?php

/* Name: Adithya Lakshman Kulkarni */

$Msg = "";
$auth_token = 'j0JsGz7HZK4AAAAAAAAAAfNpsQhGdlBXibUDhvY6cBwlm2HgGu_UfYDD1vBa6EjM';
if(isset($_FILES['image_upload']['name'])){

    /* Getting file name */
    $imgName = $_FILES['image_upload']['name'];
    $tmpName = $_FILES['image_upload']['tmp_name'];
    // $fileExtension = strtolower(pathinfo(basename($imgName), PATHINFO_EXTENSION));
    /* Location */
    $location = "./".$imgName;
    $imgType = pathinfo($location,PATHINFO_EXTENSION);
    $imgType = strtolower($imgType);

    $valid_extensions = array("jpg","jpeg","png");

    // if(in_array(strtolower($imgType), $valid_extensions)) {
    if(in_array(strtolower($imgType), $valid_extensions)) {
        if(move_uploaded_file($tmpName,$location)){
        // $Msg = "File uploaded successfully!"; 
            if(upload($imgName)){
                $Msg = "File uploaded successfully!";
                unlink($imgName);
                

    }}}
    else {
        $Msg =  "Error uploading file.";
    }
 }

# delete img
if (isset($_POST['delete'])) {

    $deleted = delete($_GET['delete']);
    $Msg =" deleted successfully!";
}


# download img
if (isset($_GET['download'])) {
    // header("Location:album.php");
    $downloaded = download($_GET['download'], "./images/".$_GET['download']);
    // $dltMsg = $filePath . " deleted successfully!";
    $Msg = "Download Successful";
    $src1 = "./images/{$_GET['download']}";
}


 

// set it to true to display debugging info
$debug = true;

function delete( $path ) {
    global $auth_token, $debug;
   $args = array("path" => $path);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
   		    'Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/files/delete_v2');
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
//    if ($debug)
//       print_r($result);
   $array = json_decode(trim($result), TRUE);
//    if ($debug)
//       print_r($array);
   curl_close($ch);
   return $array;
}


function download ( $path, $target_path ) {
   global $auth_token, $debug;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
      		    'Content-Type:', 'Dropbox-API-Arg: {"path":"/'.$path.'"}'));
   curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/download');
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
   file_put_contents($target_path,$result);
   curl_close($ch);

}

function upload ( $path ) {
   global $auth_token, $debug;
   $args = array("path" => $path, "mode" => "add");
   $fp = fopen($path, 'rb');
   $size = filesize($path);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_PUT, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
   		     'Content-Type: application/octet-stream',
		     'Dropbox-API-Arg: {"path":"/'.$path.'", "mode":"add"}'));
   curl_setopt($ch, CURLOPT_URL, 'https://content.dropboxapi.com/2/files/upload');
   curl_setopt($ch, CURLOPT_INFILE, $fp);
   curl_setopt($ch, CURLOPT_INFILESIZE, $size);
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
     return false;
   }
   if ($debug)
      print_r($result);
   curl_close($ch);
   fclose($fp);
   return true;
}

function directoryList ( $path ) {
   global $auth_token, $debug;
   $args = array("path" => $path);
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $auth_token,
   		    'Content-Type: application/json'));
   curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/files/list_folder');
   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
   try {
     $result = curl_exec($ch);
   } catch (Exception $e) {
     echo 'Error: ', $e->getMessage(), "\n";
   }
//    if ($debug)
//       print_r($result);
   $array = json_decode(trim($result), TRUE);
//    if ($debug)
//       print_r($array);
   curl_close($ch);
   return $array;
}

// $result = directoryList("");
// foreach ($result['entries'] as $x) {
//    echo $x['name'], "\n";
// }


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body onload="initilize();">
    <nav class="navbar navbar-light" style="background-color: #B7B7B7   ;">
        <div class="container">
            <a class="navbar-brand" href="#">Photo Album</a>
            <form action="album.php" method="post" enctype="multipart/form-data">
                <div class="input-group" style="width: 30rem;">
                    <input type="file" name="image_upload" id="fileToUpload" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                    <button class="btn btn-outline-primary" type="submit" id="inputGroupFileAddon04">Upload</button>
                </div>
            </form>
        </div>
    </nav>
    <br><br>
    <div class="container">
        <div class="row">
            <p class='msg'><?= $Msg ?></p>
        </div>
        <div class="row">
            <div class="col-lg-6">
            <h2>Images List</h2>
                <div class="list-group">
                    <form method="post">
                        <ul>
                            <?php
                            $result = directoryList("");
                            // $images = scandir("./images/");
                            
                            foreach ($result['entries'] as $x) {

                                # to display img preview
                                echo '<li class="list-group-item">';
                                //s"<a href='./album.php?download=" . $x['name'] ."' onclick='showPreview(./images/".$x['name'].");' Style='color: Black;'>" . $x['name']. "</a> ";
                                echo "<a href='./album.php?download={$x['name']}' Style='color: Black;'>{$x['name']}</a> ";
                                // echo "<button class='btn btn-outline-primary' type='submit'  formaction='asslbum.php?download=" . $x['name'] . "' name='download'> Download </button> ";
                                echo "<button class='btn btn-outline-primary' type='submit' formaction='album.php?delete=" . $x['path_lower'] . "' name='delete'> Delete </button> ";
                                echo "</li>";
                                //"<p class='' id=".$x['name']." hidden>".$x['name']."</p>"
                                //href = '".download($x['name'], "./images/".$x['name'])."'
                            }
                            ?>
                        </ul>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="img-wrapper">
                    <h2>Image Preview:</h2>
                    <img class="rounded" src="<?= $src1 ?>" style="width: 600px; height: 500px;">
                    <div id="no-img"></div>
                 </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js" integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous" async></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

<script>
    var preview;
    var noImg;

    function showPreview(img) {
        console.log(img);
        if (preview) {
            
            preview.src = img.href
            noImg.innerHTML = "";
        }

    }   

    function initilize() {
        preview = document.getElementById('preview');
        noImg = document.getElementById('no-img');
        
    }

    
</script>
</html>