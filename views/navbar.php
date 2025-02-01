<?php
function getNavBar($role, $homeActive, $profileActive, $manageUsersActive, $dashboardActive, $auditLActive, $reportsActive) {
    $navItems = '';

    if ($role === 'admin') {
        //admin roles
        $navItems .= '
            <li class="nav-item"><a class="nav-link '.$homeActive.'" href="/missing/views/Home.php">Home '.getIcon("home").'</a></li>
            <li class="nav-item"><a class="nav-link '.$profileActive.'" href="/missing/views/profile.php">Profile '.getIcon("profile").'</a></li>
            <li class="nav-item"><a class="nav-link '.$manageUsersActive.'" href="/missing/views/Users/admin/manage_users.php">Manage Users '.getIcon("manage users").'</a></li>
            <li class="nav-item"><a class="nav-link '.$dashboardActive.'" href="/missing/views/Users/admin/admin_dashboard.php">Dashboard '.getIcon("dashboard").'</a></li>
            <li class="nav-item"><a class="nav-link '.$auditLActive.'" href="/missing/views/Users/admin/view_audit_logs.php">Audit logs '.getIcon("audit logs").'</a></li>
            <li class="nav-item dropdown">    
                
                <a class="nav-link btn-dark dropdown-btn dropdown-toggle nav-item '.$reportsActive.'" data-bs-auto-close="outside" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    reports
                </a>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li class="nav-item"><a class="dropdown-item nav-link" href="/missing/views/Users/admin/approve_reports.php">Manage Reports '.getIcon("approve reports").'</a></li>
                    <li class="nav-item"><a class="dropdown-item nav-link" href="/missing/views/submit_report.php">File Report '.getIcon("file reports").'</a></li>
                    <li><hr class="dropdown-divider"></li>   
                    <!-- inner drop down for the view -->
                    <a class="btn btn-dark dropdown-btn dropdown-toggle nav-item" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        view reports
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li class="nav-item"><a class="dropdown-item nav-link" href="/missing/views/Users/admin/match_reports.php">Match Reports '.getIcon("potential matches").'</a></li>
                        <li class="nav-item"><a class="dropdown-item nav-link" href="/missing/views/view_missing_persons.php">All Reports '.getIcon("reports").'</a></li>
                    </ul>
                </ul>
                    
                
            <li>
            
            <li class="nav-item"><a class="nav-link" href="/missing/views/logout.php">Logout '.getIcon("logout").'</a></li>
        ';
    } elseif ($role === 'allowed_user') {
        // allowed user roles
        $navItems .='
            <li class="nav-item"><a class="nav-link '.$homeActive.'" href="/missing/views/Home.php">Home '.getIcon("home").'</a></li>
            <li class="nav-item"><a class="nav-link '.$profileActive.'" href="/missing/views/profile.php">Profile '.getIcon("profile").'</a></li>
            <li class="nav-item dropdown">    
                
                <a class="nav-link btn-dark dropdown-btn dropdown-toggle nav-item '.$reportsActive.'" data-bs-auto-close="outside" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    reports
                </a>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li class="nav-item"><a class="dropdown-item" href="/missing/views/view_missing_persons.php">view reports '.getIcon("reports").'</a></li>
                    <li class="nav-item"><a class="dropdown-item" href="/missing/views/submit_report.php">File Report '.getIcon("file reports").'</a></li>
                </ul>
                    
                
            <li>
            
            <li class="nav-item"><a class="nav-link" href="/missing/views/logout.php">Logout '.getIcon("logout").'</a></li>
        ';
    }
    
    return $navItems;
}
