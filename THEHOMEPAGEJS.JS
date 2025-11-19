const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("navLinks");

hamburger.addEventListener("click", () => {
  navLinks.classList.toggle("active");
});


const greetings = [
  "Selamat Datang ke KVKaunsel",
  "Welcome to KVKaunsel",
  "கே.வி. கவுன்சிலிங்கிற்கு வருக",
  "欢迎来到 KV 咨询",
  "selamat salam terbuka"
];

let index = 0;

window.addEventListener("DOMContentLoaded", () => {
  const greetingEl = document.getElementById("greeting-text");

  setInterval(() => {
    // Fade out
    greetingEl.classList.remove("fade-in");
    greetingEl.classList.add("fade-out");

    setTimeout(() => {
      // Change text after fade-out
      index = (index + 1) % greetings.length;
      greetingEl.textContent = greetings[index];

      // Fade back in
      greetingEl.classList.remove("fade-out");
      greetingEl.classList.add("fade-in");
    }, 600); // match fade-out duration in ms
  }, 4000); // change every 4 seconds
});

