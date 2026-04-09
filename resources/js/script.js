/*
Author       : Dreamguys
Template Name: POS - Bootstrap Admin Template
*/


$(document).ready(function(){

	// Variables declarations
	const $wrapper = $('.main-wrapper');
	const $overlay = $('<div class="sidebar-overlay"></div>');
	$overlay.insertBefore('.main-wrapper');


	// Toggle Mobile Menu
	$(document).on('click', '#mobile_btn', function (e) {
		e.preventDefault();
		$wrapper.toggleClass('slide-nav');
		$overlay.toggleClass('opened');
		$('html').toggleClass('menu-opened');
	});

	// Close sidebar on close button click
	$(document).on('click', '.sidebar-close, .sidebar-overlay', function () {
		$wrapper.removeClass('slide-nav');
		$overlay.removeClass('opened');
		$('html').removeClass('menu-opened');
	});

	// Table Responsive
	setTimeout(function () {
		$(document).ready(function () {
			$('.table').parent().addClass('table-responsive');
		});
	}, 1000);

	// Datatable - only init when table has consistent columns (avoids error when empty state uses colspan)
	if ($('.datatable').length > 0) {
		$('.datatable').each(function () {
			var $table = $(this);
			var colCount = $table.find('thead th').length;
			var $rows = $table.find('tbody tr');
			var canInit = true;
			if (colCount > 0 && $rows.length > 0) {
				$rows.each(function () {
					var cellCount = $(this).find('td').length;
					if (cellCount !== colCount) {
						canInit = false;
						return false;
					}
				});
			}
			if (canInit) {
				$table.DataTable({
					"bFilter": true,
					"sDom": 'fBtlp',
					"ordering": false,
					"language": {
						search: ' ',
						sLengthMenu: '_MENU_',
						searchPlaceholder: "Search",
						sLengthMenu: ' _MENU_ Entries',
						info: "_START_ - _END_ of _TOTAL_ items",
						emptyTable: "No data available",
						zeroRecords: "No matching records found",
						paginate: {
							next: ' Next <i class="icon-chevron-right"></i>',
							previous: '<i class="icon-chevron-left"></i> Prev'
						},
					},
					initComplete: function (settings, json) {
						$('.dt-search').appendTo('#tableSearch');
						$('.dt-search').appendTo('.search-input');
					},
				});
			}
		});
	}


	// Datetimepicker
	if($('.datetimepicker').length > 0 ){
		$('.datetimepicker').datetimepicker({
			format: 'DD-MM-YYYY',
			icons: {
				up: "fas fa-angle-up",
				down: "fas fa-angle-down",
				next: 'fas fa-angle-right',
				previous: 'fas fa-angle-left'
			}
		});
	}

	// Date Range Picker
	if ($('.daterangepick').length > 0) {
		const isOrdersPage = document.getElementById('orders-search') !== null;
		let start = moment().subtract(29, "days");
		let end = moment();
		if (isOrdersPage && window.ordersDateRange && window.ordersDateRange.from && window.ordersDateRange.to) {
			start = moment(window.ordersDateRange.from);
			end = moment(window.ordersDateRange.to);
		}
		const report_range = (start, end) => {
			$(".daterangepick span").html(start.format("D MMM YY") + " - " + end.format("D MMM YY"));
			if (isOrdersPage) {
				const path = window.location.pathname;
				window.location = path + "?from=" + start.format("YYYY-MM-DD") + "&to=" + end.format("YYYY-MM-DD");
			}
		};
		$(".daterangepick").daterangepicker(
			{
				startDate: start,
				endDate: end,
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, "days"), moment().subtract(1, "days")],
					"Last 7 Days": [moment().subtract(6, "days"), moment()],
					"Last 30 Days": [moment().subtract(29, "days"), moment()],
					"This Month": [moment().startOf("month"), moment().endOf("month")],
					"Last Month": [
						moment().subtract(1, "month").startOf("month"),
						moment().subtract(1, "month").endOf("month")
					]
				}
			},
			report_range
		);
		if (!isOrdersPage) {
			report_range(start, end);
		} else {
			$(".daterangepick span").html(start.format("D MMM YY") + " - " + end.format("D MMM YY"));
		}
	}

		// Select 2 Search
	if ($('.select2').length > 0) {
		$('.select2').select2({
			// Set to 0 to always show search, or a number like 10 
			// to show only when there are 10+ results
			minimumResultsForSearch: 0, 
			width: '100%'
		});
	}

	// Select 2
	if ($('.select').length > 0) {
		$('.select').select2({
			minimumResultsForSearch: -1,
			width: '100%'
		});
	}

	// Select Table Checkbox
	$('#select-all').on('change', function () {
		$('.form-check.form-check-md input[type="checkbox"]').prop('checked', this.checked);
	});

	// Counter 
	if($('.counter').length > 0) {
		$('.counter').counterUp({
			delay: 20,
			time: 2000
		});
	}

	// Toggle Password
	if ($('.toggle-password').length > 0) {
		$(document).on('click', '.toggle-password', function () {
			const $icon = $(this).find('i');
			const $input = $(this).closest('.input-group').find('.pass-input');
			if ($input.attr('type') === 'password') {
				$input.attr('type', 'text');
				$icon.removeClass('icon-eye-off').addClass('icon-eye');
			} else {
				$input.attr('type', 'password');
				$icon.removeClass('icon-eye').addClass('icon-eye-off');
			}
		});
	}

	// Sidebar
	var Sidemenu = function() {
		this.$menuItem = $('.sidebar-menu a');
	};

	function init() {
		var $this = Sidemenu;
		$('.sidebar-menu a').on('click', function(e) {
			if($(this).parent().hasClass('submenu')) {
				e.preventDefault();
			}
			if(!$(this).hasClass('subdrop')) {
				$('ul', $(this).parents('ul:first')).slideUp(250);
				$('a', $(this).parents('ul:first')).removeClass('subdrop');
				$(this).next('ul').slideDown(350);
				$(this).addClass('subdrop');
			} else if($(this).hasClass('subdrop')) {
				$(this).removeClass('subdrop');
				$(this).next('ul').slideUp(350);
			}
		});
		$('.sidebar-menu ul li.submenu a.active').parents('li:last').children('a:first').addClass('active').trigger('click');
	}

	// Sidebar
	var Colsidemenu = function() {
		this.$menuItems = $('.sidebar-right a');
	};

	function colinit() {
    var $this = Colsidemenu;

    // Unbind previous click handlers to avoid duplicates
		$('.sidebar-right ul a').off('click').on('click', function (e) {

			// Check if parent has 'submenu' class
			if ($(this).parent().hasClass('submenu')) {
				e.preventDefault();
				console.log("1");
			}

			// If this is not currently expanded
			if (!$(this).hasClass('subdrop')) {
				// Close all sibling submenus
				$(this).closest('ul').find('ul').slideUp(250);
				$(this).closest('ul').find('a').removeClass('subdrop');

				// Open the clicked submenu
				$(this).next('ul').slideDown(350);
				$(this).addClass('subdrop');
				console.log("0");

			} else { // If already expanded, collapse it
				$(this).removeClass('subdrop');
				$(this).next('ul').slideUp(350);
				console.log("3");
			}
		});

		// Open submenu if an active item is inside
		$('.sidebar-right ul li.submenu a.active').parents('li').children('a').addClass('active subdrop');
		$('.sidebar-right ul li.submenu a.active').parents('ul').slideDown(350);
	}

	colinit();


	
	// Sidebar Initiate
	init();
	$(document).on('mouseover', function(e) {
        e.stopPropagation();
        if ($('body').hasClass('mini-sidebar')) {
            var targ = $(e.target).closest('.sidebar, .header-left').length;
            if (targ) {
                $('body').addClass('expand-menu');
                $('.subdrop + ul').slideDown();
            } else {
                $('body').removeClass('expand-menu');
                $('.subdrop + ul').slideUp();
            }
            return false;
        }
		if ($('body').hasClass('mini-sidebar')) {
            var targ = $(e.target).closest('.sidebar, .header-left').length;
            if (targ) {
                $('body.layout-box-mode').addClass('expand-menu');
                $('.subdrop + ul').slideDown();
            } else {
                $('body').removeClass('expand-menu');
                $('.subdrop + ul').slideUp();
            }
            return false;
        }
    });

	// Toggle Button
	$(document).on('click', '#toggle_btn', function () {
		const $body = $('body');
		const $html = $('html');
		const isMini = $body.hasClass('mini-sidebar');
	
		if (isMini) {
			$body.removeClass('mini-sidebar');
			$(this).addClass('active');
			localStorage.setItem('screenModeNightTokenState', 'night');
			setTimeout(function () {
				$(".header-left").addClass("active");
			}, 100);
		} else {
			$body.addClass('mini-sidebar');
			$(this).removeClass('active');
			localStorage.removeItem('screenModeNightTokenState');
			setTimeout(function () {
				$(".header-left").removeClass("active");
			}, 100);
		}

	
		return false;
	});
		
	// Tooltip
	if($('[data-bs-toggle="tooltip"]').length > 0) {
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	}

		// Initialize Flatpickr on elements with data-provider="flatpickr"
		document.querySelectorAll('[data-provider="flatpickr"]').forEach(el => {
			const config = {
				disableMobile: true
			};
			if (el.hasAttribute('data-date-format')) {
				config.dateFormat = el.getAttribute('data-date-format');
			}
			if (el.hasAttribute('data-enable-time')) {
				config.enableTime = true;
				config.dateFormat = config.dateFormat ? `${config.dateFormat} H:i` : 'Y-m-d H:i';
			}
			if (el.hasAttribute('data-altFormat')) {
				config.altInput = true;
				config.altFormat = el.getAttribute('data-altFormat');
			}
			if (el.hasAttribute('data-minDate')) {
				config.minDate = el.getAttribute('data-minDate');
			}
			if (el.hasAttribute('data-maxDate')) {
				config.maxDate = el.getAttribute('data-maxDate');
			}
			if (el.hasAttribute('data-default-date')) {
				const defaultDate = el.getAttribute('data-default-date');
				// Check if it's a valid date string
				if (!["true", "false", "", null].includes(defaultDate) && !isNaN(Date.parse(defaultDate))) {
					config.defaultDate = defaultDate;
				}
			}
			if (el.hasAttribute('data-multiple-date')) {
				config.mode = 'multiple';
			}
			if (el.hasAttribute('data-range-date')) {
				config.mode = 'range';
			}
			if (el.hasAttribute('data-inline-date')) {
				config.inline = true;
				const inlineDate = el.getAttribute('data-inline-date');
				if (!["true", "false", "", null].includes(inlineDate) && !isNaN(Date.parse(inlineDate))) {
					config.defaultDate = inlineDate;
				}
			}
			if (el.hasAttribute('data-disable-date')) {
				config.disable = el.getAttribute('data-disable-date').split(',');
			}
			if (el.hasAttribute('data-week-number')) {
				config.weekNumbers = true;
			}
			flatpickr(el, config);
		});
		
		// Add input in modal

		// Add new row (works for all groups)
			$(document).on("click", ".addRowBtn", function () {
				let target = $(this).data("target");  // get group id
				let template = $("#" + target + "-template").clone();

				template.removeClass("d-none rowTemplate");
				$("#" + target).append(template);
			});

			// Delete row (works for all groups)
			$(document).on("click", ".deleteRowBtn", function () {
				$(this).closest(".row").remove();
			});

		//Copy to Clipboard
		$(document).on("click", ".copytoclipboard", function () {
			let text = document.getElementById("copytext").innerText;
			navigator.clipboard.writeText(text)
				.then(() => alert("Copied to clipboard!"))
				.catch(err => console.error("Failed to copy: ", err));
			});
	
	// Timer
	$(".card").each(function () {
		const card = $(this);
		const btn = card.find(".timer-btn");
		const icon = btn.find("i");
		const label = btn.find(".label");
		const timeText = btn.find(".time");

		// Timer state per card
		let seconds = 0;
		let timerInterval = null;

		function formatTime(sec) {
			const m = String(Math.floor(sec / 60)).padStart(2, "0");
			const s = String(sec % 60).padStart(2, "0");
			return `${m}:${s}`;
		}

		function startTimer() {
			if (timerInterval) return;
			btn.addClass("running");
			icon.removeClass("icon-play").addClass("icon-pause");
			label.text("Pause");

			timerInterval = setInterval(() => {
				seconds++;
				timeText.text(formatTime(seconds));
			}, 1000);
		}

		function pauseTimer() {
			if (!timerInterval) return;
			clearInterval(timerInterval);
			timerInterval = null;
			btn.removeClass("running");
			icon.removeClass("icon-pause").addClass("icon-play");
			label.text("Play");
		}

		btn.on("click", function (e) {
			e.preventDefault(); // prevent default link behavior

			if (btn.hasClass("running")) {
				pauseTimer();
			} else {
				startTimer();
			}
		});
	});



		// Attach keydown event only when modal is open
		$('#calculator').on('shown.bs.modal', function () {
			document.addEventListener("keydown", myFunction);
		});

		// Remove keydown event when modal is closed
		$('#calculator').on('hidden.bs.modal', function () {
			document.removeEventListener("keydown", myFunction);
		});
		
		// Kanban Drag
		if($('.kanban-drag-wrap').length > 0) {
			$(".kanban-drag-wrap").sortable({
				connectWith: ".kanban-drag-wrap",
				handle: ".kanban-card",
				placeholder: "drag-placeholder"
			});
		}

		// Timer
		$(".card").each(function () {

			let seconds = 0;
			let timerInterval = null;
			let startedOnce = false;

			const card = $(this);
			const btn = card.find(".timer-btn");
			const icon = btn.find("i");
			const label = btn.find(".label");
			const timeText = btn.find(".time");
			const modalId = btn.data("bs-target");

			function formatTime(sec) {
				let m = String(Math.floor(sec / 60)).padStart(2, "0");
				let s = String(sec % 60).padStart(2, "0");
				return `${m}:${s}`;
			}

			function startTimer() {
				if (timerInterval) return;

				btn.addClass("running");
				icon.removeClass("icon-play").addClass("icon-pause");
				label.text("Pause");

				timerInterval = setInterval(() => {
					seconds++;
					timeText.text(formatTime(seconds));
				}, 1000);
			}

			function pauseTimer() {
				clearInterval(timerInterval);
				timerInterval = null;

				btn.removeClass("running");
				icon.removeClass("icon-pause").addClass("icon-play");
				label.text("Play");
			}

			// ▶ Start timer ONLY first time modal opens
			$(modalId).on("shown.bs.modal", function () {
				if (!startedOnce) {
					startedOnce = true;
					startTimer();
				}
			});

			// ⏯ Toggle play / pause
			btn.on("click", function (e) {

				// If already running → pause (don’t reopen modal)
				if (btn.hasClass("running")) {
					e.preventDefault();
					pauseTimer();
					return;
				}

				// If paused and modal already opened → resume
				if (startedOnce) {
					e.preventDefault();
					startTimer();
				}
			});

		});

	//Increment Decrement Numberes (skip POS item cards – they use delegated handlers below)
	document.querySelectorAll(".quantity-control").forEach(container => {
		if (container.closest && container.closest(".pos-item-card")) return;
		const input = container.querySelector(".quantity-input");
		if (!input) return;
		const addBtn = container.querySelector(".add-btn");
		const minusBtn = container.querySelector(".minus-btn");
		if (addBtn) addBtn.addEventListener("click", () => { input.value = Math.max(0, Number(input.value) || 0) + 1; });
		if (minusBtn) minusBtn.addEventListener("click", () => { if (Number(input.value) > 1) input.value = Number(input.value) - 1; });
	});

	// POS: delegated add/minus – push/pop posCartLines so cart reflects variant lines
	$(document).on("click", ".pos-item-card .add-btn", function (e) {
		e.preventDefault();
		var $card = $(this).closest(".pos-item-card");
		var itemId = $card.attr("data-item-id");
		var name = $card.attr("data-name") || "";
		var price = parseFloat($card.attr("data-price")) || 0;
		if (!itemId) return;
		window.posCartLines = window.posCartLines || [];
		window.posCartLines.push({ item_id: itemId, name: name, price: price, quantity: 1, variation_name: null, image: $card.attr("data-image") || "" });
		if (typeof window.updatePosCart === "function") window.updatePosCart();
	});
	$(document).on("click", ".pos-item-card .minus-btn", function (e) {
		e.preventDefault();
		var $card = $(this).closest(".pos-item-card");
		var itemId = $card.attr("data-item-id");
		if (!itemId) return;
		window.posCartLines = window.posCartLines || [];
		for (var i = window.posCartLines.length - 1; i >= 0; i--) {
			if (String(window.posCartLines[i].item_id) === String(itemId)) {
				window.posCartLines.splice(i, 1);
				break;
			}
		}
		if (typeof window.updatePosCart === "function") window.updatePosCart();
	});

	// POS: dynamic cart and place order (only on POS page)
	if ($("#pos-place-order-btn").length) {
		window.posCartLines = window.posCartLines || [];
		if (window.posEditOrder) {
			var o = window.posEditOrder;
			window.posCartLines = (o.items || []).map(function (it) {
				return { item_id: it.item_id, name: it.item_name, price: parseFloat(it.unit_price) || 0, quantity: parseInt(it.quantity, 10) || 1, variation_name: null, image: "" };
			});
			$("#pos-order-type").val(o.order_type || "dine_in");
			$("#pos-form-table-id").val(o.restaurant_table_id || "");
			$("#pos-form-customer-name").val(o.customer_name || "");
			var tabId = o.order_type === "takeaway" ? "order-tab2" : (o.order_type === "delivery" ? "order-tab3" : "order-tab1");
			var tabEl = document.querySelector('a[data-bs-target="#' + tabId + '"]');
			if (tabEl && typeof bootstrap !== "undefined") {
				var tab = bootstrap.Tab.getOrCreateInstance(tabEl);
				tab.show();
			}
			setTimeout(function () {
				$("#pos-table-dinein, #pos-table-tab4").val(o.restaurant_table_id || "");
				$(".tab-pane.active .pos-customer-name").val(o.customer_name || "");
			}, 150);
		}
		window.updatePosCart = function () {
			const cart = window.posCartLines || [];
			const totalMenus = cart.reduce(function (s, r) { return s + (r.quantity || 1); }, 0);
			const subtotal = cart.reduce(function (s, r) { return s + (r.price || 0) * (r.quantity || 1); }, 0);
			const taxRate = 0.18;
			const tax = subtotal * taxRate;
			const total = subtotal + tax;
			const $empty = $("#pos-cart-empty");
			const $container = $("#pos-cart-items");
			const $summary = $("#pos-cart-summary");
			$("#pos-total-menus").text(totalMenus);
			var cur = (typeof window.currencySymbol !== "undefined" ? window.currencySymbol : "₹");
			$("#pos-subtotal").text(cur + subtotal.toFixed(2));
			if ($("#pos-tax").length) $("#pos-tax").text(cur + tax.toFixed(2));
			$("#pos-amount-paid").text(cur + total.toFixed(2));
			document.querySelectorAll(".pos-item-card").forEach(function (card) {
				const itemId = card.getAttribute("data-item-id");
				const sum = (window.posCartLines || []).reduce(function (s, r) {
					return s + (String(r.item_id) === String(itemId) ? (r.quantity || 1) : 0);
				}, 0);
				var inp = card.querySelector(".quantity-input");
				if (inp) inp.value = sum;
			});
			if (cart.length === 0) {
				$empty.show();
				$summary.addClass("d-none");
				$container.find(".menu-item.pos-cart-line").remove();
			} else {
				$empty.hide();
				$summary.removeClass("d-none");
				$container.find(".menu-item.pos-cart-line").remove();
				cart.forEach(function (row, idx) {
					const qty = row.quantity || 1;
					const unitPrice = row.price || 0;
					const amount = unitPrice * qty;
					const lineTotal = amount.toFixed(2);
					const nameSafe = (row.name || "").replace(/</g, "&lt;").replace(/"/g, "&quot;");
					const badge = (row.variation_name || "Default").replace(/</g, "&lt;");
					const imgSrc = (row.image || "").replace(/^about:blank$/i, "");
					const collapseId = "pos-cart-collapse-" + idx;
					const isFirst = idx === 0;
					const itemHtml =
						'<div class="menu-item pos-cart-line ' + (isFirst ? "active " : "") + 'p-2 rounded border shadow mb-3" data-cart-index="' + idx + '">' +
						'<div class="d-flex align-items-center justify-content-between flex-wrap flex-xl-nowrap gap-2">' +
						'<a href="#" class="d-flex align-items-center overflow-hidden pos-cart-toggle" data-bs-toggle="collapse" data-bs-target="#' + collapseId + '">' +
						'<div class="avatar avatar-lg flex-shrink-0 me-2">' +
						(imgSrc ? '<img src="' + imgSrc.replace(/"/g, "&quot;") + '" alt="food-img" class="img-fluid rounded">' : '<div class="bg-light rounded w-100 h-100 d-flex align-items-center justify-content-center"><i class="icon-layout-list text-muted"></i></div>') +
						'</div>' +
						'<div class="overflow-hidden">' +
						'<h6 class="mb-1 fs-14 fw-semibold text-truncate">' + nameSafe + '</h6>' +
						'<p class="badge badge-md bg-light text-dark mb-0">' + badge + '</p>' +
						'</div></a>' +
						'<div class="d-flex align-items-center gap-2 flex-shrink-0">' +
						'<div class="quantity-control pos-cart-qty">' +
						'<button type="button" class="minus-btn pos-cart-minus"><i class="icon-minus"></i></button>' +
						'<input type="text" class="quantity-input pos-cart-qty-input" value="' + qty + '" aria-label="Quantity" data-cart-index="' + idx + '">' +
						'<button type="button" class="add-btn pos-cart-plus"><i class="icon-plus"></i></button>' +
						'</div>' +
						'<a href="#" class="btn btn-xs fs-12 py-1 px-2 btn-white pos-cart-note" data-bs-toggle="modal" data-bs-target="#add_notes" data-cart-index="' + idx + '">Add Note</a>' +
						'<a href="#" class="btn btn-xs btn-icon fs-12 btn-light close-icon rounded-circle pos-cart-remove" data-cart-index="' + idx + '" aria-label="Remove"><i class="icon-x"></i></a>' +
						'</div></div>' +
						'<div id="' + collapseId + '" class="collapse' + (isFirst ? " show" : "") + '">' +
						'<div class="pt-2 mt-2 border-top">' +
						'<div class="d-flex align-items-center justify-content-between">' +
						'<div class="text-center"><span class="fs-12 mb-1 d-block fw-medium text-dark">Item Rate</span><p class="mb-0 fs-14 fw-normal">' + (window.currencySymbol || "₹") + unitPrice.toFixed(2) + '</p></div>' +
						'<div class="text-center"><span class="fs-12 mb-1 d-block fw-medium text-dark">Amount</span><p class="mb-0 fs-14 fw-normal">' + (window.currencySymbol || "₹") + amount.toFixed(2) + '</p></div>' +
						'<div class="text-center"><span class="fs-12 mb-1 d-block fw-medium text-dark">Total</span><p class="mb-0 fs-14 fw-semibold text-dark">' + (window.currencySymbol || "₹") + lineTotal + '</p></div>' +
						'</div></div></div></div>';
					$container.append(itemHtml);
				});
			}
		};

		// POS cart: quantity +/- and remove (delegated)
		$(document).on("click", "#pos-cart-items .pos-cart-minus", function () {
			var idx = parseInt($(this).closest(".pos-cart-line").attr("data-cart-index"), 10);
			if (isNaN(idx) || !window.posCartLines || !window.posCartLines[idx]) return;
			var q = (window.posCartLines[idx].quantity || 1) - 1;
			if (q < 1) {
				window.posCartLines.splice(idx, 1);
			} else {
				window.posCartLines[idx].quantity = q;
			}
			if (typeof window.updatePosCart === "function") window.updatePosCart();
		});
		$(document).on("click", "#pos-cart-items .pos-cart-plus", function () {
			var idx = parseInt($(this).closest(".pos-cart-line").attr("data-cart-index"), 10);
			if (isNaN(idx) || !window.posCartLines || !window.posCartLines[idx]) return;
			window.posCartLines[idx].quantity = (window.posCartLines[idx].quantity || 1) + 1;
			if (typeof window.updatePosCart === "function") window.updatePosCart();
		});
		$(document).on("change", "#pos-cart-items .pos-cart-qty-input", function () {
			var idx = parseInt($(this).attr("data-cart-index"), 10);
			if (isNaN(idx) || !window.posCartLines || !window.posCartLines[idx]) return;
			var q = parseInt($(this).val(), 10) || 1;
			if (q < 1) {
				window.posCartLines.splice(idx, 1);
			} else {
				window.posCartLines[idx].quantity = q;
			}
			if (typeof window.updatePosCart === "function") window.updatePosCart();
		});
		$(document).on("click", "#pos-cart-items .pos-cart-remove", function (e) {
			e.preventDefault();
			var idx = parseInt($(this).attr("data-cart-index"), 10);
			if (isNaN(idx) || !window.posCartLines) return;
			window.posCartLines.splice(idx, 1);
			if (typeof window.updatePosCart === "function") window.updatePosCart();
		});

		// POS: Veg / Non Veg / Egg filter + search – show card only when type in checked set (or all if none checked) AND name matches search
		function applyPosMenuVisibility() {
			const checked = [];
			$(".pos-filter-check:checked").each(function () { checked.push($(this).val()); });
			const showAllTypes = checked.length === 0;
			const searchQ = ($(".pos-search-menu").val() || "").trim().toLowerCase();
			$(".pos-item-card").each(function () {
				const type = ($(this).attr("data-food-type") || "veg").toLowerCase();
				const typeOk = showAllTypes || checked.indexOf(type) !== -1;
				const name = ($(this).attr("data-name") || "").toLowerCase();
				const searchOk = !searchQ || name.indexOf(searchQ) !== -1;
				$(this).closest(".col-xl-3, .col-md-4, .col-sm-6").toggle(typeOk && searchOk);
			});
		}
		$(document).on("change", ".pos-filter-check", applyPosMenuVisibility);
		$(document).on("input", ".pos-search-menu", applyPosMenuVisibility);
		applyPosMenuVisibility();

		window.updatePosCart();

		$("#pos-place-order-btn").on("click", function () {
			const cart = window.posCartLines || [];
			if (cart.length === 0) {
				alert("Please add at least one item to the order.");
				return;
			}
			const $tab1 = $("#order-tab1");
			const $tab2 = $("#order-tab2");
			const $tab3 = $("#order-tab3");
			const $tab4 = $("#order-tab4");
			let orderType = "dine_in";
			let tableId = "";
			let customerName = "";
			if ($tab1.hasClass("active") && $tab1.hasClass("show")) {
				orderType = "dine_in";
				tableId = $("#pos-table-dinein").val() || "";
				customerName = $tab1.find(".pos-customer-name").val() || "";
				if (!tableId) {
					alert("Please select a table for Dine In.");
					return;
				}
			} else if ($tab2.hasClass("active") && $tab2.hasClass("show")) {
				orderType = "takeaway";
				customerName = $tab2.find(".pos-customer-name").val() || "";
			} else if ($tab3.hasClass("active") && $tab3.hasClass("show")) {
				orderType = "delivery";
				customerName = $tab3.find(".pos-customer-name").val() || "";
			} else if ($tab4.hasClass("active") && $tab4.hasClass("show")) {
				orderType = "dine_in";
				tableId = $("#pos-table-tab4").val() || "";
				customerName = $tab4.find(".pos-customer-name").val() || "";
				if (!tableId) {
					alert("Please select a table.");
					return;
				}
			}
			const $form = $("#pos-order-form");
			$form.find("#pos-order-type").val(orderType);
			$form.find("#pos-form-table-id").val(tableId);
			$form.find("#pos-form-customer-name").val(customerName);
			$form.find("[name^='items']").remove();
			cart.forEach(function (row, i) {
				$form.append('<input type="hidden" name="items[' + i + '][item_id]" value="' + row.item_id + '">');
				$form.append('<input type="hidden" name="items[' + i + '][quantity]" value="' + (row.quantity || 1) + '">');
				if (row.price != null) $form.append('<input type="hidden" name="items[' + i + '][unit_price]" value="' + parseFloat(row.price).toFixed(2) + '">');
			});
			$form.off("submit").submit();
		});

		// Plus button: show Add new customer form (theme style)
		$(document).on("click", ".pos-add-customer-trigger", function () {
			$("#pos-new-customer-form").removeClass("d-none");
			$("#pos-new-customer-msg").text("");
		});

		// Customer dropdown: sync hidden .pos-customer-name when selection changes
		$(document).on("change", ".pos-customer-select", function () {
			var $sel = $(this);
			var tabId = $sel.data("tab");
			var $tab = $("#" + tabId);
			var $nameInput = $tab.find(".pos-customer-name");
			$("#pos-new-customer-form").addClass("d-none");
			var opt = $sel.find("option:selected");
			$nameInput.val(opt.data("name") || "");
		});

		// Add new customer (AJAX) and add to all dropdowns
		$("#pos-add-customer-btn").on("click", function () {
			var name = $("#pos-new-customer-name").val().trim();
			if (!name) {
				$("#pos-new-customer-msg").text("Name is required.").addClass("text-danger");
				return;
			}
			$("#pos-new-customer-msg").text("Saving...").removeClass("text-danger text-success");
			var $btn = $(this).prop("disabled", true);
			var customerStoreUrl = $("#pos-right").data("customer-store-url") || "/customer";
			$.ajax({
				url: customerStoreUrl,
				method: "POST",
				data: {
					_token: $("meta[name=csrf-token]").attr("content") || $("#pos-order-form input[name=_token]").val(),
					name: name,
					phone: $("#pos-new-customer-phone").val().trim(),
					email: $("#pos-new-customer-email").val().trim()
				},
				headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
			}).done(function (res) {
				if (res.customer) {
					var opt = '<option value="' + res.customer.id + '" data-name="' + (res.customer.name || "").replace(/"/g, "&quot;") + '">' + (res.customer.name || "").replace(/</g, "&lt;") + '</option>';
					$(".pos-customer-select").each(function () {
						var $s = $(this);
						if ($s.find('option[value="__new__"]').length) {
							$s.find('option[value="__new__"]').before(opt);
						} else {
							$s.append(opt);
						}
					});
					var $activeTab = $(".tab-pane.active.show").first();
					$activeTab.find(".pos-customer-select").val(res.customer.id);
					$activeTab.find(".pos-customer-name").val(res.customer.name || "");
					$("#pos-new-customer-form").addClass("d-none");
					$("#pos-new-customer-name, #pos-new-customer-phone, #pos-new-customer-email").val("");
					$("#pos-new-customer-msg").text("Customer added.").addClass("text-success").removeClass("text-danger");
				}
			}).fail(function (xhr) {
				var msg = (xhr.responseJSON && xhr.responseJSON.message) || (xhr.responseJSON && xhr.responseJSON.errors && JSON.stringify(xhr.responseJSON.errors)) || "Could not add customer.";
				$("#pos-new-customer-msg").text(msg).addClass("text-danger").removeClass("text-success");
			}).always(function () {
				$btn.prop("disabled", false);
			});
		});

		$("#pos-cancel-new-customer").on("click", function () {
			$("#pos-new-customer-form").addClass("d-none");
			$(".pos-customer-select").val("");
			$(".pos-customer-name").val("");
			$("#pos-new-customer-msg").text("");
		});

		// Product card click: open details modal (first ask) – do not open when clicking quantity controls
		$(document).on("click", ".pos-item-click", function (e) {
			if ($(e.target).closest(".quantity-control").length) return;
			var $card = $(this).closest(".pos-item-card");
			if (!$card.length) return;
			var id = $card.data("item-id");
			var name = $card.data("name");
			var price = parseFloat($card.attr("data-price")) || 0;
			var desc = $card.data("description") || "";
			var img = $card.data("image") || "";
			var addons = [];
			var variations = [];
			try {
				var addonsStr = $card.attr("data-addons");
				if (addonsStr) addons = JSON.parse(addonsStr.replace(/&quot;/g, '"'));
				if (!Array.isArray(addons)) addons = [];
			} catch (x) { addons = []; }
			try {
				var variationsStr = $card.attr("data-variations");
				if (variationsStr) variations = JSON.parse(variationsStr.replace(/&quot;/g, '"'));
				if (!Array.isArray(variations)) variations = [];
			} catch (x) { variations = []; }
			$("#pos-modal-title").text(name || "Item");
			$("#pos-modal-description").text(desc || "No description.");
			if (img) {
				$("#pos-modal-image").attr("src", img).removeClass("d-none");
				$("#pos-modal-image-placeholder").addClass("d-none");
			} else {
				$("#pos-modal-image").addClass("d-none");
				$("#pos-modal-image-placeholder").removeClass("d-none");
			}
			var $sizesWrap = $("#pos-modal-sizes-wrap");
			var $sizesEl = $("#pos-modal-sizes");
			$sizesEl.empty();
			var basePrice = price;
			var selectedPrice = basePrice;
			var selectedVariationName = "";
			if (variations && variations.length > 0) {
				$(variations).each(function (i) {
					var v = this;
					var vPrice = parseFloat(v.price) || 0;
					var vName = (v.name || "Option " + (i + 1)).replace(/</g, "&lt;");
					var isFirst = i === 0;
					if (isFirst) selectedPrice = vPrice; selectedVariationName = v.name || "";
					$sizesEl.append('<div class="size-tab"><button type="button" class="tag d-flex align-items-center gap-2 pos-size-tag' + (isFirst ? " active" : "") + '" data-price="' + vPrice + '" data-name="' + (v.name || "") + '">' + vName + ' <span class="pos-modal-price-val">' + (window.currencySymbol || "₹") + vPrice.toFixed(2) + '</span></button></div>');
				});
			} else {
				$sizesEl.append('<div class="size-tab"><button type="button" class="tag d-flex align-items-center gap-2 pos-size-tag active" data-price="' + basePrice + '" data-name="">Default <span class="pos-modal-price-val">' + (window.currencySymbol || "₹") + basePrice.toFixed(2) + '</span></button></div>');
				selectedPrice = basePrice;
			}
			$sizesWrap.show();
			var $addonsEl = $("#pos-modal-addons");
			$addonsEl.empty();
			if (addons && addons.length > 0) {
				$(addons).each(function () {
					var a = this;
					var p = parseFloat(a.price) || 0;
					var label = (a.addon_name || a.name || "").replace(/</g, "&lt;").replace(/"/g, "&quot;");
					$addonsEl.append('<div class="col-6"><div class="border p-2 rounded pos-addon-item" data-price="' + p + '" data-name="' + label + '" role="button" tabindex="0"><div class="d-flex align-items-center gap-2"><div class="avatar rounded-circle border bg-light flex-shrink-0" style="width:40px;height:40px;"></div><p class="fw-medium mb-0 small">' + (a.addon_name || a.name || "").replace(/</g, "&lt;") + '</p><span class="ms-auto fw-medium">' + (window.currencySymbol || "₹") + p.toFixed(2) + '</span></div></div></div>');
				});
			} else {
				$addonsEl.html('<p class="small text-muted mb-0">No add-ons</p>');
			}
			$("#pos-modal-qty").val(1);
			$("#pos-item-modal").data("pos-item-id", id);
			$("#pos-item-modal").data("pos-item-name", name);
			$("#pos-item-modal").data("pos-item-price", selectedPrice);
			$("#pos-item-modal").data("pos-item-base-price", basePrice);
			$("#pos-item-modal").data("pos-item-variation-name", selectedVariationName);
			posModalUpdateTotal();
			new bootstrap.Modal(document.getElementById("pos-item-modal")).show();
		});

		// Modal: size selection (from variations)
		$(document).on("click", "#pos-item-modal .pos-size-tag", function () {
			$(this).closest(".size-group").find(".pos-size-tag").removeClass("active");
			$(this).addClass("active");
			var p = parseFloat($(this).data("price")) || 0;
			var name = $(this).data("name") || "";
			$("#pos-item-modal").data("pos-item-price", p);
			$("#pos-item-modal").data("pos-item-variation-name", name);
			posModalUpdateTotal();
		});

		// Modal: addon toggle (add price to total when selected)
		$(document).on("click", "#pos-item-modal .pos-addon-item", function () {
			$(this).toggleClass("active");
			posModalUpdateTotal();
		});
		$(document).on("keydown", "#pos-item-modal .pos-addon-item", function (e) {
			if (e.key === "Enter" || e.key === " ") {
				e.preventDefault();
				$(this).toggleClass("active");
				posModalUpdateTotal();
			}
		});

		function posModalUpdateTotal() {
			var basePrice = parseFloat($("#pos-item-modal").data("pos-item-price")) || 0;
			var qty = parseInt($("#pos-modal-qty").val(), 10) || 1;
			var addonTotal = 0;
			$("#pos-modal-addons .pos-addon-item.active").each(function () {
				addonTotal += parseFloat($(this).attr("data-price")) || 0;
			});
			var lineTotal = (basePrice + addonTotal) * qty;
			$("#pos-modal-total").text((window.currencySymbol || "₹") + lineTotal.toFixed(2));
		}
		$("#pos-modal-qty-plus").on("click", function () {
			var $in = $("#pos-modal-qty");
			$in.val(Math.max(0, (parseInt($in.val(), 10) || 0) + 1));
			posModalUpdateTotal();
		});
		$("#pos-modal-qty-minus").on("click", function () {
			var $in = $("#pos-modal-qty");
			if ((parseInt($in.val(), 10) || 0) > 1) $in.val((parseInt($in.val(), 10) || 0) - 1);
			posModalUpdateTotal();
		});
		$("#pos-modal-qty").on("change", posModalUpdateTotal);

		// Modal: Add to Cart (push to posCartLines so variant/addon price is kept)
		$("#pos-modal-add-to-cart").on("click", function () {
			var itemId = $("#pos-item-modal").data("pos-item-id");
			var name = $("#pos-item-modal").data("pos-item-name") || "";
			var price = parseFloat($("#pos-item-modal").data("pos-item-price")) || 0;
			var variationName = $("#pos-item-modal").data("pos-item-variation-name") || "";
			var qty = parseInt($("#pos-modal-qty").val(), 10) || 1;
			if (!itemId || qty < 1) return;
			var addonTotal = 0;
			$("#pos-modal-addons .pos-addon-item.active").each(function () {
				addonTotal += (parseFloat($(this).attr("data-price")) || 0) * qty;
			});
			var unitPrice = price + (addonTotal / qty);
			var imgSrc = ($("#pos-modal-image").attr("src") || "").replace(/^about:blank$/i, "");
			window.posCartLines = window.posCartLines || [];
			for (var i = 0; i < qty; i++) {
				window.posCartLines.push({
					item_id: itemId,
					name: name,
					price: unitPrice,
					quantity: 1,
					variation_name: variationName || null,
					image: imgSrc
				});
			}
			if (typeof window.updatePosCart === "function") window.updatePosCart();
			bootstrap.Modal.getInstance(document.getElementById("pos-item-modal")).hide();
		});

		// POS: Print Receipt – fill modal from cart when shown
		$("#pos-print-receipt").on("shown.bs.modal", function () {
			var cart = window.posCartLines || [];
			var $body = $(".pos-print-receipt-body");
			if (!cart.length) {
				$body.html("<p class=\"text-muted small mb-0\">Cart is empty. Add items to print receipt.</p>");
				$("#pos-print-receipt-btn").prop("disabled", true);
				return;
			}
			var subtotal = 0;
			var totalQty = 0;
			var itemLines = [];
			cart.forEach(function (row) {
				var qty = row.quantity || 1;
				totalQty += qty;
				var price = parseFloat(row.price) || 0;
				var amount = price * qty;
				subtotal += amount;
				var name = (row.name || "Item").replace(/</g, "&lt;");
				itemLines.push("<div class=\"pos-receipt-item d-flex align-items-center justify-content-between py-1\"><span class=\"text-truncate me-2\">" + name + " &times;" + qty + "</span><span class=\"fw-medium text-dark flex-shrink-0\">" + (window.currencySymbol || "₹") + amount.toFixed(2) + "</span></div>");
			});
			var taxRate = 0.18;
			var tax = subtotal * taxRate;
			var total = subtotal + tax;
			var now = new Date();
			var dateStr = now.getDate() + "/" + (now.getMonth() + 1) + "/" + now.getFullYear();
			var timeStr = now.getHours() % 12 || 12;
			var min = now.getMinutes();
			timeStr += ":" + (min < 10 ? "0" : "") + min + " " + (now.getHours() >= 12 ? "PM" : "AM");
			var tokenNo = String(now.getTime()).slice(-3);
			var orderType = $("#pos-order-type").val() || "dine_in";
			var $activeTab = $(".pos-tab .nav-link.active");
			if ($activeTab.length) {
				var tabText = ($activeTab.text() || "").trim().toLowerCase();
				if (tabText.indexOf("take away") !== -1) orderType = "takeaway";
				else if (tabText.indexOf("delivery") !== -1) orderType = "delivery";
				else if (tabText.indexOf("dine") !== -1 || tabText.indexOf("table") !== -1) orderType = tabText.indexOf("table") !== -1 ? "table" : "dine_in";
			}
			orderType = orderType.replace(/_/g, " ");
			var tableName = "";
			var $tableSel = $(".tab-pane.active .pos-table-select").filter(function() { return $(this).val(); });
			if ($tableSel.length) tableName = $tableSel.find("option:selected").text().trim();
			var orderTypeLabel = orderType.charAt(0).toUpperCase() + orderType.slice(1);
			if (tableName) orderTypeLabel += " (Table " + tableName + ")";
			var $meta = $("#pos-receipt-meta");
			var brandLogo = $meta.data("logo") || "";
			var brandName = $meta.data("restaurant") || "";
			var receiptHeader = "<div class=\"pos-receipt-header mb-3 pb-3 border-bottom\">" +
				(brandLogo ? "<div class=\"pos-receipt-logo\"><img src=\"" + brandLogo + "\" alt=\"\" /></div>" : "") +
				(brandName ? "<div class=\"pos-receipt-brand\">" + String(brandName).replace(/</g, '&lt;') + "</div>" : "") +
				"</div>";
			var orderInfo = "<div class=\"mb-3 pb-3 border-bottom\"><h5 class=\"mb-3 fs-16\">Order Info</h5>" +
				"<div class=\"fs-14 fw-normal d-flex align-items-center justify-content-between mb-2\">Date &amp; Time <span class=\"fw-medium text-dark\">" + dateStr + " - " + timeStr + "</span></div>" +
				"<div class=\"fs-14 fw-normal d-flex align-items-center justify-content-between mb-2\">Order No <span class=\"fw-medium text-dark\">—</span></div>" +
				"<div class=\"fs-14 fw-normal d-flex align-items-center justify-content-between mb-2\">Token No <span class=\"fw-medium text-dark\">" + tokenNo + "</span></div>" +
				"<div class=\"fs-14 fw-normal d-flex align-items-center justify-content-between mb-2\">No of Items <span class=\"fw-medium text-dark\">" + totalQty + "</span></div>" +
				"<div class=\"fs-14 fw-normal d-flex align-items-center justify-content-between mb-0\">Order Type <span class=\"fw-medium text-dark\">" + orderTypeLabel + "</span></div></div>";
			var menusHtml = "<div class=\"mb-3 pb-3 border-bottom pos-receipt-menus\"><h5 class=\"mb-2 fs-16\">Ordered Menus</h5><div class=\"pos-receipt-item-list\">" + itemLines.join("") + "</div></div>";
			var curR = window.currencySymbol || "₹";
			var totalsHtml = "<div class=\"mb-3 pb-3 border-bottom\"><div class=\"fs-14 fw-normal d-flex align-items-center justify-content-between mb-2\">Sub Total <span class=\"fw-medium text-dark\">" + curR + subtotal.toFixed(2) + "</span></div>" +
				"<div class=\"fs-14 fw-normal d-flex align-items-center justify-content-between mb-2\">Tax (18%) <span class=\"fw-medium text-dark\">" + curR + tax.toFixed(2) + "</span></div>" +
				"<div class=\"fs-14 fw-normal d-flex align-items-center justify-content-between mb-0\">Service Charge <span class=\"fw-medium text-dark\">" + curR + "0.00</span></div></div>" +
				"<h5 class=\"mb-0 d-flex align-items-center justify-content-between\">Total <span>" + curR + total.toFixed(2) + "</span></h5>";
			$body.html("<div id=\"pos-receipt-content\">" + receiptHeader + orderInfo + menusHtml + totalsHtml + "</div>");
			$("#pos-print-receipt-btn").prop("disabled", false);
		});
		$("#pos-print-receipt-btn").on("click", function () {
			var cart = window.posCartLines || [];
			if (!cart.length) return;
			var $content = $("#pos-receipt-content");
			if (!$content.length) return;

			// 1) Enqueue a server print job (Android bridge will fetch and print via Bluetooth ESC/POS)
			try {
				var csrfEl = document.querySelector('meta[name=\"csrf-token\"]');
				var csrf = (csrfEl && csrfEl.getAttribute('content')) ? csrfEl.getAttribute('content') : '';
				var payload = {
					type: "receipt",
					generated_at: new Date().toISOString(),
					html_preview: $content.get(0).outerHTML
				};
				fetch("/print-jobs/enqueue", {
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						"X-CSRF-TOKEN": csrf
					},
					body: JSON.stringify({ type: "receipt", payload: payload })
				}).catch(function () { /* ignore */ });
			} catch (e) { /* ignore */ }

			// 2) Fallback: browser print (thermal width) so it still works without Android bridge
			var printCss =
				"@page{size:58mm auto;margin:0;} " +
				"html,body{width:58mm;margin:0;padding:0;} " +
				"body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;padding:6mm 4mm 6mm;font-size:12px;line-height:1.3;color:#111;} " +
				".pos-receipt-header{text-align:center;} " +
				".pos-receipt-logo img{max-width:26mm;max-height:26mm;display:inline-block;object-fit:contain;} " +
				".pos-receipt-brand{font-weight:700;font-size:12px;margin-top:2mm;} " +
				"h5{font-size:13px;margin:0 0 6px;} .fs-16{font-size:13px;} .fs-14{font-size:12px;} " +
				".border-bottom{border-bottom:1px dashed #9ca3af;} " +
				".d-flex{display:flex;} .justify-content-between{justify-content:space-between;} .fw-medium{font-weight:600;} .text-dark{color:#111;} " +
				".mb-0{margin-bottom:0;} .mb-2{margin-bottom:5px;} .mb-3{margin-bottom:8px;} .pb-3{padding-bottom:8px;} " +
				".pos-receipt-item-list .pos-receipt-item{font-size:11px;line-height:1.25;padding:2px 0;} " +
				".pos-receipt-item-list .pos-receipt-item span:first-child{max-width:66%;} " +
				".text-truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}";
			var clone = $content.clone().get(0);
			var w = window.open("", "_blank", "width=320,height=600");
			w.document.write("<!DOCTYPE html><html><head><title>Receipt</title><style>" + printCss + "</style></head><body>" + clone.outerHTML + "</body></html>");
			w.document.close();
			w.focus();
			setTimeout(function () { w.print(); w.close(); }, 250);
		});

		// POS: Cancel – clear cart and close modal
		$("#pos-cancel-cart-btn").on("click", function () {
			window.posCartLines = [];
			if (typeof window.updatePosCart === "function") window.updatePosCart();
			bootstrap.Modal.getInstance(document.getElementById("pos-cancel-modal")).hide();
		});

		// POS: Void – clear cart and close modal
		$("#pos-void-cart-btn").on("click", function () {
			window.posCartLines = [];
			if (typeof window.updatePosCart === "function") window.updatePosCart();
			bootstrap.Modal.getInstance(document.getElementById("pos-void-modal")).hide();
		});
	}

	// Category Slider
	$('.category-slider').each(function () {
		const $slider = $(this);
		if (!$slider.hasClass('slick-initialized')) {
			$slider.slick({
				dots: false,
				infinite: true,
				speed: 2000,
				slidesToShow: 5,
				slidesToScroll: 1,
				autoplay: false,
				arrows: true,
				prevArrow: $('.category-prev'),
				nextArrow: $('.category-next'),
				responsive: [
					{
						breakpoint: 1400,
						settings: { slidesToShow: 4, slidesToScroll: 1 }
					},
					{
						breakpoint: 1200,
						settings: { slidesToShow: 3, slidesToScroll: 1 }
					},
					{
						breakpoint: 992,
						settings: { slidesToShow: 3, slidesToScroll: 1 }
					},
					{
						breakpoint: 768,
						settings: { slidesToShow: 2, slidesToScroll: 1 }
					},
					{
						breakpoint: 576,
						settings: { slidesToShow: 1, slidesToScroll: 1 }
					}
				]
			});
		}
	});

	// Upgrade Slider
	$('.upgrade-slider').each(function () {
		const $slider = $(this);
		if (!$slider.hasClass('slick-initialized')) {
			$slider.slick({
				dots: false,
				infinite: true,
				speed: 2000,
				slidesToShow: 2,
				slidesToScroll: 1,
				autoplay: false,
				arrows: true,
				prevArrow: $('.upgrade-prev'),
				nextArrow: $('.upgrade-next'),
				responsive: [
					{
						breakpoint: 1400,
						settings: { slidesToShow: 2, slidesToScroll: 1 }
					},
					{
						breakpoint: 1200,
						settings: { slidesToShow: 2, slidesToScroll: 1 }
					},
					{
						breakpoint: 992,
						settings: { slidesToShow: 2, slidesToScroll: 1 }
					},
					{
						breakpoint: 768,
						settings: { slidesToShow: 2, slidesToScroll: 1 }
					},
					{
						breakpoint: 576,
						settings: { slidesToShow: 1, slidesToScroll: 1 }
					}
				]
			});
		}
	});

	// Size Tab
	document.addEventListener("click", function(e) {
		const btn = e.target.closest(".size-tab .tag");
		if (!btn) return;
	
		const parent = btn.closest(".size-group");
	
		parent.querySelectorAll(".size-tab").forEach(tab => tab.classList.remove("active"));
	
		btn.closest(".size-tab").classList.add("active");
	});

	// All Sliders
	if ($('.slider-wrapper').length > 0) {
		function initSliders() {
			$('.all-slider, .dinein-slider, .delivery-slider, .takeaway-slider, .table-slider').each(function () {
				const $slider = $(this);
				if (!$slider.hasClass('slick-initialized')) {
					$slider.slick({
						dots: false,
						infinite: true,
						speed: 2000,
						slidesToShow: 3,
						slidesToScroll: 1,
						autoplay: false,
						arrows: false, // disable default arrows
						responsive: [
							{ breakpoint: 1400, settings: { slidesToShow: 3 } },
							{ breakpoint: 1200, settings: { slidesToShow: 2 } },
							{ breakpoint: 992, settings: { slidesToShow: 2 } },
							{ breakpoint: 768, settings: { slidesToShow: 1 } }
						]
					});
				}
			});
		}

		initSliders();

		// Global arrow buttons control the currently active tab’s slider
		$('.all-prev').on('click', function () {
			$('.slider-wrapper .tab-pane.active .slick-slider').slick('slickPrev');
		});

		$('.all-next').on('click', function () {
			$('.slider-wrapper .tab-pane.active .slick-slider').slick('slickNext');
		});

		// Fix Slick when switching tabs
		$('.slider-wrapper a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
			const target = $($(this).attr('data-bs-target'));
			setTimeout(() => {
				target.find('.slick-slider').slick('setPosition');
			}, 100);
		});
	}

	// Accordion Active
	if ($('.menu-item').length > 0) {
	  	$(".menu-item .collapse").on("shown.bs.collapse", function() {
			var $menuItem = $(this).closest(".menu-item");

			// Remove active from all other menu-items
			$(".menu-item.active").not($menuItem).removeClass("active");

			// Add active to this menu-item
			$menuItem.addClass("active");
		});

		// Handle collapse hidden
		$(".menu-item .collapse").on("hidden.bs.collapse", function() {
			var $menuItem = $(this).closest(".menu-item");
			$menuItem.removeClass("active");
		});

		// Optional: mark active for initially open collapse
		$(".menu-item .collapse.show").each(function() {
			$(this).closest(".menu-item").addClass("active");
		});
	}

	// Modal
	if ($('.modal .slick-slider').length > 0) {
		$('.modal').on('shown.bs.modal', function () {
			$('.slick-slider').slick('setPosition');
		});
	}

	// Addon Active (exclude POS modal – POS uses .pos-addon-item and its own handler)
	$(document).on("click", ".addon-item", function () {
		if ($(this).closest("#pos-item-modal").length) return;
		$(this).toggleClass("active");
	});

	if ($('#drag-container').length > 0) {
		const container = document.getElementById('drag-container');
		let draggingElement = null;

		container.addEventListener('dragstart', (e) => {
			draggingElement = e.target.closest('.drag-item');
			e.target.classList.add('dragging');
			// For Firefox support
			e.dataTransfer.setData('text/plain', ''); 
		});

		container.addEventListener('dragend', (e) => {
			e.target.classList.remove('dragging');
			draggingElement = null;
		});

		container.addEventListener('dragover', (e) => {
			e.preventDefault(); // Necessary to allow drop
			const afterElement = getDragAfterElement(container, e.clientY);
			if (afterElement == null) {
				container.appendChild(draggingElement);
			} else {
				container.insertBefore(draggingElement, afterElement);
			}
		});

		// Function to find the element immediately below the mouse cursor
		function getDragAfterElement(container, y) {
			const draggableElements = [...container.querySelectorAll('.drag-item:not(.dragging)')];

			return draggableElements.reduce((closest, child) => {
				const box = child.getBoundingClientRect();
				const offset = y - box.top - box.height / 2;
				if (offset < 0 && offset > closest.offset) {
					return { offset: offset, element: child };
				} else {
					return closest;
				}
			}, { offset: Number.NEGATIVE_INFINITY }).element;
		}
	}


});