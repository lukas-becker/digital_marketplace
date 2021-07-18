let editor;
let highlightInput;
setTimeout(function () {
    editor = new wysihtml.Editor("description", { // id of textarea element
        toolbar: "wysihtml-toolbar", // id of toolbar element
        parserRules: wysihtmlParserRules // defined in parser rules set
    });

    highlightInput = document.getElementById("newHighlight");

    // Execute a function when the user releases a key on the keyboard
    highlightInput.addEventListener("keydown", function (event) {
        // Number 13 is the "Enter" key on the keyboard
        if (event.keyCode === 13) {
            // Cancel the default action, if needed
            event.preventDefault();
            // Trigger the button element with a click
            document.getElementById("newHighlightButton").click();
        }
    });
}, 100)

function addHighlight() {
    let highlightGroup = document.getElementById("highlightGroup")
    let highlight = highlightInput.value;

    if (highlight == "" || highlight == null) {
        return;
    }

    let newHighlight = document.createElement("LI");
    newHighlight.classList.add("list-group-item");
    newHighlight.innerHTML = '<div class="input-group"><input type="text" readonly class="form-control form-control-plaintext bg-white" name="highlights[]"><span class="input-group-text btn btn-danger" onclick="deleteHighlight(this)"><i class="fas fa-trash"></i></span></div>';
    newHighlight.firstChild.firstChild.value = highlight;
    highlightGroup.appendChild(newHighlight);


    highlightInput.focus();
    highlightInput.value = null;
}

function deleteHighlight(caller) {
    caller.parentElement.parentElement.remove();
}

let imgCount = 1;

function addPicture() {
    const xmlhttp = new XMLHttpRequest();

    xmlhttp.open("POST", "/php_backend/image_pipeline.php?square_pipeline=true", true);
    //xmlhttp.setRequestHeader("Content-type", "multipart/form-data");

    const fileInput = document.getElementById("addImgInput");
    const file = fileInput.files[0];

    const formData = new FormData();
    formData.append("upload", file, file.name);

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const result = JSON.parse(this.responseText);
            if (result["status"] == true) {

                let imgBox = document.getElementById("imgBox");
                let div;
                let input;


                div = imgBox.children[0].cloneNode(true);
                input = imgBox.children[1].cloneNode(true);


                div.id = "drop_" + imgCount;
                div.classList.remove("d-none");
                div.children[0].id = "img_" + imgCount;
                div.children[0].src = "data:image;base64," + result["image"];
                input.id = "txt_" + imgCount;
                input.value = result["image"];

                imgBox.appendChild(div);
                imgBox.appendChild(input);

                imgCount++;
                fileInput.value = null;

            } else if (result["status"] == false) {

            } else {

            }
        }
    };

    xmlhttp.send(formData);
}

function deletePicture(caller) {
    caller.parentElement.remove();
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("id", ev.target.id);
    ev.dataTransfer.setData("parent", document.getElementById(ev.target.id).parentElement.id);
}

function drop(ev) {
    let temp;
    ev.preventDefault();
    let imgId = ev.dataTransfer.getData("id");
    let srcParent = ev.dataTransfer.getData("parent");

    if (ev.target.id.startsWith("img_")) {
        temp = document.getElementById(ev.target.id);
        temp.parentElement.insertBefore(document.getElementById(imgId), temp.parentElement.firstChild);
    } else {
        temp = document.getElementById(ev.target.id).firstChild;
        ev.target.insertBefore(document.getElementById(imgId), ev.target.firstChild);
    }
    document.getElementById(srcParent).insertBefore(temp, document.getElementById(srcParent).firstChild);

    updateImgInput(imgId.split("_")[1]);
    updateImgInput(ev.target.id.split("_")[1]);
}

function updateImgInput(id) {
    let img = document.getElementById("drop_" + id).children[0];
    let input = document.getElementById("txt_" + id);

    if (img.src.startsWith("data:image;base64,")) {
        input.value = img.src.substring(18);
    } else {
        input.value = img.src;
    }
}