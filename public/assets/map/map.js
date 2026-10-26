let selectedItems = Array();
let  infoWindow = null;
let hasSelected = false;

const locations = {
    Tehran: [35.71, 51.42],
    Mashhad: [36.3206, 59.5771],
    Esfahan: [32.674061, 51.672134],
    Karaj: [35.835628, 50.974503],
    Tabriz: [38.077285, 46.310463],
    Shiraz: [29.602118, 52.533188],
    Ahvaz: [31.31671, 48.68438],
    Qom: [34.654109, 50.876202],
    Rasht: [37.28083, 49.58306],
    Kerman: [30.28027, 57.06702],
    Yazd: [31.876683, 54.352455],
    Urmia: [37.555329, 45.072414],
    Kermanshah: [34.320755, 47.065376],
    Arak: [34.092758, 49.685615],
    Zanjan: [36.685491, 48.486679],
    Hamedan: [34.805347, 48.516601],
    Zahedan: [29.503561, 60.863861],
    Ardabil: [38.25975, 48.295105],
    BandarAbbas: [27.194793, 56.267059],
    Qazvin: [36.268097, 50.003585],
    Eslamshahr: [35.542813, 51.231075]
};


document.addEventListener('DOMContentLoaded', () => {
    const city = 'Tehran';
    const color = '#44afcb';
    const mapElement = document.getElementById('map');

    if (!mapElement) return;

    const config = {
        city: city,
        color: color,
        selectColor: '#C0A5FF',
        location: locations // Example: { Tehran: [35.6892, 51.3890], ... }
    };

    // Initialize Leaflet map
    const map = L.map(mapElement, {
        center: config.location[city],
        zoom: 13,
        zoomControl: false, // disables default UI like in Google Maps
    });

    // Add base map layer (OpenStreetMap tiles)
    L.tileLayer('https://memaps.ir/hot/{z}/{x}/{y}.png', {
        attribution: '©Max sms map'
    }).addTo(map);

    // Load initial polygons
    loadPolygons(map, city, config);

    // City dropdown change
    const citySelect = document.querySelector('input[name="city"]');
    if (citySelect) {
        const onCityChange = function () {
            const val = this.value;
            map.panTo(config.location[val]);
            loadPolygons(map, val, config);
        };

        citySelect.addEventListener('input', onCityChange);
        citySelect.addEventListener('change', onCityChange);
    }

    // Modal close
    const modal = document.getElementById('polygonModal');
    const modalClose = document.getElementById('modalClose');
    if (modalClose) modalClose.addEventListener('click', () => modal.style.display = 'none');
});

async function loadPolygons(map, city, opts) {
    city = city ?? opts.city;

    try {
        const response = await fetch(`${basicPolygonFile}${city.toLowerCase()}.json`);
        const data = await response.json();

        let irancelltedad = 0, hamrahtedad = 0;

        for (const [x, polygon] of Object.entries(data)) {
            // Convert polygon coordinates
            const myTrip = polygon.position
                .split(';')
                .filter(Boolean)
                .map(pos => {
                    const [lat, lng] = pos.split(',');
                    return [parseFloat(lat), parseFloat(lng)];
                });

            // Create Leaflet polygon
            const flightPath = L.polygon(myTrip, {
                color: opts.color,
                weight: 2,
                opacity: 1,
                fillColor: opts.color,
                fillOpacity: 0
            }).addTo(map);

            // Store postal code like before
            flightPath.PostalCode = x;

            // Polygon click event
            flightPath.on('click', async function (e) {

                let items = 0;

                // Change color to selected
                this.setStyle({ fillColor: opts.selectColor, fillOpacity: 0.5 });

                if (window.infoPopup) {
                    map.closePopup(window.infoPopup);
                }

                const that = this.PostalCode;
                const thatobject = this;

                getPostalCodeCount(this.PostalCode, e, L, map)

                const theme = tmp;

                const marker = L.marker(e.latlng, {
                    icon: L.icon({
                        iconUrl: 'images/flag.png',
                        iconSize: [24, 24]
                    })
                }).addTo(map);



                window.selectedItems = window.selectedItems || [];
                window.selectedItems[items++] = thatobject;
            });
        }
    } catch (error) {
        console.error('Error loading polygon data:', error);
    }
}



