<?php
include $_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php";


//Redirect already signed in User
if (isset($_SESSION["login"])) {
    header("Location: /pages/profile/index");
    die();
}
?>
<!doctype html>
<html lang="en">
<head>
    <?php
    $title = "Login";
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php";
    ?>

    <script>
        function loginButtonPress() {

            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/controller/user_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            const mail = document.getElementById("mail").value;
            const password = document.getElementById("password").value;
            const fingerprint = document.getElementById("fingerprint").value;
            const parameters = "login=login&mail=" + mail + "&password=" + password + "&fingerprint=" + fingerprint;
            const loggedInNameField = document.getElementById("loggedInName");
            const loggedInPicture = document.getElementById("loggedInPicture");

            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    if (result["status"] == true) {
                        loggedInNameField.innerText = result["first_name"];
                        if (result["picture"] != null) {
                            loggedInPicture.src = "data:image;base64," + result["picture"];
                        }
                        animateCallouts(document.getElementById("calloutOne"), document.getElementById("calloutTwo"));
                    } else if (result["status"] == false) {
                        document.getElementById("errorMessage").classList.remove("d-none");
                    } else {

                    }
                }
            };

            xmlhttp.send(parameters);
        }

        function showRegisterForm() {
            animateCallouts(document.getElementById("calloutOne"), document.getElementById("calloutRegisterOne"))
        }

        function showLoginForm() {
            animateCallouts(document.getElementById("calloutRegisterOne"), document.getElementById("calloutOne"))
        }

        function registerButtonPress() {
            const firstName = document.getElementById("fName").value;
            const lastName = document.getElementById("lName").value;
            const mail = document.getElementById("registerMail").value;
            const street = document.getElementById("street").value;
            const nr = document.getElementById("nr").value;
            const zip = document.getElementById("zip").value;
            const city = document.getElementById("city").value;
            const password = document.getElementById("registerPassword").value;

            let parameters = "register=register";
            parameters += "&firstName=" + firstName;
            parameters += "&lastName=" + lastName;
            parameters += "&mail=" + mail;
            parameters += "&street=" + street;
            parameters += "&nr=" + nr;
            parameters += "&zip=" + zip;
            parameters += "&city=" + city;
            parameters += "&password=" + password;


            const xmlhttp = new XMLHttpRequest();

            xmlhttp.open("POST", "/controller/user_controller.php", true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');


            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const result = JSON.parse(this.responseText);
                    if (result["status"] == true) {
                        document.getElementById("signupSuccess").style.display = "block";
                    } else if (result["status"] == false) {
                        document.getElementById("signupFailure").style.display = "block";
                    } else {

                    }
                    animateCallouts(document.getElementById("calloutRegisterOne"), document.getElementById("calloutRegisterTwo"));
                }
            };

            xmlhttp.send(parameters);


        }

        function animateCallouts(calloutOne, calloutTwo) {
            let animation = 0;
            clearInterval(animation);
            let posValue = 1000;
            let opacityValue = 1;
            animation = setInterval(moveCalloutOne, 10);
            calloutOne.style.position = "relative";
            calloutTwo.style.position = "relative";

            function moveCalloutOne() {
                if (posValue <= 950) {
                    calloutOne.style.display = "none";
                    calloutOne.style.position = "static";
                    calloutOne.style.removeProperty("left");
                    calloutOne.style.removeProperty("right");
                    calloutTwo.style.display = "flex";
                    //clearInterval(animation);
                    posValue = 50;
                    opacityValue = 0;
                    clearInterval(animation);
                    animation = setInterval(moveCalloutTwo, 10);
                } else {
                    posValue = posValue - 2;
                    opacityValue = opacityValue - 0.05;
                    calloutOne.style.right = (1000 - posValue) + "px";
                    calloutOne.style.opacity = opacityValue;
                }
                if (posValue > 0) {

                } else {
                    clearInterval(animation);

                    calloutTwo.style.position = "static;"
                }
            }

            function moveCalloutTwo() {
                if (posValue <= 0) {
                    calloutTwo.style.position = "static";
                    calloutTwo.style.opacity = "1";
                    calloutTwo.style.removeProperty("left");
                    calloutTwo.style.removeProperty("right");
                    clearInterval(animation);
                } else {
                    posValue = posValue - 2;
                    opacityValue = opacityValue + 0.05;
                    calloutTwo.style.left = posValue + "px";
                    calloutTwo.style.opacity = opacityValue;
                }
            }
        }

        function initFingerprintJS() {
            // Initialize an agent at application startup.
            const fpPromise = FingerprintJS.load();

            // Get the visitor identifier when you need it.
            fpPromise
                .then(fp => fp.get())
                .then(result => {
                    // This is the visitor identifier:
                    const visitorId = result.visitorId;
                    document.getElementById("fingerprint").value = visitorId;
                });
        }

        setTimeout(checkEnter, 100);

        function checkEnter() {
            document.getElementById("password").addEventListener("keyup", event => {
                if (event.key !== "Enter") return; // Use `.key` instead.
                loginButtonPress(); // Things you want to do.
                event.preventDefault(); // No need to `return false;`.
            });
        }
    </script>

