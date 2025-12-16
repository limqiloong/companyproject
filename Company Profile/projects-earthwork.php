<?php
require_once 'config.php';
$pageTitle = 'Projects';
include 'header.php';

// Default category label; can be overridden by wrapper files (e.g., mining page)
if (!isset($category)) {
$category = 'earthwork';
}

// Function to determine if a project belongs to a category
function isProjectInCategory($project, $category) {
    if (empty($category)) {
        return true; // Show all if no category filter
    }
    
    // Handle both 'name' (for hardcoded) and 'project_name' (for database)
    $projectName = isset($project['project_name']) ? strtolower($project['project_name']) : (isset($project['name']) ? strtolower($project['name']) : '');
    $projectRef = isset($project['reference_no']) ? strtolower($project['reference_no']) : (isset($project['ref']) ? strtolower($project['ref']) : '');
    
    if ($category === 'earthwork') {
        // Earthwork, Infrastructure & Transportation projects
        $keywords = ['earthwork', 'infrastructure', 'drainage', 'flood', 'levee', 'river', 'road', 'construction', 'civil works', 'transportation', 'supply', 'pembangunan', 'kerja-kerja tanah', 'plsb', 'rtb', 'solar', 'perumahan', 'kedai', 'pangsapuri', 'hotel', 'laluan'];
        foreach ($keywords as $keyword) {
            if (strpos($projectName, $keyword) !== false || strpos($projectRef, $keyword) !== false) {
                return true;
            }
        }
        return false;
    } elseif ($category === 'mining') {
        // Mining projects
        $keywords = ['mining', 'bauxite', 'tin', 'manganese', 'iron', 'ore', 'mineral', 'quarry', 'batu', 'pasir', 'sand', 'stone'];
        foreach ($keywords as $keyword) {
            if (strpos($projectName, $keyword) !== false || strpos($projectRef, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    return true; // Default: show all
}

// Helper functions to fetch projects by status from DB and map to display structure
function fetchProjectsByStatus($mysqli, $statusCode) {
    $data = [];
    if ($stmt = $mysqli->prepare("
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
        WHERE s.status_code = ?
        ORDER BY p.id DESC
    ")) {
        $stmt->bind_param('s', $statusCode);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
    }
    return $data;
}

function mapDbProjectToDisplay($row) {
    return [
        'id' => $row['id'],
        'name' => $row['project_name'],
        'ref' => $row['reference_no'] ?: '-',
        'value' => $row['contract_value'],
        'start' => $row['commence_date'] ? date('M-y', strtotime($row['commence_date'])) : '-',
        'end' => $row['completion_date'] ? date('M-y', strtotime($row['completion_date'])) : '-',
        'client' => $row['client_name'],
        'icon' => 'fa-briefcase',
        'image_files' => $row['image_files'] ?? ''
    ];
}

// Helper: limit displayed items (e.g., show top 3 on page)
function limitProjects(array $items, $limit = 3) {
    return array_slice($items, 0, $limit);
}

function formatCurrency($value) {
    return 'RM' . number_format((float)str_replace(',', '', $value), 2, '.', ',');
}

// Function to check if project has images and get image paths
function getProjectImages($projectId = null, $referenceNo = null, $isFromDB = false, $imageList = null) {
    $imagesDir = 'assets/images/projects/';
    $images = [];

    // Priority: explicit list from DB column (comma separated paths or filenames)
    if (!empty($imageList)) {
        $entries = array_filter(array_map('trim', explode(',', $imageList)));
        foreach ($entries as $entry) {
            if ($entry === '') continue;
            if (preg_match('~^https?://~i', $entry) || strpos($entry, '/') === 0 || strpos($entry, 'assets/') === 0) {
                $images[] = $entry;
            } else {
                $images[] = $imagesDir . $entry;
            }
        }
    }

    // Fallback: auto-detect by id
    if (empty($images) && $isFromDB && $projectId) {
        // For database projects: project_{id}_{index}.jpg
        $index = 1;
        while (file_exists($imagesDir . "project_{$projectId}_{$index}.jpg") || 
               file_exists($imagesDir . "project_{$projectId}_{$index}.jpeg") ||
               file_exists($imagesDir . "project_{$projectId}_{$index}.png")) {
            $extensions = ['jpg', 'jpeg', 'png'];
            foreach ($extensions as $ext) {
                $filePath = $imagesDir . "project_{$projectId}_{$index}.{$ext}";
                if (file_exists($filePath)) {
                    $images[] = $filePath;
                    break;
                }
            }
            $index++;
        }
    } elseif (empty($images) && $referenceNo) {
        // For hardcoded projects: sanitize reference number
        $sanitizedRef = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $referenceNo);
        $sanitizedRef = preg_replace('/-+/', '-', $sanitizedRef); // Replace multiple dashes with single
        $sanitizedRef = trim($sanitizedRef, '-');
        
        $index = 1;
        while (file_exists($imagesDir . "{$sanitizedRef}_{$index}.jpg") || 
               file_exists($imagesDir . "{$sanitizedRef}_{$index}.jpeg") ||
               file_exists($imagesDir . "{$sanitizedRef}_{$index}.png")) {
            $extensions = ['jpg', 'jpeg', 'png'];
            foreach ($extensions as $ext) {
                $filePath = $imagesDir . "{$sanitizedRef}_{$index}.{$ext}";
                if (file_exists($filePath)) {
                    $images[] = $filePath;
                    break;
                }
            }
            $index++;
        }
    }
    
    return $images;
}

// 1. Connect to MySQL
$mysqli = new mysqli('127.0.0.1', 'root', '', 'company');
if ($mysqli->connect_errno) {
    die('Failed to connect MySQL: ' . $mysqli->connect_error);
}

// 2. Load projects by status from DB
$projects = fetchProjectsByStatus($mysqli, 'COMPLETED'); // completed
$ongoingDb = fetchProjectsByStatus($mysqli, 'ONGOING');
$futureDb = fetchProjectsByStatus($mysqli, 'FUTURE');

// Map DB rows for ongoing/future display
$ongoingProjects = array_map('mapDbProjectToDisplay', $ongoingDb);
$futureProjects = array_map('mapDbProjectToDisplay', $futureDb);

// Load featured projects (max 3) to surface first (featured flag in project table)
$featuredIds = [];
if ($featuredResult = $mysqli->query("SELECT id FROM project WHERE featured = 1 ORDER BY id ASC LIMIT 3")) {
    while ($r = $featuredResult->fetch_assoc()) {
        $featuredIds[] = (int)$r['id'];
    }
}

function orderProjectsByFeatured(array $items, array $featuredIds, $useMapped = false) {
    if (empty($featuredIds)) return $items;
    $byId = [];
    foreach ($items as $item) {
        $id = $useMapped ? ($item['id'] ?? null) : ($item['id'] ?? null);
        if ($id !== null) {
            $byId[(int)$id] = $item;
        }
    }
    $ordered = [];
    foreach ($featuredIds as $fid) {
        if (isset($byId[$fid])) {
            $ordered[] = $byId[$fid];
            unset($byId[$fid]);
}
    }
    // Append remaining in original order
    foreach ($items as $item) {
        $id = $useMapped ? ($item['id'] ?? null) : ($item['id'] ?? null);
        if ($id !== null && isset($byId[(int)$id])) {
            $ordered[] = $item;
            unset($byId[(int)$id]);
        }
    }
    return $ordered;
}

// Reorder by featured then limit to 3 for display
$projects = orderProjectsByFeatured($projects, $featuredIds, false);
$ongoingProjects = orderProjectsByFeatured($ongoingProjects, $featuredIds, true);
$futureProjects = orderProjectsByFeatured($futureProjects, $featuredIds, true);

// Display only top 3 on page; full lists remain for modal
$displayCompleted = limitProjects($projects, 3);
$displayOngoing   = limitProjects($ongoingProjects, 3);
$displayFuture    = limitProjects($futureProjects, 3);

// Set category title
$categoryTitle = '';
if ($category === 'earthwork') {
$categoryTitle = 'Earthwork, Infrastructure & Transportation';
} elseif ($category === 'mining') {
    $categoryTitle = 'Mining';
}
$hideEmptySections = isset($hideEmptySections) ? (bool)$hideEmptySections : false;
$showProjectSections = !isset($disableProjectLists) || !$disableProjectLists;

?>

<section class="page-header">
    <div class="container">
        <h1><?php echo !empty($categoryTitle) ? $categoryTitle : 'Our Services'; ?></h1>
        <?php if (!empty($category)): ?>
            <p>Viewing services in the <?php echo htmlspecialchars($categoryTitle); ?> category</p>
        <?php endif; ?>
    </div>
</section>

<section class="projects-content">
    <div class="container">
        <div class="projects-intro">
            <?php if (!empty($category)): ?>
                <?php if ($category === 'earthwork'): ?>
                    <p>Learn more about our earthwork, infrastructure and transportation services. <?php echo SITE_NAME; ?> delivers integrated site prep, infrastructure build-out, haulage, and material supply across Malaysia.</p>
                <?php else: ?>
                    <p>Learn more about our services in the <?php echo htmlspecialchars($categoryTitle); ?> category. <?php echo SITE_NAME; ?> delivers integrated solutions with a proven track record across Malaysia.</p>
                <?php endif; ?>
            <?php else: ?>
                <p>Over the past 21 years, <?php echo SITE_NAME; ?> has delivered comprehensive services across Malaysia, specializing in earthwork, infrastructure development, and resource extraction. Our commitment to quality and excellence has earned the trust of clients across various industries.</p>
            <?php endif; ?>
        </div>

        <?php if ($category === 'mining' && !empty($customMiningContent)): ?>
            <?php echo $customMiningContent; ?>
        <?php endif; ?>

        <?php if ($category === 'earthwork'): ?>
        <!-- Business Model Section for Earthwork & Infrastructure -->
        <div class="business-model-section">
            <h2 class="section-title">Business Model</h2>
            <div class="business-model-container">
                <div class="business-model-main">
                    <div class="business-model-header">
                        <h3>TSS CEMERLANG SDN BHD</h3>
                    </div>
                    <div class="business-model-grid">
                        <!-- Primary Business Functions -->
                        <div class="business-function">
                            <div class="function-header">
                                <i class="fas fa-recycle"></i>
                                <h4>Landfill Remediation</h4>
                            </div>
                        </div>

                        <div class="business-function">
                            <div class="function-header">
                                <i class="fas fa-boxes"></i>
                                <h4>Supply of Raw Materials</h4>
                            </div>
                            <div class="function-details">
                                <div class="detail-category">
                                    <div class="category-header own-source">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <strong>Own Source</strong>
                                    </div>
                                    <div class="category-items">
                                        <div class="category-item">Bukit Penggorak</div>
                                        <div class="category-item">Panching</div>
                                        <div class="category-item">Bukit Rangin</div>
                                    </div>
                                </div>
                                <div class="detail-category">
                                    <div class="category-header external-supplier">
                                        <i class="fas fa-truck-loading"></i>
                                        <strong>External Supplier</strong>
                                    </div>
                                    <div class="category-items">
                                        <div class="category-item">Armoured Rocks/Boulder</div>
                                        <div class="supplier-list">
                                            <div class="supplier-item">KBB Marketing Sdn Bhd</div>
                                            <div class="supplier-item">Hanson Quarry Products Sdn Bhd</div>
                                            <div class="supplier-item">Malaysian Rock Products Sdn Bhd</div>
                                            <div class="supplier-item">LCS Group of Companies</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="business-function">
                            <div class="function-header">
                                <i class="fas fa-tractor"></i>
                                <h4>Earthworks & Infrastructure</h4>
                            </div>
                        </div>

                        <div class="business-function">
                            <div class="function-header">
                                <i class="fas fa-tools"></i>
                                <h4>Rental of Machineries</h4>
                            </div>
                        </div>

                        <div class="business-function">
                            <div class="function-header">
                                <i class="fas fa-shipping-fast"></i>
                                <h4>Transportation</h4>
                            </div>
                            <div class="function-details">
                                <div class="category-items">
                                    <div class="category-item">Own Lorries</div>
                                    <div class="category-item">External Transporter</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($showProjectSections && (!$hideEmptySections || !empty($displayCompleted))): ?>
            <!-- Completed Projects Section -->
            <div class="projects-section">
                <h2 class="section-title">Completed Projects</h2>
                <div class="projects-list" id="completedProjectsList">
                <?php if (!empty($displayCompleted)): ?>
                    <?php 
                    $displayCount = 0;
                    foreach ($displayCompleted as $index => $p): 
                        $displayCount++;
                        $isHidden = $displayCount > 3 ? 'project-hidden' : '';
                        $commence = $p['commence_date']
                            ? date('M-y', strtotime($p['commence_date']))
                            : '-';
                        $completion = $p['completion_date']
                            ? date('M-y', strtotime($p['completion_date']))
                            : '-';
                        
                        $projectImages = getProjectImages($p['id'], null, true, $p['image_files'] ?? null);
                        $hasImages = !empty($projectImages);
                    ?>
                        <div class="project-item <?php echo $isHidden; ?>" data-index="<?php echo $index; ?>">
                            <div class="project-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="project-content">
                                <div class="project-title-wrapper">
                                    <h3><?php echo htmlspecialchars($p['project_name']); ?></h3>
                                    <button class="project-details-btn" type="button" aria-label="View more details" <?php echo $hasImages ? '' : 'disabled'; ?>>
                                            <i class="fas fa-images"></i>
                                        <span><?php echo $hasImages ? 'More Details' : 'No Images'; ?></span>
                                        </button>
                                </div>
                                <div class="project-details">
                                    <div class="detail-item">
                                        <i class="fas fa-file-alt"></i>
                                        <span><strong>Reference No:</strong> <?php echo htmlspecialchars($p['reference_no'] ?: '-'); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span><strong>Contract Value:</strong> RM<?php echo number_format((float)$p['contract_value'], 2); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><strong>Commencement Date:</strong> <?php echo $commence; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span><strong>Completion Date:</strong> <?php echo $completion; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-building"></i>
                                        <span><strong>Client:</strong> <?php echo htmlspecialchars($p['client_name']); ?></span>
                                    </div>
                                </div>
                                
                                    <div class="project-gallery" data-project-id="project-<?php echo $p['id']; ?>">
                                        <div class="project-gallery-grid">
                                        <?php if ($hasImages): ?>
                                            <?php foreach ($projectImages as $imgIndex => $imgPath): ?>
                                                <div class="project-gallery-item">
                                                    <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                                                         alt="Project image <?php echo $imgIndex + 1; ?>" 
                                                         loading="lazy"
                                                         class="project-gallery-image">
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="project-gallery-item">
                                                <span>No images available for this project.</span>
                                    </div>
                                <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php if (!empty($category)): ?>
                            No completed projects found in the <?php echo htmlspecialchars($categoryTitle); ?> category.
                        <?php else: ?>
                            No completed projects found.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                </div>
                <?php if (!empty($projects) && count($projects) > 3): ?>
                <div class="view-all-container">
                    <button class="btn btn-primary" id="viewAllCompleted">View All Completed Projects (<?php echo count($projects); ?>)</button>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>




        <?php if ($showProjectSections && (!$hideEmptySections || !empty($displayOngoing))): ?>
        <!-- Ongoing Projects Section -->
        <div class="projects-section">
            <h2 class="section-title">Ongoing Projects</h2>
            <div class="projects-list" id="ongoingProjectsList">
                <?php if (!empty($displayOngoing)): ?>
                <?php 
                foreach ($displayOngoing as $index => $project): 
                    $isHidden = $index >= 3 ? 'project-hidden' : '';
                    
                    // Check for images
                    $projectImages = getProjectImages($project['id'], null, true, $project['image_files'] ?? null);
                    $hasImages = !empty($projectImages);
                ?>
                <div class="project-item <?php echo $isHidden; ?>" data-index="<?php echo $index; ?>">
                    <div class="project-icon">
                        <i class="fas <?php echo $project['icon']; ?>"></i>
                    </div>
                    <div class="project-content">
                        <div class="project-title-wrapper">
                            <h3><?php echo htmlspecialchars($project['name']); ?></h3>
                            <button class="project-details-btn" type="button" aria-label="View more details" <?php echo $hasImages ? '' : 'disabled'; ?>>
                                    <i class="fas fa-images"></i>
                                <span><?php echo $hasImages ? 'More Details' : 'No Images'; ?></span>
                                </button>
                        </div>
                        <div class="project-details">
                            <div class="detail-item">
                                <i class="fas fa-file-alt"></i>
                                <span><strong>Reference No:</strong> <?php echo htmlspecialchars($project['ref']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-dollar-sign"></i>
                                <span><strong>Contract Value:</strong> <?php echo formatCurrency($project['value']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span><strong>Commencement Date:</strong> <?php echo htmlspecialchars($project['start']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar-check"></i>
                                <span><strong>Completion Date:</strong> <?php echo htmlspecialchars($project['end']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-building"></i>
                                <span><strong>Client:</strong> <?php echo htmlspecialchars($project['client']); ?></span>
                            </div>
                        </div>
                        
                            <div class="project-gallery" data-project-id="ongoing-<?php echo $index; ?>">
                                <div class="project-gallery-grid">
                                <?php if ($hasImages): ?>
                                    <?php foreach ($projectImages as $imgIndex => $imgPath): ?>
                                        <div class="project-gallery-item">
                                            <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                                                 alt="Project image <?php echo $imgIndex + 1; ?>" 
                                                 loading="lazy"
                                                 class="project-gallery-image">
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="project-gallery-item">
                                        <span>No images available for this project.</span>
                            </div>
                        <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php if (!empty($category)): ?>
                            No ongoing projects found in the <?php echo htmlspecialchars($categoryTitle); ?> category.
                        <?php else: ?>
                            No ongoing projects found.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($ongoingProjects) && count($ongoingProjects) > 3): ?>
            <div class="view-all-container">
                <button class="btn btn-primary" id="viewAllOngoing">View All Ongoing Projects (<?php echo count($ongoingProjects); ?>)</button>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($showProjectSections && (!$hideEmptySections || !empty($displayFuture))): ?>
        <!-- Future Projects Section -->
        <div class="projects-section">
            <h2 class="section-title">Future Projects</h2>
            <div class="projects-list" id="futureProjectsList">
                <?php if (!empty($displayFuture)): ?>
                <?php 
                foreach ($displayFuture as $index => $project): 
                    $isHidden = $index >= 3 ? 'project-hidden' : '';
                    
                    // Check for images
                    $projectImages = getProjectImages($project['id'], null, true, $project['image_files'] ?? null);
                    $hasImages = !empty($projectImages);
                ?>
                <div class="project-item <?php echo $isHidden; ?>" data-index="<?php echo $index; ?>">
                    <div class="project-icon">
                        <i class="fas <?php echo $project['icon']; ?>"></i>
                    </div>
                    <div class="project-content">
                        <div class="project-title-wrapper">
                            <h3><?php echo htmlspecialchars($project['name']); ?></h3>
                            <button class="project-details-btn" type="button" aria-label="View more details" <?php echo $hasImages ? '' : 'disabled'; ?>>
                                    <i class="fas fa-images"></i>
                                <span><?php echo $hasImages ? 'More Details' : 'No Images'; ?></span>
                                </button>
                        </div>
                        <div class="project-details">
                            <div class="detail-item">
                                <i class="fas fa-file-alt"></i>
                                <span><strong>Reference No:</strong> <?php echo htmlspecialchars($project['ref']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-dollar-sign"></i>
                                <span><strong>Contract Value:</strong> <?php echo formatCurrency($project['value']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span><strong>Commencement Date:</strong> <?php echo htmlspecialchars($project['start']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar-check"></i>
                                <span><strong>Completion Date:</strong> <?php echo htmlspecialchars($project['end']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-building"></i>
                                <span><strong>Client:</strong> <?php echo htmlspecialchars($project['client']); ?></span>
                            </div>
                        </div>
                        
                            <div class="project-gallery" data-project-id="future-<?php echo $index; ?>">
                                <div class="project-gallery-grid">
                                <?php if ($hasImages): ?>
                                    <?php foreach ($projectImages as $imgIndex => $imgPath): ?>
                                        <div class="project-gallery-item">
                                            <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                                                 alt="Project image <?php echo $imgIndex + 1; ?>" 
                                                 loading="lazy"
                                                 class="project-gallery-image">
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="project-gallery-item">
                                        <span>No images available for this project.</span>
                            </div>
                        <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php if (!empty($category)): ?>
                            No future projects found in the <?php echo htmlspecialchars($categoryTitle); ?> category.
                        <?php else: ?>
                            No future projects found.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($futureProjects) && count($futureProjects) > 3): ?>
            <div class="view-all-container">
                <button class="btn btn-primary" id="viewAllFuture">View All Future Projects (<?php echo count($futureProjects); ?>)</button>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

<!-- Projects Modal -->
<div class="projects-modal" id="projectsModal">
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h2 id="modalTitle">All Projects</h2>
            <button class="modal-close" id="modalClose" aria-label="Close modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Projects will be inserted here -->
        </div>
    </div>
</div>

<script>
// Projects Modal Functionality
(function() {
    'use strict';
    
    // Project data from PHP
    const ongoingProjectsData = <?php echo json_encode($ongoingProjects); ?>;
    const futureProjectsData = <?php echo json_encode($futureProjects); ?>;
    const completedProjectsData = <?php 
        // Format completed projects data for JavaScript
        $completedProjectsJS = [];
        foreach ($projects as $p) {
            $commence = $p['commence_date'] ? date('M-y', strtotime($p['commence_date'])) : '-';
            $completion = $p['completion_date'] ? date('M-y', strtotime($p['completion_date'])) : '-';
            $completedProjectsJS[] = [
                'name' => $p['project_name'],
                'ref' => $p['reference_no'] ?: '-',
                'value' => number_format((float)$p['contract_value'], 2, '.', ''),
                'start' => $commence,
                'end' => $completion,
                'client' => $p['client_name']
            ];
        }
        echo json_encode($completedProjectsJS);
    ?>;
    
    function formatCurrency(value) {
        const numValue = parseFloat(value.replace(/,/g, ''));
        return 'RM' + numValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    
    function createProjectHTML(project, index, hasIcon = true) {
        const icons = {
            'fa-solar-panel': 'fa-solar-panel',
            'fa-water': 'fa-water',
            'fa-tractor': 'fa-tractor',
            'fa-hammer': 'fa-hammer',
            'fa-landmark': 'fa-landmark',
            'fa-home': 'fa-home',
            'fa-store': 'fa-store',
            'fa-shield-alt': 'fa-shield-alt',
            'fa-building': 'fa-building',
            'fa-hotel': 'fa-hotel',
            'fa-road': 'fa-road'
        };
        
        const iconHTML = hasIcon ? `
            <div class="modal-project-icon">
                <i class="fas ${project.icon || 'fa-project-diagram'}"></i>
            </div>
        ` : '';
        
        return `
            <div class="modal-project-item">
                ${iconHTML}
                <div class="modal-project-content">
                    <h3>${project.name}</h3>
                    <div class="modal-project-details">
                        <div class="detail-item">
                            <i class="fas fa-file-alt"></i>
                            <span><strong>Reference No:</strong> ${project.ref}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-dollar-sign"></i>
                            <span><strong>Contract Value:</strong> ${formatCurrency(project.value)}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span><strong>Commencement Date:</strong> ${project.start}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-calendar-check"></i>
                            <span><strong>Completion Date:</strong> ${project.end}</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-building"></i>
                            <span><strong>Client:</strong> ${project.client}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    function openModal(projects, title, hasIcon = true) {
        const modal = document.getElementById('projectsModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        
        modalTitle.textContent = title;
        modalBody.innerHTML = '';
        
        projects.forEach(function(project, index) {
            modalBody.innerHTML += createProjectHTML(project, index, hasIcon);
        });
        
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal() {
        const modal = document.getElementById('projectsModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    function initProjectsModal() {
        const viewAllOngoing = document.getElementById('viewAllOngoing');
        const viewAllFuture = document.getElementById('viewAllFuture');
        const viewAllCompleted = document.getElementById('viewAllCompleted');
        const modal = document.getElementById('projectsModal');
        const modalClose = document.getElementById('modalClose');
        const modalOverlay = document.getElementById('modalOverlay');
        
        // Open Ongoing Projects Modal
        if (viewAllOngoing && ongoingProjectsData.length > 3) {
            viewAllOngoing.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(ongoingProjectsData, 'All Ongoing Projects', true);
            });
        } else if (viewAllOngoing) {
            viewAllOngoing.style.display = 'none';
        }
        
        // Open Future Projects Modal
        if (viewAllFuture && futureProjectsData.length > 3) {
            viewAllFuture.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(futureProjectsData, 'All Future Projects', true);
            });
        } else if (viewAllFuture) {
            viewAllFuture.style.display = 'none';
        }
        
        // Open Completed Projects Modal
        if (viewAllCompleted && completedProjectsData.length > 3) {
            viewAllCompleted.addEventListener('click', function(e) {
                e.preventDefault();
                openModal(completedProjectsData, 'All Completed Projects', false);
            });
        } else if (viewAllCompleted) {
            viewAllCompleted.style.display = 'none';
        }
        
        // Close modal events
        if (modalClose) {
            modalClose.addEventListener('click', closeModal);
        }
        
        if (modalOverlay) {
            modalOverlay.addEventListener('click', closeModal);
        }
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProjectsModal);
    } else {
        initProjectsModal();
    }
})();
</script>

<?php include 'footer.php'; ?>

