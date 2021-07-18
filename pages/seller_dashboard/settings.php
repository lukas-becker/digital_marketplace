<?php


include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/model/Organization.php";
include $_SERVER['DOCUMENT_ROOT'] . "/controller/organization_controller.php";
$user = $_SESSION["user"];

if ($_SESSION["user"]->organization == 0) {
    header("Location: /index.php?message=no_org");
    die();
} else {
    $org = new Organization($_SESSION["user"]->organization);
    $org_controller = new organization_controller();
}

?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Marketplace";
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php";
    ?>

    <!-- Text Editor -->
    <!-- Library -->
    <script src="/scripts/wysihtml/wysihtml.js"></script>
    <script src="/scripts/wysihtml/wysihtml.toolbar.min.js"></script>

    <!-- wysihtml5 parser rules -->
    <script src="/scripts/wysihtml/advanced_and_extended.js"></script>

    <script>

        function changeOrganizationPicture() {

            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/controller/organization_controller.php?set_profile_picture=true", true);


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
                    } else if (result["status"] == false) {

                    } else {

                    }
                }
            };

            xmlhttp.send(formData);
        }

        function saveAddress() {

            let street = document.getElementById("street").value;
            let nr = document.getElementById("nr").value;
            let zip = document.getElementById("zip").value;
            let city = document.getElementById("city").value;


            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/controller/organization_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            let parameters = "set_address=true";
            parameters += "&street=" + street;
            parameters += "&nr=" + nr;
            parameters += "&zip=" + zip;
            parameters += "&city=" + city;

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    if (result["status"] == true) {
                        document.getElementById("addressButton").disabled = true;
                    } else if (result["status"] == false) {
                        document.getElementById("addressButton").classList.add("btn-danger");
                    } else {

                    }
                }
            };

            xmlhttp.send(parameters);
        }

        function saveName() {

            let name = document.getElementById("nameInput").value;


            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/controller/organization_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            let parameters = "set_name=true";
            parameters += "&name=" + name;

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    if (result["status"] == true) {
                        document.getElementById("nameButton").disabled = true;
                        location.reload();
                    } else if (result["status"] == false) {
                        document.getElementById("nameButton").classList.add("btn-danger");
                    } else {

                    }
                }
            };

            xmlhttp.send(parameters);
        }

        function saveDescription() {

            let desc = document.getElementById("description").value;


            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/controller/organization_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            let parameters = "set_description=true";
            parameters += "&description=" + desc;

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    if (result["status"] == true) {
                        document.getElementById("descriptionButton").classList.add("btn-success");
                    } else if (result["status"] == false) {
                        document.getElementById("nameButton").classList.add("btn-danger");
                    } else {

                    }
                }
            };

            xmlhttp.send(parameters);
        }

        function removeUser(id) {
            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/controller/organization_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            let parameters = "remove_user=true";
            parameters += "&user=" + id;

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    if (result["status"] == true) {
                        location.reload();
                    } else if (result["status"] == false) {
                    } else {

                    }
                }
            };

            xmlhttp.send(parameters);
        }

        function addUser() {
            let mail = document.getElementById("newMail").value;

            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/controller/organization_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            let parameters = "add_user=true";
            parameters += "&user=" + mail;

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    if (result["status"] == true) {
                        location.reload();
                    } else if (result["status"] == false) {
                    } else {

                    }
                }
            };

            xmlhttp.send(parameters);
        }

        let editor;
        setTimeout(function () {
            console.log("Here");
            editor = new wysihtml.Editor("description", { // id of textarea element
                toolbar: "wysihtml-toolbar", // id of toolbar element
                parserRules: wysihtmlParserRules // defined in parser rules set
            });
        }, 100)

    </script>

    <style>
        #wysihtml-toolbar .btn {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        #description {
            border-top-left-radius: 0;
        }

        .imgDelete {
            opacity: 0.5;
            cursor: pointer;
            text-shadow: 1px 1px black;
        }

        .imgDelete:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Seller dashboard for <?= $org->name; ?></h1>
    <div class="row my-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/seller_dashboard_menu.inc.php"; ?>
        <!-- Spacing -->
        <div class="col-md-1"></div>
        <!-- Content -->
        <div class="col-md-8">
            <!-- Menu -->
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                    <div class="text-center">
                        <label for="orgPictureUpload">
                            <?php
                            if ($org->image == null) {
                                echo '<img src="https://via.placeholder.com/120" class="rounded-circle mb-1" id="orgPicture">';
                            } else {
                                echo '<img src="data:image;base64,' . $org->image . '" class="rounded-circle mb-1" id="orgPicture" width="120">';
                            }
                            ?>
                        </label>
                        <input type="file" id="orgPictureUpload" name="orgPictureUpload" class="d-none"
                               onchange="changeOrganizationPicture()">


                        <br>
                        <span class="fw-bold"><?= $org->name; ?></span><br>
                    </div>
                </div>
                <div class="col-4"></div>
            </div>
            <hr>

            <!-- Name -->
            <h3>Company name:</h3>
            <div class="row mb-5">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label for="new" class="col-form-label">Name:</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" class="form-control" name="nameInput" id="nameInput"
                               placeholder="<?php echo $org->name ?>"
                               oninput="document.getElementById('nameButton').disabled = false">
                    </div>
                    <button class="col-auto btn btn-success" onclick="saveName()" id="nameButton" disabled>Save</button>
                </div>
            </div>
            <hr>

            <!-- Description -->
            <h3>Company Profile:</h3>
            <div class="row mb-5">
                <div class="row align-items-center">
                    <div class="col-12">
                        <label for="new" class="col-form-label">Name:</label>
                    </div>
                    <div class="col-12 mb-2">
                        <div id="wysihtml-toolbar" class="btn-group" style="display: none;">
                            <a data-wysihtml-command="bold" class="btn btn-primary"><i class="fas fa-bold"></i></a>
                            <a data-wysihtml-command="italic" class="btn btn-primary"><i class="fas fa-italic"></i></a>
                            <a data-wysihtml-command="underline" class="btn btn-primary"><i
                                        class="fas fa-underline"></i></a>

                            <!-- Some wysihtml5 commands like 'createLink' require extra paramaters specified by the user (eg. href) -->
                            <a data-wysihtml-command="createLink" class="btn btn-primary"><i
                                        class="fas fa-link"></i></a>
                            <div data-wysihtml-dialog="createLink" class="bg-primary ps-1 pt-1" style="display: none;">
                                <label class="text-white">Link:</label>
                                <input data-wysihtml-dialog-field="href" value="http://" class="text">

                                <a data-wysihtml-dialog-action="save" class="text-white text-decoration-none">OK</a>
                                <a data-wysihtml-dialog-action="cancel" class="me-1 text-white text-decoration-none">Cancel</a>
                            </div>
                            <a onclick="editor.composer.commands.exec('removeLink');" class="btn btn-primary"><i
                                        class="fas fa-unlink"></i></a>
                        </div>


                        <textarea class="form-control" placeholder="Description" id="description" name="description">
                            <?= $org->description; ?>
                        </textarea>
                    </div>
                    <div class="col-4">
                        <button class="col-auto btn btn-outline-success" onclick="saveDescription()"
                                id="descriptionButton">Save
                        </button>
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
                                           placeholder="<?= $org->street ?>"
                                           oninput="document.getElementById('addressButton').disabled = false">
                                </div>
                                <div class="col-3">
                                    <input type="text" id="nr" name="nr" class="form-control"
                                           placeholder="<?= $org->nr ?>"
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
                                           placeholder="<?= $org->zip ?>"
                                           oninput="document.getElementById('addressButton').disabled = false">
                                </div>
                                <div class="col-8">
                                    <input type="text" id="city" name="city" class="form-control"
                                           placeholder="<?= $org->city ?>"
                                           oninput="document.getElementById('addressButton').disabled = false">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success col-12" id="addressButton" disabled onclick="saveAddress();">Save
                    </button>
                </div>
            </div>
            <hr>

            <!-- Approved Users -->
            <h3>Members of your Organization:</h3>
            <div class="row mb-5">
                <div class="row align-items-center">
                    <div class="col-12 col-sm-10 col-lg-8">
                        <ul class="list-group">
                            <?php
                            foreach ($org_controller->get_users_in_organization() as $user) {
                                echo '<li class="list-group-item d-flex justify-content-between">';
                                echo $user[0] . " " . $user[1] . " - " . $user[2];
                                echo '<a onclick="removeUser(' . $user[3] . ')" style="cursor: pointer"><i class="fas fa-user-times text-danger"></i></a>';
                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <hr>

            <!-- Add User -->
            <h3>Add User to Organization:</h3>
            <div class="row mb-5">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label for="new" class="col-form-label">E-Mail:</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" class="form-control" id="newMail" name="newMail"
                               oninput="document.getElementById('mailButton').disabled = false">
                    </div>
                    <button class="col-auto btn btn-success" id="mailButton" disabled onclick="addUser();">Save</button>
                </div>
            </div>
            <hr>

        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
