<!-- The menu displayed in the profile for users -->
<div class="col-md-3">
    <div class="card shadow-none mb-5">
        <div class="card-header">Menu</div>
        <ul class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action" href="./index" id="index_Link">Overview</a>
            <a class="list-group-item list-group-item-action" href="./orders" id="orders_Link">My orders</a>
            <a class="list-group-item list-group-item-action" href="./personal_data" id="personal_data_Link">Personal
                data</a>
            <!-- Seller dashboard or become a seller -->
            <?php
            $usr = $_SESSION["user"];
            if ($usr->organization != null) { ?>
                <a class="list-group-item list-group-item-action" href="/pages/seller_dashboard/index">Seller
                    Dashboard</a>
            <?php } else { ?>
                <a class="list-group-item list-group-item-action" href="./new_organization" id="new_organization_Link">Become
                    a seller!</a>
            <?php }
            ?>

            <a class="list-group-item list-group-item-action bg-danger text-white" onclick="logout()"
               style="cursor: pointer">Logout</a>
        </ul>
    </div>
</div>

<script>
    //Mark current page as active in navigation
    let link = window.location.href.split("/");
    link = link[link.length - 1].split(".");
    link = link[0].split("#");
    document.getElementById(link[0] + "_Link").classList.add("active");

    /**
     * Log out the user
     */
    function logout() {
        const xmlhttp = new XMLHttpRequest();

        xmlhttp.open("POST", "/controller/user_controller.php", true);
        xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        const parameters = "logout=true";

        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                const result = JSON.parse(this.responseText);
                if (result["status"]) {
                    document.cookie = "visited_articles=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    window.location = "/index";
                }
            }
        };

        xmlhttp.send(parameters);


    }
</script>