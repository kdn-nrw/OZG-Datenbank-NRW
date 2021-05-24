/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['chart.js'], function (Chart) {
            return factory(Chart);
        });
    } else if (typeof module === 'object' && module.exports) {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('chart.js'));
    } else {
        // Browser globals (root is window)
        // noinspection JSUndefinedPropertyAssignment
        root.appChart = factory(root.Chart);
    }
}(this, function (Chart) {
    "use strict";
    return {
        setUpList: function (chartContainers) {
            let self = this;
            for (let i = 0; i < chartContainers.length; i++) {
                self.load(chartContainers.item(i));
            }
        },

        parseFunction: function (str) {
            let fn_body_idx = str.indexOf('{'),
                fn_body = str.substring(fn_body_idx + 1, str.lastIndexOf('}')),
                fn_declare = str.substring(0, fn_body_idx),
                fn_params = fn_declare.substring(fn_declare.indexOf('(') + 1, fn_declare.lastIndexOf(')')),
                args = fn_params.split(',');

            args.push(fn_body);

            function Fn() {
                return Function.apply(this, args);
            }

            Fn.prototype = Function.prototype;

            return new Fn();
        },

        parseJson: function (rawData) {
            let self = this;
            if (typeof (rawData) === 'object') {
                for (let key in rawData) {
                    if (rawData.hasOwnProperty(key)) {
                        rawData[key] = self.parseJson(rawData[key]);
                    }
                }
                return rawData;
            } else if (typeof (rawData) === 'string' && rawData.startsWith('function(')) {
                return self.parseFunction(rawData);
            }
            return rawData;
        },

        initializeCanvas: function (container, chartConfig) {
            let self = this;
            let canvas = document.createElement("canvas");
            canvas.setAttribute("class", "mb-statistics-chart");
            container.innerHTML = '';
            container.appendChild(canvas);
            const data = self.parseJson(chartConfig);
            const context = canvas.getContext('2d');
            let myChart = new Chart(context, data);
        },

        load: function(container) {
            let self = this;
            if (self.chart) {
                self.chart.destroy();
            }
            self.addLoader(container);
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                container.innerHTML = '';
                if (this.readyState === 4 && this.status === 200) {
                    const data = JSON.parse(this.responseText);
                    if (data && data.chartConfig) {
                        self.initializeCanvas(container, data.chartConfig);
                    } else if (data && data.html) {
                        container.innerHTML = data.html;
                    } else {
                        container.innerHTML = '<div class="message-chart-empty">No data found</div>';
                    }
                }
            };

            xhttp.open("GET", container.getAttribute('data-url'), true);
            xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            //xhttp.setRequestHeader("Content-Type", "application/json");
            xhttp.send();
        },
        addLoader: function(container) {
            container.innerHTML = '<div class="chart-loader"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>';
        }
    };
}));
