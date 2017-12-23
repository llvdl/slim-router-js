var Slim = Slim || {};

// Slim Router object to generate URLs for routes
Slim.Router = (function() {
    function Router(basePath, routes) {
        this.basePath = basePath;
        this.routes = routes;
    }

    Router.prototype.pathFor = function(name, data, queryParams) {
        var url = this.relativePathFor(name, data, queryParams);

        if (this.basePath) {
            url = this.basePath + url;
        }

        return url;
    }

    Router.prototype.relativePathFor = function(name, data, queryParams) {
        if (this.routes === null || this.routes[name] === undefined) {
            throw 'Unknown route "' + name + '"';
        }

        // routeDatas is an array of all possible routes that can be made. There is
        // one routedata for each optional parameter plus one for no optional parameters.
        //
        // The most specific is last, so we look for that first.
        var routeDatas = [].concat(this.routes[name]).reverse();

        var segments = [];
        var i, j, routeData, item, segmentName, url;

        for (i = 0; i < routeDatas.length; ++i) {
            routeData = routeDatas[i];
            for (j = 0; j < routeData.length; ++j) {
                item = routeData[j];

                if (typeof item === 'string') {
                    // this segment is a static string
                    segments.push(item);
                    continue;
                }

                // This segment has a parameter: first element is the name
                if (!data || data[item[0]] === undefined) {
                    // we don't have a data element for this segment: cancel
                    // testing this routeData item, so that we can try a less
                    // specific routeData item.
                    segments = [];
                    segmentName = item[0];
                    break;
                }
                segments.push(data[item[0]]);
            }
            if (segments.length > 0) {
                // we found all the parameters for this route data, no need to check
                // less specific ones
                break;
            }
        }

        if (segments.length === 0) {
            throw 'Missing data for URL segment: "' + segmentName + '"';
        }
        url = segments.join('');

        var queryString = ''
        if (queryParams) {
            for (i in queryParams) {
                queryString += (queryString === '' ? '?' : '&')
                    + encodeURIComponent(i)
                    + '='
                    + encodeURIComponent(queryParams[i]);
            }
        }

        return url + queryString;
    }

    var basePath = <?php print json_encode($basePath); ?>;
    var routes = <?php print json_encode($routes); ?>;

    return new Router(basePath, routes);
}());
