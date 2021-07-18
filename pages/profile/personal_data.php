<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";
$user = $_SESSION["user"];

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

    <script src="/scripts/profile.inc.js"></script>

    <script>

        //Add a 0.1 Second delay to fully load the bootstrap js. Tooltip does not work otherwise
        setTimeout(function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }, 100)


    </script>
</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <h1>Accountinformation for <?php echo $user->first_name; ?></h1>
    <div class="row my-4">
        <!-- Menu -->
        <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/profile_menu.inc.php"; ?>
        <!-- Spacing -->
        <div class="col-md-1"></div>
        <!-- Content -->
        <div class="col-md-8">
            <!-- Menu -->
            <div class="row">
                <div class="col-4"></div>
                <div class="col-4">
                    <div class="text-center">
                        <label for="profilePictureUpload">
                            <?php
                            $user = $_SESSION["user"];
                            if ($user->image == null) {
                                ?>
                                <img src="https://via.placeholder.com/120" class="rounded-circle mb-1"
                                     id="profilePicture">
                                <?php
                            } else {
                                echo '<img src="data:image;base64,' . $user->image . '" class="rounded-circle mb-1" id="profilePicture" width="120">';
                            }
                            ?>
                        </label>
                        <input type="file" id="profilePictureUpload" name="profilePictureUpload" class="d-none"
                               onchange="changeProfilePicture()">


                        <br>
                        <span class="fw-bold"><?php echo $user->first_name . " " . $user->last_name; ?></span><br>
                    </div>
                </div>
                <div class="col-4"></div>
            </div>
            <hr>
            <!-- Addresses -->
            <div class="d-flex flex-row justify-content-between">
                <h3>Addresses: </h3>
                <span data-bs-toggle="modal" data-bs-target="#editAddressModal" style="cursor: pointer;"><i
                            class="fas fa-pen"></i></span>
            </div>
            <div class="row mb-5">
                <?php
                $addresses = $user->get_all_addresses();
                foreach ($addresses as $add) {
                    echo '<div class="col-2 d-sm-none"></div>';
                    echo '<div class="bg-white col-8 col-sm-4 col-lg-3 position-relative py-2 shadow-sm rounded me-3 mb-2 ms-2" style="cursor: pointer;" id="addr_' . $add->get_id() . '" onclick="clickAddress(' . $add->get_id() . ')" onmouseover="hoverAddress(' . $add->get_id() . ')" onmouseleave="leaveAddress(' . $add->get_id() . ')">';
                    echo $add->street . " " . $add->number . "<br>";
                    echo $add->zip . " " . $add->city . "<br>";
                    if ($add->get_primary()) {
                        echo '<span class="badge rounded-pill bg-success position-absolute top-0 end-0 p-2 m-2"><span class="visually-hidden">Primary</span></span>';
                    } else {
                        echo '<span class="badge rounded-pill bg-white border position-absolute top-0 end-0 p-2 m-2" id="address_' . $add->get_id() . '"><span class="visually-hidden">Primary</span></span>';
                    }

                    echo '</div>';
                }

                ?>
                <div class="col-2 d-sm-none"></div>
                <div class="bg-white col-8 col-sm-2 col-lg-1 position-relative py-2 shadow-sm rounded ms-2 me-3 border border-success fs-2 text-center mb-2"
                     style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#newAddressModal">
                    +
                </div>
            </div>
            <hr>
            <!-- Public profile -->
            <h3>Public profile information:</h3>
            <div class="row mb-5">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label for="new" class="col-form-label">Display name: <span class="fas fa-info-circle"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="Used for Ratings etc."></span></label>
                    </div>
                    <div class="col-auto">
                        <input type="text" class="form-control" name="publicName" id="publicName"
                               placeholder="<?php echo $user->public_name ?>"
                               oninput="document.getElementById('publicNameButton').disabled = false">
                    </div>
                    <button class="col-auto btn btn-success" onclick="savePublicName()" id="publicNameButton" disabled>
                        Save
                    </button>
                </div>
            </div>
            <hr>
            <!-- E-Mail -->
            <h3>E-Mail-Address:</h3>
            <div class="row mb-5">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label for="new" class="col-form-label">E-Mail:</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" class="form-control" id="mail" name="mail"
                               placeholder="<?php echo $user->e_mail ?>"
                               oninput="document.getElementById('mailButton').disabled = false">
                    </div>
                    <button class="col-auto btn btn-success" id="mailButton" disabled onclick="saveMail()">Save</button>
                </div>
            </div>
            <hr>
            <!-- Password -->
            <h3>Change password:</h3>
            <div class="mb-5">
                <div class="row g-3 align-items-center mb-2">
                    <div class="col-md-3">
                        <label for="old" class="col-form-label">Current password:</label>
                    </div>
                    <div class="col-auto">
                        <input type="password" class="form-control" name="old" id="oldPassword">
                    </div>
                </div>
                <div class="row g-3 align-items-center mb-2">
                    <div class="col-md-3">
                        <label for="new" class="col-form-label">New password:</label>
                    </div>
                    <div class="col-auto">
                        <input type="password" class="form-control" name="new" id="newPassword">
                    </div>
                    <div class="col-auto">
                        <input type="password" class="form-control" name="repeat" id="repeatNewPassword">
                    </div>
                </div>
                <div class="row mb-1 d-none" id="passwordMatchError">
                    <span class="text-danger">New Passwords must match!</span>
                </div>
                <div class="row mb-1 d-none" id="oldPasswordError">
                    <span class="text-danger">Current Password incorrect!</span>
                </div>
                <div class="row mb-1 d-none" id="passwordChangeSuccess">
                    <span class="text-success">Success!</span>
                </div>
                <button class="btn btn-success" onclick="savePassword()" id="savePasswordButton">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="newAddressModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="registerForm">
                    <!-- street & number -->
                    <div class="row align-items-center mb-2">
                        <div class="col-4">
                            <label for="address">Street & Number:</label>
                        </div>
                        <div class="col-8">
                            <div class="row">
                                <div class="col-7">
                                    <input type="text" id="street" name="street" class="form-control"
                                           placeholder="Street">
                                </div>
                                <div class="col-5">
                                    <input type="text" id="number" name="number" class="form-control" placeholder="Num">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- zip & city -->
                    <div class="row align-items-center mb-2">
                        <div class="col-4">
                            <label for="address">Zip & City.:</label>
                        </div>
                        <div class="col-8">
                            <div class="row">
                                <div class="col-4">
                                    <input type="text" id="zip" name="zip" class="form-control" placeholder="Zip">
                                </div>
                                <div class="col-8">
                                    <input type="text" id="city" name="city" class="form-control"
                                           placeholder="City">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="addAddress()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" id="editAddressModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Addresses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php
                    foreach ($addresses as $add) {
                        echo '<li class="list-group-item">';
                        echo '<div class="input-group"><input type="text" readonly class="form-control form-control-plaintext bg-white" value="' . $add->street . " " . $add->number . '">';
                        if ($add->get_primary())
                            echo '<span class="input-group-text btn btn-danger" onclick="deleteAddress(this, ' . $add->get_id() . ')" ><i class="fas fa-trash"></i></span>';
                        echo '</div>';
                        echo '</li>';
                    }

                    ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>

</body>
</html>
