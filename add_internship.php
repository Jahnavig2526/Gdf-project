<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $roll_no = $_POST['roll_no'];
    $internship_domain = $_POST['internship_domain'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $certificate_link = $_POST['certificate_link'];

    $sql = "INSERT INTO internship_completion 
            (student_name, roll_no, internship_domain, start_date, end_date, status, certificate_link) 
            VALUES ('$student_name', '$roll_no', '$internship_domain', '$start_date', '$end_date', '$status', '$certificate_link')";

    if ($conn->query($sql) === TRUE) {
        echo "✅ Internship record added successfully!";
    } else {
        echo "❌ Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<h2>Add Internship Completion</h2>
<form method="post" action="">
    <input type="text" name="student_name" placeholder="Student Name" required><br><br>
    <input type="text" name="roll_no" placeholder="Roll Number" required><br><br>
    <input type="text" name="internship_domain" placeholder="Internship Domain" required><br><br>
    <input type="date" name="start_date" required><br><br>
    <input type="date" name="end_date" required><br><br>
    <select name="status">
        <option value="Ongoing">Ongoing</option>
        <option value="Completed">Completed</option>
    </select><br><br>
    <input type="text" name="certificate_link" placeholder="Certificate Link (URL)"><br><br>
    <button type="submit">Add Internship</button>
</form>
