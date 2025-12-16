<?php
require_once 'auth.php';
require_once '../config.php';

// Featured projects persistence in project table (featured = 1, max 3)
function loadFeaturedIdsFromDb(PDO $pdo) {
    try {
        $stmt = $pdo->query("SELECT id FROM project WHERE featured = 1 ORDER BY id ASC LIMIT 3");
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    } catch (PDOException $e) {
        return [];
    }
}

function saveFeaturedIdsToDb(PDO $pdo, array $ids) {
    $ids = array_slice(array_unique(array_map('intval', $ids)), 0, 3);
    $pdo->beginTransaction();
    try {
        // Clear all featured flags
        $pdo->exec("UPDATE project SET featured = 0");
        if (!empty($ids)) {
            // Build placeholders dynamically
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "UPDATE project SET featured = 1 WHERE id IN ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($ids);
        }
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
    return $ids;
}

$imageBaseWeb = '../assets/images/projects/';
$imageBaseFs = realpath(__DIR__ . '/../assets/images/projects/') . DIRECTORY_SEPARATOR;

function getPrimaryImagePath(array $project, $imageBaseWeb, $imageBaseFs) {
    $candidates = [];
    if (!empty($project['image_files'])) {
        $entries = array_filter(array_map('trim', explode(',', $project['image_files'])));
        foreach ($entries as $entry) {
            if ($entry === '') continue;
            if (preg_match('~^https?://~i', $entry) || strpos($entry, '//') === 0 || strpos($entry, '/') === 0) {
                $candidates[] = $entry;
            } elseif (strpos($entry, 'assets/') === 0) {
                $candidates[] = '../' . $entry;
            } elseif (strpos($entry, '../') === 0) {
                $candidates[] = $entry;
            } else {
                $candidates[] = $imageBaseWeb . $entry;
            }
        }
    }

    foreach ($candidates as $path) {
        if (preg_match('~^https?://~i', $path) || strpos($path, '//') === 0 || strpos($path, '/') === 0) {
            return $path;
        }
        $fsPath = $imageBaseFs . ltrim(str_replace(['../assets/images/projects/', $imageBaseWeb], '', $path), DIRECTORY_SEPARATOR);
        if (file_exists($fsPath)) {
            return $path;
        }
    }

    $id = (int)($project['id'] ?? 0);
    if ($id > 0) {
        foreach (['jpg', 'jpeg', 'png'] as $ext) {
            $fs = $imageBaseFs . "project_{$id}_1.{$ext}";
            if (file_exists($fs)) {
                return $imageBaseWeb . "project_{$id}_1.{$ext}";
            }
        }
    }
    return '';
}

$featuredIds = loadFeaturedIdsFromDb($pdo);

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $projectId = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM project WHERE id = ?");
        $stmt->execute([$projectId]);
        $message = 'Project deleted successfully';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error deleting project: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Handle featured save (max 3)
if (isset($_POST['save_featured'])) {
    $ids = isset($_POST['featured_ids']) && is_array($_POST['featured_ids']) ? $_POST['featured_ids'] : [];
    $ids = array_slice(array_unique(array_map('intval', $ids)), 0, 3);
    try {
        $featuredIds = saveFeaturedIdsToDb($pdo, $ids);
        $message = 'Featured projects updated (max 3).';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error saving featured projects: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Filters
$filterKeyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$filterStatus = isset($_GET['status_id']) ? trim($_GET['status_id']) : '';
$filterCommenceFrom = isset($_GET['commence_from']) ? trim($_GET['commence_from']) : '';
$filterCommenceTo = isset($_GET['commence_to']) ? trim($_GET['commence_to']) : '';

// Get status options for filter
try {
    $statusStmt = $pdo->query("SELECT status_id, status_name FROM project_status ORDER BY status_name");
    $statusOptions = $statusStmt->fetchAll();
} catch (PDOException $e) {
    $statusOptions = [];
}

// Get filtered projects
try {
    $sql = "
        SELECT 
            p.id,
            p.project_name,
            p.reference_no,
            p.contract_value,
            p.commence_date,
            p.completion_date,
            p.client_name,
            p.image_files,
            s.status_code,
            s.status_name
        FROM project p
        LEFT JOIN project_status s ON p.status_id = s.status_id
    ";

    $conditions = [];
    $params = [];

    if ($filterKeyword !== '') {
        $conditions[] = "(p.project_name LIKE ? OR p.reference_no LIKE ? OR p.client_name LIKE ?)";
        $kw = '%' . $filterKeyword . '%';
        $params[] = $kw;
        $params[] = $kw;
        $params[] = $kw;
    }

    if ($filterStatus !== '') {
        $conditions[] = "p.status_id = ?";
        $params[] = $filterStatus;
    }

    if ($filterCommenceFrom !== '') {
        $conditions[] = "p.commence_date >= ?";
        $params[] = $filterCommenceFrom;
    }

    if ($filterCommenceTo !== '') {
        $conditions[] = "p.commence_date <= ?";
        $params[] = $filterCommenceTo;
    }

    if (!empty($conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY p.id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $projects = [];
    $message = 'Error loading projects: ' . $e->getMessage();
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 1.5rem 1rem;
            box-sizing: border-box;
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
        .admin-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .admin-table td .action-buttons a {
            padding: 0.35rem 0.7rem;
            font-size: 0.85rem;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            box-shadow: var(--shadow);
            border-radius: 0.5rem;
            overflow: hidden;
            table-layout: auto;
        }
        .admin-table thead {
            background: var(--primary-color);
            color: var(--white);
        }
        .admin-table th {
            padding: 0.6rem 0.75rem;
            text-align: left;
            font-weight: 600;
            line-height: 1.3;
        }
        .admin-table td {
            padding: 0.6rem 0.75rem;
            border-bottom: 1px solid var(--border-color);
            line-height: 1.3;
        }
        .image-thumb {
            width: 64px;
            height: 48px;
            object-fit: cover;
            border-radius: 0.3rem;
            border: 1px solid var(--border-color);
            background: var(--bg-light);
        }
        .admin-table tbody tr:hover {
            background: var(--bg-light);
        }
        .admin-table tbody tr:last-child td {
            border-bottom: none;
        }
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.875rem;
            display: inline-block;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-edit {
            background: var(--primary-color);
            color: var(--white);
        }
        .btn-edit:hover {
            background: var(--secondary-color);
        }
        .btn-delete {
            background: var(--error-color);
            color: var(--white);
        }
        .btn-delete:hover {
            background: #dc2626;
        }
        .btn-add {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.75rem 1.5rem;
        }
        .btn-add:hover {
            background: var(--secondary-color);
        }
        .btn-logout {
            background: var(--text-color);
            color: var(--white);
            padding: 0.75rem 1.5rem;
        }
        .btn-logout:hover {
            background: var(--secondary-color);
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
        .text-center {
            text-align: center;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .filter-card {
            background: var(--white);
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }
        .filter-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
        .admin-table th:nth-child(2),
        .admin-table td:nth-child(2) {
            min-width: 200px;
        }
        .filter-field label {
            display: block;
            margin-bottom: 0.35rem;
            font-weight: 600;
            color: var(--text-color);
        }
        .filter-field input,
        .filter-field select {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.95rem;
        }
        .filter-field input:focus,
        .filter-field select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .filter-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        .btn-filter {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.65rem 1.2rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-filter:hover {
            background: var(--secondary-color);
        }
        .btn-reset {
            background: var(--text-light);
            color: var(--white);
            padding: 0.65rem 1.2rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .btn-reset:hover {
            background: var(--text-color);
        }
        .featured-hint {
            margin: 0 0 0.5rem 0;
            font-size: 0.95rem;
            color: var(--text-light);
        }
        .featured-count {
            font-weight: 600;
            color: var(--primary-color);
        }
        .id-sort {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .id-sort-btn {
            border: none;
            background: transparent;
            color: inherit;
            cursor: pointer;
            padding: 0;
            display: inline-flex;
            align-items: center;
        }
        .id-sort-btn:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-tasks"></i> Project Management</h1>
            <div class="admin-actions">
                <a href="add_project.php" class="btn btn-action btn-add">
                    <i class="fas fa-plus"></i> Add New Project
                </a>
                <a href="logout.php" class="btn btn-action btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="get" class="filter-card">
            <div class="filter-grid">
                <div class="filter-field">
                    <label for="q">Search (Name / Reference / Client)</label>
                    <input type="text" id="q" name="q" placeholder="e.g. Kuantan or GPRSB" value="<?php echo htmlspecialchars($filterKeyword); ?>">
                </div>
                <div class="filter-field">
                    <label for="status_id">Status</label>
                    <select id="status_id" name="status_id">
                        <option value="">All statuses</option>
                        <?php foreach ($statusOptions as $status): ?>
                            <option value="<?php echo $status['status_id']; ?>" <?php echo ($filterStatus !== '' && $filterStatus == $status['status_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['status_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-field">
                    <label for="commence_from">Commence From</label>
                    <input type="date" id="commence_from" name="commence_from" value="<?php echo htmlspecialchars($filterCommenceFrom); ?>">
                </div>
                <div class="filter-field">
                    <label for="commence_to">Commence To</label>
                    <input type="date" id="commence_to" name="commence_to" value="<?php echo htmlspecialchars($filterCommenceTo); ?>">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
                <a href="index.php" class="btn-reset"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </form>

        <form method="post" style="overflow-x: auto;">
            <p class="featured-hint">Select up to <span class="featured-count">3</span> projects to feature. Save to apply to the frontend.</p>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>
                            <span class="id-sort">
                                <button type="button" class="id-sort-btn" id="idSortBtn" aria-label="Sort by ID">
                                    <i class="fas fa-sort"></i>
                                </button>
                                <span>ID</span>
                            </span>
                        </th>
                        <th>Project Name</th>
                        <th>Image</th>
                        <th>Reference No</th>
                        <th>Contract Value</th>
                        <th>Commence Date</th>
                        <th>Completion Date</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Show (max 3)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="11" class="text-center">No projects found. <a href="add_project.php">Add your first project</a></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['id']); ?></td>
                                <td><?php echo htmlspecialchars($project['project_name']); ?></td>
                                <td>
                                    <?php 
                                        $primaryImage = getPrimaryImagePath($project, $imageBaseWeb, $imageBaseFs);
                                        if ($primaryImage): ?>
                                        <img src="<?php echo htmlspecialchars($primaryImage); ?>" alt="Project image" class="image-thumb">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($project['reference_no'] ?: '-'); ?></td>
                                <td>RM<?php echo number_format((float)$project['contract_value'], 2); ?></td>
                                <td><?php echo $project['commence_date'] ? date('M-y', strtotime($project['commence_date'])) : '-'; ?></td>
                                <td><?php echo $project['completion_date'] ? date('M-y', strtotime($project['completion_date'])) : '-'; ?></td>
                                <td><?php echo htmlspecialchars($project['client_name']); ?></td>
                                <td><?php echo htmlspecialchars($project['status_name'] ?: $project['status_code']); ?></td>
                                <td>
                                    <input type="checkbox"
                                           class="featured-checkbox"
                                           name="featured_ids[]"
                                           value="<?php echo $project['id']; ?>"
                                           aria-label="Select project <?php echo htmlspecialchars($project['project_name']); ?> to feature"
                                           <?php echo in_array((int)$project['id'], $featuredIds, true) ? 'checked' : ''; ?> />
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $project['id']; ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this project?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="filter-actions" style="margin-top: 1rem;">
                <button type="submit" name="save_featured" class="btn-filter">
                    <i class="fas fa-save"></i> Save Featured
                </button>
            </div>
        </form>
    </div>
</body>
<script>
// Limit featured checkboxes to max 3 and disable others when limit reached
(function() {
    const checkboxes = Array.from(document.querySelectorAll('.featured-checkbox'));
    const maxSelected = 3;

    function applyLimit() {
        const checked = checkboxes.filter(cb => cb.checked);
        const disableOthers = checked.length >= maxSelected;
        checkboxes.forEach(cb => {
            if (!cb.checked) {
                cb.disabled = disableOthers;
            } else {
                cb.disabled = false;
            }
        });
    }

    function handleChange(e) {
        const checked = checkboxes.filter(cb => cb.checked);
        if (checked.length > maxSelected) {
            e.target.checked = false;
            alert('You can select up to ' + maxSelected + ' projects.');
        }
        applyLimit();
    }

    checkboxes.forEach(cb => cb.addEventListener('change', handleChange));
    applyLimit();
})();

// Client-side sort by ID (toggle asc/desc/off)
(function() {
    const sortBtn = document.getElementById('idSortBtn');
    if (!sortBtn) return;
    const icon = sortBtn.querySelector('i');
    const tbody = document.querySelector('.admin-table tbody');
    if (!tbody || !icon) return;

    let order = ''; // '', 'asc', 'desc'

    function setIcon() {
        icon.classList.remove('fa-sort', 'fa-sort-up', 'fa-sort-down');
        if (order === 'asc') icon.classList.add('fa-sort-up');
        else if (order === 'desc') icon.classList.add('fa-sort-down');
        else icon.classList.add('fa-sort');
    }

    function sortRows() {
        if (!order) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const sorted = rows.sort((a, b) => {
            const aId = parseInt(a.querySelector('td')?.textContent || '0', 10);
            const bId = parseInt(b.querySelector('td')?.textContent || '0', 10);
            if (isNaN(aId) || isNaN(bId)) return 0;
            return order === 'asc' ? aId - bId : bId - aId;
        });
        sorted.forEach(row => tbody.appendChild(row));
    }

    sortBtn.addEventListener('click', () => {
        if (order === '') order = 'asc';
        else if (order === 'asc') order = 'desc';
        else order = '';
        setIcon();
        sortRows();
    });

    setIcon();
})();
</script>
</html>



