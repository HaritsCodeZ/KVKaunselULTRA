<?php 
session_start(); 
?>

<div style="position: fixed; top: 0; width: 100%; z-index: 1000;">
    <?php include 'KVK_Navbar.php'; ?>
</div>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Aktiviti 3D | KVK</title>
    <style>
        :root {
            --page-width: 450px; 
            --page-height: 550px;
            --speed: 0.8s;
            --pink-theme: #af74b1;
            --lavender-soft: #E6E6FA; 
            --lavender-dark: #967BB6;
        }

        body, html {
            margin: 0; padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        #video-background {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: -2;
        }

        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(2px);
            z-index: -1;
        }

        .header-title {
            position: absolute;
            top: 90px;
            width: 100%;
            text-align: center;
            z-index: 10;
        }

        .header-title h1 {
            font-size: 3rem;
            color: var(--pink-theme);
            text-transform: uppercase;
            letter-spacing: 4px;
            text-shadow: 2px 2px 5px rgba(150, 123, 182, 0.3);
        }

        .scene {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            perspective: 2000px;
        }

        .book {
            width: var(--page-width);
            height: var(--page-height);
            position: relative;
            transform-style: preserve-3d;
            transform: translateX(50%); 
            transition: transform var(--speed);
        }

        .page {
            position: absolute;
            width: var(--page-width);
            height: var(--page-height);
            top: 0; left: 0;
            transform-origin: left;
            transform-style: preserve-3d;
            transition: transform var(--speed) cubic-bezier(0.645, 0.045, 0.355, 1);
            cursor: pointer;
        }

        .front, .back {
            position: absolute;
            width: 100%; height: 100%;
            backface-visibility: hidden;
            padding: 50px;
            background: var(--lavender-soft);
            border: 1px solid rgba(150, 123, 182, 0.2);
            box-shadow: inset 0 0 50px rgba(209, 77, 114, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .front::before, .back::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: url('KVK_AktivitiPrs.jfif');
            background-size: cover;
            opacity: 0.12;
            z-index: -1;
        }

        .back { transform: rotateY(180deg); }

        .flower-frame {
            width: 420px;
            height: 420px;
            background: var(--pink-theme);
            padding: 6px;
            clip-path: polygon(50% 0%, 63% 13%, 82% 10%, 87% 28%, 100% 38%, 90% 55%, 98% 75%, 80% 84%, 73% 100%, 50% 90%, 27% 100%, 20% 84%, 2% 75%, 10% 55%, 0% 38%, 13% 28%, 18% 10%, 37% 13%);
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .flower-frame img {
            width: 100%; height: 100%;
            object-fit: cover;
            clip-path: inherit;
        }

        h2 { color: var(--pink-theme); margin-bottom: 10px; font-size: 1.8rem; font-weight: 800; }
        p { color: #4b3d61; line-height: 1.6; font-size: 1.05rem; font-weight: 500; }

        .cover-front { background: var(--pink-theme) !important; color: white; }
        .cover-front h2 { color: white !important; font-size: 2.5rem; text-shadow: 2px 2px 0px var(--lavender-dark); }

        .page.flipped { transform: rotateY(-180deg); }

        .hint-box {
            position: absolute;
            bottom: 400px;
            left: 500px;
            background: var(--pink-theme);
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(209, 77, 114, 0.3);
            transition: all 0.6s ease-in-out;
            z-index: 20;
        }

        .hint-center {
            left: 50%;
            bottom: 40px;
            transform: translateX(-50%);
        }
    </style>
</head>
<body>

    <video autoplay muted loop id="video-background">
        <source src="VideoGalleries/KVK_AktivitBack.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>

    <div class="header-title">
        <h1>AKTIVITI KAUNSELING</h1>
    </div>

    <div class="scene">
        <div class="book" id="book">
            
            <div class="page" style="z-index: 7;" onclick="flipPage(this)">
                <div class="front cover-front">
                    <h2>JURNAL AKTIVITI</h2>
                    <p style="color: var(--lavender-soft);">Unit Psikologi Dan Kerjaya</p>
                    <div style="margin-top: 30px; border: 2px solid var(--lavender-soft); padding: 10px; color: white;">Mari lihat aktiviti yang dianjurkan!</div>
                </div>
                <div class="back">
                    <h2>Program Ini...</h2>
                    <p>adalah anjuran Unit Kaunseling yang telah dilaksanakan bertujuan untuk menghargai pelajar serta mewujudkan suasana yang lebih mesra, positif dan menyokong kesejahteraan emosi dalam kalangan warga institusi. Program ini juga menjadi medium untuk mengeratkan hubungan antara pelajar dan pihak kaunseling melalui pendekatan santai dan berinformal</p>
                </div>
            </div>

            <div class="page" style="z-index: 6;" onclick="flipPage(this)">
                <div class="front">
                    <div class="flower-frame">
                        <img src="AktivitiPrs/AktivitiHariJadi.jpeg" alt="Hari Jadi">
                    </div>
                    <h2>Program Sambutan Hari Jadi</h2>
                    <p>Meraikan setiap detik kehidupan dalam ukhuwah yang erat dan penuh kasih sayang.</p>
                </div>
                <div class="back">
                    <h2>Program Ini...</h2>
                    <p>adalah anjuran Unit Kaunseling yang telah dilaksanakan dengan tujuan untuk meningkatkan kesedaran pelajar terhadap kepentingan gaya hidup sihat dari aspek fizikal dan mental. Program ini juga bertujuan untuk memupuk disiplin diri, motivasi serta semangat persaingan yang sihat dalam kalangan peserta</p>
                </div>
            </div>

            <div class="page" style="z-index: 5;" onclick="flipPage(this)">
                <div class="front">
                    <div class="flower-frame">
                        <img src="AktivitiPrs/Biggest loserjpeg.jpeg" alt="Biggest Loser">
                    </div>
                    <h2>Program The Biggest Loser</h2>
                    <p>Transformasi diri ke arah gaya hidup yang lebih sihat dan aktif.</p>
                </div>
                <div class="back">
                    <h2>Program ini...</h2>
                    <p>adalah anjuran Unit Kaunseling yang telah dilaksanakan bertujuan untuk menghargai dan mengiktiraf pelajar yang menunjukkan perubahan positif, sahsiah terpuji serta pencapaian cemerlang dari aspek akademik, disiplin dan pembangunan diri. Program ini juga bertujuan untuk meningkatkan motivasi serta keyakinan diri pelajar melalui penghargaan yang bersifat membina</p>
                </div>
            </div>

            <div class="page" style="z-index: 4;" onclick="flipPage(this)">
                <div class="front">
                    <div class="flower-frame">
                        <img src="AktivitiPrs/AktivitiTheStar.jpeg" alt="The Star">
                    </div>
                    <h2>Program Anugerah The Star</h2>
                    <p>Menghargai dan mengiktiraf para pelajar juga mencerminkan diri sebagai pelajar contoh kepada semua pelajar. </p>
                </div>
                <div class="back">
                    <h2>Program ini...</h2>
                    <p>adalah anjuran Unit Kaunseling yang telah dilaksanakan bertujuan untuk meningkatkan kesedaran pelajar terhadap kepentingan penjagaan kesihatan mental serta pengurusan emosi dalam kehidupan seharian. Program ini juga bertujuan membantu pelajar mengenal pasti tanda-tanda tekanan, kebimbangan dan keletihan emosi serta cara mengatasinya secara berkesan</p>
                </div>
            </div>

            <div class="page" style="z-index: 3;" onclick="flipPage(this)">
                <div class="front">
                    <div class="flower-frame">
                        <img src="AktivitiPrs/CeramahMindaSihat.jpeg" alt="Aktiviti PRS">
                    </div>
                    <h2>Program Ceramah Minda Sihat</h2>
                    <p>Membentuk sahsiah pelajar yang cemerlang melalui aktiviti yang kreatif dan inovatif.</p>
                </div>
                <div class="back cover-front">
                    <h2>TERIMA KASIH</h2>
                    <p style="color: var(--lavender-soft);">Pelbagai lagi aktiviti kaunseling akan dianjurkan sila nantikan ya!</p>
                    <p style="font-size: 0.8rem; margin-top: 20px;">© 2026 KVKaunsel</p>
                </div>
            </div>

        </div>

        <div id="hintBox" class="hint-box">Klik pada muka surat untuk menyelak</div>
    </div>

    <script>
        let currentZIndex = 1;
        let firstClick = true;

        function flipPage(page) {
            const hint = document.getElementById('hintBox');
            
            if (firstClick) {
                hint.classList.add('hint-center');
                firstClick = false;
            }

            if (page.classList.contains('flipped')) {
                page.classList.remove('flipped');
                setTimeout(() => {
                    // Menyusun semula z-index asal berdasarkan kedudukan anak (total 5 pages = index 0 to 4)
                    const totalPages = page.parentNode.children.length;
                    const index = Array.from(page.parentNode.children).indexOf(page);
                    page.style.zIndex = (totalPages + 2) - index;
                }, 500);
            } else {
                page.classList.add('flipped');
                page.style.zIndex = 20 + currentZIndex;
                currentZIndex++;
            }
        }
    </script>

</body>
</html>