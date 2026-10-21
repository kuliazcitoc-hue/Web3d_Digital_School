<?php
// index.php — Frontend SPA del Mercado Digital
// Este archivo no necesita lógica PHP; sirve el HTML/JS que consume la API REST.
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="theme-color" content="#0b0d12"/>
<link rel="icon" href="data:,"/>
<title>Coinmash — Mercado escolar</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet"/>
<style>
  /* ── Variables ───────────────────────────────────────────────── */
  :root{
    color-scheme:dark;
    --bg:#0b0d12;
    --surface:#121620;
    --surface2:#1a1f2b;
    --border:#272e3d;
    --accent:#f4c95d;
    --accent2:#eb9752;
    --text:#f3f4f8;
    --muted:#939bad;
    --red:#e66a6a;
    --green:#62c69a;
    --radius:16px;
    --shadow:0 16px 50px rgba(0,0,0,.28);
    --ease:cubic-bezier(.2,.75,.25,1);
  }
  *{box-sizing:border-box;margin:0;padding:0}
  html{background:var(--bg);scroll-behavior:smooth}
  body{background:radial-gradient(ellipse 60% 30% at 50% -10%,#2a251a66,transparent),var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;min-height:100vh;overflow-x:hidden}
  h1,h2,h3,h4{font-family:'Syne',sans-serif}
  button,input,textarea,select{touch-action:manipulation}
  button:focus-visible,a:focus-visible,input:focus-visible,textarea:focus-visible,select:focus-visible{
    outline:2px solid var(--accent);outline-offset:3px
  }

  /* ── Scrollbar ───────────────────────────────────────────────── */
  ::-webkit-scrollbar{width:6px}::-webkit-scrollbar-track{background:var(--bg)}::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}

  /* ── Utilities ───────────────────────────────────────────────── */
  .hidden{display:none!important}
  .coinmash-intro{
    position:fixed;inset:0;z-index:3000;display:grid;place-items:center;overflow:hidden;
    background:radial-gradient(ellipse at 50% 42%,#242333 0%,#11131b 48%,#090a0f 100%);
    animation:coinmash-fade-in .28s ease-out both
  }
  .coinmash-intro::before,.coinmash-intro::after{
    content:"";position:absolute;width:340px;height:340px;border:1px solid #f0c04016;border-radius:50%;
    animation:coinmash-orbit 12s linear infinite
  }
  .coinmash-intro::after{width:500px;height:500px;animation-direction:reverse}
  .coinmash-content{position:relative;z-index:1;text-align:center;padding:28px;animation:coinmash-rise .65s cubic-bezier(.2,.8,.2,1) both}
  .coinmash-kicker{font-size:.7rem;font-weight:700;letter-spacing:.22em;color:var(--muted);text-transform:uppercase}
  .coinmash-emblems{height:112px;display:flex;align-items:center;justify-content:center;gap:24px;margin:8px 0}
  .coinmash-coin{
    width:82px;height:82px;display:grid;place-items:center;border:4px solid #ffe08a;border-radius:50%;
    background:radial-gradient(circle at 35% 28%,#ffe58b,#e8a91a 70%);color:#583700;
    box-shadow:0 0 46px #f0c04066,inset 0 0 0 4px #d28d0d;
    font-family:'Syne',sans-serif;font-size:2.4rem;font-weight:800;
    animation:coinmash-flip .75s ease-in-out 3
  }
  .coinmash-school-icon{width:38px;height:38px;color:#d9deeb;filter:drop-shadow(0 5px 12px #0008);animation:coinmash-bob 1.4s ease-in-out infinite}
  .coinmash-school-icon:last-child{animation-delay:.35s}
  .coinmash-title{font-family:'Syne',sans-serif;font-size:clamp(2.5rem,9vw,4.5rem);font-weight:800;letter-spacing:.08em;
    background:linear-gradient(100deg,#fff1bd 10%,var(--accent) 48%,#f28c42 90%);color:transparent;background-clip:text}
  .coinmash-welcome{margin-top:8px;color:var(--muted);font-size:.95rem}
  .coinmash-progress{width:min(220px,60vw);height:3px;margin:24px auto 0;border-radius:9px;background:#ffffff18;overflow:hidden}
  .coinmash-progress span{display:block;width:100%;height:100%;background:linear-gradient(90deg,var(--accent),#f28c42);
    transform-origin:left;animation:coinmash-progress 2.4s linear both}
  .coinmash-skip{position:absolute;z-index:2;right:20px;top:18px;background:#ffffff0d;border:1px solid #ffffff20;
    color:var(--text);padding:8px 13px;border-radius:8px;font-size:.8rem}
  .coinmash-skip:hover{background:#ffffff18}
  @keyframes coinmash-fade-in{from{opacity:0}to{opacity:1}}
  @keyframes coinmash-rise{from{opacity:0;transform:translateY(16px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
  @keyframes coinmash-flip{0%,100%{transform:rotateY(0) scale(1)}50%{transform:rotateY(180deg) scale(1.08)}}
  @keyframes coinmash-bob{0%,100%{transform:translateY(3px) rotate(-5deg)}50%{transform:translateY(-8px) rotate(5deg)}}
  @keyframes coinmash-orbit{to{transform:rotate(360deg)}}
  @keyframes coinmash-progress{to{transform:scaleX(0)}}
  @media(prefers-reduced-motion:reduce){
    .coinmash-intro,.coinmash-content,.coinmash-intro::before,.coinmash-intro::after,
    .coinmash-coin,.coinmash-school-icon,.coinmash-progress span{animation:none}
  }
  .badge{display:inline-block;padding:2px 10px;border-radius:20px;font-size:.72rem;font-weight:600;letter-spacing:.04em}
  .badge-yellow{background:#f0c04022;color:var(--accent);border:1px solid #f0c04055}
  .badge-green{background:#4caf8022;color:var(--green);border:1px solid #4caf8055}
  .badge-red{background:#e0555522;color:var(--red);border:1px solid #e0555555}
  .badge-blue{background:#4090e022;color:#60aaff;border:1px solid #4090e055}

  /* ── Botones ─────────────────────────────────────────────────── */
  button{cursor:pointer;border:none;font-family:'DM Sans',sans-serif}
  .btn{padding:10px 22px;border-radius:10px;font-size:.9rem;font-weight:600;transition:transform .2s var(--ease),background-color .2s,border-color .2s,box-shadow .2s}
  .btn-primary{background:linear-gradient(135deg,#f6d46e,#eeb845);color:#241a05;font-weight:800;box-shadow:0 5px 18px #eab94822}
  .btn-primary:hover{background:linear-gradient(135deg,#ffe18b,#f2c452);transform:translateY(-2px);box-shadow:0 9px 24px #eab94833}
  .btn-secondary{background:#ffffff07;color:var(--text);border:1px solid var(--border)}
  .btn-secondary:hover{background:var(--surface2);border-color:#414b5e;transform:translateY(-1px)}
  .btn-danger{background:var(--red);color:#fff}
  .btn-danger:hover{background:#ff6666}
  .btn-sm{padding:6px 14px;font-size:.8rem}
  .btn-icon{background:none;padding:8px;border-radius:8px;color:var(--muted);transition:color .15s,background .15s}
  .btn-icon:hover{background:var(--surface2);color:var(--text)}

  /* ── Inputs ──────────────────────────────────────────────────── */
  input,textarea,select{
    background:var(--surface2);border:1px solid var(--border);color:var(--text);
    border-radius:8px;padding:10px 14px;font-family:'DM Sans',sans-serif;font-size:.9rem;
    transition:border-color .15s;width:100%
  }
  input:focus,textarea:focus,select:focus{border-color:var(--accent);box-shadow:0 0 0 3px #f4c95d1c}
  label{font-size:.8rem;color:#bdc3d0;margin-bottom:7px;display:block;font-weight:600}
  .field{margin-bottom:14px}

  /* ── Tarjetas ────────────────────────────────────────────────── */
  .card{background:linear-gradient(155deg,#171c27,#11151d 76%);border:1px solid #ffffff12;border-radius:var(--radius);padding:20px;box-shadow:var(--shadow)}

  /* ── Carrito ─────────────────────────────────────────────────── */
  .cart-toggle{
    position:relative;display:inline-flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);
    color:var(--text);padding:8px 12px;border-radius:999px;font-size:.8rem;font-weight:700
  }
  .cart-toggle:hover{background:var(--border)}
  .cart-toggle svg{width:18px;height:18px;flex:none}
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
  .cart-item-media.noimg{display:flex;align-items:center;justify-content:center;color:var(--muted)}
  .cart-item-media.noimg svg{width:25px;height:25px}
  .icon-stroke{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
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
    transition:transform .3s var(--ease),opacity .3s;pointer-events:none;max-width:min(360px,calc(100vw - 32px));line-height:1.4
  }
  #toast.show{transform:translateY(0);opacity:1}
  #toast.ok{border-left:4px solid var(--green)}
  #toast.err{border-left:4px solid var(--red)}

  /* ── Modal ───────────────────────────────────────────────────── */
  .modal-overlay{
    position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:500;
    display:flex;align-items:center;justify-content:center;padding:20px
  }
  .modal{  background:linear-gradient(155deg,#191e29,#11151d 76%);border:1px solid #ffffff17;border-radius:20px;
  padding:30px;width:100%;max-width:460px;box-shadow:0 28px 90px #0009;position:relative;animation:modal-enter .22s var(--ease) both}
  .modal h3{font-size:1.2rem;margin-bottom:18px}
  .modal-close{position:absolute;top:14px;right:14px;background:none;border:none;
    color:var(--muted);cursor:pointer;padding:8px;border-radius:8px;display:grid;place-items:center}
  .modal-close svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round}
  .modal-close:hover{background:var(--surface2);color:var(--text)}

  /* ── Loader ──────────────────────────────────────────────────── */
  .spinner{width:36px;height:36px;border:3px solid var(--border);border-top-color:var(--accent);border-radius:50%;animation:spin .7s linear infinite;margin:40px auto}
  @keyframes spin{to{transform:rotate(360deg)}}

  /* ══════════════════════════════════════════════════════════════ */
  /* AUTH                                                           */
  /* ══════════════════════════════════════════════════════════════ */
  #authPage{
    min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;
    background:radial-gradient(ellipse 46% 40% at 50% 0%,#4a35151c,transparent 80%),radial-gradient(ellipse at 0% 100%,#202a3a22,transparent 48%),var(--bg)
  }
  .auth-box{position:relative;overflow:hidden;width:100%;max-width:440px;padding:38px 36px;background:linear-gradient(155deg,#171c27,#10141c 76%);border-color:#ffffff17}
  .auth-box::before{content:"";position:absolute;inset:0 0 auto;height:2px;background:linear-gradient(90deg,transparent,var(--accent),var(--accent2),transparent)}
  .auth-brand{font-size:1.45rem;margin-bottom:10px}
  .auth-sub{color:var(--muted);font-size:.9rem;margin-bottom:28px}
  .auth-tabs{display:flex;gap:6px;margin-bottom:22px;background:#0c0f15;
    border-radius:10px;padding:4px}
  .auth-tab{flex:1;padding:9px;border-radius:8px;background:none;color:var(--muted);
    font-size:.85rem;font-weight:600;transition:color .2s,background-color .2s,box-shadow .2s;font-family:'Syne',sans-serif}
  .auth-tab.active{background:var(--surface2);color:var(--text);box-shadow:0 4px 14px #0004}
  .auth-footer{margin-top:14px;color:var(--muted);font-size:.8rem;text-align:center}

  /* ══════════════════════════════════════════════════════════════ */
  /* APP SHELL                                                      */
  /* ══════════════════════════════════════════════════════════════ */
  #appPage{display:flex;flex-direction:column;min-height:100vh}

  /* ── Topbar ──────────────────────────────────────────────────── */
  #topbar{
    background:#10131bd9;border-bottom:1px solid #ffffff10;backdrop-filter:blur(18px);
    padding:0 max(28px,calc((100vw - 1440px)/2));height:72px;display:flex;align-items:center;
    justify-content:space-between;position:sticky;top:0;z-index:100
  }
  .topbar-logo,.auth-brand{display:flex;align-items:center;gap:11px;font-family:'Syne',sans-serif;font-weight:800;font-size:1.13rem;letter-spacing:.01em;color:var(--text)}
  .brand-mark{width:34px;height:34px;display:grid;place-items:center;border-radius:11px;background:linear-gradient(145deg,#f7d675,#e99a43);color:#281c08;box-shadow:0 5px 20px #eeb84a2e}
  .brand-mark svg{width:20px;height:20px}
  .topbar-nav{display:flex;gap:5px}
  .nav-btn{background:none;border:1px solid transparent;color:var(--muted);padding:9px 13px;
    border-radius:10px;font-size:.82rem;font-weight:600;font-family:'DM Sans',sans-serif;
    transition:color .18s,background-color .18s,border-color .18s;cursor:pointer}
  .nav-btn:hover{color:var(--text);background:#ffffff08}
  .nav-btn.active{color:var(--accent);background:#f4c95d12;border-color:#f4c95d22}
  .topbar-right{display:flex;align-items:center;gap:12px}
  .coin-badge{background:linear-gradient(135deg,#f4c95d18,#eb97520d);border:1px solid #f4c95d36;color:var(--accent);
    padding:8px 13px;border-radius:12px;font-size:.82rem;font-weight:700;
    font-family:'Syne',sans-serif}

  /* ── Main content ────────────────────────────────────────────── */
  #mainContent{flex:1;padding:34px 32px 60px;max-width:1440px;width:100%;margin:0 auto}

  /* ── Sección título ──────────────────────────────────────────── */
  .section-header{display:flex;justify-content:space-between;align-items:center;gap:18px;margin-bottom:24px}
  .section-title{font-size:clamp(1.4rem,3vw,1.85rem);font-weight:800;letter-spacing:-.035em;text-wrap:balance}
  .section-heading{display:grid;gap:5px}
  .section-eyebrow{font-size:.68rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase;color:var(--accent)}
  .section-caption{color:var(--muted);font-size:.88rem}
  .market-hero{
    position:relative;isolation:isolate;overflow:hidden;display:flex;align-items:center;justify-content:space-between;gap:24px;
    min-height:190px;margin:2px 0 30px;padding:32px 38px;border:1px solid #f4c95d22;border-radius:22px;
    background:radial-gradient(ellipse at 85% 50%,#f4c95d18,transparent 42%),linear-gradient(110deg,#191d28,#141821 62%,#1b1b1b);
    animation:card-enter .5s var(--ease) both
  }
  .market-hero::after{content:"";position:absolute;z-index:-1;width:250px;height:250px;right:8%;top:-125px;border:1px solid #f4c95d22;border-radius:50%;box-shadow:0 0 0 30px #f4c95d08,0 0 0 60px #f4c95d05}
  .market-hero-copy{max-width:620px}
  .market-hero-kicker{display:flex;align-items:center;gap:8px;color:var(--accent);font-size:.72rem;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
  .market-hero-kicker::before{content:"";width:22px;height:1px;background:var(--accent)}
  .market-hero h1{margin-top:12px;font-size:clamp(1.7rem,4vw,2.65rem);line-height:1.08;letter-spacing:-.045em;text-wrap:balance}
  .market-hero p{max-width:480px;margin-top:10px;color:#a7adba;font-size:.91rem;line-height:1.6}
  .market-hero-art{position:relative;z-index:1;display:grid;place-items:center;width:112px;height:112px;flex:0 0 112px;border:1px solid #f4c95d38;border-radius:30px;background:linear-gradient(145deg,#f4c95d1a,#f4c95d04);transform:rotate(8deg);animation:hero-float 4s ease-in-out infinite}
  .market-hero-art svg{width:56px;height:56px;color:var(--accent)}

  /* ══════════════════════════════════════════════════════════════ */
  /* PRODUCTOS GRID                                                 */
  /* ══════════════════════════════════════════════════════════════ */
  .products-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
    gap:20px
  }
  .product-card{
    background:linear-gradient(165deg,#171c27,#11151d 72%);border:1px solid #ffffff12;border-radius:18px;
    overflow:hidden;transition:transform .28s var(--ease),box-shadow .28s var(--ease),border-color .2s;cursor:pointer;
    animation:card-enter .48s var(--ease) both
  }
  .product-card:nth-child(2){animation-delay:35ms}
  .product-card:nth-child(3){animation-delay:70ms}
  .product-card:nth-child(4){animation-delay:105ms}
  .product-card:nth-child(5){animation-delay:140ms}
  .product-card:nth-child(6){animation-delay:175ms}
  .product-card:hover{transform:translateY(-5px);border-color:#f4c95d55;box-shadow:0 18px 36px rgba(0,0,0,.28),0 0 0 1px #f4c95d0c}
  .product-card.promoted{border-color:#f4c95d55;box-shadow:0 0 0 1px #f4c95d14}
  .product-img{width:100%;height:190px;object-fit:cover;background:linear-gradient(145deg,#202635,#171b25);
    display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:2.5rem}
  .product-img img{width:100%;height:100%;object-fit:cover}
  .product-placeholder{display:grid;place-items:center;gap:10px;color:#8d96a9;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
  .product-placeholder svg{width:36px;height:36px;color:#707b90}
  .mini-cart-btn:disabled{opacity:.55;cursor:not-allowed}
  .product-body{padding:17px}
  .product-name{font-family:'Syne',sans-serif;font-weight:700;font-size:1rem;margin-bottom:4px;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .product-desc{font-size:.8rem;color:var(--muted);margin-bottom:10px;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .product-footer{display:flex;justify-content:space-between;align-items:center}
  .product-price{font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:var(--accent);font-variant-numeric:tabular-nums}
  .product-stock{font-size:.78rem;color:var(--muted)}
  .promoted-tag{
    background:linear-gradient(90deg,var(--accent),var(--accent2));
    color:#241a05;font-size:.65rem;font-weight:800;padding:5px 10px;
    position:absolute;top:12px;right:12px;border-radius:999px;letter-spacing:.08em;box-shadow:0 4px 14px #0004
  }

  /* ══════════════════════════════════════════════════════════════ */
  /* PERFIL                                                         */
  /* ══════════════════════════════════════════════════════════════ */
  .profile-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
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
  .admin-section{margin-bottom:32px;padding:24px;border:1px solid #ffffff0d;border-radius:18px;background:#11151c99;animation:card-enter .42s var(--ease) both}
  .admin-section h3{font-size:1rem;margin-bottom:14px;color:var(--muted);
    text-transform:uppercase;letter-spacing:.08em}
  table{width:100%;border-collapse:collapse;font-size:.85rem;font-variant-numeric:tabular-nums}
  th{padding:10px 12px;text-align:left;color:var(--muted);font-weight:600;
    border-bottom:1px solid var(--border);font-size:.78rem;text-transform:uppercase}
  td{padding:12px;border-bottom:1px solid var(--border)}
  tbody tr{transition:background-color .16s}
  tr:last-child td{border-bottom:none}
  tr:hover td{background:rgba(255,255,255,.02)}

  /* ══════════════════════════════════════════════════════════════ */
  /* HISTORIAL                                                      */
  /* ══════════════════════════════════════════════════════════════ */
  .purchase-item{
    display:flex;justify-content:space-between;align-items:center;
    padding:14px 18px;background:var(--surface);border:1px solid var(--border);
    border-radius:13px;margin-bottom:10px;transition:transform .2s var(--ease),border-color .2s
  }
  .purchase-item:hover{transform:translateX(3px);border-color:#f4c95d30}
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
  .nav-btn,.mini-cart-btn,.cart-toggle,.qty-box button,.cart-remove{touch-action:manipulation}

  /* ══════════════════════════════════════════════════════════════ */
  /* RESPONSIVE                                                     */
  /* ══════════════════════════════════════════════════════════════ */
  @keyframes card-enter{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
  @keyframes modal-enter{from{opacity:0;transform:translateY(12px) scale(.985)}to{opacity:1;transform:translateY(0) scale(1)}}
  @keyframes hero-float{0%,100%{transform:translateY(0) rotate(8deg)}50%{transform:translateY(-7px) rotate(4deg)}}
  @media(prefers-reduced-motion:reduce){
    *,*::before,*::after{scroll-behavior:auto!important;animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}
  }
  @media(max-width:700px){
    #topbar{padding:0 16px;backdrop-filter:none}
    .topbar-nav{position:fixed;inset:auto 0 0;z-index:110;display:flex;gap:5px;overflow-x:auto;justify-content:flex-start;padding:9px 12px calc(9px + env(safe-area-inset-bottom));background:#10131bed;border-top:1px solid #ffffff14;backdrop-filter:blur(18px);scrollbar-width:none;overscroll-behavior:contain}
    .topbar-nav::-webkit-scrollbar{display:none}
    .nav-btn{flex:0 0 auto;padding:9px 12px}
    #mainContent{padding:22px 16px calc(92px + env(safe-area-inset-bottom))}
    .market-hero{min-height:160px;padding:24px;border-radius:18px}
    .market-hero-art{width:72px;height:72px;flex-basis:72px;border-radius:22px}
    .market-hero-art svg{width:38px;height:38px}
    .section-header{align-items:flex-start}
    .products-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr))}
    .product-img{height:155px}
    .chat-shell{grid-template-columns:1fr}
    .chat-list{min-height:0;max-height:210px;overflow-y:auto}
    .chat-box{min-height:390px}
    .msg{max-width:88%}
  }
  @media(max-width:430px){
    #topbar{height:64px;padding:0 12px}
    .topbar-logo{gap:8px;font-size:.9rem}
    .brand-mark{width:28px;height:28px}
    .topbar-right{gap:5px}
    .topbar-right .btn-sm{padding:6px 9px}
    .coin-badge{padding:6px 7px;font-size:.66rem;white-space:nowrap}
    .cart-toggle{padding:7px 9px}
    .cart-toggle>span:nth-child(2){display:none}
    .market-hero{padding:21px 18px}
    .market-hero-art{display:none}
    .section-header{flex-direction:column;align-items:stretch}
    .section-header>input{max-width:none!important}
    .products-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .product-img{height:128px}
    .product-body{padding:13px}
    .product-footer{align-items:flex-start;flex-direction:column;gap:3px}
    .product-price{font-size:.9rem;white-space:nowrap}
  }
</style>
</head>
<body>

<!-- ══ TOAST ══════════════════════════════════════════════════════ -->
<div id="toast" role="status" aria-live="polite" aria-atomic="true"></div>
<div id="coinmashIntro" class="coinmash-intro hidden" role="dialog" aria-modal="true" aria-labelledby="coinmashTitle" aria-hidden="true">
  <button id="coinmashSkip" class="coinmash-skip" type="button" onclick="closeCoinmashIntro()">Saltar</button>
  <div class="coinmash-content">
    <div class="coinmash-kicker">Tu mercado escolar</div>
    <div class="coinmash-emblems" aria-hidden="true">
      <svg class="coinmash-school-icon" viewBox="0 0 40 40" fill="none"><path d="M6 9.5C11.3 7.2 16.1 7.7 20 10v23c-3.9-2.3-8.7-2.8-14-0.5v-23ZM34 9.5C28.7 7.2 23.9 7.7 20 10v23c3.9-2.3 8.7-2.8 14-.5v-23Z" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round"/><path d="M10 14c2.7-.8 5-.5 7.2.5M10 19c2.7-.8 5-.5 7.2.5m12-6c-2.7-.8-5-.5-7.2.5m7.2 4.5c-2.7-.8-5-.5-7.2.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      <span class="coinmash-coin">M</span>
      <svg class="coinmash-school-icon" viewBox="0 0 40 40" fill="none"><path d="m9 28 2.2-7.1L27.5 4.6a3.3 3.3 0 0 1 4.7 0l3.2 3.2a3.3 3.3 0 0 1 0 4.7L19.1 28.8 12 31l-3-3Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/><path d="m24.9 7.2 7.9 7.9M8 34h25" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
    </div>
    <h1 id="coinmashTitle" class="coinmash-title">COINMASH</h1>
    <p id="coinmashWelcome" class="coinmash-welcome">Preparando tu mercado…</p>
    <div class="coinmash-progress" aria-hidden="true"><span></span></div>
  </div>
</div>

<!-- ══ AUTH PAGE ══════════════════════════════════════════════════ -->
<div id="authPage">
  <div class="card auth-box">
    <div class="auth-brand">
      <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="2"/><path d="M15.4 8.8c-.7-.7-1.8-1.1-3.2-1.1-1.8 0-3 .8-3 2s1 1.7 3.1 2.1c2 .4 3.1 1 3.1 2.2s-1.3 2.2-3.2 2.2c-1.4 0-2.7-.5-3.5-1.4M12.2 5.8v12.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
      <span>Coinmash</span>
    </div>
    <div class="auth-sub">El mercado escolar, hecho para compartir.</div>

    <div class="auth-tabs">
      <button class="auth-tab active" onclick="showAuthTab('login')">Ingresar</button>
      <button class="auth-tab" onclick="showAuthTab('register')">Registrarse</button>
    </div>

    <!-- Login -->
    <div id="loginForm">
      <div class="field"><label>Nombre completo</label><input id="lNombre" placeholder="Tu nombre completo"/></div>
      <div class="field"><label>Contraseña</label><input id="lPassword" type="password" placeholder="Contraseña"/></div>
      <button class="btn btn-primary" style="width:100%;margin-top:6px" onclick="doLogin()">Entrar</button>
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
      <button class="btn btn-primary" style="width:100%;margin-top:6px" onclick="doRegister()">Crear cuenta</button>
    </div>

    <div class="auth-footer">COINMASH · Un mercado hecho por estudiantes</div>
  </div>
</div>

<!-- ══ APP PAGE ════════════════════════════════════════════════════ -->
<div id="appPage" class="hidden">
  <!-- Topbar -->
  <div id="topbar">
    <div class="topbar-logo">
      <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="2"/><path d="M15.4 8.8c-.7-.7-1.8-1.1-3.2-1.1-1.8 0-3 .8-3 2s1 1.7 3.1 2.1c2 .4 3.1 1 3.1 2.2s-1.3 2.2-3.2 2.2c-1.4 0-2.7-.5-3.5-1.4M12.2 5.8v12.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></span>
      <span>Coinmash</span>
    </div>
    <nav class="topbar-nav">
      <button class="nav-btn active" onclick="showSection('market')">Mercado</button>
      <button class="nav-btn" onclick="showSection('myProducts')" id="navMyProducts">Mis productos</button>
      <button class="nav-btn" onclick="showSection('purchases')">Historial</button>
      <button class="nav-btn" onclick="showSection('chat')">Chat</button>
      <button class="nav-btn" onclick="showSection('profile')">Perfil</button>
      <button class="nav-btn hidden" id="navAdmin" onclick="showSection('admin')">Administración</button>
    </nav>
    <div class="topbar-right">
      <span class="coin-badge" id="coinDisplay">—</span>
      <button class="cart-toggle" onclick="toggleCart()" aria-label="Abrir carrito">
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><path d="M3 4h2l2.2 11.1a2 2 0 0 0 2 1.6h8.4a2 2 0 0 0 1.9-1.4L21 9H6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1.2" fill="currentColor"/><circle cx="18" cy="20" r="1.2" fill="currentColor"/></svg>
        <span>Carrito</span> <span class="cart-badge" id="cartBadge">0</span>
      </button>
      <button class="btn btn-secondary btn-sm" onclick="doLogout()">Salir</button>
    </div>
  </div>

  <!-- Main -->
  <div id="mainContent">

    <!-- ── MERCADO ─────────────────────────────────────────────── -->
    <div id="sectionMarket">
      <div class="section-header">
        <div class="section-heading">
          <span class="section-eyebrow">Descubre algo nuevo</span>
          <span class="section-title">El mercado</span>
        </div>
        <input id="searchInput" placeholder="Buscar productos…" style="max-width:240px" oninput="filterProducts()"/>
      </div>

      <section class="market-hero" aria-label="Bienvenido a Coinmash">
        <div class="market-hero-copy">
          <div class="market-hero-kicker">La comunidad se encuentra aquí</div>
          <h1>Buenas ideas.<br/>Grandes recreos.</h1>
          <p>Encuentra productos hechos por estudiantes y descubre lo mejor de tu comunidad escolar.</p>
        </div>
        <div class="market-hero-art" aria-hidden="true">
          <svg viewBox="0 0 64 64" fill="none"><path d="M12 19.5c6.8-3 13.4-2.5 20 1v29c-6.6-3.5-13.2-4-20-1v-29Zm40 0c-6.8-3-13.4-2.5-20 1v29c6.6-3.5 13.2-4 20-1v-29Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><path d="M18 27c3.7-1.1 7.2-.8 10.5.6M18 34c3.7-1.1 7.2-.8 10.5.6m17-8.2c-3.7-1.1-7.2-.8-10.5.6M45.5 34c-3.7-1.1-7.2-.8-10.5.6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="49" cy="13" r="7" fill="#f4c95d" fill-opacity=".2" stroke="currentColor" stroke-width="1.5"/><path d="M51.7 10.9c-.5-.5-1.3-.8-2.4-.8-1.3 0-2.2.6-2.2 1.5s.7 1.3 2.2 1.6c1.4.3 2.2.7 2.2 1.6s-.9 1.6-2.3 1.6c-1 0-1.9-.4-2.5-1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
        </div>
      </section>
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
        <button class="btn btn-secondary btn-sm" onclick="loadPurchases()">Actualizar</button>
      </div>
      <div id="purchasesList"><div class="spinner"></div></div>
    </div>

    <!-- ── CHAT ─────────────────────────────────────────────────── -->
    <div id="sectionChat" class="hidden">
      <div class="section-header">
        <span class="section-title">Chat</span>
        <button class="btn btn-secondary btn-sm" onclick="loadConversations()">Actualizar</button>
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
      <div class="section-header"><span class="section-title">Panel de administración</span></div>
      <div id="adminContent"><div class="spinner"></div></div>
    </div>

  </div><!-- /mainContent -->
</div><!-- /appPage -->

<!-- ══ MODALES ═════════════════════════════════════════════════════ -->

<!-- Modal: Ver producto -->
<div id="modalProduct" class="modal-overlay hidden" onclick="closeModalProduct(event)">
  <div class="modal">
    <button class="modal-close" aria-label="Cerrar" onclick="closeModal('modalProduct')"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
    <div id="modalProductContent"></div>
  </div>
</div>

<!-- Modal: Crear / Editar producto -->
<div id="modalProductForm" class="modal-overlay hidden" onclick="closeModalForm(event)">
  <div class="modal" style="max-width:520px">
    <button class="modal-close" aria-label="Cerrar" onclick="closeModal('modalProductForm')"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
    <h3 id="productFormTitle">Nuevo producto</h3>
    <input type="hidden" id="editProductId"/>
    <div class="field"><label>Nombre del producto</label><input id="pNombre" placeholder="Ej: Buñuelo, Cuaderno…"/></div>
    <div class="field"><label>Descripción</label><textarea id="pDesc" rows="3" placeholder="Describe bien tu producto"></textarea></div>
    <div class="field"><label>Precio (monedas)</label><input id="pPrecio" type="number" min="1" placeholder="10"/></div>
    <div class="field"><label>Stock disponible</label><input id="pStock" type="number" min="1" placeholder="5"/></div>
    <div class="field">
      <label>Imagen (opcional)</label>
      <input type="file" id="pImagen" accept="image/*" onchange="previewImage(this)"/>
      <img id="pImagePreview" data-fallback-image="true" style="max-height:120px;margin-top:10px;border-radius:8px;display:none"/>
    </div>
    <button class="btn btn-primary" style="width:100%;margin-top:8px" onclick="saveProduct()">Guardar producto</button>
  </div>
</div>

<!-- Modal: Admin — Cargar saldo -->
<div id="modalCredit" class="modal-overlay hidden" onclick="closeMCredit(event)">
  <div class="modal">
    <button class="modal-close" aria-label="Cerrar" onclick="closeModal('modalCredit')"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
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
    <button class="modal-close" aria-label="Cerrar" onclick="closeModal('modalInvites')"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
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
      <button class="btn-icon" aria-label="Cerrar carrito" onclick="toggleCart(false)"><svg class="icon-stroke" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
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
let adminProducts = [];
let cart = [];
let coinmashIntroTimer = null;
let preparedImageFile = null;
let preparedImageData = null;
let imagePreparationPromise = null;

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

document.addEventListener("error",event=>{
  const img=event.target;
  if(!(img instanceof HTMLImageElement)||!img.dataset.fallbackImage)return;
  if(img.id==="pImagePreview"){img.style.display="none";return;}
  const container=img.parentElement;
  if(container&&container.classList.contains("product-img")){
    container.outerHTML=productPlaceholderHTML();
  }else if(container&&container.classList.contains("cart-item-media")){
    container.innerHTML='<svg class="icon-stroke" viewBox="0 0 24 24" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10"/></svg>';
    container.classList.add("noimg");
  }else{
    img.replaceWith(document.createTextNode("Sin imagen"));
  }
},true);

function qs(id){return document.getElementById(id)}
function closeModal(id){qs(id).classList.add("hidden")}
function openModal(id){qs(id).classList.remove("hidden")}

function productPlaceholderHTML(){
  return '<div class="product-img product-placeholder"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="m4 7 8 4 8-4M12 11v10" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg><span>Sin imagen</span></div>';
}

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
        ${item.image_data ? `<img data-fallback-image="true" src="${item.image_data}" alt="${item.nombre}">` : '<svg class="icon-stroke" viewBox="0 0 24 24" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10"/></svg>'}
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
  showCoinmashIntro();
}

function showCoinmashIntro(){
  const intro=qs("coinmashIntro");
  qs("coinmashWelcome").textContent="¡Hola, "+currentUser.nombre+"! Tu mercado escolar te espera.";
  intro.classList.remove("hidden");
  intro.setAttribute("aria-hidden","false");
  qs("coinmashSkip").focus();
  const duration=window.matchMedia("(prefers-reduced-motion: reduce)").matches?0:2400;
  coinmashIntroTimer=setTimeout(closeCoinmashIntro,duration);
}

function closeCoinmashIntro(){
  if(coinmashIntroTimer){
    clearTimeout(coinmashIntroTimer);
    coinmashIntroTimer=null;
  }
  const intro=qs("coinmashIntro");
  if(!intro)return;
  intro.classList.add("hidden");
  intro.setAttribute("aria-hidden","true");
}

function doLogout(){
  closeCoinmashIntro();
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
  const stock=Number(p.stock)||0;
  const imgHTML=p.image_data
    ? `<div class="product-img"><img data-fallback-image="true" src="${p.image_data}" alt="${p.nombre}" loading="lazy"/></div>`
    : productPlaceholderHTML();
  return `
  <div class="product-card${isPromoted?' promoted':''}" onclick="openProduct(${p.id})" style="position:relative">
    ${isPromoted?'<span class="promoted-tag">DESTACADO</span>':''}
    ${imgHTML}
    <div class="product-body">
      <div class="product-name">${p.nombre}</div>
      <div class="product-desc">${p.descripcion}</div>
      <div class="product-footer">
        <span class="product-price">${coin(p.precio)}</span>
        <span class="product-stock">${stock>0?'Stock: '+stock:'Agotado'}</span>
      </div>
      <div style="font-size:.75rem;color:var(--muted);margin-top:6px">por ${p.vendedor_nombre||"—"}</div>
      <button class="mini-cart-btn" ${stock>0?'':'disabled'} onclick="event.stopPropagation(); addToCartById(${p.id})">${stock>0?'Agregar al carrito':'Agotado'}</button>
    </div>
  </div>`;
}

async function openProduct(id){
  const product=allProducts.find(p=>p.id==id);
  if(!product) return;
  const c=qs("modalProductContent");
  const imgHTML=product.image_data
    ? `<img data-fallback-image="true" src="${product.image_data}" alt="${product.nombre}" style="width:100%;max-height:220px;object-fit:cover;border-radius:10px;margin-bottom:16px"/>`
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
  const res=await api(API.products+"?sellerId="+currentUser.id);
  const mine=(res.products||[]).filter(p=>p.seller_id==currentUser.id);
  const g=qs("myProductsGrid");
  if(!mine.length){g.innerHTML='<p style="color:var(--muted);padding:20px">Aún no tienes productos publicados.</p>';return;}
  g.innerHTML=mine.map(p=>`
    <div class="product-card" style="position:relative">
      ${p.image_data?`<div class="product-img"><img data-fallback-image="true" src="${p.image_data}" alt="${p.nombre}"/></div>`:productPlaceholderHTML()}
      <div class="product-body">
        <div class="product-name">${p.nombre}</div>
        <div class="product-desc">${p.descripcion}</div>
        <div class="product-footer">
          <span class="product-price">${coin(p.precio)}</span>
          <span class="product-stock">${p.deleted_at?'Retirado':Number(p.stock)<=0?'Agotado':Number(p.is_active)===0?'Pausado':'Publicado'} · Stock: ${p.stock}</span>
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
  qs("pImagen").value="";
  preparedImageFile=null;
  preparedImageData=null;
  imagePreparationPromise=null;
  if(!editId){
    ["pNombre","pDesc","pPrecio","pStock"].forEach(id=>qs(id).value="");
    qs("pImagePreview").style.display="none";
    qs("pImagePreview").removeAttribute("src");
  } else {
    api(API.products+"?sellerId="+currentUser.id).then(res=>{
      if(res.error){toast(res.error,"err");return;}
      const p=(res.products||[]).find(x=>x.id==editId);
      if(!p){toast("No se pudo cargar el producto para editar.","err");return;}
      if(Number(qs("editProductId").value)!==Number(editId))return;
      qs("pNombre").value=p.nombre;
      qs("pDesc").value=p.descripcion;
      qs("pPrecio").value=p.precio;
      qs("pStock").value=p.stock;
      if(p.image_data){qs("pImagePreview").src=p.image_data;qs("pImagePreview").style.display="block";}
      else{qs("pImagePreview").removeAttribute("src");qs("pImagePreview").style.display="none";}
    });
  }
  openModal("modalProductForm");
}

function closeModalForm(e){if(e.target.id==="modalProductForm")closeModal("modalProductForm")}

function compressImage(file, maxPx, quality){
  return new Promise((resolve,reject)=>{
    const r=new FileReader();
    r.onload=e=>{
      const img=new Image();
      img.onload=()=>{
        try{
          let w=img.width, h=img.height;
          if(w>maxPx||h>maxPx){
            if(w>h){h=Math.round(h*maxPx/w);w=maxPx;}
            else{w=Math.round(w*maxPx/h);h=maxPx;}
          }
          const canvas=document.createElement("canvas");
          canvas.width=w; canvas.height=h;
          const context=canvas.getContext("2d");
          if(!context){reject(new Error("No se pudo preparar la imagen."));return;}
          context.drawImage(img,0,0,w,h);
          resolve(canvas.toDataURL("image/jpeg", quality));
        }catch(error){
          reject(error);
        }
      };
      img.onerror=()=>reject(new Error("El archivo de imagen no se pudo leer."));
      img.src=e.target.result;
    };
    r.onerror=()=>reject(new Error("No se pudo leer el archivo de imagen."));
    r.readAsDataURL(file);
  });
}

function previewImage(input){
  const file=input.files[0];
  if(!file){preparedImageFile=null;preparedImageData=null;imagePreparationPromise=null;return;}
  preparedImageFile=file;
  preparedImageData=null;
  imagePreparationPromise=compressImage(file, 800, 0.75).then(dataUrl=>{
    if(preparedImageFile!==file)return null;
    preparedImageData=dataUrl;
    qs("pImagePreview").src=dataUrl;
    qs("pImagePreview").style.display="block";
    return dataUrl;
  }).catch(error=>{
    if(preparedImageFile===file){
      preparedImageData=null;
      qs("pImagePreview").style.display="none";
    }
    toast(error.message||"No se pudo preparar la imagen.","err");
    return null;
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
  const selectedFile=imgEl.files&&imgEl.files[0];
  try{
    if(selectedFile){
      if(preparedImageFile===selectedFile&&imagePreparationPromise){
        imageData=preparedImageData||await imagePreparationPromise;
      }else{
        imageData=await compressImage(selectedFile,800,0.75);
      }
      if(!imageData){toast("No se pudo preparar la foto. Intenta seleccionarla otra vez.","err");return;}
    } else if(qs("pImagePreview").src&&qs("pImagePreview").style.display!=="none"){
      imageData=qs("pImagePreview").src;
    }
  }catch(error){
    console.error("No se pudo preparar la foto del producto:",error);
    toast("No se pudo preparar la foto. Intenta seleccionarla otra vez.","err");
    return;
  }
  if(!nombre||!descripcion||precio<1||stock<1){toast("Completa los campos y agrega al menos una unidad al stock","err");return;}
  const body={sellerId:currentUser.id,nombre,descripcion,precio,stock,imageData};
  let res;
  if(editId){
    res=await api(API.products+"?id="+editId,"PUT",body);
  } else {
    res=await api(API.products,"POST",body);
  }
  if(res.error){toast(res.error,"err");return;}
  if(imageData&&(!res.product||res.product.image_data!==imageData)){
    toast("El producto se guardó, pero el servidor no confirmó la foto. Intenta subirla de nuevo.","err");
  }
  closeModal("modalProductForm");
  if(!imageData||res.product?.image_data===imageData){
    toast(editId?"Producto actualizado.":"Producto publicado.");
  }
  qs("pImagen").value="";
  preparedImageFile=null;
  preparedImageData=null;
  imagePreparationPromise=null;
  await Promise.all([loadMyProducts(),loadProducts()]);
}

async function deleteProduct(id){
  if(!confirm("¿Retirar este producto del mercado? Se conservará en el historial de compras."))return;
  const res=await api(API.products+"?id="+id,"DELETE",{sellerId:currentUser.id});
  if(res.error){toast(res.error,"err");return;}
  toast("Producto retirado del mercado");
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
      <div class="profile-value" style="color:var(--accent)">Administrador</div>
    </div>`:""}
  `;
}

/* ════════════════════════════════════════════════════════════════
   ADMIN
════════════════════════════════════════════════════════════════ */
async function loadAdmin(){
  if(currentUser.is_admin!=1){qs("adminContent").innerHTML='<p style="color:var(--red)">No autorizado.</p>';return;}
  const [stats,commissions,credits,invites,promoted,users,products]=await Promise.all([
    api(API.admin+"?action=stats&adminId="+currentUser.id),
    api(API.admin+"?action=commissions&adminId="+currentUser.id),
    api(API.admin+"?action=credits&adminId="+currentUser.id),
    api(API.admin+"?action=invites&adminId="+currentUser.id),
    api(API.admin+"?action=promoted&adminId="+currentUser.id),
    api(API.admin+"?action=users&adminId="+currentUser.id),
    api(API.admin+"?action=products&adminId="+currentUser.id),
  ]);
  if(products.error){
    qs("adminContent").innerHTML='<p style="color:var(--red)">No se pudo cargar el inventario de productos: '+products.error+'</p>';
    return;
  }
  adminUsers=users.users||[];
  adminProducts=products.products||[];

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

    <!-- Inventario completo -->
    <div class="admin-section">
      <div style="display:flex;justify-content:space-between;gap:14px;align-items:center;margin-bottom:14px;flex-wrap:wrap">
        <h3 style="margin:0">Productos e inventario</h3>
        <select id="adminProductFilter" onchange="filterAdminProducts()" style="max-width:220px">
          <option value="">Todos los estados</option>
          <option value="Publicado">En el mercado</option>
          <option value="Agotado">Agotados</option>
          <option value="Pausado">Pausados</option>
          <option value="Retirado">Retirados</option>
        </select>
      </div>
      <div style="overflow-x:auto">
        <table>
          <thead><tr><th>Producto</th><th>Vendedor</th><th>Estado</th><th>Stock</th><th>Precio</th><th>Acciones</th></tr></thead>
          <tbody id="adminProductsList"></tbody>
        </table>
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
        <input id="adminUserSearch" type="search" placeholder="Buscar por nombre, curso, contacto o código" oninput="filterAdminUsers()" style="max-width:360px"/>
      </div>
      <div style="overflow-x:auto">
        <table>
          <thead><tr><th>Nombre</th><th>Curso</th><th>Rol</th><th>Contacto</th><th>Código utilizado</th><th>Saldo</th><th>Productos</th><th>Acciones</th></tr></thead>
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
                <td><button class="btn btn-danger btn-sm" aria-label="Eliminar recarga" onclick="deleteCredit(${c.id})">Eliminar</button></td>
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
  renderAdminProducts(adminProducts);
}

function renderAdminProducts(products){
  const list=qs("adminProductsList");
  if(!list)return;
  if(!products.length){
    list.innerHTML='<tr><td colspan="6" style="text-align:center;color:var(--muted)">No hay productos registrados.</td></tr>';
    return;
  }
  list.innerHTML=products.map(p=>{
    const statusBadge=p.estado==="Publicado"?"badge-green":p.estado==="Retirado"?"badge-red":p.estado==="Agotado"?"badge-yellow":"badge-blue";
    const action=p.estado==="Retirado"
      ?'<span style="color:var(--muted)">Sin acciones</span>'
      :'<button class="btn btn-danger btn-sm" onclick="adminDeleteProduct('+p.id+')">Retirar</button>';
    return '<tr data-status="'+p.estado+'">'
      +'<td>'+p.nombre+'</td>'
      +'<td>'+p.vendedor_nombre+'</td>'
      +'<td><span class="badge '+statusBadge+'">'+p.estado+'</span></td>'
      +'<td>'+p.stock+'</td>'
      +'<td>'+coin(p.precio)+'</td>'
      +'<td style="white-space:nowrap">'+action+'</td>'
      +'</tr>';
  }).join("");
  filterAdminProducts();
}

function filterAdminProducts(){
  const filter=qs("adminProductFilter");
  const status=filter?filter.value:"";
  const rows=[...document.querySelectorAll("#adminProductsList tr[data-status]")];
  rows.forEach(row=>{
    row.classList.toggle("hidden",!!status&&row.dataset.status!==status);
  });
  const visible=rows.some(row=>!row.classList.contains("hidden"));
  let empty=qs("adminProductsEmpty");
  if(!empty){
    empty=document.createElement("tr");
    empty.id="adminProductsEmpty";
    empty.innerHTML='<td colspan="6" style="text-align:center;color:var(--muted)">No hay productos con ese estado.</td>';
    qs("adminProductsList").appendChild(empty);
  }
  empty.classList.toggle("hidden",visible);
}

async function adminDeleteProduct(productId){
  const product=adminProducts.find(p=>Number(p.id)===Number(productId));
  if(!product)return;
  if(!confirm("¿Retirar «"+product.nombre+"» del mercado? Se conservará el registro y el historial de compras."))return;
  const res=await api(API.admin+"?action=delete_product","POST",{adminId:currentUser.id,productId});
  if(res.error){toast(res.error,"err");return;}
  toast("Producto retirado del mercado.");
  loadAdmin();
}

function renderAdminUsers(users){
  const list=qs("adminUsersList");
  if(!list)return;
  if(!users.length){
    list.innerHTML='<tr><td colspan="8" style="text-align:center;color:var(--muted)">No se encontraron usuarios.</td></tr>';
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
      +'<td>'+(u.invite_code||'<span style="color:var(--muted)">Temporal / no guardado</span>')+'</td>'
      +'<td>'+coin(u.monedas)+'</td>'
      +'<td>'+u.product_count+'</td>'
      +'<td style="white-space:nowrap"><button class="btn btn-secondary btn-sm" onclick="changeUserPassword('+u.id+')">Contraseña</button> '+deletion+'</td>'
      +'</tr>';
  }).join("");
}

function filterAdminUsers(){
  const query=qs("adminUserSearch").value.trim().toLowerCase();
  const filtered=adminUsers.filter(u=>
    [u.nombre,u.curso,u.rol,u.contacto,u.card_code,u.invite_code].some(value=>
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
