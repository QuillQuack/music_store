<?php
session_start();
include "config.php";

// ------------------------------
// HANDLE LOGIN
// ------------------------------
if (isset($_POST["login"])) {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Normal Login
    $stmt = $conn->prepare("SELECT * FROM Customer WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["Password"])) {
        $_SESSION["customer_id"] = $user["Customer_ID"];
        $_SESSION["customer_name"] = $user["Name"];

        header("Location: customer_dashboard.php");
        exit();
    } else {
        $login_error = "Incorrect email or password.";
    }
}

// ------------------------------
// HANDLE REGISTER
// ------------------------------
if (isset($_POST["register"])) {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $country = $_POST["country"];
    $password = $_POST["password"];

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO Customer (Name, Email, Country, Password)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $name, $email, $country, $hash);
    $stmt->execute();

    $_SESSION["customer_id"] = $stmt->insert_id;
    $_SESSION["customer_name"] = $name;

    header("Location: customer_dashboard.php");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Customer Authentication</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
<style>
    .card{
    width: 50%;
    }
</style>
</head>

<body>
<!-- Back Button -->
<a href="select_role.php" class="back-btn">⬅ Back</a>


<div class="card">

<ul class="nav nav-tabs mb-3" id="authTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button">
            Login
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button">
            Register
        </button>
    </li>
</ul>

<div class="tab-content">

    <!-- =====================
           LOGIN TAB
    ====================== -->
    <div class="tab-pane fade show active" id="login">

        <h4 class="text-center mb-3">Customer Login</h4>

        <?php if (!empty($login_error)): ?>
        <div class="alert alert-danger"><?= $login_error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="login">

            <label>Email</label>
            <input class="form-control mb-2" name="email" required>

            <label>Password</label>
            <input type="password" class="form-control mb-3" name="password" required>

            <button class="btn btn-primary w-100">Login</button>
        </form>

    </div>

<!-- =====================
       REGISTER TAB
====================== -->
<div class="tab-pane fade" id="register">

    <h4 class="text-center mb-3">Create Account</h4>

    <form method="POST">
        <input type="hidden" name="register">

        <label>Name</label>
        <input class="form-control mb-2" name="name" required>

        <label>Email</label>
        <input class="form-control mb-2" name="email" required>

        <label>Country</label>
        <select class="form-control mb-3" name="country" required>
            <option value="">-- Select Country --</option>

            <!-- Full ISO Country List -->
            <option value="Afghanistan">Afghanistan</option>
            <option value="Albania">Albania</option>
            <option value="Algeria">Algeria</option>
            <option value="Andorra">Andorra</option>
            <option value="Angola">Angola</option>
            <option value="Argentina">Argentina</option>
            <option value="Armenia">Armenia</option>
            <option value="Australia">Australia</option>
            <option value="Austria">Austria</option>
            <option value="Azerbaijan">Azerbaijan</option>
            <option value="Bahamas">Bahamas</option>
            <option value="Bahrain">Bahrain</option>
            <option value="Bangladesh">Bangladesh</option>
            <option value="Barbados">Barbados</option>
            <option value="Belarus">Belarus</option>
            <option value="Belgium">Belgium</option>
            <option value="Belize">Belize</option>
            <option value="Benin">Benin</option>
            <option value="Bhutan">Bhutan</option>
            <option value="Bolivia">Bolivia</option>
            <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
            <option value="Botswana">Botswana</option>
            <option value="Brazil">Brazil</option>
            <option value="Brunei">Brunei</option>
            <option value="Bulgaria">Bulgaria</option>
            <option value="Burkina Faso">Burkina Faso</option>
            <option value="Burundi">Burundi</option>
            <option value="Cambodia">Cambodia</option>
            <option value="Cameroon">Cameroon</option>
            <option value="Canada">Canada</option>
            <option value="Chad">Chad</option>
            <option value="Chile">Chile</option>
            <option value="China">China</option>
            <option value="Colombia">Colombia</option>
            <option value="Congo">Congo</option>
            <option value="Costa Rica">Costa Rica</option>
            <option value="Croatia">Croatia</option>
            <option value="Cuba">Cuba</option>
            <option value="Cyprus">Cyprus</option>
            <option value="Czech Republic">Czech Republic</option>
            <option value="Denmark">Denmark</option>
            <option value="Dominican Republic">Dominican Republic</option>
            <option value="Ecuador">Ecuador</option>
            <option value="Egypt">Egypt</option>
            <option value="Estonia">Estonia</option>
            <option value="Ethiopia">Ethiopia</option>
            <option value="Finland">Finland</option>
            <option value="France">France</option>
            <option value="Germany">Germany</option>
            <option value="Greece">Greece</option>
            <option value="Hungary">Hungary</option>
            <option value="Iceland">Iceland</option>
            <option value="India">India</option>
            <option value="Indonesia">Indonesia</option>
            <option value="Iran">Iran</option>
            <option value="Iraq">Iraq</option>
            <option value="Ireland">Ireland</option>
            <option value="Israel">Israel</option>
            <option value="Italy">Italy</option>
            <option value="Japan">Japan</option>
            <option value="Jordan">Jordan</option>
            <option value="Kazakhstan">Kazakhstan</option>
            <option value="Kenya">Kenya</option>
            <option value="Kuwait">Kuwait</option>
            <option value="Laos">Laos</option>
            <option value="Latvia">Latvia</option>
            <option value="Lebanon">Lebanon</option>
            <option value="Lithuania">Lithuania</option>
            <option value="Luxembourg">Luxembourg</option>
            <option value="Malaysia">Malaysia</option>
            <option value="Maldives">Maldives</option>
            <option value="Mexico">Mexico</option>
            <option value="Monaco">Monaco</option>
            <option value="Mongolia">Mongolia</option>
            <option value="Myanmar">Myanmar</option>
            <option value="Nepal">Nepal</option>
            <option value="Netherlands">Netherlands</option>
            <option value="New Zealand">New Zealand</option>
            <option value="Nigeria">Nigeria</option>
            <option value="North Korea">North Korea</option>
            <option value="Norway">Norway</option>
            <option value="Oman">Oman</option>
            <option value="Pakistan">Pakistan</option>
            <option value="Peru">Peru</option>
            <option value="Philippines">Philippines</option>
            <option value="Poland">Poland</option>
            <option value="Portugal">Portugal</option>
            <option value="Qatar">Qatar</option>
            <option value="Romania">Romania</option>
            <option value="Russia">Russia</option>
            <option value="Saudi Arabia">Saudi Arabia</option>
            <option value="Serbia">Serbia</option>
            <option value="Singapore">Singapore</option>
            <option value="Slovakia">Slovakia</option>
            <option value="Slovenia">Slovenia</option>
            <option value="South Africa">South Africa</option>
            <option value="South Korea">South Korea</option>
            <option value="Spain">Spain</option>
            <option value="Sri Lanka">Sri Lanka</option>
            <option value="Sweden">Sweden</option>
            <option value="Switzerland">Switzerland</option>
            <option value="Taiwan">Taiwan</option>

            <!-- DEFAULT SELECTED -->
            <option value="Thailand" selected>Thailand</option>

            <option value="Turkey">Turkey</option>
            <option value="Ukraine">Ukraine</option>
            <option value="United Arab Emirates">United Arab Emirates</option>
            <option value="United Kingdom">United Kingdom</option>
            <option value="United States">United States</option>
            <option value="Vietnam">Vietnam</option>
        </select>

        <label>Password</label>
        <input type="password" class="form-control mb-3" name="password" required>

        <button class="btn btn-success w-100">Register</button>
    </form>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
