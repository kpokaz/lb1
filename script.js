// All goods
document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('button');
    const output_all = document.getElementById('output');

    button.addEventListener('click', () => {
        logRequest("all_goods");

        fetch('all_goods.php')
            .then(response => response.text())
            .then(data => {
                output_all.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                output_all.innerHTML = '<p style="color: red;">Data download error</p>';
            });
    });
});

// By category
document.getElementById("category-button").addEventListener("click", function(event) {
    event.preventDefault();

    let selectedCheckboxes = document.querySelectorAll('input[name="category-option"]:checked');
    let selectedValues = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (selectedValues.length === 0) {
        alert("Choose at least 1 category!");
        return;
    }

    let queryString = selectedValues.map(category => `categories[]=${encodeURIComponent(category)}`).join("&");
    let url = `by_categories.php?${queryString}`;

    logRequest("by_category");

    console.log("Send GET-query on:", url);

    fetch(url)
        .then(response => response.text()) 
        .then(data => {
            document.getElementById("output").innerHTML = data;
        })
        .catch(error => console.error("Error:", error));
});

// By vendor (XML)
document.getElementById("vendors-button").addEventListener("click", function(event) {
    event.preventDefault();

    let selectedCheckboxes = document.querySelectorAll('input[name="vendor-option"]:checked');
    let selectedValues = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (selectedValues.length === 0) {
        alert("Choose at least 1 vendor!");
        return;
    }

    let queryString = selectedValues.map(vendor => `vendors[]=${encodeURIComponent(vendor)}`).join("&");
    let url = `by_vendors.php?${queryString}`;

    logRequest("by_vendor");

    console.log("Send GET-query on:", url);

    const xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const xml = xhr.responseXML;
            if (!xml) {
                document.getElementById("output").innerHTML = "<p>Ошибка при обработке XML</p>";
                return;
            }

            const items = xml.getElementsByTagName("item");
            let output = "<h3>Products by Vendor (XML):</h3><table border='1'><tr><th>Name</th><th>Price</th><th>Quantity</th><th>Quality</th><th>Vendor</th></tr>";

            for (let i = 0; i < items.length; i++) {
                const name = items[i].getElementsByTagName("name")[0].textContent;
                const price = items[i].getElementsByTagName("price")[0].textContent;
                const quantity = items[i].getElementsByTagName("quantity")[0].textContent;
                const quality = items[i].getElementsByTagName("quality")[0].textContent;
                const vendor = items[i].getElementsByTagName("vendor")[0].textContent;

                output += `<tr>
                    <td>${name}</td>
                    <td>${price}</td>
                    <td>${quantity}</td>
                    <td>${quality}</td>
                    <td>${vendor}</td>
                </tr>`;
            }

            output += "</table>";
            document.getElementById("output").innerHTML = output;
        }
    };

    xhr.send();
});

// By price(jsonp)
document.addEventListener("DOMContentLoaded", function () {
    let minPriceInput = document.getElementById("min-price");
    let maxPriceInput = document.getElementById("max-price");
    let minPriceValue = document.getElementById("min-price-value");
    let maxPriceValue = document.getElementById("max-price-value");

    minPriceInput.addEventListener("input", function () {
        minPriceValue.textContent = this.value;
    });

    maxPriceInput.addEventListener("input", function () {
        maxPriceValue.textContent = this.value;
    });

    document.getElementById("search-button").addEventListener("click", function () {
        let minPrice = minPriceInput.value;
        let maxPrice = maxPriceInput.value;

        logRequest("by_price");

        // Генерація callback функції
        let callbackName = 'jsonpCallback' + new Date().getTime();

        // Створення script тега для запиту
        let script = document.createElement('script');
        script.src = `by_price_range.php?min=${minPrice}&max=${maxPrice}&callback=${callbackName}`;
        document.body.appendChild(script);

        // Оголошення callback функції
        window[callbackName] = function (data) {
            if (!Array.isArray(data)) {
                document.getElementById("output").innerHTML = "<p>Некорректный формат данных</p>";
                return;
            }

            let output = "<h3>Products by Price (JSONP):</h3><table border='1'><tr><th>Name</th><th>Price</th><th>Quantity</th><th>Quality</th></tr>";

            data.forEach(item => {
                output += `<tr>
                    <td>${item.name}</td>
                    <td>${item.price}</td>
                    <td>${item.quantity}</td>
                    <td>${item.quality}</td>
                </tr>`;
            });

            output += "</table>";
            document.getElementById("output").innerHTML = output;
        };
    });
});