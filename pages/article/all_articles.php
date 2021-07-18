<?php
include($_SERVER['DOCUMENT_ROOT'] . "/includes/php_head.inc.php");
$article_controller = new article_controller();
$search_controller = new search_controller();
$category_controller = new category_controller();

//get search term if existing
$searchterm = $_GET["searchterm"];
//get marker for guide redirect if existing
$fromGuide = filter_var($_POST['guide'], FILTER_VALIDATE_BOOLEAN);
$fromFilter = filter_var($_GET['filter'], FILTER_VALIDATE_BOOLEAN);
$guideSession = filter_var($_SESSION['guide'], FILTER_VALIDATE_BOOLEAN);

$cat = $_GET["cat"];
$sort = $_GET["sort"];
$empty_view = true;
if (isset($searchterm)) { //if searchterm exists get articles according to it
    $all_articles = $search_controller->search_by_contained_term($searchterm);
} else if ($fromGuide) //if guide redirect get articles according to post data
{
    $answers = $_POST["q"];
    if (!isset($answers) || count($answers) < 1) {
        $all_articles = $article_controller->get_all_articles();
    } else {
        $all_articles = $search_controller->search_by_answers($answers);
        $_SESSION["q"] = $answers;
        $_SESSION["guide"] = true;
    }
} else if ($fromFilter && $guideSession) {
    $answers = $_SESSION["q"];
    $all_articles = $search_controller->search_by_answers($answers);

} else {
    $all_articles = $article_controller->get_all_articles();
    unset($_SESSION["guide"]);
    unset($_SESSION["q"]);
}
?>
<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <?php
    $title = "Articles";
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/html_head.inc.php");
    ?>
    <link rel="stylesheet" href="/style/all_articles_styles.css"/>

    <script>
        window.addEventListener('resize', changeFilterMenuClass);

        /**
         *
         */
        function changeFilterMenuClass() {
            const w = window.innerWidth;
            if (w <= 992) {
                document.getElementById("displayContainer").classList.remove("row");
                document.getElementById("filterMenu").classList.add("filterMenuTop");
            } else {
                document.getElementById("displayContainer").classList.add("row");
            }
        }

        /**
         *
         * @param idOfObject
         */
        function showSelectedFilterTab(idOfObject) {
            if (document.getElementById(idOfObject).classList.contains("active")) {
                document.getElementById(idOfObject).classList.remove("active");
            } else {
                document.getElementById(idOfObject).classList.add("active");
            }
        }

        function resetFilter() {
            window.location = window.location.href.split("?")[0];
        }
    </script>

</head>
<body>
<!--Include Navbar-->
<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.inc.php"); ?>

<div class="container my-5 pt-5">

    <h1>All articles</h1>

    <!-- Filter Utilities -->
    <!-- Representation of the filter menu -->
    <form class="d-flex flex-wrap flex-row align-items-middle border-top border-bottom border-2 pt-3 pb-3" id="filter">
        <?php if (isset($searchterm)) { ?>
            <input type="hidden" name="searchterm" value="<?php echo $searchterm ?>">
        <?php } ?>
        <input type="hidden" name="filter" value="true">

        <!-- Category Dropdown -->
        <div class="py-2 px-1 dropdown">
            <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="priceFilter"
               data-bs-toggle="dropdown" aria-expanded="false">
                Categories
            </a>
            <ul class="dropdown-menu" aria-labelledby="priceFilter">
                <?php foreach ($category_controller->get_all_categories() as $category) { ?>
                    <li>
                        <div class="dropdown-item">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cat[]"
                                       id="categoryRadios<?php echo $category->get_id() ?>"
                                       value="<?php echo $category->get_id() ?>" <?php if (isset($cat)) if (in_array($category->get_id(), $cat)) echo "checked"; ?>>
                                <div class="p-0"><label class="form-check-label"
                                                        for="categoryRadios<?php echo $category->get_id() ?>">
                                        <?php echo $category->name ?>
                                    </label>
                                </div>

                            </div>
                        </div>
                    </li>
                <?php } ?>

            </ul>
        </div>

        <!-- Sorting Select -->
        <div class="py-2 px-1">
            <select class="form-select" id="sortMethod" name="sort">
                <option value="" disabled selected>Order by</option>
                <option value="1" <?php if (isset($sort)) if ($sort == 1) echo "selected"; ?>>Price: High to Low
                </option>
                <option value="2" <?php if (isset($sort)) if ($sort == 2) echo "selected"; ?>>Price: Low to High
                </option>
                <option value="3" <?php if (isset($sort)) if ($sort == 3) echo "selected"; ?>>Avg. Customer Review
                </option>
            </select>
        </div>


        <!-- Apply Filters/Sorting -->
        <div class="py-2 px-1">
            <button class="btn btn-primary" type="submit">Apply Filters!</button>
        </div>


        <!-- Reset Filters/Sorting -->
        <div class="py-2 px-1">
            <button class="btn btn-danger" type="button" onclick="resetFilter()">Reset Filters</button>
        </div>

    </form>

    <!-- Representation of all articles registered in the database -->
    <div class="d-flex flex-wrap mt-4 justify-content-between" id="cardContainer">
        <?php
        if (isset($sort)) { //test if a sorting method has been specified
            switch ($sort) { //select appropriate sorting method
                case 1:
                    function comparator($a, $b)
                    {
                        $aval = $a->get_current_price();
                        $bval = $b->get_current_price();
                        if ($aval == $bval) return 0;
                        return $aval < $bval ? 1 : -1;
                    }

                    usort($all_articles, "comparator");
                    break;
                case 2:
                    function comparator($a, $b)
                    {
                        $aval = $a->get_current_price();
                        $bval = $b->get_current_price();
                        if ($aval == $bval) return 0;
                        return $aval > $bval ? 1 : -1;
                    }

                    usort($all_articles, "comparator");
                    break;
                case 3:
                    function comparator($a, $b)
                    {
                        $article_controller = new article_controller();
                        $aval = $article_controller->get_average_rating($a);
                        $bval = $article_controller->get_average_rating($b);
                        if ($aval == $bval) return 0;
                        return $aval < $bval ? 1 : -1;
                    }

                    usort($all_articles, "comparator");
                    break;
            }
        }

        foreach ($all_articles as $article) {
            if (!$article->get_visible())
                continue;

            if (isset($cat)) {
                if (!$category_controller->is_in_categories($article, $cat)) continue;
            }
            if ($empty_view) $empty_view = false;

            ?>
            <div class="col-11 col-md-5 col-lg-3 mx-3">
                <?php include $_SERVER["DOCUMENT_ROOT"] . "/includes/display_helpers/article_card.inc.php"; ?>
            </div>
        <?php } ?>

    </div>
    <?php if ($empty_view) { ?>
        <div class
             ="col-lg"><h1 class="border-bottom border-2 pt-3 pb-3 "> Oopsie Daisy...<br> It looks like there are no
                Articles matching your search criteria...</h1></div>

    <?php } ?>

    <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/footer.inc.php"); ?>
</div>

</body>
</html>
