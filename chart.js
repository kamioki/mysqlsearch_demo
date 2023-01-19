
//count months in JSON data
const rowmonthcount = jsonlist.reduce((result, current) => {
    const element = result.find((p) => p.month === current.month);
    if (element) {
        element.count++; // count
    }
    else {
        result.push({
            month: current.month,
            count: 1,
        });
    }
    return result;
}, []);
//console.log(rowmonthcount);

//count cities in JSON data
const rowcitycount = jsonlist.reduce((result2, current) => {
    const element = result2.find((p) => p.city === current.city);
    if (element) {
        element.count2++; // count
    }
    else {
        result2.push({
            city: current.city,
            count2: 1,
        });
    }
    return result2;
}, []);
//console.log(rowcitycount);

const city = ['四国中央市', '新居浜市', '西条市', '今治市', '上島町', '松山市', '東温市', '松前町', '伊予市', '砥部町', '久万高原町', '内子町', '大洲市', '八幡浜市', '伊方町', '西予市', '宇和島市', '鬼北町', '松野町', '愛南町'];
const month = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
const lmonth = month.map(element => element + '月');
const lcity = city.map(element => element.slice(0, -1));

//Fill null month value when there was no value match
const filledMonths = rowmonthcount.map((month) => month.month);
const monthcount = month.map(month => {
    const indexOfFilledMonth = filledMonths.indexOf(month);
    if (indexOfFilledMonth !== -1) return rowmonthcount[indexOfFilledMonth].count;
    return null;
});
//console.log(monthcount);

//Fill null city value when there was no value match
const filledCity = rowcitycount.map((city) => city.city);
const citycount = city.map(city => {
    const indexOfFilledCity = filledCity.indexOf(city);
    if (indexOfFilledCity !== -1) return rowcitycount[indexOfFilledCity].count2;
    return null;
});
//console.log(citycount);

//Change bar colour for different number of month count
const bgcolor = [];
for (i = 0; i < month.length; i++) {
    const r = 0;
    const g = 155;
    const b = 155;
    let o;

    if (monthcount[i] == 0) {
        o = 0;
    }
    else if (monthcount[i] > 0 && monthcount[i] <= 15) {
        o = 0.2;
    }
    else if (monthcount[i] > 15 && monthcount[i] <= 50) {
        o = 0.4;
    }
    else if (monthcount[i] > 50 && monthcount[i] <= 100) {
        o = 0.7;
    }
    else if (monthcount[i] > 100) {
        o = 1;
    }
    else {
        o = 0
    }
    bgcolor.push('rgba(' + r + ',' + g + ',' + b + ',' + o + ')');
};

//Change bar colour for different number of city count
const bgcolor2 = [];
for (i = 0; i < city.length; i++) {
    const r = 0;
    const g = 128;
    const b = 0;
    let o;

    if (citycount[i] == 0) {
        o = 0;
    }
    else if (citycount[i] > 0 && citycount[i] <= 15) {
        o = 0.2;
    }
    else if (citycount[i] > 15 && citycount[i] <= 50) {
        o = 0.4;
    }
    else if (citycount[i] > 50 && citycount[i] <= 100) {
        o = 0.7;
    }
    else if (citycount[i] > 100) {
        o = 1;
    }
    else {
        o = 0
    }
    bgcolor2.push('rgba(' + r + ',' + g + ',' + b + ',' + o + ')');
};

Chart.Tooltip.positioners.cursor = function (chartElements, coordinates) {
    return coordinates;
};

//Configure month chart
const ctx = monthchart.getContext('2d');
const config = {
    type: 'bar',
    data: {
        labels: lmonth,
        datasets: [{
            data: monthcount,
            backgroundColor: bgcolor,
            categoryPercentage: 1,
            barPercentage: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        tooltips: {
            position: 'cursor',
            callbacks: {
                title: () => {
                    return '';
                },
                label: (tooltipItem) => {
                    return tooltipItem.value;
                },
            },
            displayColors: false,
        },
        scales: {
            pointLabels: {
                fontWeight: 700,
            },
            xAxes: [{
                gridLines: {
                    color: '#999',
                    drawTicks: false,
                },
                ticks: {
                    fontColor: 'black',
                    fontStyle: 700,
                    padding: -25,
                    z: 1,
                },
            }],
            yAxes: [{
                gridLines: {
                    display: false,
                    drawTicks: false,
                },
                ticks: {
                    display: false,
                    max: 0.5,
                    min: 0
                }
            }]
        }
    }
};

const chart = new Chart(ctx, config);

//Configure city chart
const ctx2 = citychart.getContext('2d');
const config2 = {
    type: 'bar',
    data: {
        labels: lcity,
        datasets: [{
            data: citycount,
            backgroundColor: bgcolor2,
            categoryPercentage: 1,
            barPercentage: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        tooltips: {
            position: 'cursor',
            callbacks: {
                title: () => {
                    return '';
                },
                label: (tooltipItem) => {
                    return tooltipItem.value;
                },
            },
            displayColors: false,
        },
        scales: {
            xAxes: [{
                gridLines: {
                    color: '#999',
                    drawTicks: false,
                },
                ticks: {
                    fontColor: 'black',
                    fontSize: 10,
                    padding: -25,
                    fontStyle: 700,
                    z: 1,
                },
            }],
            yAxes: [{
                gridLines: {
                    display: false,
                    drawTicks: false,
                },
                ticks: {
                    display: false,
                    max: 0.5,
                    min: 0
                }
            }]
        }
    }
};

const chart2 = new Chart(ctx2, config2);