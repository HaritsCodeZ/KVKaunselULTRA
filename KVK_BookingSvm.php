<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempahan Kaunseling SVM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        html { overflow-y: scroll; }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 10px 0;
            font-family:'Inter',sans-serif;
            overflow-x: hidden;
        }
        /* VIDEO BACKGROUND */
        .hero__video {
            position:fixed; top:0; left:0; width:100%; height:100%;
            object-fit:cover; z-index:-2; filter:brightness(1.08);
        }
        /* MAIN CARD */
        .card {
            position:relative;
            width:1500px;
            max-width:95vw;
            background:rgba(255,255,255,0.92);
            backdrop-filter:blur(20px);
            border-radius:36px;
            box-shadow:0 40px 120px rgba(140,86,144,0.25);
            overflow:hidden; z-index:10;
            border:1px solid rgba(255,255,255,0.4);
            display:flex;
            flex-direction:column;
            margin: auto;
        }
        .card-header {
            background:linear-gradient(135deg, #8C5690 0%, #A066AA 100%);
            color:white;
            padding:22px 0;
            text-align:center;
        }
        .logo-row {
            display:flex; justify-content:center; align-items:center;
            gap:40px; margin-bottom:20px;
        }
        .logo-row img {
            height:64px; filter:brightness(0) invert(1);
            transition:transform 0.3s;
        }
        .logo-row img:hover { transform:scale(1.1); }
        .card-header h1 { font-size:52px; font-weight:900; margin-bottom:1px; }
        .card-header p { font-size:21px; opacity:0.95; }
        .card-body {
            padding: 0;
            flex:1;
            position:relative;
            overflow: hidden;
            min-height: 500px;
        }
        /* STEP INDICATOR */
        .step-indicator {
            display:flex; justify-content:center; gap:18px;
            margin-bottom:50px;
            padding-top: 80px;
            position: relative;
            z-index: 20;
        }
        .step-dot {
            width:16px; height:16px; border-radius:50%; background:rgba(140,86,144,0.3);
            transition:all 0.4s; position:relative;
        }
        .step-dot.active {
            background:#8C5690; transform:scale(1.5);
            box-shadow:0 0 0 6px rgba(140,86,144,0.2);
        }
        .step-dot::after {
            content:attr(data-step); position:absolute; top:-30px; left:50%;
            transform:translateX(-50%); font-size:13px; color:#8C5690; opacity:0;
            transition:opacity 0.3s;
        }
        .step-dot.active::after { opacity:1; }
        /* STEPS */
        .step {
            display: block;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
            position: absolute;
            width: 100%;
            top: 10px;
            left: 0;
            visibility: hidden;
            padding: 0 110px;
            pointer-events: none;
            min-height: 100%;
            z-index: 10;
        }
        .step.active {
            position: relative;
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
            pointer-events: auto;
        }
        h2 {
            font-size:40px; color:#8C5690; margin-bottom:36px; font-weight:800;
            text-align:center;
        }
        label {
            display:block; font-size:19px; font-weight:600; color:#5e3568;
            margin-bottom:14px; margin-top:28px;
        }
        input, select, textarea {
            width:100%; padding:20px 24px; border:2px solid #e2d4e8;
            border-radius:18px; font-size:18px; background:white;
            transition:all 0.4s; box-shadow:0 4px 15px rgba(0,0,0,0.03);
        }
        input:focus, select:focus, textarea:focus {
            outline:none; border-color:#8C5690; transform:translateY(-2px);
            box-shadow:0 0 0 6px rgba(140,86,144,0.15), 0 12px 30px rgba(140,86,144,0.12);
        }
        textarea { height:160px;45 resize:none; }
        .radio-group {
            display:flex; gap:40px; margin:24px 0; flex-wrap:wrap;
        }
        .radio-item {
            display:flex; align-items:center; gap:14px; font-size:18px; cursor:pointer;
            background:white; padding:16px 24px; border-radius:16px;
            border:2px solid #e2d4e8; transition:all 0.3s; flex:1; min-width:200px;
        }
        .radio-item:hover { border-color:#8C5690; }
        .radio-item input[type="radio"] { width:26px; height:26px; accent-color:#8C5690; }
        .radio-item input[type="radio"]:checked + span { font-weight:700; }
        .btn-group {
            margin-top:60px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding: 0 110px 80px 110px;
            position: relative;
            z-index: 20;
        }
        .btn {
            padding:18px 52px; border:none; border-radius:22px; font-size:19px; font-weight:700;
            cursor:pointer; transition:all 0.4s; min-width:180px;
        }
        .btn-prev {
            background:transparent; color:#8C5690; border:2.5px solid #8C5690;
        }
        .btn-prev:hover { background:#8C5690; color:white; transform:translateY(-4px); }
        .btn-next {
            background:#8C5690; color:white; box-shadow:0 10px 30px rgba(140,86,144,0.3);
        }
        .btn-next:hover { background:#7a4d82; transform:translateY(-5px); box-shadow:0 15px 40px rgba(140,86,144,0.4); }
        .final-step {
            text-align:center;
            padding:80px 30px;
        }
        .final-step .icon {
            font-size:140px; margin-bottom:10px; color:#8C5690;
            animation:check 1.5s ease-in-out;
        }
        @keyframes check { 0%,100% {transform:scale(1);} 50% {transform:scale(1.2);} }
        .final-step h2 { font-size:56px; margin-bottom:28px; }
        .final-step p { font-size:23px; color:#555; line-height:1.8; }
    </style>
</head>
<body>
    <video class="hero__video" autoplay muted loop playsinline poster="ImageGalleries/ImageCounseling1.jpg">
        <source src="VideoGalleries/KVK_BookingVideo.mp4" type="video/mp4">
    </video>

    <div class="card">
        <div class="card-header">
            <div class="logo-row"></div>
            <h1>Tiket Tempahan Kaunseling</h1>
            <p>SVM • Kolej Vokasional Betong</p>
        </div>

        <div class="card-body">
            <div class="step-indicator">
                <div class="step-dot active" data-step=""></div>
                <div class="step-dot" data-step=""></div>
                <div class="step-dot" data-step=""></div>
                <div class="step-dot" data-step=""></div>
                <div class="step-dot" data-step=""></div>
            </div>

            <div class="step active" id="step1">
                <h2>Mari kita berkenalan terlebih dahulu!</h2>
                <label>Nama Penuh Anda</label>
                <input type="text" name="nama" required placeholder="Contoh: Nur Raudhah Amirah Binti Azman">

                <label>Program Anda</label>
                <select name="program" required>
                    <option value="" disabled selected>Pilih Program</option>
                    <option>Program Teknologi Elektrik</option>
                    <option>Perkaunan</option>
                    <option>Program Sistem Komputer Dan Rangkaian</option>
                    <option>Program Hospitaliti Seni Kulinari</option>
                    <option>Program Teknologi Automotif</option>
                    <option>Program Teknologi Kimpalan</option>
                    <option>Program Teknologi Pemesinan Industri</option>
                    <option>Program Teknologi</option>
                </select>

                <label>Semester Anda</label>
                <select name="semester" required>
                    <option value="" disabled selected>Pilih Semester</option>
                    <option>Semester 1</option>
                    <option>Semester 2</option>
                    <option>Semester 3</option>
                    <option>Semester 4</option>
                </select>
            </div>

            <div class="step" id="step2">
                <h2>Maklumat Peribadi</h2>
                <label>Jantina Anda</label>
                <div class="radio-group">
                    <label class="radio-item"><input type="radio" name="jantina" value="Lelaki" required><span>Lelaki</span></label>
                    <label class="radio-item"><input type="radio" name="jantina" value="Perempuan"><span>Perempuan</span></label>
                </div>

                <label>Kaum Anda</label>
                <select name="kaum" required>
                    <option value="" disabled selected>Pilih Kaum</option>
                    <option>Melayu</option>
                    <option>Cina</option>
                    <option>India</option>
                    <option>Lain-lain</option>
                </select>

                <label>No. Telefon (WhatsApp)</label>
                <input type="tel" name="telefon" required placeholder="Contoh: 0123456789">
            </div>

            <div class="step" id="step3">
                <h2>Pilih Tarikh & Sesi</h2>
                <label>Tarikh Dan Masa Tempahan</label>
                <input type="datetime-local" name="tarikh_dan_masa" required>

                <label>Jenis Sesi</label>
                <div class="radio-group">
                    <label class="radio-item"><input type="radio" name="jenis" value="Online" required><span>Online (Google Meet)</span></label>
                    <label class="radio-item"><input type="radio" name="jenis" value="Bersemuka"><span>Bersemuka</span></label>
                </div>
            </div>

            <div class="step" id="step4">
                <h2>Pilih Kaunselor & Sebab</h2>
                <label>Pilih Panel Kaunselor</label>
                <select name="kaunselor" required>
                    <option value="" disabled selected>Pilih Kaunselor</option>
                    <option>Encik Muhirman Bin Mu Alim</option>
                    <option>Tanita Anak Numpang</option>
                    <option>Whilemina Thimah Gregory Anak Jimbun</option>
                </select>

                <label>Mengapa anda inginkan sesi kaunseling ini?</label>
                <textarea name="sebab" required placeholder="Ceritakan sedikit tentang apa yang anda rasa..."></textarea>
            </div>

            <div class="step final-step" id="step5">
                <div class="icon">✓</div>
                <h2>Terima Kasih!</h2>
                <p>Tempahan anda telah berjaya dihantar.<br>
                Sila tunggu maklum balas dari kaunselor pilihan anda dalam ruangan chat dalam masa 24 jam.</p>
                <br><br>
                <button class="btn btn-next" onclick="location.href='KVK_Registration.php'">Kembali ke Dashboard</button>
            </div>
        </div>

        <div class="btn-group">
            <button class="btn btn-prev" id="prevBtn" onclick="nextPrev(-1)">Kembali</button>
            <button class="btn btn-next" id="nextBtn" onclick="nextPrev(1)">Seterusnya</button>
        </div>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 5;
        const steps = document.querySelectorAll('.step');
        const stepDots = document.querySelectorAll('.step-dot');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const btnGroup = document.querySelector('.btn-group');

        function validateStep(stepIndex) {
            const currentStepEl = document.getElementById(`step${stepIndex}`);
            const requiredElements = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            requiredElements.forEach(input => {
                if (input.type === 'radio') {
                    const radioName = input.name;
                    if (!currentStepEl.querySelector(`input[name="${radioName}"]:checked`)) {
                        isValid = false;
                    }
                } else if (input.type === 'select-one' && input.value === "") {
                    isValid = false;
                } else if (!input.value.trim()) {
                    isValid = false;
                }
            });

            if (!isValid) {
                alert("Sila lengkapkan semua medan yang diperlukan sebelum beralih ke langkah seterusnya.");
            }
            return isValid;
        }

        function showStep(n) {
            steps.forEach(step => step.classList.remove('active'));
            const stepElement = document.getElementById(`step${n}`);
            if (stepElement) stepElement.classList.add('active');

            stepDots.forEach((dot, i) => {
                dot.classList.toggle('active', i + 1 === n);
            });

            prevBtn.style.display = n === 1 || n === totalSteps ? 'none' : 'block';

            if (n === totalSteps) {
                nextBtn.style.display = 'none';
                btnGroup.style.justifyContent = 'center';
            } else {
                nextBtn.style.display = 'block';
                nextBtn.innerHTML = (n === totalSteps - 1) ? 'Hantar Tempahan' : 'Seterusnya';
                btnGroup.style.justifyContent = 'space-between';
            }
        }

        function nextPrev(n) {
            if (n === 1 && currentStep < totalSteps - 1 && !validateStep(currentStep)) {
                return false;
            }

            if (n === 1 && currentStep === totalSteps - 1) {
                if (!validateStep(currentStep)) return false;
                // AJAX submission can be added here later
                currentStep += n;
                showStep(currentStep);
                return;
            }

            currentStep += n;
            if (currentStep > totalSteps) currentStep = totalSteps;
            if (currentStep < 1) currentStep = 1;

            showStep(currentStep);
        }

        showStep(currentStep);
    </script>
</body>
</html>