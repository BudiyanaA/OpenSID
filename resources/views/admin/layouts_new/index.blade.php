
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .brand h4 {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1.3rem;
        }

        .brand p {
            font-size: 0.85rem;
            opacity: 0.8;
            margin: 0;
        }

        .menu-list {
            padding: 15px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 4px solid #60a5fa;
        }

        .menu-item i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
        }

        .admin-account {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .admin-info h6 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .admin-info p {
            margin: 0;
            font-size: 0.75rem;
            opacity: 0.7;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        /* Header */
        .header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .breadcrumb {
            margin: 0;
            background: none;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #1e40af;
            text-decoration: none;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 8px 35px 8px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            width: 250px;
            font-size: 0.9rem;
        }

        .search-box i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #f3f4f6;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .icon-btn:hover {
            background: #e5e7eb;
        }

        .icon-btn i {
            color: #374151;
            font-size: 1.1rem;
        }

        .badge-notification {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid white;
        }

        /* Content */
        .content {
            padding: 30px;
        }

        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-title h2 {
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .current-date {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .current-date i {
            margin-right: 5px;
            color: #1e40af;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-card.blue { border-left-color: #3b82f6; }
        .stat-card.green { border-left-color: #10b981; }
        .stat-card.orange { border-left-color: #f59e0b; }
        .stat-card.purple { border-left-color: #8b5cf6; }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.blue { background: #dbeafe; color: #3b82f6; }
        .stat-icon.green { background: #d1fae5; color: #10b981; }
        .stat-icon.orange { background: #fed7aa; color: #f59e0b; }
        .stat-icon.purple { background: #ede9fe; color: #8b5cf6; }

        .stat-info h6 {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .stat-change {
            font-size: 0.85rem;
            color: #10b981;
        }

        .stat-change.negative {
            color: #ef4444;
        }

        /* Charts */
        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .chart-header {
            margin-bottom: 20px;
        }

        .chart-header h5 {
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Sections */
        .section-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
        }

        .section-header h5 {
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .view-all {
            color: #1e40af;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        /* Pengaduan List */
        .complaint-item {
            padding: 15px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: start;
            gap: 15px;
        }

        .complaint-item:last-child {
            border-bottom: none;
        }

        .complaint-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #fef3c7;
            color: #f59e0b;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .complaint-content {
            flex: 1;
        }

        .complaint-content h6 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #1f2937;
        }

        .complaint-content p {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .complaint-meta {
            display: flex;
            gap: 15px;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-badge.label-info {
            background: #fef3c7;
            color: #f59e0b;
        }

        .status-badge.label-warning {
            background: #dbeafe;
            color: #3b82f6;
        }

        .status-badge.label-success {
            background: #d1fae5;
            color: #10b981;
        }

        /* Project List */
        .project-item {
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .project-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .project-header h6 {
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .project-budget {
            background: #f0fdf4;
            color: #16a34a;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .project-info {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .progress {
            height: 8px;
            margin-bottom: 10px;
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .project-footer {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #6b7280;
        }

        /* Agenda List */
        .agenda-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            border-left: 3px solid #3b82f6;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .agenda-date {
            text-align: center;
            min-width: 60px;
        }

        .agenda-date .day {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e40af;
            line-height: 1;
        }

        .agenda-date .month {
            font-size: 0.8rem;
            color: #6b7280;
            text-transform: uppercase;
        }

        .agenda-content h6 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #1f2937;
        }

        .agenda-content p {
            font-size: 0.85rem;
            color: #6b7280;
            margin: 0;
        }

        .agenda-time {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
            color: #9ca3af;
            margin-top: 8px;
        }

        /* Menu Cards */
        .menu-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .menu-card {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            padding: 30px;
            border-radius: 12px;
            color: white;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.3);
        }

        .menu-card.green {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }

        .menu-card.orange {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        }

        .menu-card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .menu-card h5 {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .menu-card p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        /* Footer */
        .app-version {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .app-version p {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .app-version strong {
            color: #1e40af;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }

            .main-content {
                margin-left: 0;
            }

            .charts-row {
                grid-template-columns: 1fr;
            }

            .search-box input {
                width: 150px;
            }
        }

            .menu-item-wrapper {
        position: relative;
    }

    .menu-item.has-submenu {
        display: flex;
        justify-content: space-between;
    }

    .submenu-arrow {
        font-size: 0.8rem;
        transition: transform 0.3s;
    }

    .menu-item.has-submenu.active .submenu-arrow {
        transform: rotate(180deg);
    }

    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: rgba(0, 0, 0, 0.1);
    }

    .submenu.show {
        max-height: 500px;
    }

    .submenu-item {
        padding: 10px 20px 10px 50px;
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.3s;
        font-size: 0.9rem;
    }

    .submenu-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: white;
    }

    .submenu-item i {
        margin-right: 10px;
        font-size: 0.9rem;
        width: 16px;
    }
    </style>
</head>
<body>
    @include('admin.layouts_new.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content">

        @include('admin.layouts_new.partials.header')

        <!-- Content -->
        <div class="content">
            <!-- Page Title -->
            <div class="page-title">
                @yield('title')
                <div class="current-date">
                    <i class="bi bi-calendar3"></i>
                    <span id="currentDate"></span>
                </div>
            </div>

            @yield('content')

            @include('admin.pengaturan.pengaturan_modal')

            @include('admin.layouts_new.partials.footer')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>