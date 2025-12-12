<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background-color: #f5f5f5;
        overflow-x: hidden;
    }

    /* Auth Styles */
    .auth-container {
        display: flex;
        min-height: 100vh;
    }

    .auth-left {
        flex: 1;
        background-color: #e8e8e8;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
    }



    .logo-icon {
        width: 80px;
        height: 80px;
        background-color: #22863a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .logo-text {
        font-size: 42px;
        color: #22863a;
        font-weight: 600;
    }



    .info-description {
        font-size: 16px;
        color: #666;
        text-align: center;
        max-width: 500px;
        line-height: 1.6;
    }

    .auth-right {
        flex: 1;
        background-color: #1e5128;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
    }

    .login-card {
        background: white;
        padding: 50px;
        border-radius: 12px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .login-title {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 30px;
        text-align: center;
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-input {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 15px;
        transition: border-color 0.3s;
    }

    .form-input:focus {
        outline: none;
        border-color: #22863a;
    }

    .btn-login {
        width: 100%;
        padding: 14px;
        background-color: #22863a;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-login:hover {
        background-color: #1a6b2e;
    }

    .login-footer {
        text-align: center;
        margin-top: 20px;
    }

    .login-link {
        color: #22863a;
        text-decoration: none;
    }

    .error-message {
        background-color: #fee;
        color: #c33;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    /* Dashboard Styles - FIXED */
    .dashboard-container {
        display: flex;
        min-height: 100vh;
        width: 100%;
    }

    .sidebar {
        width: 260px;
        background-color: #116b27;
        color: white;
        display: flex;
        flex-direction: column;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 1000;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .sidebar-header {
        padding: 30px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-logo {
        font-size: 24px;
        font-weight: 600;
        color: white;
        text-decoration: none;
    }

    .sidebar-welcome {
        font-size: 18px;
        padding: 20px;
        color: rgba(255, 255, 255, 0.9);
    }

    .sidebar-nav {
        flex: 1;
        padding: 20px 0;
    }

    .nav-item {
        display: block;
        padding: 20px 20px;
        color: white;
        text-decoration: none;
        transition: background-color 0.2s;
        font-size: 16px;
    }

    .nav-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-item.active {
        background-color: #22863a;
        border-left: 4px solid white;
    }

    .sidebar-footer {
        padding: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-logout {
        width: 100%;
        padding: 12px;
        background-color: white;
        color: #1e5128;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-logout:hover {
        background-color: #f0f0f0;
    }

    .copyright {
        text-align: center;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 15px;
    }

    /* Main Content - FIXED */
    .main-content {
        margin-left: 260px;
        width: calc(100% - 260px);
        min-height: 100vh;
        background-color: #f5f5f5;
    }

    .top-bar {
        background-color: #116b27;
        color: white;
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .top-bar-title {
        font-size: 18px;
        font-weight: 500;
    }

    .search-box {
        background: white;
        padding: 8px 16px;
        border-radius: 6px;
        border: 1px solid #ddd;
        font-size: 14px;
        width: 250px;
    }

    .content-area {
        padding: 40px;
        width: 100%;
        max-width: 100%;
    }

    /* Cards */
    .card {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        width: 100%;
    }

    .card-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
        width: 100%;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
    }

    .stat-card.green {
        border-left-color: #22863a;
    }

    .stat-card.blue {
        border-left-color: #0366d6;
    }

    .stat-card.red {
        border-left-color: #d73a49;
    }

    .stat-card.orange {
        border-left-color: #f9826c;
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        text-transform: uppercase;
    }

    /* Feature Cards */
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        width: 100%;
    }

    .feature-card {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .feature-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .feature-description {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background-color: #0366d6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0256c1;
    }

    .btn-success {
        background-color: #22863a;
        color: white;
    }

    .btn-success:hover {
        background-color: #1a6b2e;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-purple {
        background-color: #8b5cf6;
        color: white;
    }

    .btn-purple:hover {
        background-color: #7c3aed;
    }

    .btn-danger {
        background-color: #d73a49;
        color: white;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }

    .btn-add {
        background-color: #17a2b8;
        color: white;
    }

    .btn-add:hover {
        background-color: #138496;
    }

    /* Announcements */
    .announcements-section {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    .announcements-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .announcements-icon {
        font-size: 24px;
    }

    .announcements-title {
        font-size: 20px;
        font-weight: 600;
        color: #333;
    }

    .announcement-item {
        padding: 15px;
        border-left: 4px solid;
        margin-bottom: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }

    .announcement-item:nth-child(1) {
        border-left-color: #0366d6;
    }

    .announcement-item:nth-child(2) {
        border-left-color: #d73a49;
    }

    .announcement-item:nth-child(3) {
        border-left-color: #22863a;
    }

    .announcement-item:nth-child(4) {
        border-left-color: #f9826c;
    }

    .announcement-text {
        font-size: 14px;
        color: #333;
        line-height: 1.5;
    }

    /* Edit button styling */
    .btn-primary {
        background-color: #0366d6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0256c1;
    }

    /* Ensure buttons are inline */
    td form {
        display: inline;
    }

    /* Modal improvements */
    .modal-overlay {
        backdrop-filter: blur(5px);
    }

    .modal {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Show edited timestamp in italics */
    td .edited-time {
        font-size: 12px;
        color: #999;
        font-style: italic;
    }

    /* Welcome Section */
    .welcome-section {
        text-align: center;
        margin-bottom: 40px;
        width: 100%;
    }

    .welcome-title {
        font-size: 36px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .welcome-subtitle {
        font-size: 16px;
        color: #666;
        font-style: italic;
    }

    /* Voting Page */
    .election-header {
        text-align: center;
        margin-bottom: 30px;
        width: 100%;
    }

    .election-title {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .election-link {
        color: #0366d6;
        text-decoration: none;
    }

    .election-instruction {
        font-size: 14px;
        color: #666;
        margin-top: 10px;
    }

    .positions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        width: 100%;
    }

    .position-card {
        background: #e8e8e8;
        padding: 25px;
        border-radius: 8px;
    }

    .position-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .candidate-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        background: white;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .candidate-item:hover {
        background-color: #f8f9fa;
    }

    .candidate-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #0366d6;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .candidate-name {
        flex: 1;
        font-size: 16px;
        color: #333;
    }

    .candidate-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    /* Vote Summary */
    .vote-table {
        width: 100%;
        border-collapse: collapse;
    }

    .vote-table th,
    .vote-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #e8e8e8;
    }

    .vote-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #333;
        font-size: 14px;
        text-transform: uppercase;
    }

    .vote-table td {
        font-size: 15px;
        color: #333;
    }

    .voted-status {
        color: #22863a;
        font-weight: 600;
    }

    .voted-candidate {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Results Page */
    .results-header {
        text-align: center;
        margin-bottom: 30px;
        width: 100%;
    }

    .results-meta {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 15px;
        font-size: 14px;
        color: #666;
        flex-wrap: wrap;
    }

    .results-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .position-results {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        width: 100%;
    }

    .position-results-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .candidate-result {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .result-info {
        flex: 1;
    }

    .result-name {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background-color: #e8e8e8;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .progress-bar.first {
        background-color: #0366d6;
    }

    .progress-bar.second {
        background-color: #6c757d;
    }

    .progress-bar.third {
        background-color: #6c757d;
    }

    /* Real-Time Election Updates Styles */
    #total-votes {
        transition: all 0.3s ease;
        font-weight: 600;
        font-size: 16px;
        color: #333;
    }

    #total-votes.updating {
        color: #28a745;
        transform: scale(1.15);
    }

    #last-updated {
        font-weight: 600;
        color: #333;
        transition: color 0.3s ease;
    }

    #election-status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    #election-status-badge.active {
        background-color: #28a745;
        color: white;
    }

    #election-status-badge.ended {
        background-color: #6c757d;
        color: white;
    }

    #live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        color: #28a745;
        margin-left: 8px;
    }

    .live-dot {
        width: 6px;
        height: 6px;
        background-color: #28a745;
        border-radius: 50%;
        animation: pulse-live 1.5s infinite;
    }

    @keyframes pulse-live {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.4;
        }
    }

    /* Vote count animation */
    .vote-count {
        transition: all 0.4s ease;
        font-weight: 600;
    }

    .percentage {
        font-weight: 600;
        color: #0366d6;
    }

    /* Candidate result hover effect */
    .candidate-result {
        transition: transform 0.2s ease, background-color 0.2s ease;
        border-radius: 4px;
    }

    .candidate-result:hover {
        transform: translateX(4px);
        background-color: #f9f9f9;
    }

    /* Results container fade in */
    #results-container {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Profile Page */
    .profile-card {
        background: #22863a;
        padding: 40px;
        border-radius: 8px;
        color: white;
        margin-bottom: 30px;
        width: 100%;
    }

    .profile-info {
        margin-bottom: 15px;
        font-size: 16px;
    }

    .profile-label {
        opacity: 0.8;
    }

    .profile-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn-dark {
        background-color: #1a5125;
        color: white;
        padding: 12px 24px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
    }

    .profile-footer {
        text-align: center;
        color: #666;
        font-size: 14px;
    }

    .profile-footer a {
        color: #333;
        text-decoration: none;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
        border: 2px dashed #ddd;
    }

    .empty-state-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .empty-state-text {
        font-size: 16px;
        color: #666;
        margin-bottom: 20px;
    }

    /* Admin Tables */
    .admin-table {
        width: 100%;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    .admin-table th {
        background-color: #e8e8e8;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
    }

    .admin-table td {
        padding: 15px;
        border-bottom: 1px solid #e8e8e8;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-success {
        background-color: #d4edda;
        color: #22863a;
    }

    /* Forms */
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: #22863a;
    }

    .election-form-section {
        background: #e8e8e8;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 20px;
        width: 100%;
    }

    .position-section {
        background: white;
        padding: 20px;
        border-radius: 6px;
        border-left: 4px solid #22863a;
        margin-bottom: 15px;
    }

    .candidate-input-group {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal {
        background: white;
        padding: 30px;
        border-radius: 8px;
        max-width: 500px;
        width: 90%;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 200px;
        }

        .main-content {
            margin-left: 200px;
            width: calc(100% - 200px);
        }

        .content-area {
            padding: 20px;
        }

        .positions-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid,
        .feature-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
