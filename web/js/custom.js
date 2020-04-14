var map;
var markers = [];
var ids = [];
$(document).ready(function () {
    $(".reset-data").click(function () {
        removeLastMarker();
        return true;
    });
    $(".delete-btn").click(function () {
        $('.delete-order-confirmation').attr('data-delete', $(this).data('id'));
    });
    $(".delete-order-confirmation").click(function () {
        var order_id = $('.delete-order-confirmation').attr('data-delete');
        $.ajax({
            type: "POST",
            url: "index?r=order/delete",
            data: {order_id: order_id},
            success: function (data) {
                $('#row' + order_id).remove();
                var id = ids.indexOf(parseInt(order_id));
                markers[id].setMap(null);
            },
            error: function (exception) {
            }
        });
    });


});

function initialize() {
    var styles = [
        {
            "stylers": [
                {
                    "hue": "#f1d8dc",
                    "lightness": -115, "saturation": -167

                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#9C2366",
                }
            ]
        }, {
            "featureType": "poi",
            "elementType": "geometry.fill",
            "stylers": [
                {
                    "color": "#fff",

                }
            ]
        }

    ];

    $('form').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    const locationInputs = document.getElementsByClassName("map-input");

    const autocompletes = [];
    const geocoder = new google.maps.Geocoder;
    // console.log('outer');
    for (let i = 0; i < locationInputs.length; i++) {
        const input = locationInputs[i];
        const fieldKey = input.id.replace("-input", "");
        const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';


        var locations_g = [['<b>Name</b>:&nbspSearch Location<br><br>', $('#address-latitude').val(), $('#address-longitude').val(), 0, 10000]];
        // console.log($('#address-latitude').val());
        if ($('#address-latitude').val() != 0) {
            map = new google.maps.Map(document.getElementById('address-map'), {
                zoom: 8,
                mapTypeControl: false,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.LEFT_TOP
                },
                scrollwheel: false,
                center: new google.maps.LatLng($('#address-latitude').val(), $('#address-longitude').val()),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                backgroundColor: '#fff',
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_CENTER
                },
                scaleControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });

        } else {
            map = new google.maps.Map(document.getElementById('address-map'), {
                zoom: 11,
                mapTypeControl: false,
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.LEFT_TOP
                },
                scrollwheel: false,
                center: new google.maps.LatLng(45.5016889, -73.567256),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                backgroundColor: '#fff',
                scaleControl: false,
                streetViewControl: false,
                fullscreenControl: false
            });
        }

        var infowindow = new google.maps.InfoWindow();

        var marker;
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon: locations[i][3],
                title: locations[i][0],
                animation: google.maps.Animation.DROP
            });


            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    for (var j = 0; j < markers.length; j++) {
                        if (typeof (markers[j].infowindow) != "undefined" && markers[j].infowindow !== null) {
                            markers[j].infowindow.close();
                        }

                    }
                    showOrderOnMarkerClick(locations[i][4]);
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
            markers.push(marker);
            ids.push(locations[i][4]);
        }

        for (i = 0; i < locations_g.length; i++) {
            // if (locations_g[i][1] != 0) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations_g[i][1], locations_g[i][2]),
                map: map,
                icon: 'https://www.ahdubai.com/images/placeholder.png',
                title: locations_g[i][0],
                animation: google.maps.Animation.DROP
            });

            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
            markers.push(marker);
            ids.push(locations_g[i][4]);
            // }
        }


        marker.setVisible(isEdit);

        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.key = fieldKey;
        autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
    }

    for (let i = 0; i < autocompletes.length; i++) {
        const input = autocompletes[i].input;
        const autocomplete = autocompletes[i].autocomplete;
        const map1 = autocompletes[i].map;
        const marker = autocompletes[i].marker;

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            marker.setVisible(false);
            const place = autocomplete.getPlace();

            geocoder.geocode({'placeId': place.place_id}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();
                    setLocationCoordinates(autocomplete.key, lat, lng);
                }
            });

            if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                input.value = "";
                return;
            }

            if (place.geometry.viewport) {
                map1.fitBounds(place.geometry.viewport);
            } else {
                map1.setCenter(place.geometry.location);
                map1.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

        });
    }
}

