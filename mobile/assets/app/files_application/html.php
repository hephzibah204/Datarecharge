<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Token Test</title>
</head>
<body>
    <h2>Test Fetch User Details with Token</h2>
    <form id="tokenForm">
        <label for="token">Enter Token:</label><br>
        <input type="text" id="token" name="token" required style="width: 300px;"><br><br>
        <button type="button" onclick="fetchUserDetails()">Submit</button>
    </form>

    <h3>Response:</h3>
    <pre id="response" style="border: 1px solid #ddd; padding: 10px; background: #f9f9f9; overflow: auto;"></pre>

    <script>
        function fetchUserDetails() {
            const token = document.getElementById('token').value;
            const responseElement = document.getElementById('response');

            if (!token) {
                responseElement.textContent = "Please enter a token!";
                return;
            }

            fetch('profile.php', { // Replace with your PHP script URL
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                responseElement.textContent = JSON.stringify(data, null, 4);
            })
            .catch(error => {
                responseElement.textContent = "Error: " + error.message;
            });
        }
    </script>
</body>
</html>