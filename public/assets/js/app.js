document.addEventListener("click", function (event) {
    var toggle = event.target.closest("[data-password-toggle]");

    if (!toggle) {
        return;
    }

    var targetId = toggle.getAttribute("data-target");

    if (!targetId) {
        return;
    }

    var input = document.getElementById(targetId);

    if (!input) {
        return;
    }

    var isHidden = input.type === "password";

    input.type = isHidden ? "text" : "password";
    toggle.textContent = isHidden ? "Hide" : "Show";
    toggle.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
});

document.addEventListener("DOMContentLoaded", function () {
    var header = document.querySelector(".site-header");
    var toggle = document.querySelector("[data-nav-toggle]");
    var menu = document.querySelector("[data-nav-menu]");
    var mobileQuery = window.matchMedia("(max-width: 900px)");

    if (!header || !toggle || !menu) {
        return;
    }

    var setMenuState = function (isOpen) {
        header.classList.toggle("is-nav-open", isOpen);
        menu.classList.toggle("is-open", isOpen);
        menu.setAttribute("aria-hidden", mobileQuery.matches && !isOpen ? "true" : "false");
        toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        toggle.setAttribute("aria-label", isOpen ? "Close navigation menu" : "Open navigation menu");
    };

    toggle.addEventListener("click", function (event) {
        event.stopPropagation();
        setMenuState(toggle.getAttribute("aria-expanded") !== "true");
    });

    menu.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", function () {
            if (mobileQuery.matches) {
                setMenuState(false);
            }
        });
    });

    document.addEventListener("click", function (event) {
        if (!mobileQuery.matches || header.contains(event.target)) {
            return;
        }

        setMenuState(false);
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            setMenuState(false);
        }
    });

    var syncMenuState = function (query) {
        if (!query.matches) {
            setMenuState(false);
            return;
        }

        setMenuState(false);
    };

    setMenuState(false);

    if (typeof mobileQuery.addEventListener === "function") {
        mobileQuery.addEventListener("change", syncMenuState);
    } else if (typeof mobileQuery.addListener === "function") {
        mobileQuery.addListener(syncMenuState);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    var sidebarLinks = document.querySelectorAll("[data-dashboard-target]");
    var dashboardPanels = document.querySelectorAll("[data-dashboard-panel]");

    if (!sidebarLinks.length || !dashboardPanels.length) {
        return;
    }

    var switchDashboardPanel = function (target, syncUrl) {
        if (!target) {
            return;
        }

        var hasTarget = Array.prototype.some.call(sidebarLinks, function (link) {
            return link.getAttribute("data-dashboard-target") === target;
        });

        if (!hasTarget) {
            return;
        }

        sidebarLinks.forEach(function (link) {
            link.classList.toggle("is-active", link.getAttribute("data-dashboard-target") === target);
        });

        dashboardPanels.forEach(function (panel) {
            panel.classList.toggle("is-active", panel.getAttribute("data-dashboard-panel") === target);
        });

        if (syncUrl !== false && window.location.pathname.indexOf("/dashboard/") !== -1) {
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set("section", target);
            window.history.replaceState({}, "", currentUrl.toString());
        }
    };

    sidebarLinks.forEach(function (link) {
        link.addEventListener("click", function () {
            switchDashboardPanel(link.getAttribute("data-dashboard-target"), true);
        });
    });

    var currentUrl = new URL(window.location.href);
    var initialTarget = currentUrl.searchParams.get("section") || window.location.hash.replace(/^#/, "");

    if (initialTarget) {
        switchDashboardPanel(initialTarget, false);
    }

    var showDashboardAlert = function (type, message) {
        if (!message) {
            return;
        }

        var dashboardMain = document.querySelector(".dashboard-main");
        var activePanel = document.querySelector("[data-dashboard-panel].is-active");

        if (!dashboardMain || !activePanel) {
            return;
        }

        Array.prototype.forEach.call(dashboardMain.children, function (child) {
            if (child.classList.contains("alert")) {
                child.remove();
            }
        });

        var alert = document.createElement("div");
        alert.className = "alert dashboard-ajax-alert " + (type === "error" ? "alert-error" : "alert-success");
        alert.textContent = message;
        dashboardMain.insertBefore(alert, activePanel);
    };

    document.addEventListener("submit", function (event) {
        var form = event.target.closest("form[data-dashboard-form]");

        if (!form || !window.fetch || !window.FormData) {
            return;
        }

        event.preventDefault();

        var section = form.getAttribute("data-dashboard-form") || "overview";
        var submitButton = form.querySelector("button[type='submit']");
        var originalButtonText = submitButton ? submitButton.textContent : "";

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = "Saving...";
        }

        fetch(form.action, {
            method: "POST",
            body: new FormData(form),
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            credentials: "same-origin"
        }).then(function (response) {
            return response.json();
        }).then(function (payload) {
            if (payload && payload.section) {
                section = payload.section;
            }

            var panel = document.querySelector("[data-dashboard-panel='" + section + "']");

            if (panel && payload && typeof payload.panelHtml === "string" && payload.panelHtml !== "") {
                panel.innerHTML = payload.panelHtml;
                if (window.initializeDashboardFilters) {
                    window.initializeDashboardFilters(panel);
                }
            }

            if (payload && payload.csrfToken) {
                document.querySelectorAll("input[name='_token']").forEach(function (input) {
                    input.value = payload.csrfToken;
                });
            }

            showDashboardAlert(payload && payload.error ? "error" : "success", payload ? (payload.error || payload.status) : "");
            switchDashboardPanel(section, true);
        }).catch(function () {
            form.submit();
        }).finally(function () {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var normalize = function (value) {
        return String(value || "").trim().toLowerCase();
    };

    var applyFilter = function (bar) {
        var scope = bar.parentElement || document;
        var search = normalize((bar.querySelector("[data-filter-search]") || {}).value);
        var status = normalize((bar.querySelector("[data-filter-status]") || {}).value || "all");
        var flow = normalize((bar.querySelector("[data-filter-flow]") || {}).value || "all");
        var lock = normalize((bar.querySelector("[data-filter-lock]") || {}).value || "all");
        var items = scope.querySelectorAll("[data-filter-item]");
        var shown = 0;

        items.forEach(function (item) {
            var textMatches = !search || normalize(item.getAttribute("data-filter-text")).indexOf(search) !== -1;
            var statusMatches = status === "all" || normalize(item.getAttribute("data-filter-status")) === status;
            var flowMatches = flow === "all" || normalize(item.getAttribute("data-filter-flow")) === flow;
            var lockMatches = lock === "all" || normalize(item.getAttribute("data-filter-lock")) === lock;
            var visible = textMatches && statusMatches && flowMatches && lockMatches;

            item.hidden = !visible;

            if (visible) {
                shown += 1;
            }
        });

        var empty = scope.querySelector("[data-filter-empty]");

        if (empty) {
            empty.hidden = shown !== 0;
        }
    };

    window.initializeDashboardFilters = function (root) {
        var filterBars = (root || document).querySelectorAll("[data-dashboard-filter]");

        if (!filterBars.length) {
            return;
        }

        filterBars.forEach(function (bar) {
            if (bar.getAttribute("data-filter-ready") === "true") {
                applyFilter(bar);
                return;
            }

            bar.setAttribute("data-filter-ready", "true");

        bar.querySelectorAll("input, select").forEach(function (field) {
            field.addEventListener("input", function () {
                applyFilter(bar);
            });
            field.addEventListener("change", function () {
                applyFilter(bar);
            });
        });

        var reset = bar.querySelector("[data-filter-reset]");

        if (reset) {
            reset.addEventListener("click", function () {
                bar.querySelectorAll("input, select").forEach(function (field) {
                    if (field.tagName === "SELECT") {
                        field.value = "all";
                    } else {
                        field.value = "";
                    }
                });
                applyFilter(bar);
            });
        }

        applyFilter(bar);
        });
    };

    window.initializeDashboardFilters(document);
});

document.addEventListener("DOMContentLoaded", function () {
    var orderTypeInputs = document.querySelectorAll("[data-order-type-toggle]");

    if (!orderTypeInputs.length) {
        return;
    }

    var syncOrderTypeSections = function () {
        var active = document.querySelector("[data-order-type-toggle]:checked");
        var activeValue = active ? active.value : "togo";

        document.querySelectorAll("[data-order-type-section]").forEach(function (section) {
            var isActive = section.getAttribute("data-order-type-section") === activeValue;

            section.style.display = isActive ? "grid" : "none";

            section.querySelectorAll("input, select, textarea").forEach(function (field) {
                if (isActive) {
                    field.removeAttribute("disabled");
                } else {
                    field.setAttribute("disabled", "disabled");
                }
            });
        });
    };

    orderTypeInputs.forEach(function (input) {
        input.addEventListener("change", syncOrderTypeSections);
    });

    syncOrderTypeSections();
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-receipt-input]").forEach(function (input) {
        var wrapper = input.closest(".receipt-upload-area");

        if (!wrapper) {
            return;
        }

        var previewContainer = wrapper.querySelector("[data-receipt-preview]");
        var previewImage = wrapper.querySelector("[data-receipt-preview-image]");
        var removeButton = wrapper.querySelector("[data-receipt-remove]");

        if (!previewContainer || !previewImage || !removeButton) {
            return;
        }

        input.addEventListener("change", function () {
            var file = input.files && input.files[0] ? input.files[0] : null;

            if (!file) {
                previewContainer.hidden = true;
                previewImage.removeAttribute("src");
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert("Receipt image must be 5MB or smaller.");
                input.value = "";
                previewContainer.hidden = true;
                previewImage.removeAttribute("src");
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                previewImage.src = event.target && typeof event.target.result === "string" ? event.target.result : "";
                previewContainer.hidden = false;
            };
            reader.readAsDataURL(file);
        });

        removeButton.addEventListener("click", function () {
            input.value = "";
            previewContainer.hidden = true;
            previewImage.removeAttribute("src");
        });
    });

    ["checkout-form", "reservation-checkout-form"].forEach(function (formId) {
        var form = document.getElementById(formId);

        if (!form) {
            return;
        }

        form.addEventListener("submit", function (event) {
            var receiptInput = form.querySelector("[data-receipt-input]");

            if (receiptInput && (!receiptInput.files || receiptInput.files.length === 0)) {
                event.preventDefault();
                alert("Please upload your payment receipt before completing the order.");
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var orderModal = document.getElementById("dashboard-order-modal");
    var reservationModal = document.getElementById("dashboard-reservation-modal");
    var activeModal = null;

    var formatCurrency = function (value) {
        var amount = Number(value || 0);
        return "PHP " + amount.toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    var formatDateTime = function (value, options) {
        if (!value) {
            return "-";
        }

        var normalized = String(value).replace(" ", "T");
        var date = new Date(normalized);

        if (Number.isNaN(date.getTime())) {
            return String(value);
        }

        return date.toLocaleString("en-PH", options || {
            month: "short",
            day: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit"
        });
    };

    var applyStatusBadge = function (element, status) {
        if (!element) {
            return;
        }

        var normalized = String(status || "pending").toLowerCase();
        element.className = "status-pill status-pill--" + normalized;
        element.textContent = normalized.charAt(0).toUpperCase() + normalized.slice(1);
    };

    var statusMarkup = function (status) {
        var normalized = String(status || "pending").toLowerCase();
        return '<span class="status-pill status-pill--' + normalized + '">' +
            normalized.charAt(0).toUpperCase() + normalized.slice(1) +
        '</span>';
    };

    var openModal = function (modal) {
        if (!modal) {
            return;
        }

        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");
        document.body.classList.add("modal-open");
        activeModal = modal;
    };

    var closeModal = function (modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");
        if (activeModal === modal) {
            activeModal = null;
        }
        if (!document.querySelector(".dashboard-modal.is-open")) {
            document.body.classList.remove("modal-open");
        }
    };

    var renderOrderItems = function (container, items) {
        if (!container) {
            return;
        }

        if (!Array.isArray(items) || !items.length) {
            container.innerHTML = '<p class="dashboard-modal__empty">No item lines recorded for this order.</p>';
            return;
        }

        container.innerHTML = items.map(function (item) {
            return '<article class="dashboard-modal__item">' +
                '<div>' +
                    '<h4>' + String(item.menu_item_name || "Menu item") + '</h4>' +
                    '<p>' + String(item.size || "Default") + ' · Qty ' + String(item.quantity || 0) + '</p>' +
                '</div>' +
                '<strong>' + formatCurrency(item.subtotal || 0) + '</strong>' +
            '</article>';
        }).join("");
    };

    var renderReservationOrders = function (container, orders) {
        if (!container) {
            return;
        }

        if (!Array.isArray(orders) || !orders.length) {
            container.innerHTML = '<p class="dashboard-modal__empty">No linked orders were found for this reservation yet.</p>';
            return;
        }

        container.innerHTML = orders.map(function (order) {
            var itemMarkup = Array.isArray(order.items) && order.items.length
                ? '<ul class="order-line-list order-line-list--compact">' + order.items.map(function (item) {
                    return '<li><span>' + String(item.menu_item_name || "Menu item") + ' x' + String(item.quantity || 0) + '</span><span>' + String(item.size || "Default") + '</span></li>';
                }).join("") + '</ul>'
                : '';

            return '<article class="dashboard-modal__item dashboard-modal__item--stacked">' +
                '<div class="dashboard-modal__item-head">' +
                    '<div>' +
                        '<h4>Order ' + String(order.order_number || "-") + '</h4>' +
                        '<p>' + formatDateTime(order.created_at) + '</p>' +
                    '</div>' +
                    '<div class="dashboard-modal__item-badges">' +
                        statusMarkup(order.status || "pending") +
                        statusMarkup(order.payment_status || "pending") +
                    '</div>' +
                '</div>' +
                '<p class="dashboard-modal__item-total">' + formatCurrency(order.total_amount || 0) + '</p>' +
                itemMarkup +
            '</article>';
        }).join("");
    };

    document.addEventListener("click", function (event) {
        var button = event.target.closest("[data-open-order-modal]");

        if (button) {
            if (!orderModal) {
                return;
            }

            var payload = button.getAttribute("data-order-details");
            if (!payload) {
                return;
            }

            var order = JSON.parse(payload);
            document.getElementById("dashboard-order-number").textContent = String(order.order_number || "-");
            document.getElementById("dashboard-order-date").textContent = formatDateTime(order.created_at);
            document.getElementById("dashboard-order-flow").textContent = order.reservation_id ? "Reservation-linked order" : "Direct menu checkout";
            document.getElementById("dashboard-order-total").textContent = formatCurrency(order.total_amount || 0);
            document.getElementById("dashboard-order-item-count").textContent = String((order.items || []).length) + " item(s)";
            applyStatusBadge(document.getElementById("dashboard-order-status-badge"), order.status || "pending");
            applyStatusBadge(document.getElementById("dashboard-order-payment-badge"), order.payment_status || "pending");
            renderOrderItems(document.getElementById("dashboard-order-items"), order.items || []);

            var receiptLink = document.getElementById("dashboard-order-receipt-link");
            if (receiptLink) {
                if (order.receipt_image) {
                    receiptLink.href = window.location.origin + window.location.pathname.split("/dashboard")[0] + "/public/uploads/receipts/" + encodeURIComponent(order.receipt_image);
                    receiptLink.hidden = false;
                } else {
                    receiptLink.hidden = true;
                    receiptLink.removeAttribute("href");
                }
            }

            openModal(orderModal);
            return;
        }

        button = event.target.closest("[data-open-reservation-modal]");

        if (button) {
            if (!reservationModal) {
                return;
            }

            var payload = button.getAttribute("data-reservation-details");
            if (!payload) {
                return;
            }

            var reservation = JSON.parse(payload);
            document.getElementById("dashboard-reservation-number").textContent = "#" + String(reservation.id || "-");
            document.getElementById("dashboard-reservation-name").textContent =
                [reservation.first_name || "", reservation.last_name || ""].join(" ").trim() || "Guest";
            document.getElementById("dashboard-reservation-schedule").textContent =
                formatDateTime((reservation.date || "") + " " + (reservation.time || ""));
            document.getElementById("dashboard-reservation-guests").textContent = String(reservation.guests || 0) + " guest(s)";
            document.getElementById("dashboard-reservation-email").textContent = String(reservation.email || "-");
            document.getElementById("dashboard-reservation-phone").textContent = String(reservation.phone || "-");
            document.getElementById("dashboard-reservation-created-at").textContent = formatDateTime(reservation.created_at);
            document.getElementById("dashboard-reservation-order-count").textContent = String((reservation.orders || []).length) + " order(s)";
            applyStatusBadge(document.getElementById("dashboard-reservation-status-badge"), reservation.status || "pending");
            renderReservationOrders(document.getElementById("dashboard-reservation-orders"), reservation.orders || []);

            openModal(reservationModal);
        }
    });

    document.querySelectorAll("[data-dashboard-modal-close]").forEach(function (button) {
        button.addEventListener("click", function () {
            closeModal(button.closest(".dashboard-modal"));
        });
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && activeModal) {
            closeModal(activeModal);
        }
    });
});
