<?php
declare(strict_types=1);

logout();
flash('info', 'Anda telah keluar dari portal admin.');
redirect('/auth/login');
