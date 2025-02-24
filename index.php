<?php
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";  // Change if needed
$database = "demo_app";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Data
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Demo App</a>
            <div>
                <a href="#" class="text-light">Home</a> |
                <a href="#" class="text-light">About</a> |
                <a href="#" class="text-light">Contact</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Enter Details</h2>
        <form id="userForm">
            <div class="mb-3 row">
                <div class="col">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" required>
                </div>
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary px-5">Submit</button>
            </div>
        </form>

        <hr>

        <h2>Saved Records</h2>
        <table class="table table-bordered" id="userTable">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sn = 1;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='" . $row["id"] . "'>
                            <td>" . $sn++ . "</td>
                            <td>" . htmlspecialchars($row["name"]) . "</td>
                            <td>" . htmlspecialchars($row["email"]) . "</td>
                            <td>" . htmlspecialchars($row["phone"]) . "</td>
                            <td>
                                <button class='btn btn-danger btn-sm delete-btn' data-id='" . $row["id"] . "'>Delete</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No records found</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {
        // Add user without page reload
        $("#userForm").submit(function (e) {
            e.preventDefault();

            $.ajax({
                url: "ajax_handler.php",
                type: "POST",
                data: {
                    action: "add",
                    name: $("#name").val(),
                    email: $("#email").val(),
                    phone: $("#phone").val()
                },
                dataType: "json",
                success: function (response) {
    if (response.status == "success") {
        // Remove "No records found" row
        $("#userTable tbody tr:contains('No records found')").remove();

        let newRow = `
            <tr data-id="${response.id}">
                <td></td> <!-- S.N will be updated dynamically -->
                <td>${$("#name").val()}</td>
                <td>${$("#email").val()}</td>
                <td>${$("#phone").val()}</td>
                <td>
                    <button class='btn btn-danger btn-sm delete-btn' data-id='${response.id}'>Delete</button>
                </td>
            </tr>
        `;
        $("#userTable tbody").append(newRow);
        updateSN(); // Call function to fix S.N numbering
        $("#userForm")[0].reset();
    } else {
        alert("Error: " + response.message);
    }
}

            });
        });

        // Delete user without page reload
        $(document).on("click", ".delete-btn", function () {
            let id = $(this).data("id");
            let row = $(this).closest("tr");

            $.ajax({
                url: "ajax_handler.php",
                type: "POST",
                data: { action: "delete", id: id },
                dataType: "json",
                success: function (response) {
                    if (response.status == "success") {
                        row.remove();
                        updateSN();
                    } else {
                        alert("Error: " + response.message);
                    }
                }
            });
        });

        function updateSN() {
    $("#userTable tbody tr").each(function (index) {
        $(this).find("td:first").text(index + 1);
    });
}

    });
    </script>

</body>
</html>

<?php
$conn->close();
?>
