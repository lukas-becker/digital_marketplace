<!-- The menu displayed in the seller dashboard -->
<div class="col-md-3">
    <div class="card shadow-none mb-5">
        <div class="card-header">Menu</div>
        <ul class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action" href="./index" id="index_Link">Overview</a>
            <a class="list-group-item list-group-item-action" href="./articles" id="articles_Link">All my articles</a>
            <a class="list-group-item list-group-item-action" href="./orders" id="orders_Link">All orders</a>
            <a class="list-group-item list-group-item-action" href="./reviews" id="reviews_Link">Recent reviews</a>
            <a class="list-group-item list-group-item-action" href="./settings" id="settings_Link">Organization
                Settings</a>
            <a class="list-group-item list-group-item-action bg-success text-white" href="/pages/article/new"
               id="new_Link">Create Article</a>
            <?php
            if ($_SESSION["user"]->is_site_admin()) {
                ?>
                <a class="list-group-item list-group-item-action bg-success text-white"
                   href="/pages/article/new_auction"
                   id="new_Link">Create Auction</a>
            <?php } ?>
        </ul>
    </div>
</div>

<script>
    //Mark current page active
    let link = window.location.href.split("/");
    link = link[link.length - 1].split(".");
    link = link[0].split("#");
    document.getElementById(link[0] + "_Link").classList.add("active");
</script>