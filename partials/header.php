<?php
if (!isset($pageTitle)) $pageTitle = app_name();
?><!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($pageTitle) ?></title><meta name="description" content="AfrikaFlix — African movies and entertainment."><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="site-header"><a class="brand" href="index.php"><span>AFRIKA</span>FLIX</a><nav><a href="index.php">Home</a><?php if (is_admin()): ?><a href="admin/index.php">Admin</a><?php else: ?><a href="login.php">Admin</a><?php endif; ?></nav></header>
<main class="container">
