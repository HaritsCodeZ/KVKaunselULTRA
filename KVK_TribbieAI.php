<?php 
session_start();  // PENTING! Letak di atas sekali
// include 'config.php'; atau database connection kalau ada
?>
<?php include 'KVK_Navbar.php'; ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TribbieAI - Kaunselor Vokasional</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #050b18;
            color: #e6f0ff;
        }

        #bg-video {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(40,15,90,0.52) 0%, rgba(90,40,130,0.45) 50%, rgba(30,70,150,0.38) 100%);
            z-index: -1;
        }

        .content-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: 100%;
            overflow: hidden;
        }

        /* Left side: FIXED - no scroll ever */
        .tribbie-side {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(to bottom, transparent, rgba(120,160,255,0.1));
            overflow: hidden;
        }

        .tribbie-frame {
            width: 100%;
            max-width: 720px;
            position: relative;
            animation: gentle-float 10s ease-in-out infinite;
        }

        .tribbie-frame::before {
            content: '';
            position: absolute;
            inset: -40px;
            background: radial-gradient(circle at 40% 30%, rgba(160,200,255,0.4) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.55;
            z-index: -1;
            animation: pulse-glow 6s ease-in-out infinite;
        }

        .tribbie-frame img {
            width: 100%;
            border-radius: 36px;
            border: 3px solid rgba(220,240,255,0.3);
            box-shadow: 0 25px 70px rgba(0,0,0,0.65), inset 0 0 90px rgba(160,200,255,0.25);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .tribbie-frame img.talking {
            transform: scale(1.04);
            opacity: 0.98;
        }

        @keyframes gentle-float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-22px); } }
        @keyframes pulse-glow { 0%,100% { opacity: 0.55; } 50% { opacity: 0.75; } }

        /* RIGHT SIDE: Chat Area - FULL HEIGHT FLEX */
        .chat-side {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: rgba(25,40,100,0.2);
            backdrop-filter: blur(24px);
            border-left: 1px solid rgba(200,220,255,0.12);
            overflow: hidden;
        }

        .chat-header {
            padding: 1.4rem 2.2rem;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(200,220,255,0.14);
            font-size: 1.35rem;
            font-weight: 600;
            color: #d0e0ff;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-shrink: 0;
        }

        .header-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(220,240,255,0.4);
            box-shadow: 0 0 20px rgba(160,200,255,0.5);
            transition: transform 0.4s ease, filter 0.6s ease;
        }

        .header-avatar.thinking {
            transform: scale(1.08);
            filter: brightness(1.12) drop-shadow(0 0 12px rgba(160,200,255,0.7));
        }

        /* THE ONLY SCROLLABLE PART */
        .chat-body {
            flex: 1 1 auto;
            padding: 2.2rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1.6rem;
            overscroll-behavior: contain; /* optional, smooth UX */
        }

        /* Modern scrollbar */
        .chat-body::-webkit-scrollbar {
            width: 8px;
        }
        .chat-body::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.03);
        }
        .chat-body::-webkit-scrollbar-thumb {
            background: rgba(160,200,255,0.4);
            border-radius: 4px;
        }
        .chat-body::-webkit-scrollbar-thumb:hover {
            background: rgba(160,200,255,0.7);
        }

        .message {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            max-width: 85%;
        }

        .message.bot { align-self: flex-start; }
        .message.user { align-self: flex-end; flex-direction: row-reverse; }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid rgba(220,240,255,0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }

        .bubble {
            padding: 1.2rem 1.6rem;
            border-radius: 20px;
            backdrop-filter: blur(12px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.35);
            line-height: 1.5;
            font-size: 1.05rem;
            font-weight: 650;
        }

        .bot .bubble {
            background: linear-gradient(135deg, rgba(160,200,255,0.28), rgba(120,170,255,0.2));
            border: 1px solid rgba(200,230,255,0.25);
            border-radius: 20px 20px 20px 4px;
            color: #f0f8ff;
        }

        .user .bubble {
            background: linear-gradient(135deg, #ffccdd 0%, #ffb3c6 100%);
            border: 1px solid #ff99b3;
            border-radius: 20px 20px 4px 20px;
            color: #5c1e3a;
            box-shadow: 0 6px 20px rgba(255, 182, 193, 0.4);
        }

        .welcome-bubble {
            animation: fadeInBubble 1.5s ease-out;
        }

        @keyframes fadeInBubble {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Cute Typing Indicator */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 1rem 1.4rem;
            background: rgba(250, 251, 253, 0.15);
            border-radius: 20px;
            width: fit-content;
            align-self: flex-start;
            opacity: 0;
            transform: translateY(20px);
        }

        .typing-indicator.visible {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .dot {
            width: 10px;
            height: 10px;
            background: #fdfdffff;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out;
            opacity: 0.6;
        }

        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typingBounce {
            0%, 80%, 100% { transform: translateY(0); opacity: 0.6; }
            40% { transform: translateY(-8px); opacity: 1; }
        }

        /* Input Area - pinned at bottom */
        .chat-input-area {
            padding: 1.5rem 2.2rem;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(18px);
            border-top: 1px solid rgba(200,220,255,0.14);
            flex-shrink: 0;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: linear-gradient(90deg, #2a1b4d 0%, #3a2a6b 20%, #4a3a8b 40%, #5a4a9b 60%, #6a5aab 80%, #7f709d 100%);
            background-size: 200% 100%;
            border-radius: 999px;
            padding: 0.7rem 1.2rem;
            border: 1px solid rgba(200,220,255,0.25);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            overflow: visible;
            animation: pulse-wave 7s ease-in-out infinite;
        }

        .input-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(160,200,255,0.18) 40%, rgba(255,182,193,0.12) 60%, transparent 100%);
            border-radius: 999px;
            opacity: 0.6;
            animation: pulse-wave-inner 7s ease-in-out infinite;
        }

        .plus-button {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #a0c0ff;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 1rem;
            z-index: 10;
        }

        .plus-button:hover {
            background: rgba(160,200,255,0.3);
            transform: scale(1.12);
        }

        .chat-input-area input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: #f0f8ff;
            font-size: 1.05rem;
            z-index: 2;
        }

        .chat-input-area input::placeholder {
            color: rgba(240,248,255,0.6);
        }

        .send-button {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #ffb3c6, #ff99b3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .send-button:hover {
            transform: scale(1.12);
            box-shadow: 0 0 25px rgba(255,182,193,0.6);
        }

        /* Dropdown Menu */
        .menu-dropdown {
            position: absolute;
            bottom: calc(100% + 12px);
            left: 0;
            background: rgba(20, 25, 50, 0.92);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(100, 120, 255, 0.35);
            border-radius: 12px;
            width: 220px;
            overflow: visible;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            transform: translateY(10px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 1000;
        }

        .menu-dropdown.active {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .menu-item {
            padding: 0.9rem 1.2rem;
            color: #d0e0ff;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .menu-item:hover {
            background: rgba(100, 140, 255, 0.35);
        }

        @keyframes pulse-wave {
            0%, 100% { background-position: 0% 0; opacity: 0.85; }
            50%      { background-position: 100% 0; opacity: 1; }
        }

        @keyframes pulse-wave-inner {
            0%, 100% { opacity: 0.4; }
            50%      { opacity: 0.8; }
        }

        /* Mobile */
        @media (max-width: 1024px) {
            main { grid-template-columns: 1fr; }
            .tribbie-side { padding: 1.5rem; }
            .tribbie-frame { max-width: 420px; }
            .chat-side { border-left: none; border-top: 1px solid rgba(200,220,255,0.12); }
        }
    </style>
</head>
<body>

<video autoplay muted loop playsinline id="bg-video">
    <source src="VideoGalleries/KVK_AiBack.mp4" type="video/mp4">
    <img src="fallback-bg.jpg" alt="Background">
</video>

<div class="overlay"></div>

<div class="content-wrapper">

    <?php include 'KVK_Navbar.php'; ?>

    <main>
        <!-- Left: Magical Tribbie (fixed) -->
        <div class="tribbie-side">
            <div class="tribbie-frame">
                <img id="tribbieMainImg"
                     src="TribbieAI/Tribbie_Greet-removebg-preview.png" 
                     alt="Tribbie AI">
            </div>
        </div>

        <!-- Right: Chat Area (scrollable only inside chat-body) -->
        <div class="chat-side">
            <div class="chat-header">
                <img src="TribbieAI/Tribbie_Idle-removebg-preview.png" 
                     id="tribbieHeaderAvatar" 
                     class="header-avatar" 
                     alt="Tribbie">
                TribbieAI ♥
            </div>
            
            <div class="chat-body" id="chatBody">
                <!-- Messages will appear here - THIS IS THE ONLY SCROLLABLE AREA -->
            </div>

            <div class="chat-input-area">
                <div class="input-wrapper">
                    <div class="plus-button" id="plusBtn">+</div>
                    <div class="menu-dropdown" id="menuDropdown">
                        <div class="menu-item">Chat Baru</div>
                        <div class="menu-item">Main Dengan Tribbie</div>
                    </div>
                    <input type="text" id="userInput" placeholder="Apa yang awak nak tanya saya?" autocomplete="off">
                    <div class="send-button" id="sendBtn">➤</div>
                </div>
            </div>
        </div>
    </main>

</div>

<script>
// === Configuration for expressions ===
const TRIBBIE_EXPRESSIONS = {
    IDLE:     "TribbieAI/Tribbie_Idle-removebg-preview.png",
    TALKING:  "TribbieAI/Tribbie_Talking-removebg-preview.png",
    FORGIVE:  "TribbieAI/Tribbie_Forgive-removebg-preview.png"
};

function resetChat() {
    const chatBody = document.getElementById('chatBody');
    
    // Kosongkan semua mesej
    chatBody.innerHTML = '';
    
    // Tutup dropdown kalau terbuka
    document.getElementById('menuDropdown').classList.remove('active');
    
    // Optional: tunjuk ekspresi sweet mula-mula
    setExpression('IDLE');
    
    // Tunjuk typing + mesej selamat datang semula (sama macam onload)
    setTimeout(() => {
        const typing = document.createElement('div');
        typing.className = 'typing-indicator visible';
        typing.innerHTML = `
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <span style="color:#f0f8ff; margin-left:0.8rem; font-size:0.95rem;">Tribbie sedang menaip... ♥</span>
        `;
        chatBody.appendChild(typing);
        chatBody.scrollTop = chatBody.scrollHeight;

        setTimeout(() => {
            typing.remove();

            const welcome = document.createElement('div');
            welcome.className = 'message bot';
            welcome.innerHTML = `
                <img src="${DEFAULT_AVATAR}" class="avatar" alt="Tribbie">
                <div class="bubble welcome-bubble">
                    Hai Awak! 🥰<br><br>
                    Nama saya Tribbie. Saya AI untuk KVKaunsel ni tau!<br>
                    Kalau awak nak apa-apa tips pasal masalah hidup, belajar, kerjaya, atau perasaan tanya je saya!<br><br>
                    Ingat ya: Saya hanya boleh bagi tips je tau.😁
                </div>
            `;
            chatBody.appendChild(welcome);
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 700);
    }, 700);
}

const DEFAULT_AVATAR = TRIBBIE_EXPRESSIONS.IDLE;

// === Fungsi umum untuk tukar ekspresi ===
function setExpression(type) {
    const mainImg = document.getElementById('tribbieMainImg');
    const header  = document.getElementById('tribbieHeaderAvatar');

    const src = TRIBBIE_EXPRESSIONS[type] || DEFAULT_AVATAR;

    if (mainImg) {
        mainImg.src = src;
        mainImg.classList.toggle('talking', type === 'TALKING');
    }

    if (header) {
        header.src = src;
        header.classList.toggle('thinking', type === 'TALKING' || type === 'FORGIVE');
    }
}

// === Show cute typing indicator + tukar ke talking ===
function showTyping(callback) {
    setExpression('TALKING');

    const chatBody = document.getElementById('chatBody');
    const typing = document.createElement('div');
    typing.className = 'typing-indicator visible';
    typing.innerHTML = `
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <span style="color:#f0f8ff; margin-left:0.8rem; font-size:0.95rem;">Tribbie sedang menaip... ♥</span>
    `;
    chatBody.appendChild(typing);
    chatBody.scrollTop = chatBody.scrollHeight;

    setTimeout(() => {
        typing.remove();
        if (callback) callback();
    }, 1800 + Math.random() * 600);
}

// === Tambah mesej ke chat ===
function addMessage(text, isUser = false) {
    const chatBody = document.getElementById('chatBody');
    const msgDiv = document.createElement('div');
    msgDiv.className = `message ${isUser ? 'user' : 'bot'}`;

    let avatarHtml = '';
    if (!isUser) {
        avatarHtml = `<img src="${DEFAULT_AVATAR}" class="avatar" alt="Tribbie">`;
    }

    msgDiv.innerHTML = `
        ${avatarHtml}
        <div class="bubble">${text}</div>
    `;
    chatBody.appendChild(msgDiv);
    
    // Auto-scroll
    chatBody.scrollTop = chatBody.scrollHeight;

    // Reset ke IDLE selepas mesej bot selesai (kecuali kalau forgive, delay lebih panjang)
    if (!isUser) {
        const resetDelay = text.includes("Minta maaf ye") ? 5000 : 3500;
        setTimeout(() => {
            setExpression('IDLE');
        }, resetDelay + Math.random() * 1200);
    }
}

// === Simple ELIZA-like Keyword Reply Engine ===
function getTribbieReply(message) {
    const text = message.toLowerCase().trim();


    //SCRIPT UNTUK RESPON TRIBBIE AI 

    if (
        text.includes("terima kasih") ||
        text.includes("tq") ||
        text.includes("tenkiu")
    ) {
        return (
            "Sama-sama! 😊 Kalau ada apa-apa lagi yang awak nak tanya atau perlukan tips, Tribbie sentiasa sedia membantu! 🌟"
  );
}

if (
  text.includes("rokok") ||
  text.includes("tips berhenti merokok") ||
  text.includes("merokok") ||
  text.includes("vape")
) {
  return "🚭 <b>Tips Berhenti Merokok</b><br/><br/>" +
         "Tribbie tau! 🧐<br/>" +
         "- Pertama, Kurangkan sedikit demi sedikit<br/>" +
         "- Kedua, Elakkan situasi yang buat awak teringin<br/>" +
         "- Ketiga, Alihkan perhatian (minum air, tarik nafas)<br/><br/>" +
         "Kalau awak amalkan tips ni mesti awak dapat cegah dari tabiat ni! 🌱";
}

if (
  text.includes("dadah") ||
  text.includes("hisap") ||
  text.includes("tips menjauhi dadah") ||
  text.includes("pil khayal")
) {
  return "💊 <b>Tips Menjauhi Dadah</b><br/><br/>" +
         "- Pertama, Jauhi kawan yang beri pengaruh negatif<br/>" +
         "- Kedua, Isi masa dengan aktiviti sihat<br/>" +
         "- Ketiga, Jangan takut minta bantuan<br/><br/>" +
         "Dadah ni bahaya tau! Jangan sesekali awak buat atau ikut perbuatan buruk ni. Ruang masih terbuka sekiranya awak mengamalkan tabiat buruk ni 🥺";
}

if (
  text.includes("stress") ||
  text.includes("tekanan") ||
  text.includes("tips mengurus tekanan") ||
  text.includes("stres") ||
  text.includes("penat")
) {
  return "😔 <b>Tips Mengurus Tekanan</b><br/><br/>" +
         "- Pertama, Tarik nafas 4 saat<br/>" +
         "- Kedua, Tahan 4 saat<br/>" +
         "- Ketiga, Hembus perlahan 6 saat<br/><br/>" +
         "Kalau awak stress ke, penat ke awak kena banyak bersabar ye, amalkan tips ni selalu! 🌈";
}

if (
  text.includes("emosi") ||
  text.includes("tips mengawal emosi") ||
  text.includes("marah") ||
  text.includes("sedih")
) {
  return "😌 <b>Tips Mengawal Emosi</b><br/><br/>" +
         "- Pertama, Berhenti sekejap sebelum bertindak<br/>" +
         "- Kedua, Kenal pasti apa yang awak rasa<br/>" +
         "- Ketiga, Jangan pendam terlalu lama<br/><br/>" +
         "Sebagai manusia, haha walaupun saya bukan manusia tapi saya tau, masalah emosi sangat parah sekiranya tidak diambil serius, jadi amalkan tips ini ye 🤲";
}

if (
  text.includes("masa") ||
  text.includes("tips mengurus masa") ||
  text.includes("sibuk") ||
  text.includes("tak sempat")
) {
  return "⏰ <b>Tips Mengurus Masa</b><br/><br/>" +
         "- Pertama, Senaraikan 3 perkara penting<br/>" +
         "- Kedua, Buat satu kerja pada satu masa<br/>" +
         "- Ketiga, Jangan lupa rehat<br/><br/>" +
         "Tak perlu sempurna, cukup konsisten ✨";
}

if (
  text.includes("tak yakin") ||
  text.includes("tips meningkat keyakinan diri") ||
  text.includes("keyakinan") ||
  text.includes("rendah diri")
) {
  return "💪 <b>Tips Meningkatkan Keyakinan Diri</b><br/><br/>" +
         "- Pertama, Fokus pada kelebihan diri<br/>" +
         "- Kedua, Jangan bandingkan dengan orang lain<br/>" +
         "- Ketiga, Cuba bercakap dengan yakin<br/><br/>" +
         "Dalam hidup ni, awak unik dengan cara awak sendiri 🌟";
}

if (
  text.includes("produktif") ||
  text.includes("tips menjadi lebih produktif") ||
  text.includes("fokus") ||
  text.includes("buat kerja")
) {
  return "⚡ <b>Tips Menjadi Lebih Produktif</b><br/><br/>" +
         "- Pertama, Tetapkan matlamat kecil<br/>" +
         "- Kedua, Elakkan multitasking<br/>" +
         "- Ketiga, Rehat setiap 45–50 minit<br/><br/>" +
         "Ingat tau, jangan tergesa-gesa buat kerja, pastikan terurus! ✔️";
}

if (
  text.includes("mental") ||
  text.includes("tertekan") ||
  text.includes("tips menjaga kesihatan mental") ||
  text.includes("down")
) {
  return "🧠 <b>Tips Menjaga Kesihatan Mental</b><br/><br/>" +
         "- Pertama, Tidur cukup<br/>" +
         "- Kedua, Jangan simpan semua sorang-sorang<br/>" +
         "- Ketiga, Hargai diri sendiri<br/><br/>" +
         "Sayangilah diri awak ya 💗";
}

if (
  text.includes("pengaruh") ||
  text.includes("kawan jahat") ||
  text.includes("tips mengelakkan pengaruh negatif") ||
  text.includes("ikut orang")
) {
  return "🚧 <b>Tips Mengelakkan Pengaruh Negatif</b><br/><br/>" +
         "- Pertama, Berani kata tidak<br/>" +
         "- Kedua, Pilih kawan yang menyokong<br/>" +
         "- Ketiga, Ingat matlamat hidup awak<br/><br/>" +
         "Ingat ye, hati-hati pilih kawan ya, sebab pilihan awak menentukan masa depan awak sendiri🌟";
}

if (
  text.includes("disiplin") ||
  text.includes("malas") ||
  text.includes("tips membina disiplin diri") ||
  text.includes("tak konsisten")
) {
  return "🧱 <b>Tips Membina Disiplin Diri</b><br/><br/>" +
         "- Pertama, Tetapkan rutin mudah<br/>" +
         "- Kedua, Buat walaupun tak ada mood<br/>" +
         "- Ketiga, Hargai usaha sendiri<br/><br/>" +
         "Disiplin kecil bawa perubahan besar 🚀 Orang akan tengok disiplin awak!";
}

// List Tips Tribbie Boleh Bagi
    if (
        text.includes("tips") || text.includes("bantu") || text.includes("awak lakukan") || text.includes("buat") ||
        text.includes("tribbie boleh buat") || text.includes("tribbie boleh lakukan") ||
        text.includes("tribbie boleh bantu")
    ) {
        return (
            "Tribbie boleh bantu dengan tips berikut:<br/><br/>" +

            "- Tips Berhenti Merokok<br/>" +
            "- Tips Menjauhi Dadah<br/>" +
            "- Tips Mengurus Tekanan<br/>" +
            "- Tips Mengawal Emosi<br/>" +
            "- Tips Mengurus Masa<br/>" +
            "- Tips Meningkatkan Keyakinan Diri<br/>" +
            "- Tips Menjadi Lebih Produktif<br/>" +
            "- Tips Menjaga Kesihatan Mental<br/>" +
            "- Tips Mengelakkan Pengaruh Negatif<br/>" +
            "- Tips Membina Disiplin Diri<br/><br/>" +

        "👉 Taip mana-mana topik di atas untuk dapatkan tips khusus 😊"
  );
}

if (
    text.includes("siapa") &&
    (text.includes("buat") || text.includes("cipta") || text.includes("pencipta") ||
     text.includes("buatan") || text.includes("sistem") || text.includes("team") || text.includes("kumpulan"))
) {
    return (
        "Sistem Tribbie ni dibuat oleh Kumpulan 5 yang awesome ni:<br/><br/>" +

        "• Cik Florina → Penyelia projek ni tau!<br/>" +
        "• Harits → Pencipta saya, (Programmer) dia buat saya jadi real tau<br/>" +
        "• Cahaya → Yang buat dokumentasi saya dengan cantik & teratur<br/>" +
        "• Sharfik → Yang tolong cari info & research untuk saya<br/><br/>" +

        "Terima kasih banyak-banyak kepada Kumpulan 5 sebab mewujudkan saya! ❤️<br/><br/>" +

        "Nak tahu lebih lanjut pasal saya atau apa-apa lagi? 😊"
    );
}

if (
    text.includes("hai") ||
    text.includes("hei") ||
    text.includes("hello") ||
    text.includes("hi") ||
    text.includes("helo") ||
    text.includes("halo") ||
    text.includes("assalamualaikum") ||
    text.includes("salam")
) {
    return (
        "Hai awaknya! 👋 Apa khabar awak? 😎<br/><br/>" +
        "Nak borak apa hari ni? Cer cite lah dengan Tribbie! ✨"
    );
}

if (
    text.includes("siapa") &&
    (text.includes("kaunselor") || text.includes("panel kaunselor") || text.includes("KV Betong"))
) {
    return (
        "Panel kaunselor KV Betong:<br/><br/>" +

        "• Cikgu Muhirman Bin Mu-Alim<br/>" +
        "• Cikgu Tanita Anak Numpang<br/>" +
        "• Cikgu Whilemina Thimah Gregory Anak Jimbun<br/><br/>" +

        "Team Unit Psikologi & Kerjaya yang awesome! 🔥<br/>" +
        "Apa lagi nak tanya pasal KV Betong? 😉"
    );
}

if (
  text.includes("nak tahu tak") || text.includes("tahu tak") ||
  text.includes("nak tau tak") ||
  text.includes("nak tahu ke")
) {
  return "😊 <b>Nak tahu apa tu?</b><br/><br/>" +
         "Awak boleh terus tanya je, saya sedia dengar 💬✨";
}


    // Default: tak faham → tunjuk ekspresi FORGIVE
    setTimeout(() => {
        setExpression('FORGIVE');
    }, 400); // delay kecil supaya nampak natural selepas typing hilang

    return "Minta maaf ye, Saya tak faham sangat. Saya cuma diprogram untuk bagi tips asas je. Sorry ye! 😔💦 Awak nak tahu tak tips apa yang saya boleh bagi?";
}

// === Handler hantar mesej ===
function sendMessage() {
    const input = document.getElementById('userInput');
    const text = input.value.trim();
    if (!text) return;

    addMessage(text, true);
    input.value = '';

    showTyping(() => {
        const reply = getTribbieReply(text);
        addMessage(reply, false);
    });
}

// === Event listeners ===
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');

    sendBtn.addEventListener('click', sendMessage);

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    document.getElementById('plusBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('menuDropdown').classList.toggle('active');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.plus-button') && !e.target.closest('.menu-dropdown')) {
            document.getElementById('menuDropdown').classList.remove('active');
        }
    });

        // Handle klik pada setiap item dalam menu dropdown
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', function() {
            const selectedText = this.textContent.trim();
            const dropdown = document.getElementById('menuDropdown');

            if (selectedText === "Chat Baru") {
                if (confirm("Betul nak mulakan chat baru? Semua mesej lama akan hilang ya~ 😢")) {
                    resetChat();  // Panggil fungsi reset yang dah ada
                }
            } 
            else if (selectedText === "Main Dengan Tribbie") {
                // Respons ringkas fun – boleh ubah suai kemudian
                showTyping(() => {
                    addMessage("Yeayy! 🎉 Nak main apa dengan Tribbie hari ni?<br>" +
                               "1. Tebak-tebakan lawak<br>" +
                               "2. Cerita seram pendek<br>" +
                               "3. Main teka emoji<br><br>" +
                               "Pilih satu je, atau suggest idea awak sendiri! 😜", false);
                });
            }

            // Tutup dropdown selepas pilih
            dropdown.classList.remove('active');
        });
    });
});

// Mesej selamat datang pertama
window.addEventListener('load', () => {
    const chatBody = document.getElementById('chatBody');

    setTimeout(() => {
        const typing = document.createElement('div');
        typing.className = 'typing-indicator visible';
        typing.innerHTML = `
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <span style="color:#f0f8ff; margin-left:0.8rem; font-size:0.95rem;">Tribbie sedang menaip... ♥</span>
        `;
        chatBody.appendChild(typing);
        chatBody.scrollTop = chatBody.scrollHeight;

        setTimeout(() => {
            typing.remove();

            const welcome = document.createElement('div');
            welcome.className = 'message bot';
            welcome.innerHTML = `
                <img src="${DEFAULT_AVATAR}" class="avatar" alt="Tribbie">
                <div class="bubble welcome-bubble">
                    Hai Awak! 🥰<br><br>
                    Nama saya Tribbie. Saya AI untuk KVKaunsel ni tau!<br>
                    Kalau awak nak apa-apa tips pasal masalah hidup, belajar, kerjaya, atau perasaan tanya je saya!<br><br>
                    Ingat ya: Saya hanya boleh bagi tips je tau.😁
                </div>
            `;
            chatBody.appendChild(welcome);
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 700);
    }, 700);
});


</script>

</body>
</html>