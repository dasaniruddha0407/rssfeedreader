<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? $title : 'RSS Feed Reader' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
        }
    </style>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #2c3e50;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            padding: 15px 0;
        }

        .menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .menu li {
            margin-left: 20px;
        }

        .menu li a {
            color: #fff;
            text-decoration: none;
            padding: 15px 10px;
            display: block;
            transition: 0.3s;
        }

        .menu li a:hover,
        .menu li a.active {
            background-color: #1abc9c;
            border-radius: 4px;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                display: none;
                background: #2c3e50;
                position: absolute;
                top: 60px;
                right: 0;
                width: 200px;
            }

            .menu.show {
                display: block;
            }

            .menu li {
                margin: 0;
            }

            .toggle-btn {
                display: block;
                color: #fff;
                font-size: 22px;
                cursor: pointer;
            }
        }

        .toggle-btn {
            display: none;
        }
    </style>
    <style>
        .pagination a {
            padding: 6px 12px;
            margin: 2px;
            border: 1px solid #ddd;
            text-decoration: none;
        }

        .pagination strong {
            padding: 6px 12px;
            background: #1abc9c;
            color: #fff;
        }
    </style>
    <style>
        .icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: 34px;
            height: 34px;

            margin-right: 6px;
            border-radius: 50%;

            font-size: 16px;
            cursor: pointer;
            transition: 0.25s;

            background: var(--color);
            color: #fff;
        }

        /* 🔥 ACTIVE → green border ring */
        .icon.active {
            border: 2px solid #2ecc71;
            /* green */
            box-shadow: 0 0 0 2px rgba(46, 204, 113, 0.2);
        }

        /* INACTIVE → faded */
        .icon.inactive {
            opacity: 0.3;
            border: 2px solid transparent;
        }

        /* Hover */
        .icon:hover {
            transform: scale(1.15);
            opacity: 1;
        }

        .x_warning {
            margin-top: 6px;
            color: #e74c3c;
            /* 🔥 red */
            background: #fdecea;
            /* light red bg */
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 13px;
            border: 1px solid #f5c6cb;
        }
    </style>
    <style>
        .platform-select {
            display: flex;
            gap: 10px;
        }

        /* Hide checkbox */
        .platform-checkbox {
            display: none;
        }

        /* Icon base */
        .platform-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 40px;
            height: 40px;

            border-radius: 50%;
            background: #eee;
            color: #777;

            font-size: 18px;
            cursor: pointer;

            transition: 0.25s;
        }

        /* Hover */
        .platform-icon:hover {
            transform: scale(1.1);
        }

        /* 🔥 Selected state */
        .platform-checkbox:checked+i {
            color: #fff;
        }

        .platform-checkbox:checked+i {
            position: relative;
            z-index: 2;
        }

        .platform-checkbox:checked+i::before {
            color: #fff;
        }

        /* Background change */
        .platform-checkbox:checked+i,
        .platform-checkbox:checked+i {
            background: transparent;
        }

        .platform-checkbox:checked+i {
            color: #fff;
        }

        /* Apply color to parent */
        .platform-checkbox:checked~i,
        .platform-checkbox:checked+i {
            color: #fff;
        }

        /* BEST: style parent when checked */
        .platform-icon:has(input:checked) {
            background: var(--color);
            color: #fff;
        }

        /* Optional glow */
        .platform-icon:has(input:checked) {
            box-shadow: 0 0 0 2px var(--color);
        }

        .platform-icon.active {
            background: var(--color);
            color: #fff;
        }
    </style>
    <style>
        #toast-container {
            z-index: 999999 !important;
        }
    </style>

    <style>
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .post-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            background: #fff;
        }

        .priority-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #1abc9c;
            color: #fff;
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 5px;
        }

        .thumb {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .content {
            padding: 15px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;

            /* 🔥 1 line truncate */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .desc {
            font-size: 14px;
            color: #555;
            margin: 10px 0;

            /* 🔥 2 line truncate */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .meta {
            font-size: 12px;
            color: #888;
            display: flex;
            justify-content: space-between;
        }

        .platforms {
            margin-top: 10px;
        }

        .badge {
            background: #3498db;
            color: #fff;
            padding: 4px 8px;
            font-size: 11px;
            border-radius: 4px;
            margin-right: 5px;
        }
    </style>

    <style>
        .actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 6px;
            z-index: 10;
        }

        .actions span {
            cursor: pointer;
            font-size: 14px;
            background: #fff;
            padding: 5px 7px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: 0.2s;
        }

        .actions span:hover {
            background: #f1f1f1;
            transform: scale(1.1);
        }

        /* Optional colors */
        .actions span:nth-child(1):hover {
            color: #3498db;
            /* edit */
        }

        .actions span:nth-child(2):hover {
            color: #e74c3c;
            /* delete */
        }

        .empty-state {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 300px;
        }

        .empty-card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #eee;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            max-width: 350px;
        }

        .empty-img {
            width: 120px;
            margin-bottom: 15px;
            opacity: 0.7;
        }

        .btn-import {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #3498db;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-import:hover {
            background: #2980b9;
        }

    .social-card {
    border-radius: 16px;
    cursor: pointer;
    transition: 0.3s;
    border: 1px solid #eee;
    background: #fff;
}

.social-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

/* 🔥 BIG ICON */
.social-icon {
    font-size: 60px;   /* increase size */
    display: block;
}

/* Title */
.social-name {
    font-size: 16px;
    font-weight: 600;
}

/* Optional Colors */
.fa-facebook { color: #1877f2; }
.fa-x-twitter { color: #000; }
.fa-youtube { color: #ff0000; }
.fa-instagram { color: #e4405f; }
.fa-linkedin { color: #0a66c2; } 

.social-card.active {
    border: 2px solid #007bff;
    background: #f0f7ff;
    transform: scale(1.05);
}

/* Optional: highlight icon also */
.social-card.active .social-icon {
    color: #007bff !important;
}
    </style>

</head>

<body class="bg-light">
    <div class="navbar">
        <div class="logo">RSS Panel</div>

        <div class="toggle-btn" onclick="toggleMenu()">☰</div>

        <ul class="menu" id="menu">
            <li><a href="<?= base_url('dashboard') ?>" class="<?php echo ($menu == 'dashboard') ? 'active' : ''  ?>">Dashboard </a></li>
            <li><a href="<?= base_url('import-feed') ?>" class="<?php echo ($menu == 'import_feed') ? 'active' : ''  ?>">Import Feed</a></li>
            <li><a href="<?= base_url('posts') ?>" class="<?php echo ($menu == 'posts') ? 'active' : ''  ?>">Posts</a></li>
        </ul>
    </div>


    <div class="container mt-5 main-content">