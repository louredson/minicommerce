<?php
require_once __DIR__ . '/includes/functions.php';
session_destroy();
session_start();
flash('info', 'Sessao terminada com sucesso.');
redirect('/index.php');
