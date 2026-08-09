<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ajaxResponse(['ok' => false, 'message' => 'Method tidak diizinkan.']);
}

csrfValidate();
ajaxDispatch((string) $params['modul'], (string) $params['aksi']);
