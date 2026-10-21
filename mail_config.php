<?php
define("APP_BASE_URL", getenv("APP_BASE_URL") ?: (getenv("RENDER_EXTERNAL_URL") ?: "http://localhost/mercado_digital"));
define("MAIL_FROM", getenv("MAIL_FROM") ?: "digitalmarketdevice@gmail.com");