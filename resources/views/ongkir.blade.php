
<!DOCTYPE html>
<html>
<head>
    <title>Cek Ongkir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Cek Ongkir</h4>
            </div>
            <div class="card-body">
                <form id="ongkirForm">
                    <div class="form-group">
                        <label for="province">Provinsi</label>
                        <select name="province" id="province" class="form-control">
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="city">Kota/Kabupaten</label>
                        <select name="city" id="city" class="form-control">
                            <option value="">Pilih Kota</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="weight">Berat (gram)</label>
                        <input type="number" name="weight" id="weight" class="form-control" placeholder="Berat (gram)">
                    </div>
                    <div class="form-group">
                        <label for="courier">Kurir</label>
                        <select name="courier" id="courier" class="form-control">
                            <option value="">Pilih Kurir</option>
                            <option value="jne">JNE</option>
                            <option value="tiki">TIKI</option>
                            <option value="pos">POS Indonesia</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Cek Ongkir</button>
                </form>
                <div id="result" class="mt-4"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix: Menggunakan nama meta tag yang benar
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Debug: Log untuk memastikan token tersedia
            console.log('CSRF Token:', token);
            
            fetch('/provinces')
                .then(response => {
                    console.log('Province response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Provinces data:', data);
                    if (data.rajaongkir && data.rajaongkir.status.code === 200) {
                        let provinces = data.rajaongkir.results;
                        let provinceSelect = document.getElementById('province');
                        provinces.forEach(province => {
                            let option = document.createElement('option');
                            option.value = province.province_id;
                            option.textContent = province.province;
                            provinceSelect.appendChild(option);
                        });
                    } else {
                        console.error('Failed to fetch provinces:', data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching provinces:', error);
                });

            document.getElementById('province').addEventListener('change', function() {
                let provinceId = this.value;
                if (!provinceId) return;
                
                console.log('Selected province ID:', provinceId);
                
                fetch(`/cities?province_id=${provinceId}`)
                    .then(response => {
                        console.log('City response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Cities data:', data);
                        if (data.rajaongkir && data.rajaongkir.status.code === 200) {
                            let cities = data.rajaongkir.results;
                            let citySelect = document.getElementById('city');
                            citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                            cities.forEach(city => {
                                let option = document.createElement('option');
                                option.value = city.city_id;
                                option.textContent = city.city_name;
                                citySelect.appendChild(option);
                            });
                        } else {
                            console.error('Failed to fetch cities:', data);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching cities:', error);
                    });
            });

            document.getElementById('ongkirForm').addEventListener('submit', function(event) {
                event.preventDefault();

                let origin = 501; // Kota asal
                let destination = document.getElementById('city').value;
                let weight = document.getElementById('weight').value;
                let courier = document.getElementById('courier').value;

                if (!destination || !weight || !courier) {
                    alert('Harap lengkapi semua field');
                    return;
                }

                console.log('Sending cost request:', {origin, destination, weight, courier});

                fetch('/cost', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        origin: origin,
                        destination: destination,
                        weight: weight,
                        courier: courier
                    })
                })
                .then(response => {
                    console.log('Cost response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Cost data:', data);
                    if (data.rajaongkir && data.rajaongkir.status.code === 200) {
                        let result = data.rajaongkir.results[0].costs;
                        let resultDiv = document.getElementById('result');
                        resultDiv.innerHTML = '<h5>Hasil:</h5>';
                        
                        if (result.length === 0) {
                            resultDiv.innerHTML += '<div class="alert alert-warning">Tidak ada layanan tersedia untuk rute ini</div>';
                        } else {
                            let table = document.createElement('table');
                            table.className = 'table table-striped';
                            table.innerHTML = `
                                <thead>
                                    <tr>
                                        <th>Layanan</th>
                                        <th>Biaya</th>
                                        <th>Estimasi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            `;
                            
                            result.forEach(cost => {
                                let row = table.querySelector('tbody').insertRow();
                                row.insertCell(0).textContent = cost.service;
                                row.insertCell(1).textContent = `Rp ${cost.cost[0].value.toLocaleString('id-ID')}`;
                                row.insertCell(2).textContent = `${cost.cost[0].etd} hari`;
                            });
                            
                            resultDiv.appendChild(table);
                        }
                    } else {
                        document.getElementById('result').innerHTML = `
                            <div class="alert alert-danger">
                                Error: ${data.rajaongkir?.status?.description || 'Unknown error'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error calculating cost:', error);
                    document.getElementById('result').innerHTML = `
                        <div class="alert alert-danger">
                            Terjadi kesalahan: ${error.message}
                        </div>
                    `;
                });
            });
        });
    </script>
</body>
</html>