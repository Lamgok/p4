<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Todo - Aplikasi Todolist</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --success: #10b981;
            --warning: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        
        body {
            background-color: #ffffff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--gray-900);
        }
        
        .detail-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .detail-header {
            background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
            color: white;
            padding: 2rem 2.5rem;
            position: relative;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .todo-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
            color: white;
        }
        
        .todo-content {
            padding: 2.5rem;
        }
        
        .todo-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1.2;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--gray-100);
        }
        
        .todo-description {
            font-size: 1.125rem;
            line-height: 1.7;
            color: var(--gray-600);
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid var(--primary);
            margin-bottom: 2rem;
        }
        
        .meta-section {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .meta-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .meta-card {
            background: white;
            padding: 1.25rem;
            border-radius: 8px;
            border: 1px solid var(--gray-200);
            transition: all 0.2s ease;
        }
        
        .meta-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .meta-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .meta-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-900);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
        }
        
        .btn-edit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-edit:hover {
            background: #4338ca;
            transform: translateY(-1px);
            color: white;
        }
        
        @media (max-width: 768px) {
            .detail-header {
                padding: 1.5rem;
            }
            
            .todo-content {
                padding: 1.5rem;
            }
            
            .todo-title {
                font-size: 1.75rem;
            }
            
            .meta-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4 min-vh-100 d-flex align-items-center justify-content-center">
        <div class="detail-container w-100">
            <!-- Header -->
            <div class="detail-header">
                <div class="header-content d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h1 class="h4 mb-2 fw-bold">Detail Todo</h1>
                        <?php if ($todo): ?>
                            <div class="todo-status">
                                <?php if ($todo['is_finished'] === 't'): ?>
                                    <span>✓</span>
                                    <span>Selesai</span>
                                <?php else: ?>
                                    <span>⏳</span>
                                    <span>Belum Selesai</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="index.php" class="back-btn">
                        <span>←</span>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <!-- Content -->
            <div class="todo-content">
                <?php if ($todo): ?>
                    <!-- Title -->
                    <h1 class="todo-title">
                        <?= htmlspecialchars($todo['title']) ?>
                    </h1>

                    <!-- Description -->
                    <div class="todo-description">
                        <?= nl2br(htmlspecialchars($todo['description'] ?: 'Tidak ada deskripsi')) ?>
                    </div>

                    <!-- Metadata -->
                    <div class="meta-section">
                        <h3 class="meta-title">
                            <span>📋</span>
                            Informasi Todo
                        </h3>
                        <div class="meta-grid">
                            <div class="meta-card">
                                <div class="meta-label">
                                    <span>📅</span>
                                    Dibuat
                                </div>
                                <div class="meta-value">
                                    <?= date('d F Y - H:i', strtotime($todo['created_at'])) ?>
                                </div>
                            </div>
                            <div class="meta-card">
                                <div class="meta-label">
                                    <span>🔄</span>
                                    Diupdate Terakhir
                                </div>
                                <div class="meta-value">
                                    <?= date('d F Y - H:i', strtotime($todo['updated_at'])) ?>
                                </div>
                            </div>
                            <div class="meta-card">
                                <div class="meta-label">
                                    <span>🏷️</span>
                                    Status
                                </div>
                                <div class="meta-value">
                                    <?= $todo['is_finished'] === 't' ? 'Selesai' : 'Belum Selesai' ?>
                                </div>
                            </div>
                            <div class="meta-card">
                                <div class="meta-label">
                                    <span>⏱️</span>
                                    Durasi
                                </div>
                                <div class="meta-value">
                                    <?php
                                    $created = new DateTime($todo['created_at']);
                                    $updated = new DateTime($todo['updated_at']);
                                    $interval = $created->diff($updated);
                                    echo $interval->format('%a hari, %h jam, %i menit');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <!-- <a href="index.php?page=detail&id=<?= $todo['id'] ?>" class="btn-edit">
                            ✏️ Edit Todo -->
                        </a>
                        <a href="index.php" class="back-btn" style="background: var(--gray-100); color: var(--gray-700);">
                            📋 Lihat Semua Todo
                        </a>
                    </div>

                <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <h3 class="text-muted mb-3">Todo Tidak Ditemukan</h3>
                        <p class="text-muted mb-4">Todo yang Anda cari tidak ditemukan atau mungkin telah dihapus.</p>
                        <a href="index.php" class="back-btn" style="background: var(--primary); color: white;">
                            ← Kembali ke Daftar Todo
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
</body>
</html>