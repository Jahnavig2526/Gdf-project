<?php
include 'db.php';

$sql = "SELECT * FROM internship_completion ORDER BY created_at DESC";
$result = $conn->query($sql);

echo "<h2>Internship Completion Records</h2>";
echo "<table border='1' cellpadding='8' cellspacing='0'>
<tr>
<th>ID</th>
<th>Student Name</th>
<th>Roll No</th>
<th>Domain</th>
<th>Start Date</th>
<th>End Date</th>
<th>Status</th>
<th>Certificate</th>
</tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
        <td>".$row['id']."</td>
        <td>".$row['student_name']."</td>
        <td>".$row['roll_no']."</td>
        <td>".$row['internship_domain']."</td>
        <td>".$row['start_date']."</td>
        <td>".$row['end_date']."</td>
        <td>".$row['status']."</td>
        <td><a href='".$row['certificate_link']."' target='_blank'>View</a></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No records found</td></tr>";
}
echo "</table>";
?>
