<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/model/Article.php";
if (!isset($_SESSION["login"])) {
    header("Location: /pages/login.php");
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

    <link rel="stylesheet" href="/style/profile_accordion.css">
</head>

<script>

    function changeProfilePicture() {

        const xmlhttp = new XMLHttpRequest();

        xmlhttp.open("POST", "/controller/user_controller?set_profile_picture=true", true);
        //xmlhttp.setRequestHeader("Content-type", "multipart/form-data");

        const fileInput = document.getElementById("profilePictureUpload");
        const file = fileInput.files[0];

        const formData = new FormData();
        formData.append("upload", file, file.name);

        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                const result = JSON.parse(this.responseText);
                if (result["status"] == true) {
                    document.getElementById("profilePicture").src = "data:image;base64," + result["image"];
                } else if (result["status"] == false) {

                } else {

                }
            }
        };

        xmlhttp.send(formData);
    }

</script>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Accountinformation for <?php echo $_SESSION["user"]->first_name; ?></h1>
    <div class="row mt-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/profile_menu.inc.php"; ?>

        <!-- spacing -->
        <div class="col-md-1"></div>

        <!-- Content -->
        <div class="col-md-8">
            <div class="row mb-3">
                <div class="col-4"></div>
                <div class="col-4 text-center">
                    <label for="profilePictureUpload">
                        <?php
                        $user = $_SESSION["user"];
                        if ($user->image == null) {
                            ?>
                            <img src="https://via.placeholder.com/120" class="rounded-circle mb-1" id="profilePicture"
                                 width="120">
                            <?php
                        } else {
                            echo '<img src="data:image;base64,' . $user->image . '" class="rounded-circle mb-1" id="profilePicture" width="120">';
                        }
                        ?>
                    </label>

                    <input type="file" id="profilePictureUpload" name="profilePictureUpload" class="d-none"
                           onchange="changeProfilePicture()">

                    <br>
                    <span class="fw-bold"><?php echo $user->first_name . " " . $_SESSION["user"]->last_name; ?></span><?php echo($user->get_is_vip() ? ' <i class="text-warning fas fa-star"></i>' : '') ?>
                </div>
                <div class="col-4"></div>
            </div>
            <div class="row mb-3">

                <!-- Accordion -->
                <div class="accordion my-5">
                    <!-- Recently Visited products -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true"
                                    aria-controls="panelsStayOpen-collapseOne">
                                <h5>Recently visited articles:</h5>
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show"
                             aria-labelledby="panelsStayOpen-headingOne">
                            <div class="accordion-body">
                                <div class="row mb-5 mt-2 text-center">
                                    <?php
                                    if (isset($_COOKIE["visited_articles"])) {
                                        $visited = json_decode($_COOKIE["visited_articles"]);
                                        $visited_articles = [];
                                        foreach ($visited as $id) {
                                            array_push($visited_articles, new Article($id));
                                        }
                                        foreach ($visited_articles as $article) {
                                            ?>

                                            <div class="col-10 col-md-5 col-xl-3 m-4">
                                                <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/article_card.inc.php"; ?>
                                            </div>

                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- newest order -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false"
                                    aria-controls="panelsStayOpen-collapseThree">
                                <h5>Latest Customer Order:</h5>
                            </button>
                        </h2>
                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse show"
                             aria-labelledby="panelsStayOpen-headingThree">
                            <div class="accordion-body">
                                <div class="mb-5">
                                    <?php
                                    //Iniitalize Order Controller
                                    $order_controller = new order_controller();
                                    //Get Orders for Current User
                                    $order = $order_controller->get_newest_order_for_user($_SESSION["user"]);

                                    include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/order_overview_customer.inc.php";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>
</div>


<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
