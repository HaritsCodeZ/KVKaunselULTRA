<!-- STEP 2 -->
            <div class="step" id="step2">
                <h2>Maklumat Peribadi</h2>
                <label>Jantina Anda</label>
                <div class="radio-group">
                    <label class="radio-item"><input type="radio" name="jantina" value="Lelaki" required> Lelaki</label>
                    <label class="radio-item"><input type="radio" name="jantina" value="Perempuan"> Perempuan</label>
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

            <!-- STEP 3 -->
            <div class="step" id="step3">
                <h2>Pilih Tarikh & Sesi</h2>
                <label>Tarikh Tempahan</label>
                <input type="date" name="tarikh" required>

                <label>Jenis Sesi</label>
                <div class="radio-group">
                    <label class="radio-item"><input type="radio" name="jenis" value="Online" required> Online (Google Meet)</label>
                    <label class="radio-item"><input type="radio" name="jenis" value="Bersemuka"> Bersemuka</label>
                </div>

                <label>Masa yang Diingini</label>
                <select name="masa" required>
                    <option value="" disabled selected>Pilih Masa</option>
                    <option>08:00 AM - 10 :00 AM</option>
                    <option>10:30 AM - 11:30 AM</option>
                    <option>02:00 PM - 03:00 PM</option>
                    <option>03:30 PM - 04:30 PM</option>
                </select>
            </div>

            <!-- STEP 4 -->
            <div class="step" id="step4">
                <h2>Pilih Kaunselor & Sebab</h2>
                <label>Pilih Panel Kaunselor</label>
                <select name="kaunselor" required>
                    <option value="" disabled selected>Pilih Kaunselor</option>
                    <option>Pn. Siti Nurhaliza</option>
                    <option>En. Ahmad Daniel</option>
                    <option>Cik Nur Aisyah</option>
                </select>

                <label>Mengapa anda inginkan sesi kaunseling ini?</label>
                <textarea name="sebab" required placeholder="Ceritakan sedikit tentang apa yang anda rasa..."></textarea>
            </div>

            <!-- STEP 5 - FINAL -->
            <div class="step final-step" id="step5">
                <div class="icon">✓</div>
                <h2>Terima Kasih!</h2>
                <p>Tempahan anda telah berjaya dihantar.<br>
                Sila tunggu maklum balas dari kaunselor pilihan anda dalam ruangan chat dalam masa 24 jam.</p>
                <br><br>
                <button class="btn btn-next" onclick="location.href='dashboard.php'">Kembali ke Dashboard</button>
            </div>