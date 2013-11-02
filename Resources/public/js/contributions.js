var contributions = function (query) {
    var elem = document.getElementById(query);

    if (!elem) {
        return;
    }
    var min = elem.getAttribute('data-min');
    var max = elem.getAttribute('data-max');
    var next = elem.getAttribute('data-next');
    var prev = elem.getAttribute('data-prev');
    var dataElement = window[elem.getAttribute('data-data')];

    var cal = new CalHeatMap();
    cal.init({
        start: new Date(min * 1000),
        maxDate: new Date(),
        minDate: new Date(min * 1000),
        range: 13,
        domainLabelFormat: "%m-%Y",
        domainMargin: [10, 0, 10, 0],
        legend: [1, 2, 3, 5],
        legendColors: {
            min: "#efefef",
            max: "steelblue",
            base: "#efefef",
            empty: "#efefef"
        },
        legendVerticalPosition: "bottom",
        legendHorizontalPosition: "right",
        legendOrientation: "horizontal",
        domain: "month",
        subDomain: "day",
        data: dataElement,
        previousSelector: prev,
        nextSelector: next
    });
}

contributions('cal-heatmap');
