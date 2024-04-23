<?php
// credentials
$host = "localhost";
$username = "zillowuser";
$password = "zillowpassword";
$database = "zillowdb";

// connect to database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// post new listng to database
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add"])) {
        $address = isset($_POST["address"]) ? $_POST["address"] : "";
        $city = isset($_POST["city"]) ? $_POST["city"] : "";
        $state = isset($_POST["state"]) ? $_POST["state"] : "";
        $zip = isset($_POST["zip"]) ? $_POST["zip"] : "";
        $price = isset($_POST["price"]) ? $_POST["price"] : 0;
        $bedrooms = isset($_POST["bedrooms"]) ? $_POST["bedrooms"] : 0;
        $bathrooms = isset($_POST["bathrooms"]) ? $_POST["bathrooms"] : 0;
        $sqft = isset($_POST["sqft"]) ? $_POST["sqft"] : 0;
        $owner_name = isset($_POST["owner_name"]) ? $_POST["owner_name"] : "";

        if (!empty($address) && !empty($city) && !empty($state) && !empty($zip) && !empty($owner_name)) {
            $sql = "INSERT INTO listings (address, city, state, zip, price, bedrooms, bathrooms, sqft, owner_name)
                    VALUES ('$address', '$city', '$state', '$zip', $price, $bedrooms, $bathrooms, $sqft, '$owner_name')";
            $conn->query($sql);
        }
    } elseif (isset($_POST["delete"])) {
        $id = $_POST["id"];
        $sql = "DELETE FROM listings WHERE id = $id";
        $conn->query($sql);
    } elseif (isset($_POST["buy"])) {
        $id = $_POST["id"];
        $sql = "INSERT INTO buying (listing_id) VALUES ($id)";
        $conn->query($sql);
    } elseif (isset($_POST["rent"])) {
        $id = $_POST["id"];
        $sql = "INSERT INTO renting (listing_id) VALUES ($id)";
        $conn->query($sql);
    } elseif (isset($_POST["sell"])) {
        $id = $_POST["id"];
        $sql = "INSERT INTO selling (listing_id) VALUES ($id)";
        $conn->query($sql);
    }
}

// render listings
$sql = "SELECT * FROM listings";
$result = $conn->query($sql);

// render buying listings
$buying_sql = "SELECT listings.* FROM listings INNER JOIN buying ON listings.id = buying.listing_id";
$buying_result = $conn->query($buying_sql);

// render renting listings
$renting_sql = "SELECT listings.* FROM listings INNER JOIN renting ON listings.id = renting.listing_id";
$renting_result = $conn->query($renting_sql);

// render selling listings
$selling_sql = "SELECT listings.* FROM listings INNER JOIN selling ON listings.id = selling.listing_id";
$selling_result = $conn->query($selling_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Zillow-like Listings</title>
</head>
<body>
    <h1>Zillow-like Listings</h1>
    <form method="POST">
        <h2>Add Listing</h2>
        <label for="address">Address:</label>
        <input type="text" name="address" required><br>
        <label for="city">City:</label>
        <input type="text" name="city" required><br>
        <label for="state">State:</label>
        <input type="text" name="state" required><br>
        <label for="zip">Zip:</label>
        <input type="text" name="zip" required><br>
        <label for="price">Price:</label>
        <input type="number" name="price" min="0" step="0.01" required><br>
        <label for="bedrooms">Bedrooms:</label>
        <input type="number" name="bedrooms" min="0" required><br>
        <label for="bathrooms">Bathrooms:</label>
        <input type="number" name="bathrooms" min="0" step="0.1" required><br>
        <label for="sqft">Square Feet:</label>
        <input type="number" name="sqft" min="0" required><br>
        <label for="owner_name">Owner Name:</label>
        <input type="text" name="owner_name" required><br>
        <input type="submit" name="add" value="Add Listing">
    </form>
    <hr>
    <h2>All Listings</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Price</th>
            <th>Bedrooms</th>
            <th>Bathrooms</th>
            <th>Square Feet</th>
            <th>Owner Name</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["address"]; ?></td>
                <td><?php echo $row["city"]; ?></td>
                <td><?php echo $row["state"]; ?></td>
                <td><?php echo $row["zip"]; ?></td>
                <td>$<?php echo number_format($row["price"], 2); ?></td>
                <td><?php echo $row["bedrooms"]; ?></td>
                <td><?php echo $row["bathrooms"]; ?></td>
                <td><?php echo number_format($row["sqft"]); ?></td>
                <td><?php echo $row["owner_name"]; ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                        <input type="submit" name="buy" value="Buy">
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                        <input type="submit" name="rent" value="Rent">
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                        <input type="submit" name="sell" value="Sell">
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                        <input type="submit" name="delete" value="Delete">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <hr>
    <h2>Buying Listings</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Price</th>
            <th>Bedrooms</th>
            <th>Bathrooms</th>
            <th>Square Feet</th>
            <th>Owner Name</th>
        </tr>
        <?php while ($row = $buying_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["address"]; ?></td>
                <td><?php echo $row["city"]; ?></td>
                <td><?php echo $row["state"]; ?></td>
                <td><?php echo $row["zip"]; ?></td>
                <td>$<?php echo number_format($row["price"], 2); ?></td>
                <td><?php echo $row["bedrooms"]; ?></td>
                <td><?php echo $row["bathrooms"]; ?></td>
                <td><?php echo number_format($row["sqft"]); ?></td>
                <td><?php echo $row["owner_name"]; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <hr>
    <h2>Renting Listings</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Price</th>
            <th>Bedrooms</th>
            <th>Bathrooms</th>
            <th>Square Feet</th>
            <th>Owner Name</th>
        </tr>
        <?php while ($row = $renting_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["address"]; ?></td>
                <td><?php echo $row["city"]; ?></td>
                <td><?php echo $row["state"]; ?></td>
                <td><?php echo $row["zip"]; ?></td>
                <td>$<?php echo number_format($row["price"], 2); ?></td>
                <td><?php echo $row["bedrooms"]; ?></td>
                <td><?php echo $row["bathrooms"]; ?></td>
                <td><?php echo number_format($row["sqft"]); ?></td>
                <td><?php echo $row["owner_name"]; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <hr>
    <h2>Selling Listings</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Address</th>
            <th>City</th>
            <th>State</th>
            <th>Zip</th>
            <th>Price</th>
            <th>Bedrooms</th>
            <th>Bathrooms</th>
            <th>Square Feet</th>
            <th>Owner Name</th>
        </tr>
        <?php while ($row = $selling_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["address"]; ?></td>
                <td><?php echo $row["city"]; ?></td>
                <td><?php echo $row["state"]; ?></td>
                <td><?php echo $row["zip"]; ?></td>
                <td>$<?php echo number_format($row["price"], 2); ?></td>
                <td><?php echo $row["bedrooms"]; ?></td>
                <td><?php echo $row["bathrooms"]; ?></td>
                <td><?php echo number_format($row["sqft"]); ?></td>
                <td><?php echo $row["owner_name"]; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
