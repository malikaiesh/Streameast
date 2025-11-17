<?php
require_once '../config/config.php';
Security::requireAdmin();

$db = Database::getInstance();
$video = new Video();

// Get statistics
$totalVideos = $db->fetchOne("SELECT COUNT(*) as count FROM videos")['count'];
$totalViews = $db->fetchOne("SELECT SUM(views) as total FROM videos")['total'] ?? 0;
$totalCategories = $db->fetchOne("SELECT COUNT(*) as count FROM categories")['count'];
$totalTags = $db->fetchOne("SELECT COUNT(*) as count FROM tags")['count'];

// Get video type distribution
$videoTypes = $db->fetchAll("SELECT video_type, COUNT(*) as count FROM videos GROUP BY video_type");

// Get top categories by video count
$topCategories = $db->fetchAll("
    SELECT c.name, COUNT(v.id) as count 
    FROM categories c 
    LEFT JOIN videos v ON c.id = v.category_id 
    GROUP BY c.id, c.name 
    ORDER BY count DESC 
    LIMIT 8
");

// Get views trend for last 7 days
$viewsTrend = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dayLabel = date('M d', strtotime("-$i days"));
    $views = $db->fetchOne("
        SELECT COALESCE(SUM(views), 0) as total 
        FROM videos 
        WHERE DATE(created_at) <= ?
    ", [$date])['total'] ?? 0;
    
    $viewsTrend[] = [
        'date' => $dayLabel,
        'views' => $views
    ];
}

$recentVideos = $video->getAll(1, 5);

include 'views/header.php';
?>

<!-- Modern Stats Cards -->
<div class="stats-grid">
    <div class="stat-card gradient-purple">
        <div class="stat-icon">🎥</div>
        <div class="stat-details">
            <h3><?= number_format($totalVideos) ?></h3>
            <p>Total Videos</p>
        </div>
        <div class="stat-change">+12%</div>
    </div>
    
    <div class="stat-card gradient-blue">
        <div class="stat-icon">👁️</div>
        <div class="stat-details">
            <h3><?= number_format($totalViews) ?></h3>
            <p>Total Views</p>
        </div>
        <div class="stat-change">+24%</div>
    </div>
    
    <div class="stat-card gradient-green">
        <div class="stat-icon">📁</div>
        <div class="stat-details">
            <h3><?= number_format($totalCategories) ?></h3>
            <p>Categories</p>
        </div>
        <div class="stat-change">Active</div>
    </div>
    
    <div class="stat-card gradient-pink">
        <div class="stat-icon">🏷️</div>
        <div class="stat-details">
            <h3><?= number_format($totalTags) ?></h3>
            <p>Total Tags</p>
        </div>
        <div class="stat-change">Active</div>
    </div>
</div>

<!-- Views Trend Chart -->
<div class="chart-section">
    <div class="chart-header">
        <h2>📈 Views Trend</h2>
        <div class="chart-filters">
            <button class="filter-btn active" data-period="7d">7D</button>
            <button class="filter-btn" data-period="15d">15D</button>
            <button class="filter-btn" data-period="30d">30D</button>
            <button class="filter-btn" data-period="3m">3M</button>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="viewsTrendChart"></canvas>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <!-- Top Categories Chart -->
    <div class="chart-section">
        <div class="chart-header">
            <h2>📊 Top Categories</h2>
            <span class="chart-subtitle">By Video Count</span>
        </div>
        <div class="chart-container">
            <canvas id="categoriesChart"></canvas>
        </div>
    </div>
    
    <!-- Content Type Distribution -->
    <div class="chart-section">
        <div class="chart-header">
            <h2>🎬 Content Overview</h2>
            <span class="chart-subtitle">Distribution by Type</span>
        </div>
        <div class="chart-container">
            <canvas id="contentTypeChart"></canvas>
        </div>
    </div>
</div>

<!-- Recent Videos Table -->
<div class="dashboard-section">
    <h2>Recent Videos</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Views</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentVideos as $v): ?>
                <tr>
                    <td><?= Security::output($v['title']) ?></td>
                    <td><span class="badge"><?= ucfirst($v['video_type']) ?></span></td>
                    <td><?= number_format($v['views']) ?></td>
                    <td><?= date('M d, Y', strtotime($v['created_at'])) ?></td>
                    <td>
                        <a href="edit-video.php?id=<?= $v['id'] ?>" class="btn-small">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Views Trend Chart
const viewsTrendCtx = document.getElementById('viewsTrendChart').getContext('2d');
const viewsTrendChart = new Chart(viewsTrendCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($viewsTrend, 'date')) ?>,
        datasets: [{
            label: 'Total Views',
            data: <?= json_encode(array_column($viewsTrend, 'views')) ?>,
            borderColor: '#a78bfa',
            backgroundColor: 'rgba(167, 139, 250, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#a78bfa',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#a78bfa',
                borderWidth: 1
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.05)'
                },
                ticks: {
                    color: '#9ca3af'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#9ca3af'
                }
            }
        }
    }
});

// Top Categories Chart
const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
const categoriesChart = new Chart(categoriesCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($topCategories, 'name')) ?>,
        datasets: [{
            label: 'Videos',
            data: <?= json_encode(array_column($topCategories, 'count')) ?>,
            backgroundColor: [
                'rgba(167, 139, 250, 0.8)',
                'rgba(96, 165, 250, 0.8)',
                'rgba(52, 211, 153, 0.8)',
                'rgba(251, 146, 60, 0.8)',
                'rgba(244, 114, 182, 0.8)',
                'rgba(168, 85, 247, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(34, 197, 94, 0.8)'
            ],
            borderRadius: 8,
            borderSkipped: false
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.05)'
                },
                ticks: {
                    color: '#9ca3af'
                }
            },
            y: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#9ca3af'
                }
            }
        }
    }
});

// Content Type Distribution Chart
const contentTypeCtx = document.getElementById('contentTypeChart').getContext('2d');
const contentTypeChart = new Chart(contentTypeCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($videoTypes, 'video_type')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($videoTypes, 'count')) ?>,
            backgroundColor: [
                'rgba(96, 165, 250, 0.8)',
                'rgba(52, 211, 153, 0.8)',
                'rgba(167, 139, 250, 0.8)',
                'rgba(251, 146, 60, 0.8)'
            ],
            borderColor: '#1e293b',
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#9ca3af',
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            }
        },
        cutout: '65%'
    }
});
</script>

<?php include 'views/footer.php'; ?>
