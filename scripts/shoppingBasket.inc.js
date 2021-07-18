function deleteFromShoppingBasket(article_id) {
    const xmlhttp = new XMLHttpRequest();

    let parameter = "delete_from_shopping_basket=true";
    parameter += "&article_id=" + article_id;

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
    xmlhttp.send(parameter);
}

function clearShoppingBasket() {
    const xmlhttp = new XMLHttpRequest();

    let parameter = "clear_shopping_basket=true";

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
    xmlhttp.send(parameter);
}

function shoppingCartAfterBuy() {
    const xmlhttp = new XMLHttpRequest();

    let parameter = "clear_shopping_basket=true";

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
        }
    };
    xmlhttp.send(parameter);
}

function updateAmountOfArticle(article_id) {
    const xmlhttp = new XMLHttpRequest();
    let new_amount = document.getElementById("amount_" + article_id).value;
    let max_amount = document.getElementById("availableStock_" + article_id).value;

    if (parseInt(new_amount) > parseInt(max_amount)) {
        new_amount = max_amount;
        document.getElementById("amount_" + article_id).value = max_amount;
    }

    let parameter = "update_amount_article=true";
    parameter += "&article_id=" + article_id;
    parameter += "&article_amount=" + new_amount;

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const result = JSON.parse(this.responseText);
            if (result["status"] == true) {
                updateSum();
            } else if (result["status"] == false) {

            } else {

            }

        }
    };
    xmlhttp.send(parameter);
}

function updateSum() {
    const xmlhttp = new XMLHttpRequest();
    let parameter = "recalculate_shopping_basket=true";

    xmlhttp.open("POST", "/controller/user_controller.php", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            var result = JSON.parse(this.responseText);
            document.getElementById("sumOfBasket").innerHTML = result["result"];
        }
    };
    xmlhttp.send(parameter);
}