<?php
// Menu superior com atalho para a tela inicial.
// Para usar em uma página: logo após <body>, adicione:
//   <?php include 'includes/menu.php'; ?>
?>
<style>
    .arlink-menu {
        background: #1e3a5f;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        font-family: Arial, sans-serif;
    }

    .arlink-menu-interno {
        max-width: 1200px;
        width: 95%;
        margin: 0 auto;
        padding: 12px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .arlink-menu-marca {
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        text-decoration: none;
    }

    .arlink-menu-inicio {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        text-decoration: none;
        font-size: 14px;
        font-weight: bold;
        padding: 8px 14px;
        border-radius: 6px;
        white-space: nowrap;
    }

    .arlink-menu-inicio:hover {
        background: rgba(255, 255, 255, 0.28);
    }
</style>

<nav class="arlink-menu">
    <div class="arlink-menu-interno">
        <a href="dashboard.php" class="arlink-menu-marca">AR Link</a>
        <a href="dashboard.php" class="arlink-menu-inicio">🏠 Voltar ao início</a>
    </div>
</nav>
