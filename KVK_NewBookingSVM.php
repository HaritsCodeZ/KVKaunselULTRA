<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempahan Kaunseling SVM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        html,body{height:100%;overflow:hidden;font-family:'Poppins',sans-serif;background:#000}

        .hero__video{position:fixed;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:1;filter:brightness(1.05)}
        .eclipse-left{position:absolute;width:1100px;height:1050px;top:49%;left:-530px;transform:translateY(-50%);background:rgba(175,116,177,0.82);border-radius:50%;display:flex;align-items:center;justify-content:flex-end;padding-right:130px;z-index:20}
        .eclipse-text{color:white;font-size:140px;font-weight:bold;letter-spacing:-10px;user-select:none}

        .white-card{position:absolute;height:850px;top:50%;left:57%;transform:translate(-50%,-50%);width:90%;max-width:1250px;background:#fff;border-radius:20px;box-shadow:0 20px 50px rgba(0,0,0,0.2);padding:45px 35px;z-index:10;border:1px solid #e8e8e8;overflow:hidden}

        .logo-container{text-align:center;margin-bottom:30px;margin-top:-30px}
        .logo-container img{height:70px;margin:0 15px}
        .greeting,.sub-greeting{text-align:right;font-size:36px;font-weight:bold;color:#AF74B1;margin-right:20px;letter-spacing:-1px}
        .sub-greeting{margin-bottom:8px}

        /* Progress Circle */
        .progress-master-circle{position:absolute;width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 30px rgba(255,214,10,0.5);z-index:9999;transition:all .5s cubic-bezier(0.34,1.56,0.64,1);transform:translate(-50%,-50%)}
        .progress-master-circle.active{background:#FFD557!important;box-shadow:0 12px 40px rgba(255,214,10,0.9)!important;transform:translate(-50%,-50%) scale(1.15)}
        .progress-number{font-size:42px;font-weight:bold;color:#000;user-select:none}
        #c1{background:#FFD557;top:23%;left:26.2%}
        #c2,#c3,#c4,#c5{background:#8C5C8D}
        #c2{top:36%;left:28.9%}
        #c3{top:49%;left:29.7%}
        #c4{top:62%;left:28.9%}
        #c5{top:75%;left:26.2%}

        /* Form */
        .form-area{position:absolute;top:63%;right:59px;transform:translateY(-50%);width:420px;opacity:0;visibility:hidden;transition:opacity .6s ease}
        .form-area.active{opacity:1;visibility:visible}
        .form-group{margin-bottom:28px;text-align:right}
        .form-group label{display:block;margin-bottom:10px;color:#AF74B1;font-size:24px;font-weight:bold;}
        .form-group input,.form-group select,.form-group textarea{width:100%;padding:18px 22px;border: 1px solid #AF74B1;border-radius:12px;font-size:18px;font-family:'Poppins',sans-serif;background:#f9f9f9;box-shadow:0 4px 12px rgba(0,0,0,0.5)}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:#AF74B1;box-shadow:0 0 0 4px rgba(175,116,177,0.15)}
        textarea{height:140px;resize:none}

        /* Button Group — Kembali + Teruskan */
        .button-group{
            display:flex; justify-content:space-between; align-items:center;
            margin-top:40px; gap:20px;
        }
        .btn-kembali{
            width:45%; padding:18px; background:transparent; color:#AF74B1; border:2px solid #AF74B1;
            border-radius:30px; font-size:20px; font-weight:700; cursor:pointer; transition:all .3s;
        }
        .btn-kembali:hover{
            background:#AF74B1; color:white; transform:translateY(-3px);
        }
        .btn-teruskan{
            width:55%; padding:18px; background:#AF74B1; color:white; border:none;
            border-radius:30px; font-size:20px; font-weight:700; cursor:pointer; transition:all .3s;
        }
        .btn-teruskan:hover{
            background:#945a96; transform:translateY(-3px); box-shadow:0 10px 25px rgba(175,116,177,0.4);
        }

        .floating-home-manual{position:fixed;top:58px;right:210px;z-index:999999;cursor:pointer;transition:all .35s;filter:drop-shadow(0 10px 30px rgba(175,116,177,0.6))}
        .floating-home-manual:hover{transform:scale(1.12) translateY(-4px);filter:drop-shadow(0 15px 40px rgba(175,116,177,0.8))}

        /* Animasi Step 5 */
@keyframes fadeScale {
    0% {opacity: 0; transform: translate(-50%, -50%) scale(0.6);}
    100% {opacity: 1; transform: translate(-50%, -50%) scale(1);}
}

/* Wrapper Box */
#step5-box {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    text-align: center;
    z-index: 9999;
    display: none;
}

/* Kad Step 5 */
.step5-wrapper {
    background: #ffffffee;
    padding: 50px 40px;
    width: 1050px;
    margin: auto;
    border-radius: 30px;
    box-shadow: 0 20px 50px rgba(175,116,177,0.95);
    animation: fadeScale .8s ease;
}

/* Icon Tick */
.step5-icon {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: #27ae60;
    color: white;
    font-size: 65px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: auto;
    margin-bottom: 25px;

    box-shadow: 0 10px 30px rgba(39,174,96,0.6),
                0 0 25px rgba(255,255,255,0.8) inset;
}

/* Title */
.step5-title {
    font-size: 80px;
    font-weight: 900;
    color: #AF74B1;
    margin-bottom: 10px;
}

/* Description */
.step5-desc {
    font-size: 30px;
    color: #333;
    line-height: 1.6;
    margin-bottom: 35px;
}

/* Button */
.step5-wrapper .btn-teruskan {
    width: 75%;
    padding: 18px;
    font-size: 22px;
    font-weight: 700;
    border-radius: 30px;
    transition: .3s;
}

.step5-wrapper .btn-teruskan:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(39,174,96,0.5);
}

/* BUTANG SUCCESS STEP 5 — HIJAU PREMIUM + HOVER GEMPUR */
.btn-success-dashboard {
    background: linear-gradient(135deg, #27ae60, #219653) !important;
    color: white;
    width: 70%;
    max-width: 500px;
    padding: 22px 20px;
    font-size: 26px;
    font-weight: 800;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    box-shadow: 0 15px 35px rgba(39,174,96,0.5);
    transition: all 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Hover effect — naik + glow + ripple */
.btn-success-dashboard:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 25px 60px rgba(39,174,96,0.8);
}

/* Ripple effect bila klik (cantik gila) */
.btn-success-dashboard::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255,255,255,0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
    pointer-events: none;
}

.btn-success-dashboard:active::before {
    width: 300px;
    height: 300px;
}

/* Tambah icon rumah kecil dalam butang (optional tapi nampak pro) */
.btn-success-dashboard::after {
    content: " ↩";
    margin-left: 10px;
    font-size: 28px;
    font-weight: bold;
}

    </style>
</head>
<body>

    <video class="hero__video" autoplay muted loop playsinline poster="ImageGalleries/ImageCounseling1.jpg">
        <source src="VideoGalleries/KVK_BookingVideo.mp4" type="video/mp4">
    </video>

    <div class="eclipse-left"><div class="eclipse-text">SVM</div></div>

    <div class="white-card">
        <div class="logo-container">
            <img src="ImageGalleries/KVKaunsel_Logo-New.png">
            <img src="ImageGalleries/LOGO_KV.png">
            <img src="ImageGalleries/LOGO_PRS_RELOADED.png">
        </div>
        <h2 class="greeting">Hai !</h2>
        <p class="sub-greeting">Sebelum tempahan<br>Mari kita berkenalan dahulu</p>

        <!-- STEP 1 -->
        <div class="form-area active" id="step1">
            <form onsubmit="return false;">
                <div class="form-group"><label>Nama Penuh Anda</label><input type="text" name="nama" required placeholder="Masukkan nama penuh anda"></div>
                <div class="form-group"><label>Program Anda</label>
                    <select name="program" required>
                        <option value="" disabled selected>Pilih program anda</option>
                        <option>Program Teknologi Elektrik</option><option>Perkaunan</option><option>Program Sistem Komputer Dan Rangkaian</option>
                        <option>Program Hospitaliti Seni Kulinari</option><option>Program Teknologi Automotif</option><option>Program Teknologi Kimpalan</option>
                        <option>Program Teknologi Pemesinan Industri</option>
                    </select>
                </div>
                <div class="form-group"><label>Semester Anda</label>
                    <select name="semester" required><option value="" disabled selected>Pilih semester</option><option>Semester 1</option><option>Semester 2</option><option>Semester 3</option><option>Semester 4</option></select>
                </div>
                <div class="button-group">
                    <div style="width:45%"></div> <!-- placeholder kosong supaya Teruskan kekal kanan -->
                    <button type="button" class="btn-teruskan" onclick="nextStep(2)">Teruskan</button>
                </div>
            </form>
        </div>

        <!-- STEP 2 -->
        <div class="form-area" id="step2">
            <form onsubmit="return false;">
                <div class="form-group"><label>Jantina Anda</label><select name="jantina" required><option value="" disabled selected>Pilih jantina</option><option>Lelaki</option><option>Perempuan</option></select></div>
                <div class="form-group"><label>Kaum Anda</label><select name="kaum" required><option value="" disabled selected>Pilih kaum</option><option>Melayu</option><option>Cina</option><option>Bumiputera</option><option>Lain-lain</option></select></div>
                <div class="form-group"><label>No Telefon Anda</label><input type="tel" name="telefon" required placeholder="Contoh: 60123456789" pattern="[0-9]{10,12}"></div>
                <div class="button-group">
                    <button type="button" class="btn-kembali" onclick="nextStep(1)">Kembali</button>
                    <button type="button" class="btn-teruskan" onclick="nextStep(3)">Teruskan</button>
                </div>
            </form>
        </div>

        <!-- STEP 3 -->
        <div class="form-area" id="step3">
            <form onsubmit="return false;">
                <div class="form-group"><label>Pilih Tarikh & Masa Tempahan</label><input type="datetime-local" name="tarikh_masa" required style="direction:ltr;"></div>
                <div class="form-group"><label>Jenis Sesi Kaunseling</label>
                    <select name="jenis_sesi" required>
                        <option value="" disabled selected>Pilih jenis sesi</option>
                        <option value="Online">Online (Google Meet)</option>
                        <option value="Bersemuka">Bersemuka (Di Bilik Kaunseling)</option>
                    </select>
                </div>
                <div class="form-group"><label>Jenis Kaunseling</label>
                    <select name="JenisKaunseling" required>
                        <option value="" disabled selected>Pilih Jenis Kaunseling</option>
                        <option>Kaunseling Individu</option>
                        <option>Kaunseling Kelompok</option>
                    </select>
                </div>
                <div class="button-group">
                    <button type="button" class="btn-kembali" onclick="nextStep(2)">Kembali</button>
                    <button type="button" class="btn-teruskan" onclick="nextStep(4)">Teruskan</button>
                </div>
            </form>
        </div>

        <!-- STEP 4 -->
        <div class="form-area" id="step4">
            <form onsubmit="return false;">
                <div class="form-group"><label>Plih Kaunselor anda</label>
                    <select name="JenisKaunseling" required>
                        <option value="" disabled selected>Pilih kaunselor</option>
                        <option>Encik Muhirman Bin Mu Alim</option>
                        <option>Tanita Anak Numpang</option>
                        <option>Whilemina Thimah Gregory Anak Jimbun</option>
                    </select>
                </div>

                <div class="form-group"><label>Sebab / Isu yang Ingin Dibincangkan</label>
                    <textarea name="sebab" required placeholder="Ceritakan sedikit tentang apa yang anda alami... (contoh: stress, masalah keluarga, kerjaya, dll)"></textarea>
                </div>
                <div class="button-group">
                    <button type="button" class="btn-kembali" onclick="nextStep(3)">Kembali</button>
                    <button type="button" class="btn-teruskan" onclick="nextStep(5)">Hantar Tempahan</button>
                </div>
            </form>
        </div>

        <div id="step5-content" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:90%; max-width:900px; text-align:center; z-index:9999;">
    <div style="font-size:110px; color:#27ae60; margin-bottom:20px;">✔</div>
    <h1 style="font-size:78px; font-weight:900; color:#AF74B1; margin:15px 0;">Terima Kasih!</h1>
    <p style="font-size:28px; color:#333; line-height:1.6; margin-bottom:40px;">
        Tempahan anda telah berjaya dihantar.<br>
        Kaunselor akan menghubungi anda melalui KaunselMel<br>
        dalam masa <strong>24 jam</strong>.
    </p>
    <button type="button" class="btn-success-dashboard" 
        onclick="location.href='KVK_Registration.php'">
    Tempahan Selesai
</button>
</div>




    </div>

    <!-- Progress Circles -->
    <div class="progress-master-circle active" id="c1"><span class="progress-number">1</span></div>
    <div class="progress-master-circle" id="c2"><span class="progress-number">2</span></div>
    <div class="progress-master-circle" id="c3"><span class="progress-number">3</span></div>
    <div class="progress-master-circle" id="c4"><span class="progress-number">4</span></div>
    <div class="progress-master-circle" id="c5"><span class="progress-number">5</span></div>

    <a href="KVK_Registration.php" class="floating-home-manual">
        <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="30" cy="30" r="28" fill="#AF74B1" stroke="white" stroke-width="3"/>
            <path d="M30 17L21 26V42H27V32H33V42H39V26L30 17Z" fill="white"/>
            <path d="M30 24V17L41 28H19L30 24Z" fill="#8B4789"/>
        </svg>
    </a>

    <script>
function nextStep(n) {
    // Reset semua form + step5
    document.querySelectorAll('.form-area').forEach(el => el.classList.remove('active'));
    const successBox = document.getElementById('step5-content');
    successBox.style.display = 'none';
    successBox.classList.remove('animate'); // buang animasi lama

    const g  = document.querySelector('.greeting');
    const sg = document.querySelector('.sub-greeting');

    if (n === 5) {
        g.style.display = "none";
        sg.style.display = "none";

        // Tunjuk Step 5 + trigger animasi gempak
        successBox.style.display = 'block';
        setTimeout(() => successBox.classList.add('animate'), 10);

        // Semua progress circle jadi kuning
        document.querySelectorAll('.progress-master-circle').forEach(el => el.classList.add('active'));
        return;
    }

    // Step 1-4 biasa
    if (n === 1 || n === 2) {
        g.style.display = "block"; sg.style.display = "block";
        g.textContent = "Hai !";
        sg.innerHTML = "Sebelum tempahan<br>Mari kita berkenalan dahulu";
    } else if (n === 3 || n === 4) {
        g.style.display = "block"; sg.style.display = "block";
        g.textContent = "Baiklah";
        sg.innerHTML = "Sekarang mari<br>kita mula sesi tempahan";
    }

    document.getElementById('step' + n).classList.add('active');
    document.querySelectorAll('.progress-master-circle').forEach((el, i) => {
        el.classList.toggle('active', i + 1 <= n);
    });
}
</script>


</body>
</html>