</head>
<body style="overflow: hidden">
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">
    <!-- Login Form Callout -->
    <div class="row" id="calloutOne">
        <div class="col-1 col-md-2"></div>
        <div class="col-10 col-md-8 callout callout-success">
            <h2 class="text-center mb-3">Welcome back!</h2>
            <div class="row mb-5">
                <div class="col-1 col-md-3"></div>
                <div class="col-10 col-md-6">
                    <div id="loginForm">
                        <div class="row align-items-center mb-2">
                            <div class="col-5">
                                <label for="mail">E-Mail Address:</label>
                            </div>
                            <div class="col-7">
                                <input type="text" id="mail" name="mail" placeholder="E-Mail" class="form-control">
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-5">
                                <label for="password">Password:</label>
                            </div>
                            <div class="col-7">
                                <input type="password" id="password" name="password" placeholder="Password"
                                       class="form-control">
                            </div>
                        </div>
                        <input type="hidden" id="fingerprint" name="fingerprint">
                        <div class="row text-center mb-2 d-none" id="errorMessage">
                            <span class="text-danger">Username / Password incorrect</span>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"></div>
                            <div class="col-4 text-center">
                                <button class="btn btn-success" onclick="loginButtonPress()">Login!</button>
                                <br>
                            </div>
                            <div class="col-4"></div>
                        </div>
                        <div class="row text-center">
                            <a class="link-secondary" href="#" onclick="showRegisterForm()">I'm new here</a>
                        </div>

                    </div>
                </div>
                <div class="col-auto"></div>

            </div>
            <div class="position-relative m-4">
                <div class="progress" style="height: 1px;">
                    <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button type="button"
                        class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill"
                        style="width: 2rem; height:2rem;">1
                </button>
                <button type="button"
                        class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-secondary rounded-pill"
                        style="width: 2rem; height:2rem;">2
                </button>
            </div>

        </div>
        <div class="col-1 col-md-2"></div>
    </div>
    <!-- End of login Callout -->

    <!-- Greeting -->
    <div class="row" id="calloutTwo" style="opacity: 0; display:none">
        <div class="col-1 col-md-2"></div>
        <div class="col-10 col-md-8 callout callout-success">
            <h2 class="text-center mb-3">Welcome Back!</h2>
            <div class="row mb-2">
                <div class="col-4"></div>
                <div class="col-4 text-center">
                    <img id="loggedInPicture" src="https://via.placeholder.com/100" height="100" width="100"
                         class="rounded-circle">
                </div>
                <div class="col-4"></div>
            </div>

            <div class="row mb-5">
                <div class="col-4"></div>
                <div class="col-4 text-center">
                    <span class="fw-bold" id="loggedInName"></span><br><a href="/pages/profile/index">Profile</a>
                </div>
                <div class="col-4"></div>
            </div>
            <div class="position-relative m-4">
                <div class="progress" style="height: 1px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button type="button"
                        class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill"
                        style="width: 2rem; height:2rem;">1
                </button>
                <button type="button"
                        class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-primary rounded-pill"
                        style="width: 2rem; height:2rem;">2
                </button>
            </div>

        </div>
        <div class="col-2"></div>
    </div>
    <!-- End of greeting callout -->

    <!-- Register Form Callout -->
    <div class="row" id="calloutRegisterOne" style="opacity: 0; display: none">
        <div class="col-1 col-md-2"></div>
        <div class="col-10 col-md-8 callout callout-success">
            <h2 class="text-center mb-4">It's nice meeting you!</h2>
            <div class="row mb-5">
                <div class="col-1 col-md-3"></div>
                <div class="col-10 col-md-6">
                    <div id="registerForm">
                        <!-- first name -->
                        <div class="row align-items-center mb-2">
                            <div class="col-4">
                                <label for="fName">First name:</label>
                            </div>
                            <div class="col-8">
                                <input type="text" id="fName" name="fName" placeholder="First name"
                                       class="form-control">
                            </div>
                        </div>
                        <!-- last name -->
                        <div class="row align-items-center mb-2">
                            <div class="col-4">
                                <label for="lName">Last name:</label>
                            </div>
                            <div class="col-8">
                                <input type="text" id="lName" name="lName" placeholder="Last Name" class="form-control">
                            </div>
                        </div>
                        <!-- email -->
                        <div class="row align-items-center mb-2">
                            <div class="col-4">
                                <label for="registerMail">E-Mail Address:</label>
                            </div>
                            <div class="col-8">
                                <input type="text" id="registerMail" name="registerMail" placeholder="E-Mail"
                                       class="form-control">
                            </div>
                        </div>
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
                                        <input type="text" id="nr" name="nr" class="form-control" placeholder="Num">
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
                        <!-- password -->
                        <div class="row align-items-center mb-3">
                            <div class="col-4">
                                <label for="registerPassword">Password:</label>
                            </div>
                            <div class="col-8">
                                <input type="password" id="registerPassword" name="registerPassword"
                                       placeholder="Password" class="form-control">
                            </div>
                        </div>
                        <input type="hidden" id="fingerprint" name="fingerprint">
                        <div class="row text-center mb-2 d-none" id="errorMessage">
                            <span class="text-danger">Username / Password incorrect</span>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4"></div>
                            <div class="col-4 text-center">
                                <button class="btn btn-success" onclick="registerButtonPress()">Register!</button>
                                <br>
                            </div>
                            <div class="col-4"></div>
                            <br><br>
                            <div class="row text-center">
                                <a class="link-secondary" href="#" onclick="showLoginForm()">I've got an account</a>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-auto"></div>

            </div>
            <div class="position-relative m-4">
                <div class="progress" style="height: 1px;">
                    <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button type="button"
                        class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill"
                        style="width: 2rem; height:2rem;">1
                </button>
                <button type="button"
                        class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-secondary rounded-pill"
                        style="width: 2rem; height:2rem;">2
                </button>
            </div>

        </div>
        <div class="col-2"></div>
    </div>
    <!-- End of register Callout -->

    <!-- Check Mail notice -->
    <div class="row" id="calloutRegisterTwo" style="opacity: 0; display:none">
        <div class="col-2"></div>
        <div class="col-8 callout callout-success">
            <h2 class="text-center mb-3">It's nice meeting you!</h2>

            <div class="row mb-5">
                <div class="col-1 col-md-2"></div>
                <div class="col-10 col-md-8 text-center" id="signupSuccess" style="display: none">
                    Only one step until you can use your account. <br>
                    Check your inbox and confirm your E-Mail.
                    <span class="fw-bold" id="loggedInName"></span><br><a href="/index.php">Home</a>
                </div>
                <div class="col-10 col-md-8 text-center" id="signupFailure" style="display: none;">
                    Something went wrong. If you've already got an account log in please. <br>
                    If not try again later.
                    <span class="fw-bold" id="loggedInName"></span><br><a href="/index.php">Home</a>
                </div>
                <div class="col-1 col-md-2"></div>
            </div>
            <div class="position-relative m-4">
                <div class="progress" style="height: 1px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <button type="button"
                        class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill"
                        style="width: 2rem; height:2rem;">1
                </button>
                <button type="button"
                        class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-primary rounded-pill"
                        style="width: 2rem; height:2rem;">2
                </button>
            </div>

        </div>
        <div class="col-2"></div>
    </div>
    <!-- End Check Mail notice -->
</div>


<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>


<!-- Bootstrap JS-Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf"
        crossorigin="anonymous"></script>
<!-- FingerprintJS -->
<script
        async
        src="//cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"
        onload="initFingerprintJS()"
></script>
</body>
</html>
