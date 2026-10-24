/* BASCO — skrip bersama.
   Data menu & galeri ada di js/data.js. Info kontak ada di objek INFO di bawah. */
const INFO = {
  wa: "6285196976296",
  telp: "0851 9697 6296",
  email: "basco12.indonesia@email.com",
  alamat: "Jl. Halim Perdanakusuma No.40, Bangkalan, Madura",
  jam: "09.00 – 23.00",
  maps: "https://share.google/6Fn94k9wrX396Gsru",
  ig: "https://www.instagram.com/basco_coffee/",
  tiktok: "https://www.tiktok.com/@basco.coffee",
};
const WA = `https://wa.me/${INFO.wa}?text=${encodeURIComponent("Halo Basco, saya mau tanya...")}`;
const $ = (s, r = document) => r.querySelector(s);

/* ---------- Header & footer ---------- */
const NAV = [
  ["index.html", "Beranda"],
  ["menu.html", "Menu"],
  ["galeri.html", "Galeri"],
  ["kontak.html", "Kontak"],
];
const here = location.pathname.split("/").pop() || "index.html";

$("#head").outerHTML = `
<header class="site-head" id="head">
  <div class="wrap bar">
    <a class="brand" href="index.html" aria-label="Basco, ke beranda">
      <img src="img/logo.webp" alt="" width="42" height="42" />
      <span><b>Basco</b><small>Bangkalan Specialty Coffee</small></span>
    </a>
    <button class="burger" aria-expanded="false" aria-controls="links">Menu</button>
    <ul class="links" id="links">
      ${NAV.map(([h, t]) => `<li><a href="${h}"${h === here ? ' aria-current="page"' : ""}>${t}</a></li>`).join("")}
      <li><a class="btn" href="${WA}" target="_blank" rel="noopener">Chat WhatsApp</a></li>
    </ul>
  </div>
</header>`;

const head = $("#head"),
  burger = $(".burger");
burger.addEventListener("click", () => {
  const open = head.classList.toggle("open");
  burger.setAttribute("aria-expanded", open);
});

$("#foot").outerHTML = `
<footer class="site-foot" id="foot">
  <div class="wrap cta">
    <h2>Mampir, duduk dan istirahat dulu.</h2>
    <div class="row">
      <a class="btn lime" href="${WA}" target="_blank" rel="noopener">Chat WhatsApp</a>
      <a class="btn line-light" href="${INFO.maps}" target="_blank" rel="noopener">Lihat lokasi</a>
    </div>
  </div>
  <div class="wrap foot-grid">
    <div>
      <h3>Basco</h3>
      <p>Bangkalan Specialty Coffee.<br />Sederhana, rehat dan hangat.</p>
    </div>
    <div>
      <h3>Kunjungi kami</h3>
      <p>${INFO.alamat}</p>
      <p>Buka ${INFO.jam}</p>
    </div>
    <div>
      <h3>Hubungi</h3>
      <a href="${WA}" target="_blank" rel="noopener">${INFO.telp}</a>
      <a href="mailto:${INFO.email}">${INFO.email}</a>
      <a href="${INFO.ig}" target="_blank" rel="noopener">Instagram</a>
      <a href="${INFO.tiktok}" target="_blank" rel="noopener">TikTok</a>
    </div>
  </div>
  <p class="wrap copy">&copy; ${new Date().getFullYear()} Basco Coffee. Semua hak dilindungi.</p>
</footer>`;

/* ---------- Render menu ---------- */
const pic = (it, kind, cls = "") =>
  it.i
    ? `<div class="pic ${kind === "drink" ? "drink" : ""} ${cls}"><img src="img/m-${it.i}.webp" alt="${it.n}" loading="lazy" /></div>`
    : `<div class="pic ph"><img src="img/logo.webp" alt="" loading="lazy" /></div>`;

const root = $("#menu-root");
if (root) {
  const cats = BASCO.menu;
  $("#chips").innerHTML =
    `<div class="wrap">${cats.map((c) => `<a href="#${c.id}">${c.t}</a>`).join("")}</div>`;
  root.innerHTML = cats
    .map(
      (c) => `
    <section class="kat" id="${c.id}">
      <div class="wrap">
        <h2>${c.t}</h2>
        <p>${c.s}</p>
        ${
          c.feature
            ? `<div class="trilogy">${c.items.map((it) => `<article>${pic(it, c.k)}<h3>${it.n}</h3><p>${it.d}</p><span class="harga">${it.p}</span></article>`).join("")}</div>`
            : `<div class="list">${c.items
                .map(
                  (it) => `
              <div class="item">${pic(it, c.k)}
                <div><div class="top"><h3>${it.n}</h3><span class="dots"></span><span class="harga">${it.p}</span></div>
                ${it.d ? `<p>${it.d}</p>` : ""}</div>
              </div>`,
                )
                .join("")}</div>`
        }
      </div>
    </section>`,
    )
    .join("");

  // tandai chip aktif saat scroll
  const chips = [...document.querySelectorAll("#chips a")];
  const io = new IntersectionObserver(
    (es) =>
      es.forEach((e) => {
        if (e.isIntersecting)
          chips.forEach((a) =>
            a.classList.toggle("on", a.hash === "#" + e.target.id),
          );
      }),
    { rootMargin: "-30% 0px -60% 0px" },
  );
  root.querySelectorAll(".kat").forEach((s) => io.observe(s));
}

/* ---------- Best seller (beranda) ---------- */
const best = $("#best");
if (best) {
  const all = BASCO.menu.flatMap((c) => c.items.map((i) => ({ ...i, k: c.k })));
  best.innerHTML = ["Espresso", "Cold Brew", "Montblanc", "Tiramisu"]
    .map((n) => {
      const it = all.find((x) => x.n === n);
      return `<article>${pic(it, it.k)}<h3>${it.n}</h3><p>${it.d}</p><div class="harga">${it.p}</div></article>`;
    })
    .join("");
}

/* ---------- Suasana (beranda) ---------- */
const bento = $("#bento");
if (bento)
  bento.innerHTML = [
    "p-taman",
    "p-pendopo",
    "p-teman",
    "p-coldbrew",
    "p-takeaway",
  ]
    .map(
      (f) =>
        `<img src="img/${f}.webp" alt="${BASCO.gallery.find((g) => g.f === f).a}" loading="lazy" />`,
    )
    .join("");

/* ---------- Galeri + lightbox ---------- */
const masonry = $("#masonry");
if (masonry) {
  masonry.innerHTML = BASCO.gallery
    .map(
      (g) =>
        `<button type="button" data-f="${g.f}" data-a="${g.a}" aria-label="Perbesar foto: ${g.a}"><img src="img/${g.f}.webp" alt="${g.a}" loading="lazy" /></button>`,
    )
    .join("");
  const lb = $("#lb");
  masonry.addEventListener("click", (e) => {
    const b = e.target.closest("button");
    if (!b) return;
    $("img", lb).src = `img/${b.dataset.f}.webp`;
    $("img", lb).alt = b.dataset.a;
    $("p", lb).textContent = b.dataset.a;
    lb.showModal();
  });
  lb.addEventListener("click", (e) => {
    if (e.target === lb || e.target.matches(".x")) lb.close();
  });
}
