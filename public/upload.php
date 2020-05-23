<html lang="en">
    <title>gcocr.ai</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://unpkg.com/tachyons/css/tachyons.min.css">
    <body>
    <main class="pa4 black-80">
        <form class="measure center" action="indexkax.php" method="post" enctype="multipart/form-data">
            <fieldset id="sign_up" class="ba b--transparent ph0 mh0">
                <legend class="f4 fw6 ph0 mh0">Select image to upload:</legend>
                <div class="mt3">
                    <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*;capture=camera" onchange=""> <br />

                </div>
            </fieldset>
            <div class="">
                <input class="b ph3 pv2 input-reset ba b--black bg-transparent grow pointer f6 dib" type="submit" value="Upload Image" name="submit">
            </div>
        </form>
    </main>
  </body>
</html>