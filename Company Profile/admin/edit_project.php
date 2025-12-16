<?php
require_once 'auth.php';
require_once '../config.php';

$message = '';
$messageType = '';
$project = null;

// Get project ID
$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$projectId) {
    header('Location: index.php');
    exit;
}

// Get project statuses for dropdown
try {
    $statusStmt = $pdo->query("SELECT status_id, status_name, status_code FROM project_status ORDER BY status_name");
    $statuses = $statusStmt->fetchAll();
} catch (PDOException $e) {
    $statuses = [];
}

// Get project data
try {
    $stmt = $pdo->prepare("SELECT * FROM project WHERE id = ?");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch();
    
    if (!$project) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    $message = 'Error loading project: ' . $e->getMessage();
    $messageType = 'error';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_name = isset($_POST['project_name']) ? trim($_POST['project_name']) : '';
    $reference_no = isset($_POST['reference_no']) ? trim($_POST['reference_no']) : '';
    $contract_value = isset($_POST['contract_value']) ? trim($_POST['contract_value']) : '0';
    $commence_date = isset($_POST['commence_date']) ? trim($_POST['commence_date']) : null;
    $completion_date = isset($_POST['completion_date']) ? trim($_POST['completion_date']) : null;
    $client_name = isset($_POST['client_name']) ? trim($_POST['client_name']) : '';
    $image_files = isset($_POST['image_files']) ? trim($_POST['image_files']) : '';
    $status_id = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 1;
    
    // Validation
    if (empty($project_name)) {
        $message = 'Project name is required';
        $messageType = 'error';
    } else {
        try {
            // Convert date format if needed
            $commence_date = !empty($commence_date) ? date('Y-m-d', strtotime($commence_date)) : null;
            $completion_date = !empty($completion_date) ? date('Y-m-d', strtotime($completion_date)) : null;
            
            // Clean contract value
            $contract_value = str_replace(',', '', $contract_value);
            $contract_value = (float)$contract_value;
            
            $stmt = $pdo->prepare("
                UPDATE project 
                SET project_name = ?, reference_no = ?, contract_value = ?, commence_date = ?, completion_date = ?, client_name = ?, image_files = ?, status_id = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $project_name,
                $reference_no ?: null,
                $contract_value,
                $commence_date,
                $completion_date,
                $client_name ?: null,
                $image_files ?: null,
                $status_id,
                $projectId
            ]);

            // Handle uploads and merge with existing image_files
            if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $uploadDir = realpath(__DIR__ . '/../assets/images/projects');
                if ($uploadDir === false) {
                    $uploadDir = __DIR__ . '/../assets/images/projects';
                    @mkdir($uploadDir, 0777, true);
                }
                $allowedExt = ['jpg','jpeg','png'];
                $uploadedList = [];
                if (!empty($image_files)) {
                    $uploadedList = array_filter(array_map('trim', explode(',', $image_files)));
                }
                // Determine current max index for project_{id}_n.ext pattern
                $maxIndex = 0;
                foreach ($uploadedList as $name) {
                    if (preg_match('/project_' . $projectId . '_(\d+)\./i', $name, $m)) {
                        $maxIndex = max($maxIndex, (int)$m[1]);
                    }
                }
                foreach (glob($uploadDir . "/project_{$projectId}_*.*") as $filePath) {
                    if (preg_match('/project_' . $projectId . '_(\d+)\./i', basename($filePath), $m)) {
                        $maxIndex = max($maxIndex, (int)$m[1]);
                    }
                }

                $fileNames = $_FILES['images']['name'];
                $fileTmp   = $_FILES['images']['tmp_name'];
                $fileErr   = $_FILES['images']['error'];

                for ($i = 0; $i < count($fileNames); $i++) {
                    if ($fileErr[$i] !== UPLOAD_ERR_OK || empty($fileTmp[$i])) continue;
                    $ext = strtolower(pathinfo($fileNames[$i], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExt, true)) continue;
                    $maxIndex++;
                    $newName = "project_{$projectId}_{$maxIndex}.{$ext}";
                    $targetPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;
                    if (@move_uploaded_file($fileTmp[$i], $targetPath)) {
                        $uploadedList[] = $newName;
                    }
                }

                $finalList = implode(',', $uploadedList);
                $up = $pdo->prepare("UPDATE project SET image_files = ? WHERE id = ?");
                $up->execute([$finalList, $projectId]);
                $image_files = $finalList;
            }

            $message = 'Project updated successfully!';
            $messageType = 'success';
            
            // Reload project data
            $stmt = $pdo->prepare("SELECT * FROM project WHERE id = ?");
            $stmt->execute([$projectId]);
            $project = $stmt->fetch();
        } catch (PDOException $e) {
            $message = 'Error updating project: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }
        .admin-header h1 {
            color: var(--primary-color);
            margin: 0;
        }
        .admin-form {
            background: var(--white);
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-color);
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 1rem;
            font-family: inherit;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .hint-text {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-top: 0.35rem;
        }
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-primary {
            background: var(--primary-color);
            color: var(--white);
        }
        .btn-primary:hover {
            background: var(--secondary-color);
        }
        .btn-secondary {
            background: var(--text-light);
            color: var(--white);
        }
        .btn-secondary:hover {
            background: var(--text-color);
        }
        .required {
            color: var(--error-color);
        }
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid var(--success-color);
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid var(--error-color);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-edit"></i> Edit Project</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($project): ?>
        <div class="admin-form">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="project_name">Project Name <span class="required">*</span></label>
                    <input type="text" id="project_name" name="project_name" required 
                           value="<?php echo htmlspecialchars($project['project_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="reference_no">Reference No</label>
                    <input type="text" id="reference_no" name="reference_no" 
                           value="<?php echo htmlspecialchars($project['reference_no'] ?: ''); ?>">
                </div>

                <div class="form-group">
                    <label for="contract_value">Contract Value (RM) <span class="required">*</span></label>
                    <input type="text" id="contract_value" name="contract_value" required 
                           placeholder="e.g., 25000000.00" 
                           value="<?php echo number_format((float)$project['contract_value'], 2, '.', ''); ?>">
                </div>

                <div class="form-group">
                    <label for="commence_date">Commence Date</label>
                    <input type="date" id="commence_date" name="commence_date" 
                           value="<?php echo $project['commence_date'] ? date('Y-m-d', strtotime($project['commence_date'])) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="completion_date">Completion Date</label>
                    <input type="date" id="completion_date" name="completion_date" 
                           value="<?php echo $project['completion_date'] ? date('Y-m-d', strtotime($project['completion_date'])) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="client_name">Client Name</label>
                    <input type="text" id="client_name" name="client_name" 
                           value="<?php echo htmlspecialchars($project['client_name'] ?: ''); ?>">
                </div>

                <div class="form-group">
                    <label for="image_files">Project Images (comma separated)</label>
                    <textarea id="image_files" name="image_files" rows="3" placeholder="Example: project_<?php echo $projectId; ?>_1.jpg, project_<?php echo $projectId; ?>_2.jpg"><?php echo htmlspecialchars($project['image_files'] ?? ''); ?></textarea>
                    <div class="hint-text">Use filenames in <code>assets/images/projects/</code> or full URLs. Leave blank to auto-detect <code>project_<?php echo $projectId; ?>_n.ext</code> files.</div>
                </div>

                <div class="form-group">
                    <label for="images">Upload Images (multiple)</label>
                    <input type="file" id="images" name="images[]" accept=".jpg,.jpeg,.png" multiple>
                    <div class="hint-text">Upload additional JPG/PNG files; they will be named as <code>project_<?php echo $projectId; ?>_n.ext</code> and merged with the list above.</div>
                </div>

                <div class="form-group">
                    <label for="status_id">Project Status <span class="required">*</span></label>
                    <select id="status_id" name="status_id" required>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo $status['status_id']; ?>" 
                                    <?php echo ($project['status_id'] == $status['status_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['status_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Project
                    </button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>



