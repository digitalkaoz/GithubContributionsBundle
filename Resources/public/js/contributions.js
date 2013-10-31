var contributions = function (query) {
    var elem = $(query);

    var cal = new CalHeatMap();
    cal.init({
        start: new Date(new Date().setMonth(new Date().getMonth() - 12)),
        maxDate: new Date(),
        minDate: new Date($(elem).data('min') * 1000),
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
        data: window[$(elem).data('data')],
        previousSelector: $(elem).data('prev'),
        nextSelector: $(elem).data('prev')
    });
}

$(document).ready(function () {
    contributions('#cal-heatmap');
});