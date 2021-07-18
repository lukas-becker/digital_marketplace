/**
 * Handle a click on an address and make it primary.
 * @param id Address id.
 */
function clickAddress(id) {
    const element = document.getElementById("address_" + id);
    if (element != null) {
        element.classList.remove("bg-white");
        element.classList.add("bg-success");
    }

    document.getElementById("addr_" + id).onmouseleave = "null";

    const xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let parameters = "make_primary_address=" + id;

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

/**
 * Handle hover and change visual.
 * @param id Address id.
 */
function hoverAddress(id) {
    const element = document.getElementById("address_" + id);
    if (element != null) {
        element.classList.remove("bg-white");
        element.classList.add("bg-success");
    }
}

/**
 * Handle hover leave and change visual.
 * @param id Address id.
 */
function leaveAddress(id) {
    const element = document.getElementById("address_" + id);
    if (element != null) {
        element.classList.add("bg-white");
        element.classList.remove("bg-success");
    }
}

/**
 * Add a new address.
 */
function addAddress() {
    const street = document.getElementById("street").value;
    const number = document.getElementById("number").value;
    const zip = document.getElementById("zip").value;
    const city = document.getElementById("city").value;

    let parameters = "add_address=true";
    parameters += "&street=" + street;
    parameters += "&number=" + number;
    parameters += "&zip=" + zip;
    parameters += "&city=" + city;

    const xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');


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

/**
 * Delete an existing address.
 * @param caller Object.
 * @param id Address.
 */
function deleteAddress(caller, id) {
    caller.parentElement.parentElement.remove();
    document.getElementById("addr_" + id).remove();

    let parameters = "delete_address=true";
    parameters += "&id=" + id;

    let xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');


    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            var result = JSON.parse(this.responseText);
            if (result["status"] == true) {
            } else if (result["status"] == false) {

            } else {

            }

        }
    };

    xmlhttp.send(parameters);
}

/**
 * Save a new public name for the user.
 */
function savePublicName() {
    const publicName = document.getElementById("publicName");
    const xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    const parameters = "set_public_name=true&name=" + publicName.value;

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const result = JSON.parse(this.responseText);
            if (result["status"] == true) {
                publicName.classList.add("border-success");
                document.getElementById("publicNameButton").disabled = true;
            } else if (result["status"] == false) {
                publicName.classList.add("border-danger");
            } else {

            }
        }
    };

    xmlhttp.send(parameters);
}

/**
 * Save the users mail address.
 */
function saveMail() {
    const mail = document.getElementById("mail");
    const xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    const parameters = "set_mail=true&mail=" + mail.value;

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const result = JSON.parse(this.responseText);
            if (result["status"] == true) {
                publicName.classList.add("border-success");
                document.getElementById("mailButton").disabled = true;
            } else if (result["status"] == false) {
                publicName.classList.add("border-danger");
            } else {

            }
        }
    };

    xmlhttp.send(parameters);
}

/**
 * Save a new password for an user.
 */
function savePassword() {
    let oldPasswordInput = document.getElementById("oldPassword");
    let newPasswordInput = document.getElementById("newPassword");
    let repeatNewPasswordInput = document.getElementById("repeatNewPassword");

    let passwordMatchErrorRow = document.getElementById("passwordMatchError");
    let oldPasswordError = document.getElementById("oldPasswordError");
    let passwordChangeSuccess = document.getElementById("passwordChangeSuccess");

    passwordMatchErrorRow.classList.add("d-none");
    oldPasswordError.classList.add("d-none");
    passwordChangeSuccess.classList.add("d-none");


    if (newPasswordInput.value != repeatNewPasswordInput.value) {
        passwordMatchErrorRow.classList.remove("d-none");
        return;
    }

    let xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    let parameters = "set_password=true&password=" + newPasswordInput.value;

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            let result = JSON.parse(this.responseText);
            if (result["status"] == true) {
                passwordChangeSuccess.classList.remove("d-none");
            } else if (result["status"] == false) {
                oldPasswordError.classList.remove("d-none");
            } else {

            }
        }
    };

    xmlhttp.send(parameters);

}

/**
 * Save a new profile picture
 */
function changeProfilePicture() {

    const xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "/controller/user_controller.php?set_profile_picture=true", true);
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