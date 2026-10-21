<?php
// index.php — Frontend SPA del Mercado Digital
// Este archivo no necesita lógica PHP; sirve el HTML/JS que consume la API REST.
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Mercado Digital</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet"/>
<style>
  /* ── Variables ───────────────────────────────────────────────── */
  :root{
    --bg:#0d0f14;
    --surface:#161a22;
    --surface2:#1e2330;
    --border:#2a3040;
    --accent:#f0c040;
    --accent2:#e07a30;
    --text:#eef0f5;
    --muted:#7a8099;
    --red:#e05555;
    --green:#4caf80;
    --radius:12px;
    --shadow:0 4px 24px rgba(0,0,0,.45);
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;overflow-x:hidden}
  h1,h2,h3,h4{font-family:'Syne',sans-serif}

  /* ── Scrollbar ───────────────────────────────────────────────── */
  ::-webkit-scrollbar{width:6px}::-webkit-scrollbar-track{background:var(--bg)}::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}

  /* ── Utilities ───────────────────────────────────────────────── */
  .hidden{display:none!important}
  .badge{display:inline-block;padding:2px 10px;border-radius:20px;font-size:.72rem;font-weight:600;letter-spacing:.04em}
  .badge-yellow{background:#f0c04022;color:var(--accent);border:1px solid #f0c04055}
  .badge-green{background:#4caf8022;color:var(--green);border:1px solid #4caf8055}
  .badge-red{background:#e0555522;color:var(--red);border:1px solid #e0555555}
  .badge-blue{background:#4090e022;color:#60aaff;border:1px solid #4090e055}

  /* ── Botones ─────────────────────────────────────────────────── */
  button{cursor:pointer;border:none;outline:none;font-family:'DM Sans',sans-serif}
  .btn{padding:10px 22px;border-radius:8px;font-size:.9rem;font-weight:500;transition:all .18s}
  .btn-primary{background:var(--accent);color:#000;font-weight:700}
  .btn-primary:hover{background:#ffd060;transform:translateY(-1px)}
  .btn-secondary{background:var(--surface2);color:var(--text);border:1px solid var(--border)}
  .btn-secondary:hover{background:var(--border)}
  .btn-danger{background:var(--red);color:#fff}
  .btn-danger:hover{background:#ff6666}
  .btn-sm{padding:6px 14px;font-size:.8rem}
  .btn-icon{background:none;padding:6px;border-radius:6px;color:var(--muted);transition:color .15s,background .15s}
  .btn-icon:hover{background:var(--surface2);color:var(--text)}

  /* ── Inputs ──────────────────────────────────────────────────── */
  input,textarea,select{
    background:var(--surface2);border:1px solid var(--border);color:var(--text);
    border-radius:8px;padding:10px 14px;font-family:'DM Sans',sans-serif;font-size:.9rem;
    transition:border-color .15s;width:100%
  }
  input:focus,textarea:focus,select:focus{outline:none;border-color:var(--accent)}
  label{font-size:.82rem;color:var(--muted);margin-bottom:4px;display:block}
  .field{margin-bottom:14px}

  /* ── Tarjetas ────────────────────────────────────────────────── */
  .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow)}

  /* ── Carrito ─────────────────────────────────────────────────── */
  .cart-toggle{
    position:relative;display:inline-flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);
    color:var(--text);padding:8px 12px;border-radius:999px;font-size:.8rem;font-weight:700
  }
  .cart-toggle:hover{background:var(--border)}
  .cart-badge{
    min-width:20px;height:20px;padding:0 6px;border-radius:50%;background:var(--accent);color:#111;font-size:.7rem;
    display:inline-flex;align-items:center;justify-content:center;font-weight:800
  }
  .cart-drawer{position:fixed;inset:0;display:flex;justify-content:flex-end;background:rgba(0,0,0,.5);z-index:1200}
  .cart-drawer.hidden{display:none}
  .cart-panel{width:min(420px,100%);height:100%;background:var(--bg);border-left:1px solid var(--border);padding:20px;display:flex;flex-direction:column}
  .cart-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
  .cart-header h3{font-size:1.2rem}
  .cart-items{flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:10px;padding-right:4px}
  .cart-item{display:flex;gap:12px;background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:10px}
  .cart-item-media{width:70px;height:70px;border-radius:10px;overflow:hidden;background:var(--surface2);flex-shrink:0}
  .cart-item-media img{width:100%;height:100%;object-fit:cover}
  .cart-item-media.noimg{display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:1.1rem}
  .cart-item-body{flex:1;display:flex;flex-direction:column;justify-content:space-between;min-width:0}
  .cart-item-name{font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .cart-item-price{font-size:.9rem;color:var(--accent);font-weight:700;margin-top:3px}
  .cart-item-controls{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:8px}
  .qty-box{display:inline-flex;align-items:center;border:1px solid var(--border);border-radius:8px;overflow:hidden}
  .qty-box button{width:28px;height:28px;background:var(--surface2);color:var(--text);border:none;cursor:pointer;font-weight:700}
  .qty-box span{width:34px;text-align:center;font-size:.8rem;color:var(--text);background:var(--surface)}
  .cart-remove{background:none;border:none;color:var(--muted);cursor:pointer;font-size:.75rem}
  .cart-footer{padding-top:16px;border-top:1px solid var(--border);margin-top:18px;display:flex;flex-direction:column;gap:12px}
  .cart-total-row{display:flex;justify-content:space-between;align-items:center;font-weight:700}
  .cart-summary{padding:10px 12px;background:var(--surface);border:1px solid var(--border);border-radius:10px;display:flex;flex-direction:column;gap:8px}
  .cart-summary .line{display:flex;justify-content:space-between;align-items:center;font-size:.82rem;color:var(--muted)}
  .cart-summary .line strong{color:var(--text)}
  .cart-summary .line.total{color:var(--text);font-size:.9rem;font-weight:800}
  .cart-delivery{margin-top:6px;padding:10px 12px;border:1px solid var(--border);border-radius:10px;background:var(--surface);display:flex;flex-direction:column;gap:6px}
  .cart-delivery .tiny{font-size:.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
  .cart-delivery .value{font-size:.82rem;color:var(--text);font-weight:600}
  .cart-seller{margin-top:6px;padding:10px 12px;border:1px solid var(--border);border-radius:10px;background:var(--surface);display:flex;flex-direction:column;gap:6px}
  .cart-seller .tiny{font-size:.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}
  .cart-seller .value{font-size:.82rem;color:var(--text);font-weight:600}
  .cart-empty{padding:28px 12px;text-align:center;color:var(--muted)}
  .mini-cart-btn{margin-top:8px;width:100%;padding:7px 10px;border-radius:8px;background:var(--surface2);border:1px solid var(--border);color:var(--text);font-size:.75rem;font-weight:700}
  .mini-cart-btn:hover{background:var(--border)}

  /* ── Toast ───────────────────────────────────────────────────── */
  #toast{
    position:fixed;bottom:28px;right:28px;z-index:9999;
    background:var(--surface);border:1px solid var(--border);
    border-radius:10px;padding:14px 20px;font-size:.88rem;
    box-shadow:var(--shadow);transform:translateY(20px);opacity:0;
    transition:all .3s;pointer-events:none;max-width:320px;line-height:1.4
  }
  #toast.show{transform:translateY(0);opacity:1}
  #toast.ok{border-left:4px solid var(--green)}
  #toast.err{border-left:4px solid var(--red)}

  /* ── Modal ───────────────────────────────────────────────────── */
  .modal-overlay{
    position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:500;
    display:flex;align-items:center;justify-content:center;padding:20px
  }
  .modal{background:var(--surface);border:1px solid var(--border);border-radius:16px;
    padding:28px;width:100%;max-width:460px;box-shadow:var(--shadow);position:relative}
  .modal h3{font-size:1.2rem;margin-bottom:18px}
  .modal-close{position:absolute;top:16px;right:16px;background:none;border:none;
    color:var(--muted);font-size:1.4rem;cursor:pointer;padding:4px 8px;border-radius:6px}
  .modal-close:hover{background:var(--surface2);color:var(--text)}

  /* ── Loader ──────────────────────────────────────────────────── */
  .spinner{width:36px;height:36px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite;margin:40px auto}
  @keyframes spin{to{transform:rotate(360deg)}}

  /* ══════════════════════════════════════════════════════════════ */
  /* AUTH                                                           */
  /* ══════════════════════════════════════════════════════════════ */
  #authPage{
    min-height:100vh;display:flex;align-items:center;justify-content:center;
    background:radial-gradient(ellipse 70% 60% at 50% 0%,#1e2a14 0%,var(--bg) 70%)
  }
  .auth-box{width:100%;max-width:420px;padding:36px 32px}
  .auth-logo{font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;
    background:linear-gradient(135deg,var(--accent),var(--accent2));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:6px}
  .auth-sub{color:var(--muted);font-size:.9rem;margin-bottom:28px}
  .auth-tabs{display:flex;gap:6px;margin-bottom:22px;background:var(--surface2);
    border-radius:10px;padding:4px}
  .auth-tab{flex:1;padding:8px;border-radius:7px;background:none;color:var(--muted);
    font-size:.85rem;font-weight:600;transition:all .2s;font-family:'Syne',sans-serif}
  .auth-tab.active{background:var(--surface);color:var(--text);box-shadow:var(--shadow)}
  .auth-footer{margin-top:14px;color:var(--muted);font-size:.8rem;text-align:center}

  /* ══════════════════════════════════════════════════════════════ */
  /* APP SHELL                                                      */
  /* ══════════════════════════════════════════════════════════════ */
  #appPage{display:flex;flex-direction:column;min-height:100vh}

  /* ── Topbar ──────────────────────────────────────────────────── */
  #topbar{
    background:var(--surface);border-bottom:1px solid var(--border);
    padding:0 28px;height:60px;display:flex;align-items:center;
    justify-content:space-between;position:sticky;top:0;z-index:100
  }
  .topbar-logo{font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem;
    background:linear-gradient(135deg,var(--accent),var(--accent2));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent}
  .topbar-nav{display:flex;gap:4px}
  .nav-btn{background:none;border:none;color:var(--muted);padding:7px 14px;
    border-radius:8px;font-size:.85rem;font-weight:600;font-family:'Syne',sans-serif;
    transition:all .18s;cursor:pointer}
  .nav-btn:hover{color:var(--text);background:var(--surface2)}
  .nav-btn.active{color:var(--accent);background:#f0c04015}
  .topbar-right{display:flex;align-items:center;gap:12px}
  .coin-badge{background:#f0c04018;border:1px solid #f0c04040;color:var(--accent);
    padding:5px 12px;border-radius:20px;font-size:.85rem;font-weight:700;
    font-family:'Syne',sans-serif}

  /* ── Main content ────────────────────────────────────────────── */
  #mainContent{flex:1;padding:28px;max-width:1200px;width:100%;margin:0 auto}

  /* ── Sección título ──────────────────────────────────────────── */
  .section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px}
  .section-title{font-size:1.4rem;font-weight:800}

  /* ══════════════════════════════════════════════════════════════ */
  /* PRODUCTOS GRID                                                 */
  /* ══════════════════════════════════════════════════════════════ */
  .products-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
    gap:18px
  }
  .product-card{
    background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
    overflow:hidden;transition:transform .2s,box-shadow .2s;cursor:pointer
  }
  .product-card:hover{transform:translateY(-4px);box-shadow:0 8px 32px rgba(0,0,0,.5)}
  .product-card.promoted{border-color:#f0c04055;box-shadow:0 0 0 1px #f0c04033}
  .product-img{width:100%;height:160px;object-fit:cover;background:var(--surface2);
    display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:2.5rem}
  .product-img img{width:100%;height:100%;object-fit:cover}
  .product-body{padding:14px}
  .product-name{font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;margin-bottom:4px;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .product-desc{font-size:.8rem;color:var(--muted);margin-bottom:10px;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .product-footer{display:flex;justify-content:space-between;align-items:center}
  .product-price{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;color:var(--accent)}
  .product-stock{font-size:.78rem;color:var(--muted)}
  .promoted-tag{
    background:linear-gradient(90deg,var(--accent),var(--accent2));
    color:#000;font-size:.68rem;font-weight:800;padding:2px 8px;
    position:absolute;top:10px;right:10px;border-radius:4px;letter-spacing:.05em
  }

  /* ══════════════════════════════════════════════════════════════ */
  /* PERFIL                                                         */
  /* ══════════════════════════════════════════════════════════════ */
  .profile-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
  @media(max-width:640px){.profile-grid{grid-template-columns:1fr}}
  .profile-card{padding:22px}
  .profile-label{font-size:.75rem;color:var(--muted);text-transform:uppercase;
    letter-spacing:.08em;margin-bottom:4px}
  .profile-value{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:700}
  .card-code-display{
    font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;
    letter-spacing:.12em;color:var(--accent);background:#f0c04010;
    padding:10px 18px;border-radius:8px;border:1px solid #f0c04030;
    text-align:center;margin-top:8px
  }

  /* ══════════════════════════════════════════════════════════════ */
  /* ADMIN                                                          */
  /* ══════════════════════════════════════════════════════════════ */
  .admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:28px}
  .stat-card{padding:20px;text-align:center}
  .stat-value{font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;color:var(--accent)}
  .stat-label{color:var(--muted);font-size:.82rem;margin-top:4px}
  .admin-section{margin-bottom:32px}
  .admin-section h3{font-size:1rem;margin-bottom:14px;color:var(--muted);
    text-transform:uppercase;letter-spacing:.08em}
  table{width:100%;border-collapse:collapse;font-size:.85rem}
  th{padding:10px 12px;text-align:left;color:var(--muted);font-weight:600;
    border-bottom:1px solid var(--border);font-size:.78rem;text-transform:uppercase}
  td{padding:10px 12px;border-bottom:1px solid var(--border)}
  tr:last-child td{border-bottom:none}
  tr:hover td{background:rgba(255,255,255,.02)}

  /* ══════════════════════════════════════════════════════════════ */
  /* HISTORIAL                                                      */
  /* ══════════════════════════════════════════════════════════════ */
  .purchase-item{
    display:flex;justify-content:space-between;align-items:center;
    padding:14px 18px;background:var(--surface);border:1px solid var(--border);
    border-radius:10px;margin-bottom:10px
  }
  .purchase-item .pi-name{font-weight:600;font-size:.9rem}
  .purchase-item .pi-meta{font-size:.78rem;color:var(--muted);margin-top:3px}
  .purchase-item .pi-price{font-family:'Syne',sans-serif;font-weight:800;
    font-size:1rem;color:var(--accent)}

  /* ══════════════════════════════════════════════════════════════ */
  /* CHAT                                                           */
  /* ══════════════════════════════════════════════════════════════ */
  .chat-shell{display:grid;grid-template-columns:minmax(190px,260px) minmax(0,1fr);gap:16px;margin-top:24px}
  .chat-list,.chat-box{background:var(--surface);border:1px solid var(--border);border-radius:12px}
  .chat-list{padding:12px;display:flex;flex-direction:column;gap:8px;min-height:430px}
  .chat-list::before{content:'Conversaciones';padding:4px 6px 8px;color:var(--muted);font-family:'Syne',sans-serif;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em}
  .chat-item{padding:11px 12px;border:1px solid transparent;border-radius:9px;background:var(--surface2);cursor:pointer;transition:background .15s,border-color .15s}
  .chat-item:hover{border-color:var(--border);background:#252b3a}
  .chat-item.active{border-color:#f0c04066;background:#f0c04012}
  .chat-item-title{font-weight:700;font-size:.84rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .chat-item-meta{margin-top:4px;color:var(--muted);font-size:.73rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .chat-box{display:flex;flex-direction:column;min-height:430px;overflow:hidden}
  .chat-header{padding:15px 18px;border-bottom:1px solid var(--border);font-family:'Syne',sans-serif;font-size:.9rem;font-weight:700}
  .chat-messages{flex:1;min-height:300px;padding:18px;display:flex;flex-direction:column;gap:10px;overflow-y:auto;background:rgba(255,255,255,.012)}
  .msg{max-width:min(75%,520px);padding:10px 13px;border-radius:12px;font-size:.82rem;line-height:1.4;overflow-wrap:anywhere}
  .msg.me{align-self:flex-end;background:var(--accent);color:#111;border-bottom-right-radius:4px}
  .msg.them{align-self:flex-start;background:var(--surface2);border:1px solid var(--border);border-bottom-left-radius:4px}
  .chat-input{display:flex;gap:10px;padding:12px;border-top:1px solid var(--border)}
  .chat-input input{min-width:0}
  .chat-input .btn{flex:0 0 auto}
  .chat-empty{padding:24px;color:var(--muted);text-align:center;font-size:.84rem}

  /* ══════════════════════════════════════════════════════════════ */
  /* RESPONSIVE                                                     */
  /* ══════════════════════════════════════════════════════════════ */
  @media(max-width:700px){
    #topbar{padding:0 16px}
    .topbar-nav{display:none}
    #mainContent{padding:16px}
    .products-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr))}
    .chat-shell{grid-template-columns:1fr}
    .chat-list{min-height:0;max-height:210px;overflow-y:auto}
    .chat-box{min-height:390px}
    .msg{max-width:88%}
  }
</style>
</head>
<body>

<!-- ══ TOAST ══════════════════════════════════════════════════════ -->
<div id="toast"></div>

<!-- ══ AUTH PAGE ══════════════════════════════════════════════════ -->
<div id="authPage">
  <div class="card auth-box">
    <div class="auth-logo">Mercado Digital</div>
    <div class="auth-sub">Plataforma de compras y ventas estudiantil</div>

    <div class="auth-tabs">
      <button class="auth-tab active" onclick="showAuthTab('login')">Ingresar</button>
      <button class="auth-tab" onclick="showAuthTab('register')">Registrarse</button>
    </div>

    <!-- Login -->
    <div id="loginForm">
      <div class="field"><label>Nombre completo</label><input id="lNombre" placeholder="Tu nombre completo"/></div>
      <div class="field"><label>Contraseña</label><input id="lPassword" type="password" placeholder="Contraseña"/></div>
      <button class="btn btn-primary" style="width:100%;margin-top:6px" onclick="doLogin()">Entrar →</button>
      <button class="btn-icon" style="width:100%;margin-top:10px" onclick="showRecoveryForm()">Olvidé mi contraseña</button>
    </div>

    <div id="recoveryForm" class="hidden">
      <h3 style="margin-bottom:14px">Recuperar contraseña</h3>
      <div class="field"><label>Correo electrónico</label><input id="recoveryEmail" type="email" autocomplete="email" placeholder="tu@correo.com"/></div>
      <button class="btn btn-primary" style="width:100%" onclick="requestPasswordReset()">Enviar enlace</button>
      <button class="btn-icon" style="width:100%;margin-top:10px" onclick="showAuthTab('login')">Volver a ingresar</button>
    </div>

    <div id="passwordResetForm" class="hidden">
      <h3 style="margin-bottom:14px">Crear nueva contraseña</h3>
      <div class="field"><label>Nueva contraseña (mín. 8 caracteres)</label><input id="newPassword" type="password" autocomplete="new-password" placeholder="Nueva contraseña"/></div>
      <div class="field"><label>Confirmar contraseña</label><input id="confirmPassword" type="password" autocomplete="new-password" placeholder="Repite la contraseña"/></div>
      <button class="btn btn-primary" style="width:100%" onclick="completePasswordReset()">Guardar contraseña</button>
      <button class="btn-icon" style="width:100%;margin-top:10px" onclick="showAuthTab('login')">Volver a ingresar</button>
    </div>

    <!-- Register -->
    <div id="registerForm" class="hidden">
      <div class="field"><label>Nombre completo (mín. 10 caracteres)</label><input id="rNombre" placeholder="Tu nombre completo"/></div>
      <div class="field"><label>Curso</label>
        <select id="rCurso">
          <option value="">Selecciona tu curso</option>
          <option>10-1</option><option>10-2</option><option>10-3</option>
          <option>11-1</option><option>11-2</option>
        </select>
      </div>
      <div class="field"><label>Rol</label>
        <select id="rRol">
          <option value="">¿Cómo participarás?</option>
          <option>Comprador</option><option>Vendedor</option><option>Ambos</option>
        </select>
      </div>
      <div class="field"><label>Contraseña</label><input id="rPassword" type="password" placeholder="Crea una contraseña"/></div>
      <div class="field"><label>Correo electrónico</label><input id="rEmail" type="email" autocomplete="email" placeholder="tu@correo.com"/></div>
      <div class="field"><label>Contacto (WhatsApp / Instagram)</label><input id="rContacto" placeholder="@usuario o número"/></div>
      <div class="field"><label>Código de invitación</label><input id="rCode" placeholder="Ej: MD-ABCD12"/></div>
      <div class="field"><label>Código de admin (opcional)</label><input id="rAdminCode" placeholder="Solo si eres administrador"/></div>
      <button class="btn btn-primary" style="width:100%;margin-top:6px" onclick="doRegister()">Crear cuenta →</button>
    </div>

    <div class="auth-footer">Mercado Digital · Solo para estudiantes registrados</div>
  </div>
</div>

<!-- ══ APP PAGE ════════════════════════════════════════════════════ -->
<div id="appPage" class="hidden">
  <!-- Topbar -->
  <div id="topbar">
    <div class="topbar-logo">Mercado Digital</div>
    <nav class="topbar-nav">
      <button class="nav-btn active" onclick="showSection('market')">Mercado</button>
      <button class="nav-btn" onclick="showSection('myProducts')" id="navMyProducts">Mis productos</button>
      <button class="nav-btn" onclick="showSection('purchases')">Historial</button>
      <button class="nav-btn" onclick="showSection('chat')">Chat</button>
      <button class="nav-btn" onclick="showSection('profile')">Perfil</button>
      <button class="nav-btn hidden" id="navAdmin" onclick="showSection('admin')">⚙️ Admin</button>
    </nav>
    <div class="topbar-right">
      <span class="coin-badge" id="coinDisplay">—</span>
      <button class="cart-toggle" onclick="toggleCart()">
        🛒 <span>Carrito</span> <span class="cart-badge" id="cartBadge">0</span>
      </button>
      <button class="btn btn-secondary btn-sm" onclick="doLogout()">Salir</button>
    </div>
  </div>

  <!-- Main -->
  <div id="mainContent">

    <!-- ── MERCADO ─────────────────────────────────────────────── -->
    <div id="sectionMarket">
      <div class="section-header">
        <span class="section-title">Mercado</span>
        <input id="searchInput" placeholder="Buscar productos…" style="max-width:240px" oninput="filterProducts()"/>
      </div>

      <!-- Destacados -->
      <div id="promotedSection" class="hidden" style="margin-bottom:28px">
        <h3 style="font-size:.85rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px">Destacados</h3>
        <div class="products-grid" id="promotedGrid"></div>
      </div>

      <div class="products-grid" id="productsGrid"><div class="spinner"></div></div>
    </div>

    <!-- ── MIS PRODUCTOS ────────────────────────────────────────── -->
    <div id="sectionMyProducts" class="hidden">
      <div class="section-header">
        <span class="section-title">Mis productos</span>
        <button class="btn btn-primary btn-sm" onclick="openProductModal()">+ Nuevo producto</button>
      </div>
      <div class="products-grid" id="myProductsGrid"><div class="spinner"></div></div>
    </div>

    <!-- ── HISTORIAL ────────────────────────────────────────────── -->
    <div id="sectionPurchases" class="hidden">
      <div class="section-header">
        <span class="section-title">Historial de transacciones</span>
        <button class="btn btn-secondary btn-sm" onclick="loadPurchases()">↺ Actualizar</button>
      </div>
      <div id="purchasesList"><div class="spinner"></div></div>
    </div>

    <!-- ── CHAT ─────────────────────────────────────────────────── -->
    <div id="sectionChat" class="hidden">
      <div class="section-header">
        <span class="section-title">Chat</span>
        <button class="btn btn-secondary btn-sm" onclick="loadConversations()">↺ Actualizar</button>
      </div>
      <div id="chatSection"></div>
    </div>

    <!-- ── PERFIL ────────────────────────────────────────────────── -->
    <div id="sectionProfile" class="hidden">
      <div class="section-header"><span class="section-title">Mi perfil</span></div>
      <div class="profile-grid" id="profileContent"></div>
    </div>

    <!-- ── ADMIN ─────────────────────────────────────────────────── -->
    <div id="sectionAdmin" class="hidden">
      <div class="section-header"><span class="section-title">⚙️ Panel Administrador</span></div>
      <div id="adminContent"><div class="spinner"></div></div>
    </div>

  </div><!-- /mainContent -->
</div><!-- /appPage -->

<!-- ══ MODALES ═════════════════════════════════════════════════════ -->

<!-- Modal: Ver producto -->
<div id="modalProduct" class="modal-overlay hidden" onclick="closeModalProduct(event)">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalProduct')">✕</button>
    <div id="modalProductContent"></div>
  </div>
</div>

<!-- Modal: Crear / Editar producto -->
<div id="modalProductForm" class="modal-overlay hidden" onclick="closeModalForm(event)">
  <div class="modal" style="max-width:520px">
    <button class="modal-close" onclick="closeModal('modalProductForm')">✕</button>
    <h3 id="productFormTitle">Nuevo producto</h3>
    <input type="hidden" id="editProductId"/>
    <div class="field"><label>Nombre del producto</label><input id="pNombre" placeholder="Ej: Buñuelo, Cuaderno…"/></div>
    <div class="field"><label>Descripción</label><textarea id="pDesc" rows="3" placeholder="Describe bien tu producto"></textarea></div>
    <div class="field"><label>Precio (monedas)</label><input id="pPrecio" type="number" min="1" placeholder="10"/></div>
    <div class="field"><label>Stock disponible</label><input id="pStock" type="number" min="0" placeholder="5"/></div>
    <div class="field">
      <label>Imagen (opcional)</label>
      <input type="file" id="pImagen" accept="image/*" onchange="previewImage(this)"/>
      <img id="pImagePreview" style="max-height:120px;margin-top:10px;border-radius:8px;display:none"/>
    </div>
    <button class="btn btn-primary" style="width:100%;margin-top:8px" onclick="saveProduct()">Guardar producto</button>
  </div>
</div>

<!-- Modal: Admin — Cargar saldo -->
<div id="modalCredit" class="modal-overlay hidden" onclick="closeMCredit(event)">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalCredit')">✕</button>
    <h3>Cargar saldo</h3>
    <div class="field"><label>Código de tarjeta del estudiante</label><input id="creditCard" placeholder="MDXXX…"/></div>
    <div class="field"><label>Cantidad de monedas</label><input id="creditAmount" type="number" min="1" placeholder="50"/></div>
    <div class="field"><label>Nota</label><input id="creditNote" placeholder="Recarga semanal"/></div>
    <button class="btn btn-primary" style="width:100%;margin-top:8px" onclick="doCredit()">Cargar →</button>
  </div>
</div>

<!-- Modal: Admin — Agregar códigos -->
<div id="modalInvites" class="modal-overlay hidden" onclick="closeMInvites(event)">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('modalInvites')">✕</button>
    <h3>Generar códigos de invitación</h3>
    <div class="field"><label>Cantidad a generar (máx. 500)</label><input id="inviteCount" type="number" min="1" max="500" value="10"/></div>
    <button class="btn btn-primary" style="width:100%;margin-top:8px" onclick="doAddInvites()">Generar →</button>
  </div>
</div>

<!-- Drawer: Carrito -->
<div id="cartDrawer" class="cart-drawer hidden" onclick="if(event.target.id === 'cartDrawer') toggleCart(false)">
  <div class="cart-panel">
    <div class="cart-header">
      <h3>Mi carrito</h3>
      <button class="btn-icon" onclick="toggleCart(false)">✕</button>
    </div>
    <div id="cartItems" class="cart-items"></div>
    <div class="cart-footer">
      <div class="cart-delivery">
        <div class="tiny">Entrega</div>
        <div class="value" id="cartDelivery">Recoger en sala escolar</div>
      </div>
      <div class="cart-seller">
        <div class="tiny">Vendedor</div>
        <div class="value" id="cartSeller">Varios vendedores</div>
      </div>
      <div class="cart-summary" id="cartSummary">
        <div class="line"><span>Subtotal</span><strong id="cartSubtotal">0 monedas</strong></div>
        <div class="line"><span>Envío</span><strong id="cartShipping">0 monedas</strong></div>
        <div class="line total"><span>Total</span><strong id="cartTotal">0 monedas</strong></div>
      </div>
      <button class="btn btn-primary" style="width:100%" onclick="checkoutCart()">Comprar carrito</button>
    </div>
  </div>
</div>

<script>
/* ════════════════════════════════════════════════════════════════
   CONFIG
════════════════════════════════════════════════════════════════ */
const API = {
  login:    "login.php",
  register: "register.php",
  products: "products.php",
  purchase: "purchase.php",
  chat:     "chat.php",
  user:     "user.php",
  admin:    "admin.php",
  passwordReset: "password_reset.php",
};

let currentUser = null;
let allProducts = [];
let adminUsers = [];
let cart = [];

/* ════════════════════════════════════════════════════════════════
   HELPERS
════════════════════════════════════════════════════════════════ */
function toast(msg, type="ok"){
  const t=document.getElementById("toast");
  t.textContent=msg; t.className="show "+(type==="ok"?"ok":"err");
  clearTimeout(t._t); t._t=setTimeout(()=>t.className="",3200);
}

async function api(url, method="GET", body=null){
  try{
    const opts={method,headers:{"Content-Type":"application/json"}};
    if(body) opts.body=JSON.stringify(body);
    const r=await fetch(url,opts);
    if(!r.ok){
      const txt=await r.text();
      console.error("HTTP "+r.status+" en "+url+":", txt);
      return {error:"Error del servidor ("+r.status+"). Revisa la consola (F12)."};
    }
    const txt=await r.text();
    try{ return JSON.parse(txt); }
    catch(e){ console.error("Respuesta no-JSON de "+url+":", txt); return {error:"Respuesta inválida del servidor. Revisa la consola (F12)."}; }
  } catch(e){
    console.error("Fetch falló en "+url+":", e);
    return {error:"No se pudo conectar. ¿Está corriendo XAMPP y Apache?"};
  }
}

function qs(id){return document.getElementById(id)}
function closeModal(id){qs(id).classList.add("hidden")}
function openModal(id){qs(id).classList.remove("hidden")}

function coin(n){return n+" monedas"}
function fmtDate(s){
  if(!s) return "—";
  const d=new Date(s); return d.toLocaleDateString("es-CO",{day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit"});
}

function loadCartFromStorage(){
  try {
    const saved = JSON.parse(localStorage.getItem("mdCart") || "[]");
    cart = Array.isArray(saved) ? saved : [];
  } catch (e) {
    cart = [];
  }
  updateCartBadge();
  renderCart();
}

function saveCartToStorage(){
  localStorage.setItem("mdCart", JSON.stringify(cart));
  updateCartBadge();
  renderCart();
}

function updateCartBadge(){
  const totalItems = cart.reduce((sum,item)=>sum + Number(item.quantity || 0), 0);
  const badge = qs("cartBadge");
  if (badge) badge.textContent = totalItems;
}

function toggleCart(forceState){
  const drawer = qs("cartDrawer");
  if (!drawer) return;
  const open = typeof forceState === "boolean" ? forceState : drawer.classList.contains("hidden");
  drawer.classList.toggle("hidden", !open);
  renderCart();
}

function addToCartById(productId){
  const product = allProducts.find(item => Number(item.id) === Number(productId));
  if (!product) {
    toast("Producto no encontrado","err");
    return;
  }
  addToCart(product);
}

function addToCart(product){
  if (!currentUser) {
    toast("Inicia sesión para usar el carrito","err");
    return;
  }
  if (product.seller_id == currentUser.id) {
    toast("No puedes comprar tu propio producto","err");
    return;
  }

  const existing = cart.find(item => Number(item.id) === Number(product.id));
  if (!existing) {
    cart.push({
      id: Number(product.id),
      nombre: product.nombre,
      precio: Number(product.precio),
      stock: Number(product.stock || 0),
      image_data: product.image_data || "",
      seller_id: Number(product.seller_id),
      vendedor_nombre: product.vendedor_nombre || "Vendedor",
      quantity: 1
    });
  } else {
    if (product.stock > 0 && existing.quantity >= Number(product.stock)) {
      toast("Ya agregaste todo el stock disponible","err");
      return;
    }
    existing.quantity += 1;
  }

  saveCartToStorage();
  toast(product.nombre + " agregado al carrito");
}

function updateCartQty(productId, delta){
  const item = cart.find(i => Number(i.id) === Number(productId));
  if (!item) return;
  const nextQty = item.quantity + delta;
  if (nextQty <= 0) {
    cart = cart.filter(i => Number(i.id) !== Number(productId));
  } else {
    item.quantity = Math.min(nextQty, Number(item.stock || nextQty));
  }
  saveCartToStorage();
}

function removeFromCart(productId){
  cart = cart.filter(i => Number(i.id) !== Number(productId));
  saveCartToStorage();
}

function renderCart(){
  const list = qs("cartItems");
  if (!list) return;

  if (!cart.length) {
    list.innerHTML = '<div class="cart-empty">Tu carrito está vacío. Agrega algunos productos para continuar.</div>';
    qs("cartSubtotal").textContent = "0 monedas";
    qs("cartShipping").textContent = "0 monedas";
    qs("cartTotal").textContent = "0 monedas";
    qs("cartDelivery").textContent = "Recoger en sala escolar";
    qs("cartSeller").textContent = "Varios vendedores";
    return;
  }

  const subtotal = cart.reduce((sum, item) => sum + (Number(item.precio) * Number(item.quantity || 0)), 0);
  const envio = cart.length > 0 ? 5 : 0;
  const total = subtotal + envio;
  const sellers = [...new Set(cart.map(item => item.vendedor_nombre || "Vendedor"))];

  qs("cartSubtotal").textContent = coin(subtotal);
  qs("cartShipping").textContent = coin(envio);
  qs("cartTotal").textContent = coin(total);
  qs("cartDelivery").textContent = sellers.length > 1 ? "Entrega mixta (varios vendedores)" : "Entrega por vendedor";
  qs("cartSeller").textContent = sellers.length > 1 ? sellers.join(", ") : (sellers[0] || "Varios vendedores");

  list.innerHTML = cart.map(item => `
    <div class="cart-item">
      <div class="cart-item-media ${item.image_data ? "" : "noimg"}">
        ${item.image_data ? `<img src="${item.image_data}" alt="${item.nombre}">` : "📦"}
      </div>
      <div class="cart-item-body">
        <div>
          <div class="cart-item-name">${item.nombre}</div>
          <div class="cart-item-price">${coin(item.precio)} x ${item.quantity}</div>
        </div>
        <div class="cart-item-controls">
          <div class="qty-box">
            <button onclick="updateCartQty(${item.id}, -1)">−</button>
            <span>${item.quantity}</span>
            <button onclick="updateCartQty(${item.id}, 1)">+</button>
          </div>
          <button class="cart-remove" onclick="removeFromCart(${item.id})">Quitar</button>
        </div>
      </div>
    </div>
  `).join("");
}

async function checkoutCart(){
  if (!currentUser) {
    toast("Debes iniciar sesión","err");
    return;
  }
  if (!cart.length) {
    toast("Tu carrito está vacío","err");
    return;
  }

  const res = await api(API.purchase, "POST", {
    buyerId: currentUser.id,
    cart: cart.map(item => ({ productId: item.id, quantity: item.quantity }))
  });

  if (res.error) {
    toast(res.error, "err");
    return;
  }

  currentUser = res.buyer || currentUser;
  localStorage.setItem("mdUser", JSON.stringify(currentUser));
  cart = [];
  saveCartToStorage();
  toggleCart(false);
  updateCoinDisplay();
  toast("Compra realizada con éxito");
  loadProducts();
  loadPurchases();
}

/* ════════════════════════════════════════════════════════════════
   AUTH
════════════════════════════════════════════════════════════════ */
function showAuthTab(tab){
  qs("loginForm").classList.toggle("hidden", tab!=="login");
  qs("registerForm").classList.toggle("hidden", tab!=="register");
  qs("recoveryForm").classList.add("hidden");
  qs("passwordResetForm").classList.add("hidden");
  document.querySelectorAll(".auth-tab").forEach((b,i)=>b.classList.toggle("active",(tab==="login"&&i===0)||(tab==="register"&&i===1)));
}

function showRecoveryForm(){
  showAuthTab("login");
  qs("loginForm").classList.add("hidden");
  qs("recoveryForm").classList.remove("hidden");
}

async function requestPasswordReset(){
  const email=qs("recoveryEmail").value.trim();
  if(!email){toast("Escribe tu correo electrónico","err");return;}
  const res=await api(API.passwordReset,"POST",{action:"request",email});
  if(res.error){toast(res.error,"err");return;}
  toast(res.message);
  qs("recoveryEmail").value="";
}

async function completePasswordReset(){
  const password=qs("newPassword").value;
  const confirmation=qs("confirmPassword").value;
  const token=new URLSearchParams(location.search).get("token")||"";
  if(password.length<8){toast("La contraseña debe tener al menos 8 caracteres","err");return;}
  if(password!==confirmation){toast("Las contraseñas no coinciden","err");return;}
  const res=await api(API.passwordReset,"POST",{action:"reset",token,password});
  if(res.error){toast(res.error,"err");return;}
  history.replaceState(null,"",location.pathname);
  qs("newPassword").value="";
  qs("confirmPassword").value="";
  toast("Contraseña actualizada. Ya puedes ingresar.");
  showAuthTab("login");
}

async function doLogin(){
  const nombre=qs("lNombre").value.trim();
  const password=qs("lPassword").value;
  if(!nombre||!password){toast("Completa todos los campos","err");return;}
  const res=await api(API.login,"POST",{nombre,password});
  if(res.error){toast(res.error,"err");return;}
  loginSuccess(res.user);
}

async function doRegister(){
  const nombre=qs("rNombre").value.trim();
  const curso=qs("rCurso").value;
  const rol=qs("rRol").value;
  const password=qs("rPassword").value;
  const email=qs("rEmail").value.trim();
  const contacto=qs("rContacto").value.trim();
  const verifyCode=qs("rCode").value.trim().toUpperCase();
  const adminCode=qs("rAdminCode").value.trim();
  if(!nombre||!curso||!rol||!password||!email||!contacto||!verifyCode){toast("Completa todos los campos","err");return;}
  const res=await api(API.register,"POST",{nombre,curso,rol,password,email,contacto,verifyCode,adminCode});
  if(res.error){toast(res.error,"err");return;}
  toast("¡Cuenta creada! Bienvenid@ "+res.user.nombre);
  loginSuccess(res.user);
}

function loginSuccess(user){
  currentUser=user;
  localStorage.setItem("mdUser",JSON.stringify(user));
  startApp();
}

function doLogout(){
  currentUser=null;
  cart=[];
  localStorage.removeItem("mdUser");
  localStorage.removeItem("mdCart");
  updateCartBadge();
  renderCart();
  qs("appPage").classList.add("hidden");
  qs("authPage").classList.remove("hidden");
}

/* ════════════════════════════════════════════════════════════════
   APP START
════════════════════════════════════════════════════════════════ */
function startApp(){
  qs("authPage").classList.add("hidden");
  qs("appPage").classList.remove("hidden");
  updateCoinDisplay();

  // Mostrar sección mis productos solo si vende
  const canSell=["Vendedor","Ambos"].includes(currentUser.rol);
  qs("navMyProducts").classList.toggle("hidden",!canSell);

  // Admin nav
  if(currentUser.is_admin==1) qs("navAdmin").classList.remove("hidden");

  showSection("market");
}

function updateCoinDisplay(){
  if(currentUser) qs("coinDisplay").textContent=coin(currentUser.monedas);
}

/* ════════════════════════════════════════════════════════════════
   NAV
════════════════════════════════════════════════════════════════ */
function showSection(name){
  ["market","myProducts","purchases","chat","profile","admin"].forEach(s=>{
    qs("section"+s.charAt(0).toUpperCase()+s.slice(1)).classList.add("hidden");
  });
  document.querySelectorAll(".nav-btn").forEach(b=>b.classList.remove("active"));

  qs("sectionMarket").classList.toggle("hidden",name!=="market");
  qs("sectionMyProducts").classList.toggle("hidden",name!=="myProducts");
  qs("sectionPurchases").classList.toggle("hidden",name!=="purchases");
  qs("sectionChat").classList.toggle("hidden",name!=="chat");
  qs("sectionProfile").classList.toggle("hidden",name!=="profile");
  qs("sectionAdmin").classList.toggle("hidden",name!=="admin");

  const navMap={market:0,myProducts:1,purchases:2,chat:3,profile:4,admin:5};
  const btns=document.querySelectorAll(".nav-btn");
  if(navMap[name]!==undefined) btns[navMap[name]]?.classList.add("active");

  if(name==="market") loadProducts();
  if(name==="myProducts") loadMyProducts();
  if(name==="purchases") loadPurchases();
  if(name==="chat") loadConversations();
  if(name==="profile") renderProfile();
  if(name==="admin") loadAdmin();
}

/* ════════════════════════════════════════════════════════════════
   PRODUCTOS — MERCADO
════════════════════════════════════════════════════════════════ */
async function loadProducts(){
  const [res,prom]=await Promise.all([
    api(API.products),
    api(API.products+"?popular=1")
  ]);
  allProducts=res.products||[];

  // Destacados
  const promoted=prom.products||[];
  const ps=qs("promotedSection"), pg=qs("promotedGrid");
  if(promoted.length){
    ps.classList.remove("hidden");
    pg.innerHTML=promoted.map(p=>productCardHTML(p,true)).join("");
  } else {
    ps.classList.add("hidden");
  }

  renderProductsGrid(allProducts);
}

function filterProducts(){
  const q=qs("searchInput").value.toLowerCase();
  const filtered=allProducts.filter(p=>
    p.nombre.toLowerCase().includes(q)||p.descripcion.toLowerCase().includes(q)
  );
  renderProductsGrid(filtered);
}

function renderProductsGrid(products){
  const g=qs("productsGrid");
  if(!products.length){g.innerHTML='<p style="color:var(--muted);padding:20px">No hay productos disponibles.</p>';return;}
  g.innerHTML=products.map(p=>productCardHTML(p)).join("");
}

function productCardHTML(p, isPromoted=false){
  const imgHTML=p.image_data
    ? `<div class="product-img"><img src="${p.image_data}" alt="${p.nombre}" loading="lazy"/></div>`
    : `<div class="product-img">Sin imagen</div>`;
  return `
  <div class="product-card${isPromoted?' promoted':''}" onclick="openProduct(${p.id})" style="position:relative">
    ${isPromoted?'<span class="promoted-tag">DESTACADO</span>':''}
    ${imgHTML}
    <div class="product-body">
      <div class="product-name">${p.nombre}</div>
      <div class="product-desc">${p.descripcion}</div>
      <div class="product-footer">
        <span class="product-price">${coin(p.precio)}</span>
        <span class="product-stock">Stock: ${p.stock}</span>
      </div>
      <div style="font-size:.75rem;color:var(--muted);margin-top:6px">por ${p.vendedor_nombre||"—"}</div>
      <button class="mini-cart-btn" onclick="event.stopPropagation(); addToCartById(${p.id})">Agregar al carrito</button>
    </div>
  </div>`;
}

async function openProduct(id){
  const product=allProducts.find(p=>p.id==id);
  if(!product) return;
  const c=qs("modalProductContent");
  const imgHTML=product.image_data
    ? `<img src="${product.image_data}" style="width:100%;max-height:220px;object-fit:cover;border-radius:10px;margin-bottom:16px"/>`
    : "";
  const canBuy=["Comprador","Ambos"].includes(currentUser.rol)&&product.stock>0&&product.seller_id!=currentUser.id;
  c.innerHTML=`
    ${imgHTML}
    <h3 style="font-size:1.3rem;margin-bottom:8px">${product.nombre}</h3>
    <p style="color:var(--muted);font-size:.88rem;margin-bottom:14px">${product.descripcion}</p>
    <div style="display:flex;gap:12px;align-items:center;margin-bottom:18px">
      <span style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:var(--accent)">${coin(product.precio)}</span>
      <span class="badge badge-${product.stock>0?'green':'red'}">${product.stock>0?'Stock: '+product.stock:'Agotado'}</span>
    </div>
    <div style="font-size:.82rem;color:var(--muted);margin-bottom:18px">
      Vendedor: <strong style="color:var(--text)">${product.vendedor_nombre||"—"}</strong>
      ${product.vendedor_curso?` · Curso ${product.vendedor_curso}`:""}
      ${product.vendedor_contacto?` · <a href="https://wa.me/${product.vendedor_contacto.replace(/\D/g,'')}" target="_blank" style="color:var(--accent);text-decoration:none">Contactar</a>`:""}
    </div>
    ${canBuy?`
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <input id="buyQty" type="number" min="1" max="${product.stock}" value="1" style="width:80px"/>
        <button class="btn btn-primary" onclick="doBuy(${product.id})">Comprar ahora</button>
        <button class="btn btn-secondary" onclick="addToCartById(${product.id}); closeModal('modalProduct')">Agregar al carrito</button>
      </div>`
    :product.seller_id==currentUser.id?'<p style="color:var(--muted);font-size:.85rem">Este es tu producto.</p>'
    :'<p style="color:var(--muted);font-size:.85rem">No disponible.</p>'}
  `;
  openModal("modalProduct");
}

function closeModalProduct(e){if(e.target.id==="modalProduct")closeModal("modalProduct")}

async function doBuy(productId){
  const qty=parseInt(qs("buyQty").value)||1;
  const res=await api(API.purchase,"POST",{productId,buyerId:currentUser.id,quantity:qty});
  if(res.error){toast(res.error,"err");return;}
  currentUser=res.buyer;
  localStorage.setItem("mdUser",JSON.stringify(currentUser));
  updateCoinDisplay();
  closeModal("modalProduct");
  toast("Compra realizada.");
  loadProducts();
}

/* ════════════════════════════════════════════════════════════════
   MIS PRODUCTOS
════════════════════════════════════════════════════════════════ */
async function loadMyProducts(){
  const res=await api(API.products);
  const mine=(res.products||[]).filter(p=>p.seller_id==currentUser.id);
  const g=qs("myProductsGrid");
  if(!mine.length){g.innerHTML='<p style="color:var(--muted);padding:20px">Aún no tienes productos publicados.</p>';return;}
  g.innerHTML=mine.map(p=>`
    <div class="product-card" style="position:relative">
      ${p.image_data?`<div class="product-img"><img src="${p.image_data}" alt="${p.nombre}"/></div>`:'<div class="product-img">Sin imagen</div>'}
      <div class="product-body">
        <div class="product-name">${p.nombre}</div>
        <div class="product-desc">${p.descripcion}</div>
        <div class="product-footer">
          <span class="product-price">${coin(p.precio)}</span>
          <span class="product-stock">Stock: ${p.stock}</span>
        </div>
        ${p.promoted_until&&new Date(p.promoted_until)>new Date()?'<span class="badge badge-yellow" style="margin-top:8px">Destacado hasta '+fmtDate(p.promoted_until)+'</span>'
        :`<button class="btn btn-secondary btn-sm" style="margin-top:8px;width:100%" onclick="promoteProduct(${p.id})">Destacar (10 monedas)</button>`}
        <div style="display:flex;gap:8px;margin-top:10px">
          <button class="btn btn-secondary btn-sm" style="flex:1" onclick="openProductModal(${p.id})">Editar</button>
          <button class="btn btn-danger btn-sm" style="flex:1" onclick="deleteProduct(${p.id})">Eliminar</button>
        </div>
      </div>
    </div>`).join("");
}

function openProductModal(editId=null){
  qs("editProductId").value=editId||"";
  qs("productFormTitle").textContent=editId?"Editar producto":"Nuevo producto";
  if(!editId){
    ["pNombre","pDesc","pPrecio","pStock"].forEach(id=>qs(id).value="");
    qs("pImagePreview").style.display="none";
  } else {
    api(API.products).then(res=>{
      const p=(res.products||[]).find(x=>x.id==editId);
      if(!p)return;
      qs("pNombre").value=p.nombre;
      qs("pDesc").value=p.descripcion;
      qs("pPrecio").value=p.precio;
      qs("pStock").value=p.stock;
      if(p.image_data){qs("pImagePreview").src=p.image_data;qs("pImagePreview").style.display="block";}
      else qs("pImagePreview").style.display="none";
    });
  }
  openModal("modalProductForm");
}

function closeModalForm(e){if(e.target.id==="modalProductForm")closeModal("modalProductForm")}

function compressImage(file, maxPx, quality){
  return new Promise(resolve=>{
    const r=new FileReader();
    r.onload=e=>{
      const img=new Image();
      img.onload=()=>{
        let w=img.width, h=img.height;
        if(w>maxPx||h>maxPx){
          if(w>h){h=Math.round(h*maxPx/w);w=maxPx;}
          else{w=Math.round(w*maxPx/h);h=maxPx;}
        }
        const canvas=document.createElement("canvas");
        canvas.width=w; canvas.height=h;
        canvas.getContext("2d").drawImage(img,0,0,w,h);
        resolve(canvas.toDataURL("image/jpeg", quality));
      };
      img.src=e.target.result;
    };
    r.readAsDataURL(file);
  });
}

function previewImage(input){
  const file=input.files[0];
  if(!file)return;
  compressImage(file, 800, 0.75).then(dataUrl=>{
    qs("pImagePreview").src=dataUrl;
    qs("pImagePreview").style.display="block";
  });
}

async function saveProduct(){
  const editId=parseInt(qs("editProductId").value)||0;
  const nombre=qs("pNombre").value.trim();
  const descripcion=qs("pDesc").value.trim();
  const precio=parseInt(qs("pPrecio").value)||0;
  const stock=parseInt(qs("pStock").value)||0;
  const imgEl=qs("pImagen");
  let imageData=null;
  if(imgEl.files&&imgEl.files[0]){
    imageData=await compressImage(imgEl.files[0], 800, 0.75);
  } else if(qs("pImagePreview").src&&qs("pImagePreview").style.display!=="none"){
    imageData=qs("pImagePreview").src;
  }
  if(!nombre||!descripcion||precio<1){toast("Completa todos los campos requeridos","err");return;}
  const body={sellerId:currentUser.id,nombre,descripcion,precio,stock,imageData};
  let res;
  if(editId){
    res=await api(API.products+"?id="+editId,"PUT",body);
  } else {
    res=await api(API.products,"POST",body);
  }
  if(res.error){toast(res.error,"err");return;}
  closeModal("modalProductForm");
  toast(editId?"Producto actualizado.":"Producto publicado.");
  loadMyProducts();
}

async function deleteProduct(id){
  if(!confirm("¿Eliminar este producto?"))return;
  const res=await api(API.products+"?id="+id,"DELETE",{sellerId:currentUser.id});
  if(res.error){toast(res.error,"err");return;}
  toast("Producto eliminado");
  loadMyProducts();
}

async function promoteProduct(id){
  if(currentUser.monedas<10){toast("Necesitas al menos 10 monedas","err");return;}
  if(!confirm("¿Gastar 10 monedas para destacar este producto por 7 días?"))return;
  const res=await api(API.products+"?id="+id+"&action=promote","POST",{sellerId:currentUser.id});
  if(res.error){toast(res.error,"err");return;}
  currentUser=res.seller;
  localStorage.setItem("mdUser",JSON.stringify(currentUser));
  updateCoinDisplay();
  toast("Producto destacado por 7 días.");
  loadMyProducts();
}

/* ════════════════════════════════════════════════════════════════
   HISTORIAL
════════════════════════════════════════════════════════════════ */
async function loadPurchases(){
  const res=await api(API.purchase+"?userId="+currentUser.id);
  const purchases=res.purchases||[];
  const el=qs("purchasesList");
  if(!purchases.length){el.innerHTML='<p style="color:var(--muted);padding:20px">No hay transacciones aún.</p>';}
  else {
    el.innerHTML=purchases.map(p=>{
      const isBuyer=p.buyer_id==currentUser.id;
      return `
      <div class="purchase-item">
        <div>
          <div class="pi-name">${p.producto_nombre}</div>
          <div class="pi-meta">${isBuyer?'Compraste a '+p.vendedor_nombre:'Vendiste a '+p.comprador_nombre} · Cant: ${p.quantity} · ${fmtDate(p.created_at)}</div>
        </div>
        <div class="pi-price" style="color:${isBuyer?'var(--red)':'var(--green)'}">
          ${isBuyer?'-':'+'} ${coin(p.total)}
        </div>
      </div>`;
    }).join("");
  }

}

let chatConversations = [];
let activeConversationId = null;

async function loadConversations(){
  const res = await api("chat.php?userId=" + currentUser.id);
  const conversations = res.conversations || [];
  chatConversations = conversations;
  const chatSection = qs("chatSection");

  if (!conversations.length) {
    chatSection.innerHTML = '<div class="chat-empty">No hay conversaciones aún. Cuando realices una compra, aparecerá el chat aquí.</div>';
    return;
  }

  const firstId = conversations[0].id;
  if (!activeConversationId || !conversations.some(c => c.id === activeConversationId)) activeConversationId = firstId;

  chatSection.innerHTML = `
    <div class="chat-shell">
      <div class="chat-list">
        ${conversations.map(c => `
          <div class="chat-item ${c.id === activeConversationId ? 'active' : ''}" data-id="${c.id}" onclick="openConversation(${c.id})">
            <div class="chat-item-title">${c.product_nombre}</div>
            <div class="chat-item-meta">${c.buyer_id == currentUser.id ? c.seller_nombre : c.buyer_nombre}</div>
          </div>
        `).join('')}
      </div>
      <div class="chat-box">
        <div class="chat-header" id="chatHeader">Chat</div>
        <div class="chat-messages" id="chatMessages"></div>
        <div class="chat-input">
          <input id="chatInput" placeholder="Escribe un mensaje..." />
          <button class="btn btn-primary btn-sm" onclick="sendMessage()">Enviar</button>
        </div>
      </div>
    </div>
  `;

  openConversation(activeConversationId);
}

async function openConversation(conversationId){
  activeConversationId = conversationId;
  const conversation = chatConversations.find(c => c.id == conversationId);
  if (!conversation) return;

  const chatHeader = qs("chatHeader");
  chatHeader.textContent = conversation.product_nombre + " · " + (conversation.buyer_id == currentUser.id ? conversation.seller_nombre : conversation.buyer_nombre);

  const res = await api("chat.php?userId=" + currentUser.id + "&conversationId=" + conversationId);
  const messages = res.messages || [];
  const chatMessages = qs("chatMessages");

  chatMessages.innerHTML = messages.length ? messages.map(m => {
    const mine = m.sender_id == currentUser.id;
    return `<div class="msg ${mine ? 'me' : 'them'}">${m.message}</div>`;
  }).join('') : '<div class="chat-empty">No hay mensajes aún.</div>';

  const list = document.querySelectorAll('.chat-item');
  list.forEach(item => item.classList.toggle('active', Number(item.dataset.id) === Number(conversationId)));
}

async function sendMessage(){
  if (!activeConversationId) return;
  const input = qs("chatInput");
  const text = input.value.trim();
  if (!text) return;

  const res = await api("chat.php?action=send", "POST", {
    conversationId: activeConversationId,
    senderId: currentUser.id,
    message: text
  });

  if (res.error) {
    toast(res.error, "err");
    return;
  }

  input.value = "";
  openConversation(activeConversationId);
}

/* ════════════════════════════════════════════════════════════════
   PERFIL
════════════════════════════════════════════════════════════════ */
function renderProfile(){
  const u=currentUser;
  qs("profileContent").innerHTML=`
    <div class="card profile-card">
      <div class="profile-label">Nombre</div>
      <div class="profile-value">${u.nombre}</div>
    </div>
    <div class="card profile-card">
      <div class="profile-label">Curso</div>
      <div class="profile-value">${u.curso}</div>
    </div>
    <div class="card profile-card">
      <div class="profile-label">Rol</div>
      <div class="profile-value">${u.rol}</div>
    </div>
    <div class="card profile-card">
      <div class="profile-label">Contacto</div>
      <div class="profile-value">${u.contacto}</div>
    </div>
    <div class="card profile-card" style="grid-column:1/-1">
      <div class="profile-label">Tu tarjeta digital</div>
      <div class="card-code-display">${u.card_code}</div>
      <p style="color:var(--muted);font-size:.78rem;margin-top:10px">Comparte este código con el administrador para recargar saldo.</p>
    </div>
    <div class="card profile-card" style="text-align:center">
      <div class="profile-label">Saldo disponible</div>
      <div style="font-family:'Syne',sans-serif;font-size:2.5rem;font-weight:800;color:var(--accent)">${u.monedas} monedas</div>
    </div>
    ${u.is_admin==1?`<div class="card profile-card" style="text-align:center;border-color:#f0c04055">
      <div class="profile-label">Estado</div>
      <div class="profile-value" style="color:var(--accent)">⚙️ Administrador</div>
    </div>`:""}
  `;
}

/* ════════════════════════════════════════════════════════════════
   ADMIN
════════════════════════════════════════════════════════════════ */
async function loadAdmin(){
  if(currentUser.is_admin!=1){qs("adminContent").innerHTML='<p style="color:var(--red)">No autorizado.</p>';return;}
  const [stats,commissions,credits,invites,promoted,users]=await Promise.all([
    api(API.admin+"?action=stats&adminId="+currentUser.id),
    api(API.admin+"?action=commissions&adminId="+currentUser.id),
    api(API.admin+"?action=credits&adminId="+currentUser.id),
    api(API.admin+"?action=invites&adminId="+currentUser.id),
    api(API.admin+"?action=promoted&adminId="+currentUser.id),
    api(API.admin+"?action=users&adminId="+currentUser.id),
  ]);
  adminUsers=users.users||[];

  qs("adminContent").innerHTML=`
    <!-- Stats -->
    <div class="admin-grid">
      <div class="card stat-card"><div class="stat-value">${stats.users||0}</div><div class="stat-label">Usuarios totales</div></div>
      <div class="card stat-card"><div class="stat-value">${stats.admins||0}</div><div class="stat-label">Administradores</div></div>
      <div class="card stat-card"><div class="stat-value">${commissions.total||0}</div><div class="stat-label">Comisiones cobradas</div></div>
      <div class="card stat-card"><div class="stat-value">${(invites.codes||[]).length}</div><div class="stat-label">Códigos disponibles</div></div>
    </div>

    <!-- Acciones -->
    <div class="admin-section">
      <h3>Acciones rápidas</h3>
      <div style="display:flex;flex-wrap:wrap;gap:10px">
        <button class="btn btn-primary" onclick="openModal('modalCredit')">Cargar saldo</button>
        <button class="btn btn-secondary" onclick="openModal('modalInvites')">Generar invitaciones</button>
        <button class="btn btn-danger" onclick="doReset()">Reinicio total</button>
      </div>
    </div>

    <!-- Códigos disponibles -->
    <div class="admin-section">
      <h3>Códigos de invitación disponibles</h3>
      ${(invites.codes||[]).length?`
        <div style="display:flex;flex-wrap:wrap;gap:8px">
          ${(invites.codes||[]).map(c=>`<span class="badge badge-blue" style="font-size:.8rem">${c}</span>`).join("")}
        </div>`
      :'<p style="color:var(--muted);font-size:.85rem">No hay códigos disponibles.</p>'}
    </div>

    <!-- Usuarios registrados -->
    <div class="admin-section">
      <div style="display:flex;justify-content:space-between;gap:14px;align-items:center;margin-bottom:14px;flex-wrap:wrap">
        <h3 style="margin:0">Usuarios registrados</h3>
        <input id="adminUserSearch" type="search" placeholder="Buscar por nombre, curso o contacto" oninput="filterAdminUsers()" style="max-width:330px"/>
      </div>
      <div style="overflow-x:auto">
        <table>
          <thead><tr><th>Nombre</th><th>Curso</th><th>Rol</th><th>Contacto</th><th>Saldo</th><th>Productos</th><th>Acciones</th></tr></thead>
          <tbody id="adminUsersList"></tbody>
        </table>
      </div>
    </div>

    <!-- Historial recargas -->
    <div class="admin-section">
      <h3>Últimas recargas</h3>
      ${(credits.credits||[]).length?`
        <table>
          <thead><tr><th>Usuario</th><th>Cantidad</th><th>Nota</th><th>Fecha</th><th></th></tr></thead>
          <tbody>
            ${(credits.credits||[]).map(c=>`
              <tr>
                <td>${c.usuario}</td>
                <td>${coin(c.amount)}</td>
                <td>${c.note}</td>
                <td>${fmtDate(c.created_at)}</td>
                <td><button class="btn btn-danger btn-sm" onclick="deleteCredit(${c.id})">✕</button></td>
              </tr>`).join("")}
          </tbody>
        </table>`
      :'<p style="color:var(--muted);font-size:.85rem">No hay recargas.</p>'}
    </div>

    <!-- Productos destacados -->
    <div class="admin-section">
      <h3>Productos actualmente destacados</h3>
      ${(promoted.products||[]).length?`
        <table>
          <thead><tr><th>Producto</th><th>Vendedor</th><th>Destacado hasta</th></tr></thead>
          <tbody>
            ${(promoted.products||[]).map(p=>`
              <tr>
                <td>${p.nombre}</td>
                <td>${p.vendedor_nombre}</td>
                <td>${fmtDate(p.promoted_until)}</td>
              </tr>`).join("")}
          </tbody>
        </table>`
      :'<p style="color:var(--muted);font-size:.85rem">Ningún producto destacado.</p>'}
    </div>
  `;
  renderAdminUsers(adminUsers);
}

function renderAdminUsers(users){
  const list=qs("adminUsersList");
  if(!list)return;
  if(!users.length){
    list.innerHTML='<tr><td colspan="7" style="text-align:center;color:var(--muted)">No se encontraron usuarios.</td></tr>';
    return;
  }
  list.innerHTML=users.map(u=>{
    const admin=u.is_admin==1?' <span class="badge badge-yellow">Administrador</span>':'';
    const deletion=u.id==currentUser.id
      ?'<button class="btn btn-secondary btn-sm" disabled>Sesión actual</button>'
      :'<button class="btn btn-danger btn-sm" onclick="deleteUser('+u.id+')">Eliminar</button>';
    return '<tr>'
      +'<td>'+u.nombre+admin+'</td>'
      +'<td>'+u.curso+'</td>'
      +'<td>'+u.rol+'</td>'
      +'<td>'+u.contacto+'</td>'
      +'<td>'+coin(u.monedas)+'</td>'
      +'<td>'+u.product_count+'</td>'
      +'<td style="white-space:nowrap"><button class="btn btn-secondary btn-sm" onclick="changeUserPassword('+u.id+')">Contraseña</button> '+deletion+'</td>'
      +'</tr>';
  }).join("");
}

function filterAdminUsers(){
  const query=qs("adminUserSearch").value.trim().toLowerCase();
  const filtered=adminUsers.filter(u=>
    [u.nombre,u.curso,u.rol,u.contacto,u.card_code].some(value=>
      String(value||"").toLowerCase().includes(query)
    )
  );
  renderAdminUsers(filtered);
}

async function changeUserPassword(userId){
  const user=adminUsers.find(u=>u.id==userId);
  const password=prompt("Nueva contraseña para "+(user?.nombre||"este usuario")+" (mínimo 4 caracteres):");
  if(password===null)return;
  if(password.length<4){toast("La contraseña debe tener al menos 4 caracteres.","err");return;}
  const res=await api(API.admin+"?action=update_password","POST",{adminId:currentUser.id,userId,password});
  if(res.error){toast(res.error,"err");return;}
  toast("Contraseña actualizada.");
}

async function deleteUser(userId){
  const user=adminUsers.find(u=>u.id==userId);
  if(!user)return;
  if(!confirm("¿Eliminar a "+user.nombre+"? También se borrarán sus productos, compras y recargas."))return;
  const res=await api(API.admin+"?action=delete_user","POST",{adminId:currentUser.id,userId});
  if(res.error){toast(res.error,"err");return;}
  toast("Usuario eliminado.");
  loadAdmin();
}

async function doCredit(){
  const cardCode=qs("creditCard").value.trim().toUpperCase();
  const amount=parseInt(qs("creditAmount").value)||0;
  const note=qs("creditNote").value.trim()||"Recarga semanal";
  if(!cardCode||amount<1){toast("Datos inválidos","err");return;}
  const res=await api(API.admin+"?action=credit","POST",{adminId:currentUser.id,cardCode,amount,note});
  if(res.error){toast(res.error,"err");return;}
  closeModal("modalCredit");
  toast("Saldo cargado a "+res.user.nombre+".");
  loadAdmin();
}
function closeMCredit(e){if(e.target.id==="modalCredit")closeModal("modalCredit")}

async function doAddInvites(){
  const count=parseInt(qs("inviteCount").value)||0;
  if(count<1||count>500){toast("Cantidad inválida","err");return;}
  const res=await api(API.admin+"?action=invites_add","POST",{adminId:currentUser.id,count});
  if(res.error){toast(res.error,"err");return;}
  closeModal("modalInvites");
  toast(res.added+" código(s) generados.");
  loadAdmin();
}
function closeMInvites(e){if(e.target.id==="modalInvites")closeModal("modalInvites")}

async function deleteCredit(id){
  if(!confirm("¿Eliminar este registro de recarga?"))return;
  const res=await api(API.admin+"?action=delete_credit","POST",{adminId:currentUser.id,id});
  if(res.error){toast(res.error,"err");return;}
  toast("Registro eliminado");
  loadAdmin();
}

async function doReset(){
  if(!confirm("ESTO BORRARÁ TODOS LOS USUARIOS, PRODUCTOS Y COMPRAS.\n¿Estás absolutamente seguro?"))return;
  if(!confirm("Segunda confirmación: ¿borrar todo el sistema?"))return;
  const res=await api(API.admin+"?action=reset","POST",{adminId:currentUser.id});
  if(res.error){toast(res.error,"err");return;}
  toast("Sistema reiniciado. Cerrando sesión…");
  setTimeout(doLogout,2000);
}

/* ════════════════════════════════════════════════════════════════
   INIT — restaurar sesión
════════════════════════════════════════════════════════════════ */
(function init(){
  const resetToken=new URLSearchParams(location.search).get("token");
  if(resetToken){
    showAuthTab("login");
    qs("loginForm").classList.add("hidden");
    qs("passwordResetForm").classList.remove("hidden");
  }
  const saved=resetToken ? null : localStorage.getItem("mdUser");
  if(saved){
    try{
      currentUser=JSON.parse(saved);
      // Refrescar datos del usuario desde el servidor
      api(API.user+"?id="+currentUser.id).then(res=>{
        if(res.user){
          currentUser=res.user;
          localStorage.setItem("mdUser",JSON.stringify(currentUser));
          updateCoinDisplay();
        }
      });
      startApp();
    } catch(e){localStorage.removeItem("mdUser");}
  }
  loadCartFromStorage();
})();
</script>
</body>
</html>
