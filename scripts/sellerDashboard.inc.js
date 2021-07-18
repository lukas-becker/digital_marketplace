window.onload = function () {
    //KPI Animation
    let numOrdersAmount = document.getElementById("numOrdersAmount").innerText;
    const numOrders = new CountUp('numOrders', numOrdersAmount);
    numOrders.start();

    let soldArticlesAmount = document.getElementById("soldArticlesAmount").innerText;
    const soldArticles = new CountUp('soldArticles', soldArticlesAmount);
    soldArticles.start();

    const options = {
        decimalPlaces: 2,
        separator: '.',
        decimal: ',',
        suffix: '€',
    };
    let revenueGeneratedAmount = document.getElementById("revenueGeneratedAmount").innerText;
    const revenueGenerated = new CountUp('revenueGenerated', revenueGeneratedAmount, options);
    revenueGenerated.start();
}