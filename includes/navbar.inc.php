<!-- Nav bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="navigation">
    <div class="container-fluid">
        <!-- Logo area -->
        <a class="navbar-brand text-warning" href="/index">E-Marketplace<!--<img src="./logo.png">--></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Dropdown area for mobile -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Search -->
                <li class="nav-item">
                    <a class="nav-link" onclick="toggleSearch()" style="cursor: pointer"><i
                                class="fas fa-search"></i><span style="opacity: 0">.</span></a>
                </li>
                <li class="nav-item pt-1">
                    <form action="/pages/article/all_articles.php" method="get" class="mb-0">
                        <input class="border border-1 rounded me-1 py-0 d-none" id="searchterm" name="searchterm">
                        <button class="btn btn-outline-success btn-sm d-none" id="searchButton">Search</button>
                    </form>
                </li>
                <!-- Search end -->
                <li class="nav-item">
                    <a class="nav-link" href="/index">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/article/all_articles">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/categories">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/guide">Guide</a>
                </li>

            </ul>
        </div>

        <!-- Shopping Basket -->
        <?php

        if (isset($_SESSION["login"])) {

            echo "<a class='nav-link position-relative' href='/pages/shopping_basket.php'><i class='fas fa-shopping-cart'></i>";
            $us_controller = new user_controller();
            $articles_in_basket = $us_controller->get_number_of_articles_in_basket()[1];
            echo "<span class='position-absolute top-1 start-1 translate-middle badge rounded-pill bg-secondary' id='number_articles_in_basket'> $articles_in_basket         
                 <span class='visually-hidden'>unread messages</span></span></a>";
            echo "<div class='d-flex me-2'><a href='/pages/profile/index' class='text-decoration-none text-white'>Hallo " . $_SESSION["user"]->first_name . "!</a></div>";
        } else {
            echo '<div class="d-flex me-2"><a href="/pages/login.php" class="btn btn-outline-success">Login</a></div>';
        }

        ?>
    </div>
</nav>
<script>
    function toggleSearch() {
        var input = document.getElementById("searchterm");
        var button = document.getElementById("searchButton");
        if (input.classList.contains("d-none")) {
            input.classList.remove("d-none");
            button.classList.remove("d-none");
        } else {
            input.classList.add("d-none");
            button.classList.add("d-none");
        }
    }


    setTimeout(activateDropdowns, 100);

    let dropdownElementList;
    let dropdownList;

    function activateDropdowns() {
        dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl)
        })
    }

</script>