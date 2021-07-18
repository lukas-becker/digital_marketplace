<?php


include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/model/Organization.php";
include $_SERVER['DOCUMENT_ROOT'] . "/controller/organization_controller.php";
$user = $_SESSION["user"];

if (!isset($_SESSION["login"])) {
    header("Location: /pages/login.php");
    die();
}

if ($_SESSION["user"]->organization != 0) {
    header("Location: /pages/seller_dashboard/index");
    die();
}

?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Marketplace";
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php";
    ?>

    <script>

        function uploadOrganizationPicture() {

            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/php_backend/image_pipeline.php?square_pipeline=true", true);


            const fileInput = document.getElementById("orgPictureUpload");
            const file = fileInput.files[0];

            const formData = new FormData();
            formData.append("upload", file, file.name);

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    if (result["status"] == true) {
                        document.getElementById("orgPicture").src = "data:image;base64," + result["image"];
                        document.getElementById("orgPictureText").value = result["image"];
                        fileInput.files = null;
                    } else if (result["status"] == false) {

                    } else {

                    }
                }
            };

            xmlhttp.send(formData);
        }

    </script>
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 py-5">
    <h1>New Organization</h1>
    <div class="row my-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/profile_menu.inc.php"; ?>
        <!-- Spacing -->
        <div class="col-md-1"></div>
        <!-- Content -->
        <div class="col-md-8">
            <form action="/controller/organization_controller.php?new_org=true" method="post">
                <!-- Menu -->
                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-4">
                        <div class="text-center">
                            <label for="orgPictureUpload">
                                <img src="https://via.placeholder.com/120" class="rounded-circle mb-1" width="120"
                                     id="orgPicture">
                            </label>
                            <input type="file" id="orgPictureUpload" name="orgPictureUpload" class="d-none"
                                   onchange="uploadOrganizationPicture()">
                            <input type="text" id="orgPictureText" name="orgPictureText" class="d-none">

                            <br>
                        </div>
                    </div>
                    <div class="col-4"></div>
                </div>
                <hr>

                <!-- Name -->
                <h3>Company name:</h3>
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-8 col-lg-4">
                                <label for="new" class="col-form-label">Name:</label>
                            </div>
                            <div class="col-12 col-lg-8">
                                <input type="text" class="form-control" name="nameInput" id="nameInput"
                                       oninput="document.getElementById('nameButton').disabled = false">
                            </div>
                        </div>
                    </div>

                </div>
                <hr>

                <!-- Addresses -->
                <h3>Address:</h3>
                <div class="row mb-5">
                    <div class="col-12">
                        <!-- street & number -->
                        <div class="row align-items-center mb-2">
                            <div class="col-8 col-lg-4">
                                <label for="address">Street & Number:</label>
                            </div>
                            <div class="col-12 col-lg-8">
                                <div class="row">
                                    <div class="col-9">
                                        <input type="text" id="street" name="street" class="form-control"
                                               oninput="document.getElementById('addressButton').disabled = false">
                                    </div>
                                    <div class="col-3">
                                        <input type="text" id="nr" name="nr" class="form-control"
                                               oninput="document.getElementById('addressButton').disabled = false">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- zip & city -->
                        <div class="row align-items-center mb-2">
                            <div class="col-8 col-lg-4">
                                <label for="address">Zip & City.:</label>
                            </div>
                            <div class="col-12 col-lg-8">
                                <div class="row">
                                    <div class="col-4">
                                        <input type="text" id="zip" name="zip" class="form-control"
                                               oninput="document.getElementById('addressButton').disabled = false">
                                    </div>
                                    <div class="col-8">
                                        <input type="text" id="city" name="city" class="form-control"
                                               oninput="document.getElementById('addressButton').disabled = false">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <button class="col-12 btn btn-success" type="submit">Save</button>
                </div>


                <div class="row">
                    Please note: for security reasons you will be logged out after the organization is created!
                </div>
            </form>

        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
