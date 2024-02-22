<div class="flex justify-center items-center h-screen" id="country-form-div">
    <div class="w-full max-w-xs">
        <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" id="country-data-form">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="country_name">
                    Country name
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                       id="country_name"
                       type="text"
                       name="country_name"
                       placeholder="Enter country name"
                        required>
                <p class="text-red-500 text-xs italic" id="error-message"></p>
            </div>
            <div class="flex items-center justify-between">
                <button id="submit-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                    Proceed
                </button>
            </div>
        </form>
    </div>
</div>

<div class="flex justify-center items-center">
    <pre class="json" id="result-data"></pre>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('submit-btn').addEventListener('click', function () {
            const countryName = document.getElementById('country_name').value;
            const errorMessage = document.getElementById('error-message');

            if (!countryName.trim()) {
                errorMessage.textContent = "Please enter a country name";
                document.getElementById('country_name').classList.add('border-red-500');
                return;
            } else {
                errorMessage.textContent = "";
                document.getElementById('country_name').classList.remove('border-red-500');
            }

            fetch('{{ route("api.get-geo-data") }}?country_name=' + countryName, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                const resultDataDiv = document.getElementById('result-data');
                resultDataDiv.textContent = JSON.stringify(data.data, null, 2);

                var divToHide = document.getElementById("country-form-div");
                divToHide.style.display = "none";
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
</script>
