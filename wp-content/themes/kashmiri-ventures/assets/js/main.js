(function () {
	'use strict';

	/* Mobile menu */
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('main-nav');
	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	/* Homepage hero slideshow */
	var slides = document.querySelectorAll('.hero-slide');
	if (slides.length > 1 && !(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
		var cur = 0;
		var cap = document.getElementById('hero-cap');
		setInterval(function () {
			slides[cur].classList.remove('on');
			cur = (cur + 1) % slides.length;
			slides[cur].classList.add('on');
			if (cap) { cap.textContent = slides[cur].getAttribute('data-cap'); }
		}, 6500);
	}

	/* Tour page: keep Book Now and WhatsApp links in sync with the chosen date and travellers */
	var card = document.getElementById('book-card');
	if (card) {
		var dep = document.getElementById('kv-dep');
		var trav = document.getElementById('kv-trav');
		var book = document.getElementById('kv-book-link');
		var wa = document.getElementById('kv-wa-link');
		var update = function () {
			var date = dep ? dep.value : '';
			var opt = dep && dep.selectedOptions[0];
			var label = opt ? opt.getAttribute('data-label') : '';
			var n = parseInt(trav.value, 10) || 1;
			var url = card.getAttribute('data-book');
			if (date) { url += (url.indexOf('?') > -1 ? '&' : '?') + 'date=' + encodeURIComponent(date); }
			book.href = url;
			var msg = 'Hello, I want to book: ' + card.getAttribute('data-title') + '.' + (label ? ' Departure: ' + label + '.' : '') + ' Travellers: ' + n + '.';
			wa.href = card.getAttribute('data-wa') + '?text=' + encodeURIComponent(msg);
		};
		if (dep) { dep.addEventListener('change', update); }
		trav.addEventListener('input', update);
		update();
	}

	/* Booking form */
	var form = document.getElementById('booking-form');
	var dataEl = document.getElementById('kv-tours-data');
	if (form && dataEl) {
		var data = {};
		try { data = JSON.parse(dataEl.textContent || '{}'); } catch (e) { data = {}; }
		var cur = form.getAttribute('data-currency') || '';
		var tourSel = document.getElementById('kv-tour');
		var depSel = document.getElementById('kv-dep');
		var depWrap = document.getElementById('kv-dep-wrap');
		var prefWrap = document.getElementById('kv-pref-wrap');
		var pref = document.getElementById('kv-pref');
		var adults = document.getElementById('kv-adults');
		var children = document.getElementById('kv-children');
		var nameEl = document.getElementById('kv-name');
		var pkgWrap = document.getElementById('kv-pkg-wrap');
		var pkgSel = document.getElementById('kv-pkg');
		var roomWrap = document.getElementById('kv-room-wrap');
		var roomSel = document.getElementById('kv-room');
		var tierLabels = { standard: 'Standard Package', upstandard: 'Up-Standard Package', deluxe: 'Deluxe Package' };
		var roomLabels = { solo: 'Solo (4 to 5 persons room sharing)', couple: 'Couple (2 persons, separate room)', single: 'Single (1 person, separate room)' };
		var est = document.getElementById('kv-est');
		var seatNote = document.getElementById('kv-seat-note');
		var waQuick = document.getElementById('kv-wa-quick');
		var fmt = function (n) { return cur + ' ' + Math.round(n).toLocaleString('en-US'); };

		var fillRooms = function (keepRoom) {
			var t = data[tourSel.value];
			roomSel.innerHTML = '';
			if (!t || !t.packages || !t.packages[pkgSel.value]) { return; }
			var row = t.packages[pkgSel.value];
			['solo', 'couple', 'single'].forEach(function (r) {
				if (row[r] > 0) {
					var o = document.createElement('option');
					o.value = r;
					o.textContent = roomLabels[r] + ' - ' + fmt(row[r]);
					if (r === keepRoom) { o.selected = true; }
					roomSel.appendChild(o);
				}
			});
		};
		var fillPackages = function (keepTier, keepRoom) {
			var t = data[tourSel.value];
			var has = !!(t && t.packages && Object.keys(t.packages).length);
			pkgSel.innerHTML = '';
			roomSel.innerHTML = '';
			pkgWrap.hidden = !has; roomWrap.hidden = !has;
			pkgSel.disabled = !has; roomSel.disabled = !has;
			if (!has) { return; }
			['standard', 'upstandard', 'deluxe'].forEach(function (k) {
				if (t.packages[k]) {
					var o = document.createElement('option');
					o.value = k; o.textContent = tierLabels[k];
					if (k === keepTier) { o.selected = true; }
					pkgSel.appendChild(o);
				}
			});
			fillRooms(keepRoom);
		};

		var fillDates = function (keep) {
			var t = data[tourSel.value];
			depSel.innerHTML = '';
			if (!t) { depWrap.hidden = true; prefWrap.hidden = true; depSel.disabled = true; pref.disabled = true; return; }
			if (t.deps.length) {
				depWrap.hidden = false; prefWrap.hidden = true; depSel.disabled = false; pref.disabled = true;
				t.deps.forEach(function (d) {
					var o = document.createElement('option');
					o.value = d.date;
					o.textContent = d.label + ' (' + (d.remaining > 0 ? d.remaining + ' seats left' : 'fully booked') + ')';
					if (d.remaining <= 0) { o.disabled = true; }
					if (d.date === keep) { o.selected = true; }
					depSel.appendChild(o);
				});
				if (!depSel.value || depSel.selectedOptions[0].disabled) {
					for (var i = 0; i < depSel.options.length; i++) {
						if (!depSel.options[i].disabled) { depSel.selectedIndex = i; break; }
					}
				}
			} else {
				depWrap.hidden = true; prefWrap.hidden = false; depSel.disabled = true; pref.disabled = false;
			}
		};

		var recalc = function () {
			var t = data[tourSel.value];
			var a = Math.max(parseInt(adults.value, 10) || 0, 0);
			var c = Math.max(parseInt(children.value, 10) || 0, 0);
			var label = '';
			var remaining = null;
			if (!t) {
				est.textContent = 'Choose a tour';
				seatNote.textContent = '';
				waQuick.href = form.getAttribute('data-wa') + '?text=' + encodeURIComponent('Hello, I would like to book a tour.');
				return;
			}
			var n = a + c;
			if (t.packages && Object.keys(t.packages).length) {
				var prow = t.packages[pkgSel.value];
				var unit = prow ? (prow[roomSel.value] || 0) : 0;
				if (roomSel.value === 'couple' && n % 2 !== 0) {
					est.textContent = 'Couple rooms need 2 persons';
				} else {
					est.textContent = unit ? fmt(unit * (roomSel.value === 'couple' ? n / 2 : n)) : 'Choose a package';
				}
			} else {
				est.textContent = (t.price <= 0 && t.child <= 0) ? 'Price on request' : fmt(a * t.price + c * t.child);
			}
			t.deps.forEach(function (d) {
				if (d.date === depSel.value) { label = d.label; remaining = d.remaining; }
			});
			if (remaining !== null && (a + c) > remaining) {
				seatNote.textContent = 'Only ' + remaining + ' seats are left on this date. Please choose another date or fewer travellers.';
			} else {
				seatNote.textContent = remaining !== null ? remaining + ' seats left on this date.' : '';
			}
			var msg = 'Hello, I want to book: ' + t.title + '.' + (label ? ' Departure: ' + label + '.' : '') + ' Adults: ' + a + ', children: ' + c + '.' + (nameEl.value ? ' Name: ' + nameEl.value + '.' : '');
			waQuick.href = form.getAttribute('data-wa') + '?text=' + encodeURIComponent(msg);
		};

		tourSel.addEventListener('change', function () { fillDates(''); fillPackages('', ''); recalc(); });
		pkgSel.addEventListener('change', function () { fillRooms(roomSel.value); recalc(); });
		[depSel, roomSel, adults, children, nameEl].forEach(function (el) {
			el.addEventListener('input', recalc);
			el.addEventListener('change', recalc);
		});
		fillDates(form.getAttribute('data-date'));
		fillPackages(form.getAttribute('data-pkg'), form.getAttribute('data-room'));
		recalc();
	}
})();
