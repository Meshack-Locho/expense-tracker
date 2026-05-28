<?php
require_once 'env/conf.php';
// require_once "auth_check.php";
include 'assets/temps/functions.php';
$pageTitle = $pageTitle ?? 'B2BConnect Dashboard';
// $localization = array();

require_once 'bootstrap.php';
$page_name = '';



$page = basename($_SERVER['PHP_SELF'], ".php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo WEB_URL;?>img/devure-logo.png" type="image/icon">
    
    <title><?php echo $pageTitle?></title>
    <?php include 'partials/conns.php';?>

</head>
<body>
    <header>
        <div class="navigation">
            <div class="left-top-menu-adm">
                <i class="fa-solid fa-bars left-menu-toggle"></i>
                <div class="logo">
                    <a href="<?php echo WEB_URL;?>">
                        <img src="<?php echo WEB_URL;?>img/logo.png" alt="devure logo">
                    </a>
                </div>
            </div>

            <div class="top-modal-opener">
                    <img src="<?php echo WEB_URL;?>img/no_image.jpg" alt="profile" width="40px" class="top-profile-img">

                    <div class="top-modal">
                        <div class="top">
                            <img src="<?php echo WEB_URL;?>img/no_image.jpg" alt="profile image" class="profile-image">
                        </div>
                        <div class="bottom">
                            <a href="<?= htmlspecialchars(WEB_URL);?>logout">Logout</a>
                        </div>
                    </div>
                </div>

            
        </div>


    </header>

    <div class="dashboard-wrapper">
        <!-- Sidebar container -->
        <aside class="sidebar">
        <div class="sidebar-header">
            <div class="profile-rep">
                <img src="<?= WEB_URL?>img/no_image.jpg" alt="user profile">
                <h5>User</h5>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="treeview">
                <a href="<?php echo WEB_URL;?>dashboard" class="active-link <?php if($page === 'dashboard'){echo 'active';}?>">
                    <i class="fa fa-chart-simple"></i>
                    <span>Dashboard</span>
                </a>
            </li>


            <li class="treeview <?php if($page === 'addcat' || $page === 'categories'){echo 'open';}?>">
            <a href="#" class="treeview-toggle">
                <i class="fa fa-boxes-stacked"></i>
                <span>Category Management</span>
                <i class="fa fa-chevron-down toggle-arrow"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="<?php echo WEB_URL;?>cats/addcat" class="<?php if($page === 'addcat'){echo 'active';}?>">Add Expense Category</a></li>
                <li><a href="<?php echo WEB_URL;?>cats/categories" class="<?php if($page === 'categories'){echo 'active';}?>">Categories</a></li>
            
            </ul>
            </li>

            <li class="treeview <?php if($page === 'addexp' || $page === 'expenses'){echo 'open';}?>">
            <a href="#" class="treeview-toggle">
                <i class="fa fa-file-lines"></i>
                <span>Expense Management</span>
                <i class="fa fa-chevron-down toggle-arrow"></i>
            </a>
            <ul class="treeview-menu">
                <li><a href="<?php echo WEB_URL;?>exp/addexp" class="<?php if($page === 'addexp'){echo 'active';}?>">Add Expense</a></li>
                <li><a href="<?php echo WEB_URL;?>exp/expenses" class="<?php if($page === 'expenses'){echo 'active';}?>">My Expenses</a></li>
            </ul>
            </li>

            <li class="treeview">
                <a href="<?php echo WEB_URL;?>income" class="active-link <?php if($page === 'income'){echo 'active';}?>">
                    <i class="fa fa-money-bill"></i>
                    <span>Income</span>
                </a>
            </li>
        

            

            
        </ul>
        </aside>