google.maps.event.addDomListener(window, 'resize', initialize);

google.maps.event.addDomListener(window, 'load', initialize);

function setLocationCoordinates(key, lat, lng) {
    const latitudeField = document.getElementById(key + "-" + "latitude");
    const longitudeField = document.getElementById(key + "-" + "longitude");
    latitudeField.value = lat;
    longitudeField.value = lng;
}

function previewLocation() {
    markers[markers.length - 1].infowindow = new google.maps.InfoWindow();
    markers[markers.length - 1].infowindow.setContent('Current marker you have searched');
    markers[markers.length - 1].infowindow.open(map, markers[markers.length - 1]);
}

function showOrderOnMarkerClick(id) {
    $('#orders-tables tr').each(function (index, tr) {
        // console.log(tr);
        $(tr).removeClass("lightblue");
    });
    $('#row' + id).addClass("lightblue");
}

function openInfoWindow(order_id) {
    // open info window
    var id = ids.indexOf(order_id);
    for (var i = 0; i < markers.length; i++) {
        if (typeof (markers[i].infowindow) != "undefined" && markers[i].infowindow !== null) {
            markers[i].infowindow.close();
        }

    }
    markers[id].infowindow = new google.maps.InfoWindow();
    markers[id].infowindow.setContent(markers[id].title);
    markers[id].infowindow.open(map, markers[id]);
}

function onChangeAddress() {
    setTimeout(function () {
        console.log($('#address-latitude').val());
        console.log($('#address-longitude').val());
        if ($('#address-latitude').val() != 0 && $('#address-longitude').val() != 0) {
            $('.preview').attr("disabled", false);
        }
    }, 4000);
}

function removeLastMarker() {
    markers[markers.length - 1].setMap(null);
}

function changeOrderStatus(current, order_id) {
    var status = current.value;
    $(current).removeClass();
    if (status == 0) {
        $(current).addClass('btn btn-default');
        $(current).next(".delete-btn").attr("disabled", false);
    } else if (status == 1) {
        $(current).addClass('btn btn-primary');
        $(current).next(".delete-btn").attr("disabled", false);
    } else if (status == 2) {
        $(current).addClass('btn btn-warning');
        $(current).next(".delete-btn").attr("disabled", true);
    } else if (status == 3) {
        $(current).addClass('btn btn-success');
        $(current).next(".delete-btn").attr("disabled", true);
    } else {
        $(current).addClass('btn btn-danger');
        $(current).next(".delete-btn").attr("disabled", true);
    }
    $.ajax({
        type: "POST",
        url: 'index?r=order/status',
        data: {status: status, order_id: order_id},
        success: function (data) {
            var id = ids.indexOf(parseInt(order_id));
            markers[id].setIcon(data.data.image);
        },
        error: function (exception) {
        }
    });
}

function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("orders-tables");
    switching = true;
    //Set the sorting direction to ascending:
    dir = "asc";
    /*Make a loop that will continue until
    no switching has been done:*/
    while (switching) {
        //start by saying: no switching is done:
        switching = false;
        rows = table.rows;
        /*Loop through all table rows (except the
        first, which contains table headers):*/
        for (i = 1; i < (rows.length - 1); i++) {
            //start by saying there should be no switching:
            shouldSwitch = false;
            /*Get the two elements you want to compare,
            one from current row and one from the next:*/
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /*check if the two rows should switch place,
            based on the direction, asc or desc:*/
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /*If a switch has been marked, make the switch
            and mark that a switch has been done:*/
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            //Each time a switch is done, increase this count by 1:
            switchcount++;
        } else {
            /*If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again.*/
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }
        }
    }
}