(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery', 'chart.js'], function ($, Chart) {
            return factory($, Chart);
        });
    } else if (typeof module === 'object' && module.exports) {
        // Node. Does not work with strict CommonJS, but
        // only CommonJS-like environments that support module.exports,
        // like Node.
        module.exports = factory(require('jquery', 'chart.js'));
    } else {
        // Browser globals (root is window)
        // noinspection JSUndefinedPropertyAssignment
        root.brainChart = factory(root.jquery, root.Chart);
    }
}(this, function ($, Chart) {
    "use strict";
    return {
        setUpList: function ($chartContainers) {
            let self = this;
            $chartContainers.each(function () {
                self.load($(this));
            });
        },

        parseFunction: function (str) {
            let self = this;
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

        initializeCanvas: function ($chartContainer, chartConfig) {
            let self = this;
            let $canvas = $('<canvas class="mb-statistics-chart"></canvas>');
            $chartContainer.html($canvas);
            const data = self.parseJson(chartConfig);
            const context = $canvas.get(0).getContext('2d');
            let myChart = new Chart(context, data);
        },

        load: function($chartContainer, url) {
            let self = this;
            if (self.chart) {
                self.chart.destroy();
            }
            let container = $chartContainer.get(0);
            self.addLoader(container);
            $.ajax({
                type: "GET",
                url: container.getAttribute('data-url')
            }).done(function(data){
                if (data && data.chartConfig) {
                    self.initializeCanvas($chartContainer, data.chartConfig);
                } else {
                    container.innerHTML = '<div class="message-chart-empty">No data found</div>';
                }
            }).fail(function(data){
                container.innerHTML = '';
            });
        },
        addLoader: function(container) {
            container.innerHTML = '<div class="chart-loader"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>';
        }
    };
}));
