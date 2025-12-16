<?php
// 1. Connect to MySQL
$mysqli = new mysqli('127.0.0.1', 'root', '', 'company');
if ($mysqli->connect_errno) {
    die('Failed to connect MySQL: ' . $mysqli->connect_error);
}

// 2. Query completed projects
$sql = "
SELECT 
    p.id,
    p.project_name,
    p.reference_no,
    p.contract_value,
    p.commence_date,
    p.completion_date,
    p.client_name
FROM projects p
JOIN project_status s ON p.status_id = s.status_id
WHERE s.status_code = 'COMPLETED'
ORDER BY p.completion_date DESC
";

$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Projects</title>
    <!-- optional: Bootstrap -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    >
</head>
<body class="p-4">
    <h1 class="mb-3">List of Completed Projects</h1>

    <table class="table table-bordered table-striped table-sm align-middle">
        <thead class="table-danger">
        <tr>
            <th>No</th>
            <th>Project Name</th>
            <th>Reference No</th>
            <th>Contract Value (RM)</th>
            <th>Commence Date</th>
            <th>Completion Date</th>
            <th>Client Name</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['project_name']) ?></td>
                    <td><?= htmlspecialchars($row['reference_no']) ?></td>
                    <td><?= number_format((float)$row['contract_value'], 2) ?></td>
                    <td><?= htmlspecialchars($row['commence_date']) ?></td>
                    <td><?= htmlspecialchars($row['completion_date']) ?></td>
                    <td><?= htmlspecialchars($row['client_name']) ?></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="7" class="text-center">No completed projects found.</td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</body>
</html>