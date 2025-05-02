<?php
    session_start();
    if(!isset($_SESSION['userId']))
    {
      header("Location: login.php");
    }

    $userId = $_SESSION['userId'];
    include "db.php";
    $sql = "SELECT * FROM interviews WHERE created_by='$userId'";
    $result = $conn->query($sql);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AI Mock Interview</title>
  <link rel="stylesheet" href="css/dashboard.css">


</head>
<body>
  <?php include('header.php'); ?>
  <div class="container1">
    <h2 class="title">Dashboard</h2>
    <h3 class="subtitle">Create and Start your AI Mock Interview</h3>

    <div class="add-div">
        <div class="add" onclick="openDialog()">
          <H2>+Add New</H2>
        </div>
    </div>

    


    <h2 class="section-title" >Previous Mock Interviews</h2>
    <div class="slider-container">
        <!-- <button class="nav-btn left-btn" onclick="scrollLeft()">&#10094;</button>
        
        <div class="slider" id="slider"> -->
        <?php
        if ($result && $result->num_rows > 0) {
          echo '<button class="nav-btn left-btn" onclick="scrollLeft()">&#10094;</button>';
          echo '<div class="slider" id="slider">';
        while ($row = $result->fetch_assoc()) {
        ?>
            <div class="card">
                <h3><?php echo ucwords(strtolower($row['job_position']));; ?></h3>
                <p style="color: #2c5364; font-weight:bold;"> <?php echo $row['job_description']; ?></p>
                <p><strong>Years of Experience :</strong> <?php echo $row['job_experience']; ?></p>
                <p><strong>Created At :</strong> <?php 
                $date=explode(' ', $row['created_at']);
                
                echo $date[0] ?></p>
                <p><strong>Time :</strong> <?php echo $date[1]; ?></p>
                <a href="result.php?mockid=<?php echo $row['mockid']; ?>" class="feedback-btn">Feedback</a>
              </div>
            <?php } 
            echo '</div>'; // close slider div
            echo '<button class="nav-btn right-btn" onclick="scrollRight()">&#10095;</button>';
            }
            else{
              echo '<div class="no-interviews">';
                echo '<img src="images/not_found.png" alt="No Interviews" class="no-data-img">';
                echo ' <h2>No previous interviews available currently</h2>';
              echo '</div>';
            } ?>
        </div>

        <!-- <button class="nav-btn right-btn" onclick="scrollRight()">&#10095;</button>
    </div> -->
</div>

<div class="container" id="dialog">
  <div class="dialog-content">

    <h2 style="padding-bottom: 10px;font-family:'Brushed Script';">Start Mock Interview</h2>
    <form id="interviewForm">
      <label >Job Position</label>
      <input type="text" id="jobPosition" placeholder="Ex. Full Stack Developer" required>
  
      <label>Job Description</label>
      <textarea id="jobDesc" placeholder="Ex. React, Angular, Node.js, MySQL etc" required></textarea>
  
      <label>Years of Experience</label>
      <input type="number" id="jobExperience" placeholder="Ex. 5" required>
  
      <button type="button" id="startBtn" onclick="startInterview()">Generate Questions</button>
      <button type="button" class="cancel" onclick="closeDialog()">Cancel</button>
  
      <!-- ✅ Loading spinner and message -->
      <!-- <div class="loading-container" id="loadingContainer">
        <div class="loading-spinner"></div>
        <div class="loading-text">Generating Questions...</div>
      </div> -->
  
  
      <!-- ✅ Loader (Initially Hidden) -->
        <div class="loader" id="loadingContainer">
          <div class="load-inner load-one"></div>
          <div class="load-inner load-two"></div>
          <div class="load-inner load-three"></div>
          <span class="text"><strong>Generating... </strong></span>
        </div>
  
  
  
    </form>
  </div>

</div>

<script>
  function openDialog() {
    document.getElementById("dialog").classList.add("show");
}

function closeDialog() {
    document.getElementById("dialog").classList.remove("show");
}

// Remove the automatic opening of the form on page load
window.onload = function() {
    document.getElementById("dialog").classList.remove("show"); // Ensures form stays hidden initially
};

  async function startInterview() {
    const jobPosition = document.getElementById('jobPosition').value;
    const jobDesc = document.getElementById('jobDesc').value;
    const jobExperience = document.getElementById('jobExperience').value;
    const startBtn = document.getElementById('startBtn');
    const loadingContainer = document.getElementById('loadingContainer');

    if (!jobPosition || !jobDesc || !jobExperience) {
      alert("Please fill all fields");
      return;
    }

    // ✅ Disable button and show loading animation
    startBtn.disabled = true;
    loadingContainer.style.display = 'flex';

    const requestData = {
      jobPosition,
      jobDesc,
      jobExperience
    };

    try {
      const response = await fetch('generate_questions.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
      });

      const data = await response.json();

      if (data.mockid) {
        alert(`Mock Interview Created! Mock ID: ${data.mockid}`);
        window.location.href = `start_interview.php?mockid=${data.mockid}`;
      } else {
        alert(`Failed to create mock interview: ${data.error}`);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An error occurred while processing your request');
    } finally {
      // ✅ Re-enable button and hide loading animation
      startBtn.disabled = false;
      loadingContainer.style.display = 'none';
    }
  }

  function resetForm() {
    document.getElementById('interviewForm').reset();
  }
</script>
<script>
        let slider = document.getElementById("slider");

        function scrollLeft() {
            slider.scrollBy({ left: -270, behavior: "smooth" });
        }

        function scrollRight() {
            slider.scrollBy({ left: 270, behavior: "smooth" });
        }
    </script>

</body>
</html>
