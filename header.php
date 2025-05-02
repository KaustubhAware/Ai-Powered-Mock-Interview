<?php
    include "db.php";
    $userId = $_SESSION['userId'];
    $sql = "SELECT * FROM users WHERE email = '$userId'";
    $query = mysqli_query($conn, $sql);
    $info = mysqli_fetch_assoc($query);
    $userName = trim(($info['firstname'] ?? '').' '.($info['lastname'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header with User Profile Card</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f4f4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            /* background-color: #007bff; */
            background: linear-gradient(90deg, #0f2027, #203a43, #2c5364);
            color: white;
            position: relative;
        }
        .logo {
            font-size: 30px;
            font-weight: bold;
            font-family:'Brushed Script';
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            text-decoration: none;
            color: white;
            font-size: 18px;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
        .profile-container {
            position: relative;
            cursor: pointer;
        }
        .profile-circle {
            width: 40px;
            height: 40px;
            background-color: white;
            color: #007bff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        .profile-circle:hover {
            transform: scale(1.1);
        }
        .profile-card {
            position: absolute;
            right: 0;
            top: 50px;
            width: 220px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 15px;
            display: none;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.3s ease;
            z-index: 1100;
        }
        .profile-card .profile-circle-large {
            background: lightblue;
            width: 60px;
            height: 60px;
            font-size: 26px;
            margin-bottom: 10px;
        }
        .profile-card p {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }
        .logout-btn {
            background: #ff4d4d;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
            font-size: 14px;
        }
        .logout-btn:hover {
            background: #cc0000;
        }
        .show {
            display: flex;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">AI Mock Interview</div>
    <!-- <ul class="nav-links">
        <li><a href="#">Home</a></li>
        <li><a href="#">Mock Interview</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Contact</a></li>
    </ul> -->
    <div class="profile-container" onclick="toggleCard()">
        <div class="profile-circle" id="profileInitial">U</div>
        <div class="profile-card" id="profileCard">
            <div class="profile-circle profile-circle-large" id="cardInitial">U</div>
            <p id="profileName" style="font-weight:bold;"><?php echo $userName; ?></p>
            <p id="userEmail"   style="font-weight:bold;"><?php echo $userId; ?></p>
            <button class="logout-btn" onclick="logout()">Log Out</button>
        </div>
    </div>
</header>

<script>
    function toggleCard() {
        document.getElementById("profileCard").classList.toggle("show");
    }

    function logout() {
        alert("Logging out...");
        window.location.href = "logout.php"; // Redirect to logout page
    }

    // Set user initials dynamically
    function setUserInitial() {
        const userEmail = "<?php echo $userId;?>"; // Replace with dynamic user data
        const userName= "<?php echo $userName;?>"; // Replace with dynamic user data
        const userInitial = userName.charAt(0).toUpperCase();
        document.getElementById("profileInitial").innerText = userInitial;
        document.getElementById("cardInitial").innerText = userInitial;
        document.getElementById("profileName").innerText = userName;
        document.getElementById("userEmail").innerText = userEmail;
    }

    setUserInitial();
</script>

</body>
</html>
